<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <x-breadcrumb :items="[['label' => 'Master Data', 'url' => '#'], ['label' => 'Tags', 'url' => route('admin.tags.index')], ['label' => 'Create', 'url' => '#']]" />

        <div class="mt-4">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Create Tag</h1>
        </div>

        <div class="mt-6 max-w-3xl glass-panel rounded-xl shadow-sm border border-slate-200 dark:border-slate-700/50 p-6">
            <form action="{{ route('admin.tags.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <x-input-label for="name" value="Tag Name" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="slug" value="Slug (URL)" />
                        <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" :value="old('slug')" required />
                        <x-input-error :messages="$errors->get('slug')" class="mt-2" />
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <a href="{{ route('admin.tags.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">Cancel</a>
                    <x-primary-button>Save Tag</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
