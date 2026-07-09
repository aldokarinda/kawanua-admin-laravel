<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('dashboard')], ['label' => 'Profile Settings']]" />
        
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ __('Profile Settings') }}</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Manage your administrative profile parameters, credentials, and authentication layers.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side: Profile Forms (Col span 2) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Profile Information Card -->
                <div class="p-6 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
                    <div class="max-w-2xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>

                <!-- Update Password Card -->
                <div class="p-6 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
                    <div class="max-w-2xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>

                <!-- Delete Account Card -->
                <div class="p-6 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
                    <div class="max-w-2xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>

            <!-- Right Side: Status Widgets (Col span 1) -->
            <div class="space-y-6">
                <!-- User Profile Preview Widget -->
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 flex flex-col items-center text-center">
                    <div class="relative w-28 h-28 rounded-full border-4 border-slate-100 dark:border-slate-700 overflow-hidden shadow-md mb-4 bg-slate-50 dark:bg-slate-800">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" class="w-full h-full object-cover">
                        @else
                            <img src="{{ asset('images/logo.webp') }}" class="w-full h-full object-cover">
                        @endif
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $user->name }}</h3>
                    <p class="text-xs text-gray-500 dark:text-slate-400 font-medium">{{ $user->email }}</p>

                    <div class="w-full border-t border-slate-100 dark:border-slate-700/50 my-4 pt-4 space-y-2.5 text-left text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-400 dark:text-slate-500">Department</span>
                            <span class="font-semibold text-gray-700 dark:text-slate-300">{{ $user->department ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 dark:text-slate-500">Phone</span>
                            <span class="font-semibold text-gray-700 dark:text-slate-300">{{ $user->phone_number ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400 dark:text-slate-500">System Role</span>
                            <span class="font-semibold text-gray-700 dark:text-slate-300 capitalize">{{ $user->roles->pluck('name')->first() ?? 'User' }}</span>
                        </div>
                    </div>
                </div>

                <!-- 2FA Status Card Widget -->
                @php
                    $is2faActive = $user->twoFactorAuth && $user->twoFactorAuth->enabled;
                @endphp
                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-lg @if($is2faActive) bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 @else bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 @endif flex items-center justify-center text-lg">
                            <i class="bi bi-shield-lock-fill"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 dark:text-white">Two-Factor Auth</h4>
                            <p class="text-xs text-gray-400 dark:text-slate-500 mt-0.5">TOTP Multi-factor Protection</p>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-lg p-3.5 mb-4 text-xs">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 dark:text-slate-400">Status</span>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold @if($is2faActive) bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 @else bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-300 @endif">
                                {{ $is2faActive ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>

                    @if($is2faActive)
                        <p class="text-xs text-slate-400 dark:text-slate-500 mb-4">Your account is heavily secured. To disable or rotate security keys, please visit the central 2FA portal.</p>
                        <a href="{{ route('admin.security.2fa') }}" class="block text-center w-full py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-semibold text-xs rounded-lg transition-colors">
                            Manage Security Settings
                        </a>
                    @else
                        <p class="text-xs text-slate-400 dark:text-slate-500 mb-4">Protect your account from unauthorized logins by setting up Two-Factor Authenticator now.</p>
                        <a href="{{ route('admin.security.2fa') }}" class="block text-center w-full py-2 bg-primary-600 hover:bg-primary-700 text-white font-semibold text-xs rounded-lg transition-colors">
                            Setup Authenticator (2FA)
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
