<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-2xl">
        <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Security Center', 'url' => route('admin.security.index')], ['label' => '2FA Setup']]" />
        
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-200">Two-Factor Authentication Setup</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-slate-400">Set up 2FA for {{ $user->name }} by scanning the QR code with an authenticator app (e.g. Google Authenticator, Microsoft Authenticator).</p>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-100 dark:border-slate-700 p-8 flex flex-col items-center">
            {{-- QR Code --}}
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-inner mb-6">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qr_url) }}" alt="QR Code" class="w-48 h-48">
            </div>

            {{-- Text Secret --}}
            <div class="w-full text-center mb-8">
                <p class="text-xs font-semibold text-gray-400 dark:text-slate-500 uppercase tracking-wider">Secret Key</p>
                <code class="mt-1 block text-lg font-mono font-bold text-gray-800 dark:text-slate-100 tracking-wider bg-gray-50 dark:bg-slate-900 py-2 px-4 rounded-lg select-all border border-gray-100 dark:border-slate-700 max-w-xs mx-auto">
                    {{ chunk_split($secret, 4, ' ') }}
                </code>
                <p class="text-xs text-gray-400 dark:text-slate-500 mt-2">Enter this key manually if your app cannot scan the QR code.</p>
            </div>

            {{-- Verify form --}}
            <form action="{{ route('admin.security.2fa-enable', $user->id) }}" method="POST" class="w-full max-w-xs">
                @csrf
                <div class="mb-4">
                    <label for="code" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Verification Code</label>
                    <input type="text" name="code" id="code" required max="6" placeholder="000000" class="block w-full rounded-lg border-gray-300 dark:border-slate-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 bg-white dark:bg-slate-700 text-center text-xl font-bold tracking-widest dark:text-slate-200">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="w-full py-2.5 px-4 border border-transparent rounded-lg text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Verify and Enable
                </button>
            </form>
        </div>
    </div>
</x-admin-layout>
