<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashAccount;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\DB;
use App\Models\CashTransaction;

class FinanceController extends Controller
{
    // 1. DASHBOARD KEUANGAN
    public function index()
    {
        $accounts = CashAccount::withCount('transactions')->get();
        $categories = ExpenseCategory::orderBy('name')->get();
        $totalCash = $accounts->sum('balance');

        return view('finance.index', compact('accounts', 'categories', 'totalCash'));
    }

    // --- BAGIAN AKUN KAS/BANK ---

    // 2. HALAMAN TAMBAH AKUN (BARU)
    public function createAccount()
    {
        return view('finance.accounts.create');
    }

    // 3. PROSES SIMPAN AKUN
    public function storeAccount(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'balance' => 'nullable|numeric|min:0',
        ]);

        CashAccount::create([
            'name' => $request->name,
            'account_number' => $request->account_number,
            'description' => $request->description,
            'balance' => $request->balance ?? 0,
        ]);

        return redirect()->route('finance.index')->with('success', 'Akun Kas/Bank berhasil dibuat.');
    }

    // 4. HALAMAN EDIT AKUN (BARU)
    public function editAccount($id)
    {
        $account = CashAccount::findOrFail($id);
        return view('finance.accounts.edit', compact('account'));
    }

    // 5. PROSES UPDATE AKUN
    public function updateAccount(Request $request, $id)
    {
        $account = CashAccount::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $account->update([
            'name' => $request->name,
            'account_number' => $request->account_number,
            'description' => $request->description,
            // Balance tidak diupdate manual di sini untuk keamanan, harus via transaksi / adjustment
        ]);

        return redirect()->route('finance.index')->with('success', 'Data akun berhasil diperbarui.');
    }

    // 6. HAPUS AKUN
    public function destroyAccount($id)
    {
        $account = CashAccount::withCount('transactions')->findOrFail($id);
        
        if ($account->transactions_count > 0) {
            return back()->with('error', 'Gagal hapus! Akun ini sudah memiliki riwayat transaksi.');
        }

        $account->delete();
        return back()->with('success', 'Akun berhasil dihapus.');
    }

    // --- BAGIAN KATEGORI BIAYA ---

    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:expense_categories,name']);
        ExpenseCategory::create(['name' => $request->name]);
        return back()->with('success', 'Kategori biaya berhasil ditambahkan.');
    }

    public function destroyCategory($id)
    {
        $isUsed = DB::table('cash_transactions')->where('expense_category_id', $id)->exists();
        if($isUsed) {
            return back()->with('error', 'Kategori digunakan dalam transaksi, tidak bisa dihapus.');
        }
        ExpenseCategory::destroy($id);
        return back()->with('success', 'Kategori dihapus.');
    }

    // 7. LIST RIWAYAT TRANSAKSI
    public function transactions(Request $request)
    {
        $query = CashTransaction::with(['account', 'category', 'user']);

        // Filter Tanggal
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }
        
        // Filter Akun
        if ($request->account_id) {
            $query->where('cash_account_id', $request->account_id);
        }

        $transactions = $query->latest('date')->latest('id')->paginate(15);
        $accounts = CashAccount::orderBy('name')->get();

        return view('finance.transactions.index', compact('transactions', 'accounts'));
    }

    // 8. FORM TAMBAH TRANSAKSI
    public function createTransaction()
    {
        $accounts = CashAccount::orderBy('name')->get();
        $categories = ExpenseCategory::orderBy('name')->get();
        
        return view('finance.transactions.create', compact('accounts', 'categories'));
    }

    // 9. PROSES SIMPAN TRANSAKSI
    public function storeTransaction(Request $request)
    {
        $request->validate([
            'cash_account_id' => 'required|exists:cash_accounts,id',
            'type' => 'required|in:in,out', // Masuk (Income) atau Keluar (Expense)
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'expense_category_id' => 'nullable|required_if:type,out|exists:expense_categories,id',
        ]);

        $account = CashAccount::findOrFail($request->cash_account_id);

        // Validasi Saldo Cukup (Khusus Pengeluaran)
        if ($request->type == 'out' && $account->balance < $request->amount) {
            return back()->with('error', 'Saldo tidak cukup! Saldo saat ini: Rp ' . number_format($account->balance, 0));
        }

        DB::transaction(function () use ($request, $account) {
            // 1. Simpan Log Transaksi
            CashTransaction::create([
                'cash_account_id' => $request->cash_account_id,
                'expense_category_id' => $request->type == 'out' ? $request->expense_category_id : null,
                'user_id' => auth()->id(),
                'type' => $request->type,
                'amount' => $request->amount,
                'date' => $request->date,
                'description' => $request->description,
                'reference_id' => $request->reference_id,
            ]);

            // 2. Update Saldo Akun
            if ($request->type == 'in') {
                $account->increment('balance', $request->amount);
            } else {
                $account->decrement('balance', $request->amount);
            }
        });

        return redirect()->route('finance.transactions.index')->with('success', 'Transaksi berhasil dicatat & saldo diperbarui.');
    }

    // 10. HALAMAN FORM TRANSFER
    public function createTransfer()
    {
        // Ambil akun yang punya saldo > 0 untuk Sumber
        $accounts = CashAccount::orderBy('name')->get();
        return view('finance.transfer.create', compact('accounts'));
    }

    // 11. PROSES SIMPAN TRANSFER
    public function storeTransfer(Request $request)
    {
        $request->validate([
            'from_account_id' => 'required|exists:cash_accounts,id',
            'to_account_id' => 'required|exists:cash_accounts,id|different:from_account_id', // Tidak boleh sama
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
        ]);

        $fromAccount = CashAccount::findOrFail($request->from_account_id);
        $toAccount = CashAccount::findOrFail($request->to_account_id);

        // 1. Cek Saldo Pengirim
        if ($fromAccount->balance < $request->amount) {
            return back()->with('error', 'Saldo akun asal tidak cukup! Saldo: Rp ' . number_format($fromAccount->balance, 0));
        }

        DB::transaction(function () use ($request, $fromAccount, $toAccount) {
            $refId = 'TRF-' . time(); // ID Unik untuk pairing transaksi

            // 2. CATAT PENGELUARAN DI AKUN ASAL
            CashTransaction::create([
                'cash_account_id' => $fromAccount->id,
                'user_id' => auth()->id(),
                'type' => 'out', // Kita catat sebagai OUT agar grafik pengeluaran konsisten
                'amount' => $request->amount,
                'date' => $request->date,
                'description' => 'Transfer ke ' . $toAccount->name,
                'reference_id' => $refId,
            ]);
            $fromAccount->decrement('balance', $request->amount);

            // 3. CATAT PEMASUKAN DI AKUN TUJUAN
            CashTransaction::create([
                'cash_account_id' => $toAccount->id,
                'user_id' => auth()->id(),
                'type' => 'in', // Kita catat sebagai IN
                'amount' => $request->amount,
                'date' => $request->date,
                'description' => 'Transfer dari ' . $fromAccount->name,
                'reference_id' => $refId,
            ]);
            $toAccount->increment('balance', $request->amount);
        });

        return redirect()->route('finance.index')->with('success', 'Transfer saldo berhasil dilakukan.');
    }
}