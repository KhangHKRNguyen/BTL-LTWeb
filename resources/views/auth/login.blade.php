<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Địa chỉ Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Mật khẩu')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Ghi nhớ đăng nhập') }}</span>
            </label>
        </div>

        @if (session('login_attempts', 0) >= 2)
            <div class="mt-4">
                <x-input-label for="captcha" value="Xác minh hình ảnh (Captcha)" />
                
                <div class="flex items-center mt-1 gap-3">
                    <img src="{{ url('/captcha-image') }}" alt="Captcha" id="captcha-image" class="border rounded-md shadow-sm">
                    
                    <button type="button" 
                            onclick="document.getElementById('captcha-image').src = '{{ url('/captcha-image') }}?refresh=' + Date.now();" 
                            class="px-3 py-2 bg-gray-800 text-white text-xs font-semibold rounded-md hover:bg-gray-700 tracking-widest uppercase transition ease-in-out duration-150">
                        Tải lại
                    </button>
                </div>

                <x-text-input id="captcha" class="block mt-2 w-full" type="text" name="captcha" required placeholder="Nhập chữ bạn thấy trong ảnh..." autocomplete="off" />
                <x-input-error :messages="$errors->get('captcha')" class="mt-2" />
            </div>
        @endif

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    {{ __('Quên mật khẩu?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Đăng nhập') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>