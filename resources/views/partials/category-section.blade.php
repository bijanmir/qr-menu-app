{{-- resources/views/partials/category-section.blade.php --}}
<div id="category-{{ $category->id }}" data-category="{{ $category->id }}" class="mb-8">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-neutral-900 flex items-center">
            @if($category->icon)
                <span class="mr-3 text-3xl">{{ $category->icon }}</span>
            @endif
            {{ $category->name }}
        </h2>
    </div>

    <div class="grid gap-4">
        @forelse($category->items->where('visible', true)->where('available', true) as $item)
            @include('partials.item-card', ['item' => $item])
        @empty
            <p class="text-neutral-500 text-center py-8">No items available in this category.</p>
        @endforelse
    </div>
</div>
