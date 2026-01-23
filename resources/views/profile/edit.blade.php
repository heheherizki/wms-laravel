<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        
        <h1 class="text-3xl font-bold text-slate-900 tracking-tight">Pengaturan Profil</h1>
        
        @if (session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="bg-green-500 text-white px-4 py-3 rounded-lg text-sm font-bold">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 mb-4">Informasi Akun</h2>
                
                <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                            class="w-full mt-1 rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required 
                            class="w-full mt-1 rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-lg text-sm font-medium w-full md:w-auto">
                            Simpan Profil
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h2 class="text-lg font-bold text-slate-900 mb-4">Ganti Password</h2>
                <p class="text-xs text-slate-500 mb-4">Pastikan password minimal 6 karakter demi keamanan.</p>

                <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Password Saat Ini</label>
                        <input type="password" name="current_password" required 
                            class="w-full mt-1 rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Password Baru</label>
                        <input type="password" name="password" required 
                            class="w-full mt-1 rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" required 
                            class="w-full mt-1 rounded-lg border-slate-300 focus:ring-red-500 focus:border-red-500">
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium w-full md:w-auto">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>