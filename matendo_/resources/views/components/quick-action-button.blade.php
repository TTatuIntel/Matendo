@props(['href', 'title', 'icon', 'color' => 'indigo'])

<a href="{{ $href }}" class="flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-700 p-3 rounded-md hover:bg-gray-100 dark:hover:bg-gray-600 transition">
    <div class="text-{{ $color }}-500 dark:text-{{ $color }}-400">
        @svg($icon, 'h-6 w-6')
    </div>
    <span class="mt-2 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $title }}</span>
</a>
```