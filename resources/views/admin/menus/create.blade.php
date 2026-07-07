<x-admin-layout>
    <div class="py-6 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Create Menu</h2>
                <p class="text-sm text-gray-500 mt-1">Design a new navigation link or group with live icon preview.</p>
            </div>
            <a href="{{ route('admin.menus.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">&larr; Back to menus</a>
        </div>

        <div class="bg-white rounded-xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 p-8" x-data="{ svgIcon: '' }">
            <form action="{{ route('admin.menus.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Menu Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" class="w-full rounded-lg border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="e.g. Sales Reports" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug <span class="text-red-500">*</span></label>
                        <input type="text" name="slug" class="w-full rounded-lg border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="e.g. sales-reports" required>
                    </div>
                </div>

                <div class="mb-6 bg-blue-50/50 p-4 rounded-lg border border-blue-100">
                    <label class="block text-sm font-semibold text-blue-900 mb-2">Hierarchy Configuration</label>
                    <p class="text-xs text-blue-700 mb-4">Select a parent to make this a Sub-Menu. Leave empty to make it a Root Menu.</p>
                    <select name="parent_id" class="w-full rounded-lg border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">-- None (Root Menu) --</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }} (Root)</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Route Name</label>
                        <input type="text" name="route_name" class="w-full rounded-lg border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="e.g. admin.reports.index">
                        <p class="text-xs text-gray-500 mt-1">Laravel route name. Leave empty if this is just an accordion group.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">URL (Optional)</label>
                        <input type="text" name="url" class="w-full rounded-lg border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="e.g. https://google.com">
                        <p class="text-xs text-gray-500 mt-1">External link or static URL. Overridden by Route Name.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Required Permission</label>
                        <input type="text" name="permission_name" class="w-full rounded-lg border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" placeholder="e.g. menu.view">
                        <p class="text-xs text-gray-500 mt-1">Hide this menu from users who don't have this permission.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Order Index</label>
                        <input type="number" name="order" value="0" class="w-full rounded-lg border-gray-200 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <p class="text-xs text-gray-500 mt-1">Lower numbers appear first.</p>
                    </div>
                </div>

                <!-- Visual Icon Picker -->
                <div class="mb-8" x-data="{ 
                    selectedIcon: '', 
                    searchQuery: '',
                    icons: [
                        'bi-grid-fill', 'bi-gear-fill', 'bi-people-fill', 'bi-person-badge-fill', 
                        'bi-shield-lock-fill', 'bi-list-ul', 'bi-file-earmark-text-fill', 'bi-box-seam-fill',
                        'bi-envelope-fill', 'bi-bell-fill', 'bi-calendar-event-fill', 'bi-chat-dots-fill',
                        'bi-bar-chart-fill', 'bi-pie-chart-fill', 'bi-house-fill', 'bi-folder-fill',
                        'bi-image-fill', 'bi-camera-video-fill', 'bi-mic-fill', 'bi-music-note-beamed',
                        'bi-play-circle-fill', 'bi-shop', 'bi-cart-fill', 'bi-credit-card-fill',
                        'bi-wallet-fill', 'bi-bank', 'bi-award-fill', 'bi-bookmark-fill',
                        'bi-star-fill', 'bi-heart-fill', 'bi-lightbulb-fill', 'bi-lightning-fill',
                        'bi-globe', 'bi-map-fill', 'bi-geo-alt-fill', 'bi-telephone-fill',
                        'bi-key-fill', 'bi-lock-fill', 'bi-unlock-fill', 'bi-shield-check',
                        'bi-check-circle-fill', 'bi-x-circle-fill', 'bi-exclamation-triangle-fill', 'bi-info-circle-fill',
                        'bi-question-circle-fill', 'bi-arrow-right-circle-fill', 'bi-download', 'bi-upload',
                        'bi-cloud-fill', 'bi-cloud-arrow-up-fill', 'bi-cloud-arrow-down-fill', 'bi-hdd-fill',
                        'bi-laptop', 'bi-phone', 'bi-tablet', 'bi-printer-fill',
                        'bi-tv', 'bi-watch', 'bi-wifi', 'bi-bluetooth'
                    ],
                    get filteredIcons() {
                        if(this.searchQuery === '') return this.icons;
                        return this.icons.filter(i => i.includes(this.searchQuery.toLowerCase()));
                    }
                }">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center rounded-t-lg border-x border-t">
                        <label class="block text-sm font-semibold text-gray-900">Icon Picker</label>
                        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded font-medium">Select an Icon</span>
                    </div>
                    <div class="p-4 border border-t-0 border-gray-200 rounded-b-lg">
                        <input type="hidden" name="icon" x-model="selectedIcon">
                        
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-16 h-16 flex-shrink-0 flex items-center justify-center bg-blue-50 border border-blue-200 rounded-xl text-blue-600">
                                <i x-show="selectedIcon" :class="selectedIcon + ' text-3xl'"></i>
                                <svg x-show="!selectedIcon" class="w-8 h-8 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            </div>
                            <div class="flex-1 relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                    <i class="bi bi-search text-gray-400"></i>
                                </span>
                                <input type="text" x-model="searchQuery" placeholder="Search icons..." class="w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-2 max-h-60 overflow-y-auto p-1 border border-gray-100 rounded bg-gray-50/50">
                            <template x-for="icon in filteredIcons" :key="icon">
                                <button type="button" 
                                        @click="selectedIcon = 'bi ' + icon"
                                        :class="{'ring-2 ring-blue-500 bg-blue-50 text-blue-600': selectedIcon === 'bi ' + icon, 'text-gray-500 hover:bg-gray-100': selectedIcon !== 'bi ' + icon}"
                                        class="p-2 flex items-center justify-center rounded-lg transition-colors border border-transparent hover:border-gray-200">
                                    <i :class="'bi ' + icon + ' text-xl'"></i>
                                </button>
                            </template>
                        </div>
                        <p class="text-xs text-gray-400 mt-3 text-right">Powered by Bootstrap Icons</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('admin.menus.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 shadow-sm">Save Menu</button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
