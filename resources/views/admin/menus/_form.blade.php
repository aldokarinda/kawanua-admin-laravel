@php
    $fieldClass = 'w-full rounded-lg border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm';
    $colorOptions = [
        'bg-blue-50', 'text-blue-500', 'text-blue-700',
        'bg-slate-50', 'text-slate-600', 'text-slate-900',
        'bg-purple-50', 'text-purple-500',
        'bg-emerald-50', 'text-emerald-600',
        'bg-amber-50', 'text-amber-600',
        'bg-rose-50', 'text-rose-600',
    ];

    $oldItems = old('items');
    $formItems = $oldItems !== null
        ? collect($oldItems)
        : $menu->items->map(fn ($item) => [
            'id' => $item->id,
            'label' => $item->label,
            'icon' => $item->icon,
            'route_name' => $item->route_name,
            'url' => $item->url,
            'bg_color_class' => $item->bg_color_class,
            'text_color_class' => $item->text_color_class,
            'permission_required' => $item->permission_required,
            'order' => $item->order,
        ]);

    for ($i = 0; $i < ($menu->exists ? 2 : 3); $i++) {
        $formItems->push([
            'id' => null,
            'label' => null,
            'icon' => null,
            'route_name' => null,
            'url' => null,
            'bg_color_class' => 'bg-blue-50',
            'text_color_class' => 'text-blue-500',
            'permission_required' => null,
            'order' => null,
        ]);
    }
@endphp

@if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg shadow-sm">
        Periksa kembali data menu yang ditandai.
    </div>
@endif

<form action="{{ $action }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    @csrf
    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Menu</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Menu</label>
                <input type="text" name="name" value="{{ old('name', $menu->name) }}" class="{{ $fieldClass }}" required>
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $menu->slug) }}" class="{{ $fieldClass }}" placeholder="otomatis dari nama">
                @error('slug') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                <input type="number" name="order" value="{{ old('order', $menu->order) }}" class="{{ $fieldClass }}">
                @error('order') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Warna Icon</label>
                <input type="text" name="color_class" value="{{ old('color_class', $menu->color_class) }}" list="menu-color-options" class="{{ $fieldClass }}" placeholder="text-blue-700">
                @error('color_class') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Permission Menu</label>
                <input type="text" name="permission_required" value="{{ old('permission_required', $menu->permission_required) }}" list="permission-options" class="{{ $fieldClass }}" placeholder="opsional">
                @error('permission_required') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-3">
                <label class="block text-sm font-medium text-gray-700 mb-1">Icon SVG Menu</label>
                <textarea name="icon" rows="3" class="{{ $fieldClass }}" placeholder="<svg ...></svg>">{{ old('icon', $menu->icon) }}</textarea>
                @error('icon') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <div class="border-t border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Item Menu</h3>
            <span class="text-xs text-gray-500">Isi baris kosong untuk menambah item baru.</span>
        </div>

        <datalist id="route-options">
            @foreach($namedRoutes as $routeName)
                <option value="{{ $routeName }}"></option>
            @endforeach
        </datalist>

        <datalist id="permission-options">
            @foreach($permissions as $permission)
                <option value="{{ $permission }}"></option>
            @endforeach
        </datalist>

        <datalist id="menu-color-options">
            @foreach($colorOptions as $colorOption)
                <option value="{{ $colorOption }}"></option>
            @endforeach
        </datalist>

        <div class="divide-y divide-gray-200 border-y border-gray-200">
            @foreach($formItems as $index => $item)
                <div class="py-4 grid grid-cols-1 lg:grid-cols-12 gap-3">
                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ data_get($item, 'id') }}">

                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Label</label>
                        <input type="text" name="items[{{ $index }}][label]" value="{{ data_get($item, 'label') }}" class="{{ $fieldClass }}">
                        @error("items.$index.label") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="lg:col-span-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Urutan</label>
                        <input type="number" name="items[{{ $index }}][order]" value="{{ data_get($item, 'order') }}" class="{{ $fieldClass }}">
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Route Name</label>
                        <input type="text" name="items[{{ $index }}][route_name]" value="{{ data_get($item, 'route_name') }}" list="route-options" class="{{ $fieldClass }}">
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">URL</label>
                        <input type="text" name="items[{{ $index }}][url]" value="{{ data_get($item, 'url') }}" class="{{ $fieldClass }}" placeholder="/admin/contoh">
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Style</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" name="items[{{ $index }}][bg_color_class]" value="{{ data_get($item, 'bg_color_class') }}" list="menu-color-options" class="{{ $fieldClass }}" placeholder="bg-blue-50">
                            <input type="text" name="items[{{ $index }}][text_color_class]" value="{{ data_get($item, 'text_color_class') }}" list="menu-color-options" class="{{ $fieldClass }}" placeholder="text-blue-500">
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Permission</label>
                        <input type="text" name="items[{{ $index }}][permission_required]" value="{{ data_get($item, 'permission_required') }}" list="permission-options" class="{{ $fieldClass }}">
                    </div>

                    <div class="lg:col-span-1 flex lg:items-end">
                        @if(data_get($item, 'id'))
                            <label class="inline-flex items-center gap-2 text-sm text-red-600">
                                <input type="checkbox" name="items[{{ $index }}][_delete]" value="1" class="kawanua-checkbox">
                                Hapus
                            </label>
                        @else
                            <span class="text-xs text-gray-400 lg:pb-3">Baru</span>
                        @endif
                    </div>

                    <div class="lg:col-span-12">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Icon SVG Item</label>
                        <textarea name="items[{{ $index }}][icon]" rows="2" class="{{ $fieldClass }}">{{ data_get($item, 'icon') }}</textarea>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
        <a href="{{ route('admin.menus.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Batal</a>
        <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 shadow-sm">
            {{ $submitLabel }}
        </button>
    </div>
</form>
