{{--
 * Copyright (c) 2026 Aldo Karinda, UNKLAB Business School.
 * All rights reserved.
 * Licensed under the Non-Commercial Software License Agreement.
--}}
@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 focus:border-primary-600 dark:focus:border-primary-500 focus:ring-primary-600 dark:focus:ring-primary-500 rounded-lg shadow-sm']) }}>
