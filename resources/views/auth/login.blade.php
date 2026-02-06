<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - WMS Gudang</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Animasi Bubble Bergerak */
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    {{-- BACKGROUND EFFECTS (Bubbles) --}}
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
        <div class="absolute top-0 -left-4 w-72 h-72 bg-red-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
        <div class="absolute top-0 -right-4 w-72 h-72 bg-orange-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-20 w-72 h-72 bg-rose-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>

    {{-- LOGIN CARD (Glassmorphism) --}}
    <div class="relative z-10 bg-white/80 backdrop-blur-xl p-8 rounded-3xl shadow-2xl w-full max-w-md border border-white/50">
        
        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-tr from-red-600 to-orange-500 text-white mb-4 shadow-lg shadow-red-500/30">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Selamat Datang</h1>
            <p class="text-slate-500 text-sm mt-1">Akses dashboard WMS Gudang Anda.</p>
        </div>

        {{-- Form --}}
        <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
            @csrf

            {{-- Email Input --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1 ml-1">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all outline-none text-slate-800 placeholder-slate-400"
                        placeholder="nama@perusahaan.com">
                </div>
                @error('email')
                    <p class="text-red-500 text-xs mt-1 ml-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password Input --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1 ml-1">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input type="password" name="password" required
                        class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all outline-none text-slate-800 placeholder-••••••••"
                        placeholder="••••••••">
                </div>
            </div>

            {{-- Remember Me & Forgot (Optional Add-on) --}}
            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center text-slate-500 cursor-pointer hover:text-slate-700">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-red-600 focus:ring-red-500 mr-2">
                    Ingat Saya
                </label>
                {{-- <a href="#" class="text-red-600 hover:text-red-700 font-medium">Lupa Password?</a> --}}
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white font-bold py-3 rounded-xl transition-all shadow-lg shadow-red-500/30 transform hover:-translate-y-0.5 active:scale-95">
                Masuk ke Sistem
            </button>
        </form>

        {{-- Footer --}}
        <div class="mt-8 text-center">
            <p class="text-xs text-slate-400">&copy; {{ date('Y') }} WMS Gudang System v2.0</p>
        </div>
    </div>

</body>
</html>