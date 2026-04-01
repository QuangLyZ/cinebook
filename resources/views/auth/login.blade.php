@extends('layouts.app')

@section('content')
<!-- Background Iron Man xịn xò -->
<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('ironman.jpg') }}');">
    <!-- Overlay tối để làm nổi bật form và dịu mắt -->
    <div class="absolute inset-0 bg-black/50 z-0"></div>

    <!-- Container Form Glassmorphism (Blur Mica) -->
    <div class="w-full max-w-md space-y-8 p-10 rounded-[2rem] shadow-[0_8px_32px_0_rgba(0,0,0,0.5)] border border-white/10 relative z-10 block backdrop-blur-xl bg-gray-900/50">
        <div>
            <h2 class="text-center text-4xl font-extrabold text-white tracking-widest font-sans mb-2" style="text-shadow: 0 2px 10px rgba(0,0,0,0.5);">
                CINE<span class="text-red-500">BOOK</span>
            </h2>
            <p class="text-center text-xl text-gray-200 tracking-wide mb-6">
                Welcome Back
            </p>
            
            <p class="text-center text-sm text-gray-300">
                Are You New Member? 
                <a href="/register" class="font-bold text-white hover:text-red-400 transition-colors drop-shadow-md">
                    Sign UP
                </a>
            </p>
        </div>
        
        <form class="mt-8 space-y-6" action="{{ route('login.post') }}" method="POST">
            @csrf
            
            @if($errors->any())
                <div class="bg-red-500/20 backdrop-blur-sm border border-red-500/50 text-red-100 text-sm p-3 rounded-xl shadow-lg">
                    <ul class="list-disc list-inside text-left">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-5">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-200 mb-1">Email address / Phone</label>
                    <input id="email" name="email" value="{{ old('email') }}" type="text" autocomplete="email" required class="appearance-none relative block w-full px-4 py-3 border border-white/30 bg-transparent text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-400/50 focus:border-cyan-400/50 sm:text-sm transition-all placeholder-gray-400 shadow-inner" placeholder="example@gmail.com">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-200 mb-1">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none relative block w-full px-4 py-3 border border-white/30 bg-transparent text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-cyan-400/50 focus:border-cyan-400/50 sm:text-sm transition-all placeholder-gray-400 shadow-inner" placeholder="••••••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-cyan-500 focus:ring-cyan-400 border-white/30 rounded bg-transparent outline-none cursor-pointer">
                    <label for="remember" class="ml-2 block text-sm text-gray-300 cursor-pointer">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="/forgot-password" class="font-medium text-gray-300 hover:text-white transition-colors">
                        Forget Password ?
                    </a>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="group relative w-full flex justify-center py-3.5 px-4 border border-cyan-400/50 text-lg font-bold rounded-xl text-white bg-cyan-600/60 hover:bg-cyan-500/80 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-400 focus:ring-offset-transparent transition-all shadow-[0_0_15px_rgba(8,145,178,0.5)] hover:shadow-[0_0_25px_rgba(8,145,178,0.8)] backdrop-blur-sm tracking-wider">
                    Login
                </button>
            </div>
            
            <div class="mt-8">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-white/20"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-3 bg-transparent text-gray-300 backdrop-blur-sm rounded-full">Or continue with</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-4">
                    <a href="{{ route('google.login') }}" class="w-full inline-flex justify-center py-2.5 px-4 border border-white/20 rounded-xl shadow-sm bg-white/5 text-sm font-medium text-white hover:bg-white/10 transition-colors backdrop-blur-sm">
                        <i class="fa-brands fa-google text-red-400 text-lg"></i>
                    </a>
                    <a href="#" class="w-full inline-flex justify-center py-2.5 px-4 border border-white/20 rounded-xl shadow-sm bg-white/5 text-sm font-medium text-white hover:bg-white/10 transition-colors backdrop-blur-sm">
                        <i class="fa-brands fa-facebook text-blue-400 text-lg"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
