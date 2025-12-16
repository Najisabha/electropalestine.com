<section class="container py-4 text-light">
    <div class="row g-4">
        <div class="col-12">
            <div class="glass p-4">
                <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                    <div>
                        <p class="text-success small mb-1">مركز الحملات الإعلانية</p>
                        <h1 class="h4 fw-bold mb-2">هل لديك حملات إعلانية سابقة؟</h1>
                        <p class="text-secondary small mb-0">
                            يمكنك مراجعة الحملات التي أنشأتها من قبل أو البدء في إنشاء حملة جديدة بخطوة واحدة.
                        </p>
                    </div>
                    <div class="text-end">
                        <div class="small text-secondary mb-1">إجمالي الحملات التي قمت بها حتى الآن</div>
                        <div class="display-6 fw-bold text-success">{{ number_format($totalCampaigns ?? 0) }}</div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a href="#recent-campaigns" class="btn btn-outline-light btn-sm d-flex align-items-center gap-2">
                        <i class="bi bi-collection-play"></i>
                        <span>نعم، أريد استعراض الحملات السابقة</span>
                    </a>
                    <a href="{{ route('admin.add-campaign') }}" class="btn btn-main btn-sm d-flex align-items-center gap-2">
                        <i class="bi bi-plus-lg"></i>
                        <span>إنشاء حملة إعلانية جديدة</span>
                    </a>
                </div>

                <div class="row g-3 mt-4">
                    <div class="col-12 col-md-4">
                        <div class="bg-dark rounded-4 border border-secondary px-3 py-3 h-100">
                            <div class="small text-secondary mb-1">إجمالي الحملات</div>
                            <div class="fs-4 fw-bold text-white mb-0">{{ number_format($totalCampaigns ?? 0) }}</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="bg-dark rounded-4 border border-secondary px-3 py-3 h-100">
                            <div class="small text-secondary mb-1">الحملات النشطة حالياً</div>
                            <div class="fs-4 fw-bold text-success mb-0">{{ number_format($activeCampaigns ?? 0) }}</div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <div class="bg-dark rounded-4 border border-secondary px-3 py-3 h-100">
                            <div class="small text-secondary mb-1">الحملات المنتهية / المؤرشفة</div>
                            <div class="fs-4 fw-bold text-warning mb-0">
                                {{ max(($totalCampaigns ?? 0) - ($activeCampaigns ?? 0), 0) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- قائمة مختصرة لأحدث الحملات --}}
        <div class="col-12" id="recent-campaigns">
            <div class="glass p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <p class="text-info small mb-1">الحملات الأخيرة</p>
                        <h2 class="h6 fw-bold mb-0">آخر الحملات التي قمت بإنشائها</h2>
                    </div>
                    <a href="{{ route('admin.add-campaign') }}" class="btn btn-sm btn-outline-main">
                        إنشاء حملة جديدة
                    </a>
                </div>

                @if(($recentCampaigns ?? collect())->isEmpty())
                    <p class="text-secondary small mb-0">
                        لا توجد حملات بعد. ابدأ أول حملة لك من خلال زر <strong>إنشاء حملة جديدة</strong> في الأعلى.
                    </p>
                @else
                    <div class="table-responsive small">
                        <table class="table table-dark table-hover align-middle mb-0">
                            <thead>
                            <tr class="text-secondary">
                                <th>عنوان الحملة</th>
                                <th>تاريخ الإنشاء</th>
                                <th>تاريخ البداية</th>
                                <th>تاريخ النهاية</th>
                                <th>الميزانية</th>
                                <th>الحالة</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($recentCampaigns as $campaign)
                                <tr>
                                    <td>{{ $campaign->title }}</td>
                                    <td>{{ optional($campaign->created_at)->format('Y-m-d') }}</td>
                                    <td>{{ optional($campaign->start_date ?? $campaign->starts_at)->format('Y-m-d') }}</td>
                                    <td>{{ optional($campaign->end_date ?? $campaign->ends_at)->format('Y-m-d') }}</td>
                                    <td>${{ number_format($campaign->budget ?? 0, 2) }}</td>
                                    <td>
                                        @if($campaign->is_active)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">نشطة</span>
                                        @else
                                            <span class="badge bg-secondary">غير نشطة</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>


