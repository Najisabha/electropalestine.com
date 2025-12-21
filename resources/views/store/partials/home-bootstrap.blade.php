@php($bestSelling = $bestSelling ?? collect())
@php($campaigns = $campaigns ?? collect())

<section class="py-4 text-light">
    <div class="container">
        @if ($campaigns->isNotEmpty())
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <p class="text-success small mb-1">{{ __('common.special_offers') }}</p>
                        <h2 class="h5 fw-bold mb-0">{{ __('common.advertising_campaigns') }}</h2>
                    </div>
                </div>
                <div id="campaignCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-indicators">
                        @foreach ($campaigns as $index => $campaign)
                            <button type="button"
                                    data-bs-target="#campaignCarousel"
                                    data-bs-slide-to="{{ $index }}"
                                    @if($index === 0) class="active" aria-current="true" @endif
                                    aria-label="{{ __('common.campaign_slide') }} {{ $index + 1 }}"></button>
                        @endforeach
                    </div>
                    <div class="carousel-inner rounded-4 overflow-hidden">
                        @foreach ($campaigns as $index => $campaign)
                            <div class="carousel-item @if($index === 0) active @endif">
                                <div class="row g-0 align-items-stretch">
                                    <div class="col-md-4 d-none d-md-block">
                                        @if (!empty($campaign->image))
                                            <img src="{{ asset('storage/'.$campaign->image) }}"
                                                 class="w-100 h-100 object-fit-cover"
                                                 alt="{{ $campaign->title }}">
                                        @else
                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-black text-secondary">
                                                {{ __('common.no_campaign_image') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-8">
                                        <div class="p-4 bg-dark h-100 d-flex flex-column justify-content-center">
                                            <h3 class="h5 fw-bold mb-2 text-success">{{ $campaign->title }}</h3>
                                            @if($campaign->starts_at || $campaign->ends_at)
                                                <p class="small text-secondary mb-2">
                                                    @if($campaign->starts_at)
                                                        {{ __('common.from') }} {{ $campaign->starts_at->format('Y-m-d') }}
                                                    @endif
                                                    @if($campaign->ends_at)
                                                        {{ __('common.to') }} {{ $campaign->ends_at->format('Y-m-d') }}
                                                    @endif
                                                </p>
                                            @endif
                                            <p class="mb-0 text-light small">
                                                {{ \Illuminate\Support\Str::limit($campaign->description, 160) ?: __('common.special_offer_default') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#campaignCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">{{ __('common.previous') }}</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#campaignCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">{{ __('common.next') }}</span>
                    </button>
                </div>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <p class="text-success small mb-1">{{ __('common.main_categories') }}</p>
                <h2 class="h5 fw-bold mb-0">{{ __('common.category_images_strip') }}</h2>
            </div>
        </div>

        <div class="strip-scroll">
            @forelse ($categories as $category)
                <a href="{{ route('categories.show', $category) }}" class="strip-card">
                    @if(!empty($category->image))
                        <img src="{{ asset('storage/'.$category->image) }}" class="strip-img" alt="{{ $category->translated_name }}">
                    @else
                        <div class="strip-img d-flex align-items-center justify-content-center bg-black text-secondary small">
                            {{ __('common.no_image') }}
                        </div>
                    @endif
                    <div class="p-3">
                        <div class="d-flex justify-content_between align-items-center mb-1">
                            <strong class="text-white">{{ $category->translated_name }}</strong>
                            <span class="badge bg-success text-dark">{{ __('common.main_category') }}</span>
                        </div>
                        <p class="text-secondary small mb-0">{{ \Illuminate\Support\Str::limit($category->translated_description, 80) }}</p>
                    </div>
                </a>
            @empty
                <p class="text-secondary">{{ __('common.no_categories_yet') }}</p>
            @endforelse
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
            <div>
                <p class="text-success small mb-1">{{ __('common.best_selling') }}</p>
                <h2 class="h5 fw-bold mb-0">{{ __('common.best_selling_strip') }}</h2>
            </div>
        </div>
        <div class="strip-scroll mb-4">
            @forelse ($bestSelling as $product)
                <a href="{{ route('products.show', $product) }}" class="strip-card text-decoration-none">
                    <div class="position-relative">
                        @if(!empty($product->image))
                            <img src="{{ asset('storage/'.$product->image) }}" class="strip-img" alt="{{ $product->translated_name }}">
                        @else
                            <div class="strip-img d-flex align-items-center justify-content-center bg-black text-secondary small">
                                لا توجد صورة
                            </div>
                        @endif
                        <span class="badge bg-success position-absolute top-0 start-0 m-2 small">
                            {{ $product->sales_count ?? 0 }} {{ __('common.sold') }}
                        </span>
                        <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2 small">
                            ★ {{ number_format($product->rating_average ?? 0, 1) }}
                        </span>
                    </div>
                    <div class="p-3">
                        <h6 class="mb-1 text-white">{{ $product->translated_name }}</h6>
                        <div class="text-muted small mb-1">
                            {{ $product->category->name ?? 'بدون تصنيف' }} • {{ $product->company->name ?? 'شركة غير معروفة' }}
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-success fw-bold">${{ number_format($product->price, 2) }}</span>
                            <span class="badge bg-secondary small">المخزون: {{ $product->stock }}</span>
                        </div>
                    </div>
                </a>
            @empty
                <p class="text-secondary">{{ __('common.no_products_message') }}</p>
            @endforelse
        </div>

        {{-- جميع المنتجات في قاعدة البيانات --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <p class="text-success small mb-1">{{ __('common.all_products_title') }}</p>
                <h2 class="h5 fw-bold mb-0">{{ __('common.all_store_products') }}</h2>
            </div>
        </div>

        <div class="strip-scroll mb-2">
            @forelse(($allProducts ?? collect()) as $product)
                <a href="{{ route('products.show', $product) }}" class="strip-card text-decoration-none">
                    <div class="position-relative">
                        @if(!empty($product->image))
                            <img src="{{ asset('storage/'.$product->image) }}" class="strip-img" alt="{{ $product->translated_name }}">
                        @else
                            <div class="strip-img d-flex align-items-center justify-content-center bg-black text-secondary small">
                                لا توجد صورة
                            </div>
                        @endif
                        @if($product->sales_count)
                            <span class="badge bg-success position-absolute top-0 start-0 m-2 small">
                                {{ $product->sales_count ?? 0 }} {{ __('common.sold') }}
                            </span>
                        @endif
                        @if(!is_null($product->rating_average))
                            <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2 small">
                                ★ {{ number_format($product->rating_average ?? 0, 1) }}
                            </span>
                        @endif
                    </div>
                    <div class="p-3">
                        <h6 class="mb-1 text-white">{{ $product->translated_name }}</h6>
                        <div class="text-muted small mb-1">
                            {{ $product->category->name ?? __('common.no_category') }} • {{ $product->company->name ?? __('common.unknown_company') }}
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-success fw-bold">${{ number_format($product->price, 2) }}</span>
                            <span class="badge bg-secondary small">{{ __('common.stock_label') }}: {{ $product->stock }}</span>
                        </div>
                    </div>
                </a>
            @empty
                <p class="text-secondary">{{ __('common.no_products_in_store') }}</p>
            @endforelse
        </div>
    </div>
</section>

