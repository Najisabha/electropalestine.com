<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0" style="background: linear-gradient(135deg, rgba(26, 29, 36, 0.95), rgba(21, 24, 32, 0.9)); border: 1px solid rgba(255, 255, 255, 0.1) !important;">
                <div class="card-body p-5">
                    <h1 class="text-center mb-5 text-white" style="color: #0db777 !important;">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>
                        {{ __('common.return_policy') }}
                    </h1>

                    <div class="content text-white-50" style="line-height: 2; font-size: 1.1rem;">
                        <section class="mb-5">
                            <h3 class="text-white mb-3" style="color: #0db777 !important;">
                                <i class="bi bi-clock-history me-2"></i>
                                {{ __('common.return_period') ?? 'فترة الاسترجاع' }}
                            </h3>
                            <p>
                                {{ __('common.return_period_text') ?? 'يمكنك استرجاع أو استبدال المنتج خلال 14 يوم من تاريخ الاستلام. يجب أن يكون المنتج في حالته الأصلية مع جميع الملحقات والتغليف الأصلي.' }}
                            </p>
                        </section>

                        <section class="mb-5">
                            <h3 class="text-white mb-3" style="color: #0db777 !important;">
                                <i class="bi bi-check-circle me-2"></i>
                                {{ __('common.return_conditions') ?? 'شروط الاسترجاع' }}
                            </h3>
                            <ul class="list-unstyled ps-4">
                                <li class="mb-2">
                                    <i class="bi bi-dot text-white me-2"></i>
                                    {{ __('common.condition_1') ?? 'المنتج يجب أن يكون في حالته الأصلية غير مستخدم' }}
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-dot text-white me-2"></i>
                                    {{ __('common.condition_2') ?? 'يجب أن يحتوي على جميع الملحقات والتغليف الأصلي' }}
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-dot text-white me-2"></i>
                                    {{ __('common.condition_3') ?? 'يجب تقديم الفاتورة الأصلية' }}
                                </li>
                                <li class="mb-2">
                                    <i class="bi bi-dot text-white me-2"></i>
                                    {{ __('common.condition_4') ?? 'المنتجات المخصصة أو المطبوعة لا يمكن استرجاعها' }}
                                </li>
                            </ul>
                        </section>

                        <section class="mb-5">
                            <h3 class="text-white mb-3" style="color: #0db777 !important;">
                                <i class="bi bi-arrow-repeat me-2"></i>
                                {{ __('common.return_process') ?? 'عملية الاسترجاع' }}
                            </h3>
                            <ol class="ps-4">
                                <li class="mb-2">
                                    {{ __('common.step_1') ?? 'تواصل معنا عبر صفحة التواصل أو البريد الإلكتروني' }}
                                </li>
                                <li class="mb-2">
                                    {{ __('common.step_2') ?? 'سنقوم بمراجعة طلبك والرد خلال 24-48 ساعة' }}
                                </li>
                                <li class="mb-2">
                                    {{ __('common.step_3') ?? 'بعد الموافقة، سنزودك بتفاصيل إرجاع المنتج' }}
                                </li>
                                <li class="mb-2">
                                    {{ __('common.step_4') ?? 'بعد استلام المنتج والتحقق منه، سنقوم بإرجاع المبلغ خلال 5-7 أيام عمل' }}
                                </li>
                            </ol>
                        </section>

                        <section class="mb-5">
                            <h3 class="text-white mb-3" style="color: #0db777 !important;">
                                <i class="bi bi-currency-exchange me-2"></i>
                                {{ __('common.refund_policy') ?? 'سياسة الاسترداد' }}
                            </h3>
                            <p>
                                {{ __('common.refund_policy_text') ?? 'سيتم إرجاع المبلغ بنفس طريقة الدفع المستخدمة في الطلب الأصلي. في حالة الدفع النقدي عند الاستلام، سيتم إرجاع المبلغ عبر تحويل بنكي أو شيك.' }}
                            </p>
                        </section>

                        <section class="mb-5">
                            <h3 class="text-white mb-3" style="color: #0db777 !important;">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                {{ __('common.exceptions') ?? 'استثناءات' }}
                            </h3>
                            <p>
                                {{ __('common.exceptions_text') ?? 'لا يمكن استرجاع المنتجات التالفة بسبب سوء الاستخدام، أو المنتجات التي تم فتحها وتجربتها بشكل يمنع إعادة بيعها.' }}
                            </p>
                        </section>

                        <div class="text-center mt-5">
                            <a href="{{ route('store.contact') }}" class="btn btn-lg" style="background: linear-gradient(135deg, #0db777, #0aa066); color: white; border: none;">
                                <i class="bi bi-envelope me-2"></i>
                                {{ __('common.contact_for_return') ?? 'تواصل معنا لطلب الاسترجاع' }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
