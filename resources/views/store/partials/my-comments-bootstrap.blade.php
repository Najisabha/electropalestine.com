<section class="py-5 text-light">
    <div class="container">
        <h1 class="h4 fw-bold mb-4">تعليقاتي</h1>

        @if($comments->isEmpty())
            <div class="glass rounded-4 p-5 text-center">
                <i class="bi bi-chat-left-text display-1 text-secondary mb-3"></i>
                <h3 class="h5 text-secondary mb-2">لا توجد تعليقات</h3>
                <p class="text-secondary small mb-4">لم تقم بإضافة أي تعليقات على المنتجات حتى الآن.</p>
                <a href="{{ route('home') }}" class="btn btn-main">
                    <i class="bi bi-arrow-left"></i>
                    العودة للمتجر
                </a>
            </div>
        @else
            <div class="glass rounded-4 p-4">
                <div class="list-group">
                    @foreach($comments as $comment)
                        <div class="list-group-item glass border border-secondary-subtle mb-3 rounded-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <strong class="text-white">{{ $comment->product_name ?? 'منتج محذوف' }}</strong>
                                    <span class="text-secondary small ms-2">
                                        {{ $comment->created_at->format('Y/m/d H:i') }}
                                    </span>
                                </div>
                                <div>
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= ($comment->rating ?? 0))
                                            <i class="bi bi-star-fill text-warning"></i>
                                        @else
                                            <i class="bi bi-star text-secondary"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <p class="text-light mb-0">{{ $comment->comment ?? 'لا يوجد نص' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
