{{-- resources/views/partials/item-card.blade.php --}}
<div class="card hover:shadow-soft-lg transition-shadow cursor-pointer" 
     onclick="window.itemModal.open({{ $item->id }})">
    <div class="flex">
        @if($item->image)
        <div class="w-24 h-24 flex-shrink-0">
            <img src="{{ asset('storage/' . $item->image) }}" 
                 alt="{{ $item->name }}" 
                 class="w-full h-full object-cover rounded-l-2xl">
        </div>
        @endif
        
        <div class="flex-1 p-4">
            <div class="flex justify-between items-start mb-2">
                <h3 class="font-semibold text-neutral-900">{{ $item->name }}</h3>
                <span class="font-bold text-primary-600">{{ $item->formatted_price }}</span>
            </div>
            
            @if($item->description)
            <p class="text-sm text-neutral-600 mb-3 line-clamp-2">{{ $item->description }}</p>
            @endif
            
            <div class="flex items-center justify-between">
                <!-- Tags and Allergens -->
                <div class="flex flex-wrap gap-1">
                    @if($item->tags)
                        @foreach($item->tags as $tag)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-700">
                            {{ $tag }}
                        </span>
                        @endforeach
                    @endif
                    
                    @if($item->allergens)
                        @foreach($item->allergens as $allergen)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                            {{ $allergen }}
                        </span>
                        @endforeach
                    @endif
                </div>
                
                <!-- Rating -->
                @if($item->totalRatings() > 0)
                <div class="flex items-center text-sm text-neutral-600">
                    <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    {{ number_format($item->averageRating(), 1) }} ({{ $item->totalRatings() }})
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
