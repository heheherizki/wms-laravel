<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TransactionExport implements FromView, ShouldAutoSize
{
    protected $transactions;
    protected $startDate;
    protected $endDate;

    // Terima data dari Controller
    public function __construct($transactions, $startDate, $endDate)
    {
        $this->transactions = $transactions;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    // Load view khusus untuk Excel
    public function view(): View
    {
        return view('reports.excel', [
            'transactions' => $this->transactions,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }
}