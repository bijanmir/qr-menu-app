<!-- resources/views/auth/register.blade.php -->
<x-guest-layout>
    <!-- Premium Header -->
    <div class="text-center mb-8">
        <div class="auth-logo">
            <svg class="w-16 h-16" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="64" height="64" rx="16" fill="url(#gradient)"/>
                <path d="M20 24h24v4H20v-4zm0 8h24v4H20v-4zm0 8h16v4H20v-4z" fill="white"/>
                <defs>
                    <linearGradient id="gradient" x1="0" y1="0" x2="64" y2="64">
                        <stop offset="0%" stop-color="rgb(59 130 246)"/>
                        <stop offset="100%" stop-color="rgb(147 51 234)"/>
                    </linearGradient>
                </defs>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">Create Your Account</h1>
        <p class="text-gray-600 dark:text-gray-400">Start your digital menu transformation today</p>
    </div>
    
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" 
                class="block mt-1 w-full" 
                type="text" 
                name="name" 
                :value="old('name')" 
                required 
                autofocus 
                autocomplete="name"
                placeholder="Enter your full name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" 
                class="block mt-1 w-full" 
                type="email" 
                name="email" 
                :value="old('email')" 
                required 
                autocomplete="username"
                placeholder="Enter your email address" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" 
                class="block mt-1 w-full"
                type="password"
                name="password"
                required 
                autocomplete="new-password"
                placeholder="Create a strong password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            
            <!-- Password Requirements -->
            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                <div class="password-requirement">At least 8 characters long</div>
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" 
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation" 
                required 
                autocomplete="new-password"
                placeholder="Confirm your password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Terms Agreement -->
        <div class="block mt-6">
            <label for="terms" class="inline-flex items-start">
                <input id="terms" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 mt-1" name="terms" required>
                <span class="ms-3 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                    I agree to the 
                    <a href="#" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium underline">Terms of Service</a> 
                    and 
                    <a href="#" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium underline">Privacy Policy</a>
                </span>
            </label>
        </div>

        <!-- Marketing Opt-in -->
        <div class="block mt-4">
            <label for="marketing" class="inline-flex items-start">
                <input id="marketing" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 mt-1" name="marketing">
                <span class="ms-3 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                    Send me product updates and restaurant industry tips (optional)
                </span>
            </label>
        </div>

        <div class="auth-actions mt-6">
            <x-primary-button class="w-full justify-center">
                {{ __('Create Account') }}
            </x-primary-button>
        </div>
    </form>
    
    <!-- Login Link -->
    <div class="text-center mt-6">
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Already have an account?</p>
        <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
            Sign in instead
        </a>
    </div>
</x-guest-layout>