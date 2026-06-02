<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Quên mật khẩu? Không sao cả. Chỉ cần nhập địa chỉ email và chúng tôi sẽ gửi cho bạn một liên kết đặt lại mật khẩu mới qua email.') }}
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Địa chỉ Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

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

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Gửi liên kết đặt lại mật khẩu') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>