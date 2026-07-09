<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <x-breadcrumb :items="[['label' => 'Master Data', 'url' => '#'], ['label' => 'Regions', 'url' => route('admin.regions.index')], ['label' => 'Create', 'url' => '#']]" />

        <div class="mt-4">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Create Region</h1>
        </div>

        <div class="mt-6 max-w-3xl glass-panel rounded-xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-6" x-data="{ type: '{{ old('type', 'province') }}' }">
            <form action="{{ route('admin.regions.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <x-input-label for="name" value="Region Name" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="type" value="Type" />
                        <select id="type" name="type" x-model="type" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm" required>
                            <option value="province">Province</option>
                            <option value="city">City/Regency</option>
                        </select>
                        <x-input-error :messages="$errors->get('type')" class="mt-2" />
                    </div>

                    <div x-show="type === 'city'">
                        <x-input-label for="parent_id" value="Parent Province" />
                        <select id="parent_id" name="parent_id" class="mt-1 block w-full border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm">
                            <option value="">Select Province...</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('parent_id')" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('admin.regions.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">Cancel</a>
                    <x-primary-button>Save Region</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
