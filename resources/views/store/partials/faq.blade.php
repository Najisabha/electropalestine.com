<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0" style="background: linear-gradient(135deg, rgba(26, 29, 36, 0.95), rgba(21, 24, 32, 0.9)); border: 1px solid rgba(255, 255, 255, 0.1) !important;">
                <div class="card-body p-5">
                    <h1 class="text-center mb-5 text-white" style="color: #0db777 !important;">
                        <i class="bi bi-question-circle me-2"></i>
                        {{ __('common.faq') }}
                    </h1>

                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item mb-3 border-0" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1) !important;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed text-white" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" style="background: rgba(255, 255, 255, 0.05);">
                                    {{ __('common.faq_question_1') ?? 'كيف يمكنني الطلب من الموقع؟' }}
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-white-50" style="background: rgba(255, 255, 255, 0.02);">
                                    {{ __('common.faq_answer_1') ?? 'يمكنك تصفح المنتجات وإضافتها إلى السلة، ثم إتمام عملية الشراء. يجب أن تكون مسجلاً في الموقع لإتمام الطلب.' }}
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-3 border-0" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1) !important;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed text-white" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" style="background: rgba(255, 255, 255, 0.05);">
                                    {{ __('common.faq_question_2') ?? 'ما هي طرق الدفع المتاحة؟' }}
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-white-50" style="background: rgba(255, 255, 255, 0.02);">
                                    {{ __('common.faq_answer_2') ?? 'نقبل الدفع النقدي عند الاستلام، والدفع بالرصيد/النقاط للمستخدمين المسجلين.' }}
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-3 border-0" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1) !important;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed text-white" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" style="background: rgba(255, 255, 255, 0.05);">
                                    {{ __('common.faq_question_3') ?? 'كم تستغرق عملية التوصيل؟' }}
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-white-50" style="background: rgba(255, 255, 255, 0.02);">
                                    {{ __('common.faq_answer_3') ?? 'مدة التوصيل تتراوح بين 3-7 أيام عمل حسب الموقع. سيتم التواصل معك لتحديد موعد التوصيل.' }}
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-3 border-0" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1) !important;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed text-white" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" style="background: rgba(255, 255, 255, 0.05);">
                                    {{ __('common.faq_question_4') ?? 'هل يمكنني تتبع طلبي؟' }}
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-white-50" style="background: rgba(255, 255, 255, 0.02);">
                                    {{ __('common.faq_answer_4') ?? 'نعم، يمكنك تتبع طلبك من خلال صفحة "تتبع الطلب" باستخدام رقم الطلب، أو من خلال صفحة "طلباتي" إذا كنت مسجلاً.' }}
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-3 border-0" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1) !important;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed text-white" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" style="background: rgba(255, 255, 255, 0.05);">
                                    {{ __('common.faq_question_5') ?? 'ما هي سياسة الاسترجاع؟' }}
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-white-50" style="background: rgba(255, 255, 255, 0.02);">
                                    {{ __('common.faq_answer_5') ?? 'يمكنك استرجاع المنتج خلال 14 يوم من تاريخ الاستلام بشرط أن يكون في حالته الأصلية. راجع صفحة "سياسة الاسترجاع" للتفاصيل الكاملة.' }}
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-3 border-0" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1) !important;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed text-white" type="button" data-bs-toggle="collapse" data-bs-target="#faq6" style="background: rgba(255, 255, 255, 0.05);">
                                    {{ __('common.faq_question_6') ?? 'هل المنتجات مضمونة؟' }}
                                </button>
                            </h2>
                            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-white-50" style="background: rgba(255, 255, 255, 0.02);">
                                    {{ __('common.faq_answer_6') ?? 'نعم، جميع المنتجات أصلية ومضمونة. نقدم ضمان على جميع المنتجات حسب نوع المنتج.' }}
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-3 border-0" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1) !important;">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed text-white" type="button" data-bs-toggle="collapse" data-bs-target="#faq7" style="background: rgba(255, 255, 255, 0.05);">
                                    {{ __('common.faq_question_7') ?? 'كيف يمكنني التواصل مع خدمة العملاء؟' }}
                                </button>
                            </h2>
                            <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body text-white-50" style="background: rgba(255, 255, 255, 0.02);">
                                    {{ __('common.faq_answer_7') ?? 'يمكنك التواصل معنا عبر صفحة "تواصل معنا" أو عبر البريد الإلكتروني: info@electropalestine.com أو الهاتف: +970598134332' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-5">
                        <p class="text-white-50 mb-3">
                            {{ __('common.faq_more_questions') ?? 'لا تزال لديك أسئلة؟' }}
                        </p>
                        <a href="{{ route('store.contact') }}" class="btn btn-lg" style="background: linear-gradient(135deg, #0db777, #0aa066); color: white; border: none;">
                            <i class="bi bi-envelope me-2"></i>
                            {{ __('common.contact_us') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
