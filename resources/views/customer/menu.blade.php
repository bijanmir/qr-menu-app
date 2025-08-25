{{-- resources/views/customer/menu.blade.php --}}
@extends('layouts.customer')

@section('main-content')
<div class="pb-20">
    <!-- Category Navigation -->
    @if($categories->isNotEmpty())
    <div class="sticky top-20 z-30 bg-white/90 backdrop-blur-sm border-b border-neutral-100">
        <div class="flex gap-2 overflow-x-auto py-4 px-4 mobile-scroll-snap custom-scrollbar">
            @foreach($categories as $category)
            <button 
                data-category="{{ $category->id }}"
                onclick="scrollToCategory('{{ $category->id }}')"
                class="category-nav-btn px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all duration-200 border border-gray-200 bg-white text-gray-700 hover:bg-amber-50 hover:border-amber-200 hover:text-amber-800"
            >
                @if($category->icon)
                    <span class="mr-2">{{ $category->icon }}</span>
                @endif
                {{ $category->name }}
            </button>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Menu Items -->
    <div id="menu-items" class="px-4 py-6">
        @foreach($categories as $category)
            @include('partials.category-section', ['category' => $category])
        @endforeach
    </div>
</div>

<script>
function scrollToCategory(categoryId) {
    const categoryElement = document.getElementById(`category-${categoryId}`);
    const navButtons = document.querySelectorAll('.category-nav-btn');
    
    if (categoryElement) {
        // Update active states
        navButtons.forEach(btn => {
            if (btn.dataset.category === categoryId) {
                btn.classList.remove('bg-white', 'text-gray-700', 'border-gray-200');
                btn.classList.add('bg-amber-100', 'text-amber-800', 'border-amber-300', 'shadow-sm');
            } else {
                btn.classList.remove('bg-amber-100', 'text-amber-800', 'border-amber-300', 'shadow-sm');
                btn.classList.add('bg-white', 'text-gray-700', 'border-gray-200');
            }
        });
        
        // Smooth scroll to category with offset for sticky header
        const headerOffset = 140; // Adjust based on your header height
        const categoryTop = categoryElement.offsetTop - headerOffset;
        
        window.scrollTo({
            top: categoryTop,
            behavior: 'smooth'
        });
    }
}

// Intersection Observer for automatic active state updates while scrolling
document.addEventListener('DOMContentLoaded', function() {
    const categories = document.querySelectorAll('[data-category]');
    const navButtons = document.querySelectorAll('.category-nav-btn');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const categoryId = entry.target.dataset.category;
                
                // Update active nav button
                navButtons.forEach(btn => {
                    if (btn.dataset.category === categoryId) {
                        btn.classList.remove('bg-white', 'text-gray-700', 'border-gray-200');
                        btn.classList.add('bg-amber-100', 'text-amber-800', 'border-amber-300', 'shadow-sm');
                    } else {
                        btn.classList.remove('bg-amber-100', 'text-amber-800', 'border-amber-300', 'shadow-sm');
                        btn.classList.add('bg-white', 'text-gray-700', 'border-gray-200');
                    }
                });
            }
        });
    }, {
        root: null,
        rootMargin: '-30% 0% -60% 0%', // Trigger when category is in middle third of viewport
        threshold: 0
    });
    
    // Observe all category sections
    categories.forEach(category => {
        observer.observe(category);
    });
});
</script>
@endsection
