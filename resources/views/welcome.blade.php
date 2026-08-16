@extends('WebSite.layouts.master')

@section('content')
    <!-- Main Slider Three -->
    <section class="main-slider-three">
        <div class="banner-carousel">
            <!-- Swiper -->
            <div class="swiper-wrapper">

                <div class="swiper-slide slide">
                    <div class="auto-container">
                        <div class="row clearfix">

                            <!-- Content Column -->
                            <div class="content-column col-lg-6 col-md-12 col-sm-12">
                                <div class="inner-column">
                                    <h2>{{ __('website.hero_title_1') }}</h2>
                                    <div class="text">
                                        {{ __('website.hero_text_1') }}
                                    </div>
                                    <div class="btn-box">
                                    <a href="#appointment" class="theme-btn hms-hero-btn hms-hero-btn--primary"><span class="txt">{{ __('website.appointments') }}</span></a>
                                    <a href="#departments" class="theme-btn hms-hero-btn hms-hero-btn--ghost"><span class="txt">{{ __('website.departments') }}</span></a>
                                    </div>
                                </div>
                            </div>

                            <!-- Image Column -->
                            <div class="image-column col-lg-6 col-md-12 col-sm-12">
                                <div class="inner-column">
                                    <div class="image hms-hero-photo hms-hero-photo--logo">
                                        <img src="{{ asset('Dashboard/img/brand/hospital-logo.png') }}" alt="شعار مستشفى الشام التخصصي"/>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>


                <div class="swiper-slide slide">
                    <div class="auto-container">
                        <div class="row clearfix">
                            <div class="content-column col-lg-6 col-md-12 col-sm-12">
                                <div class="inner-column">
                                    <h2>{{ __('website.hero_title_2') }}</h2>
                                    <div class="text">
                                        {{ __('website.hero_text_2') }}
                                    </div>
                                    <div class="btn-box">
                                        <a href="#appointment" class="theme-btn hms-hero-btn hms-hero-btn--primary"><span class="txt">{{ __('website.appointments') }}</span></a>
                                        <a href="#doctors" class="theme-btn hms-hero-btn hms-hero-btn--ghost"><span class="txt">{{ __('website.doctors') }}</span></a>
                                    </div>
                                </div>
                            </div>
                            <div class="image-column col-lg-6 col-md-12 col-sm-12">
                                <div class="inner-column">
                                    <div class="image hms-hero-photo">
                                        <img src="{{ asset('WebSite/images/hms/about.jpg') }}" alt="مرافق مستشفى الشام التخصصي"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="swiper-slide slide">
                    <div class="auto-container">
                        <div class="row clearfix">
                            <div class="content-column col-lg-6 col-md-12 col-sm-12">
                                <div class="inner-column">
                                    <h2>{{ __('website.hero_title_3') }}</h2>
                                    <div class="text">
                                        {{ __('website.hero_text_3') }}
                                    </div>
                                    <div class="btn-box">
                                        <a href="#appointment" class="theme-btn hms-hero-btn hms-hero-btn--primary"><span class="txt">{{ __('website.book_now') }}</span></a>
                                        <a href="#articles" class="theme-btn hms-hero-btn hms-hero-btn--ghost"><span class="txt">{{ __('website.articles') }}</span></a>
                                    </div>
                                </div>
                            </div>
                            <div class="image-column col-lg-6 col-md-12 col-sm-12">
                                <div class="inner-column">
                                    <div class="image hms-hero-photo">
                                        <img src="{{ asset('WebSite/images/hms/appointment.jpg') }}" alt="رعاية المرضى في المستشفى"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="swiper-button-next hms-slider-nav" aria-label="التالي">
                <i class="fas fa-chevron-left"></i>
            </div>
            <div class="swiper-button-prev hms-slider-nav" aria-label="السابق">
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>
    </section>
    <!-- End Main Slider -->

    <!-- Health Section -->
    <section class="health-section">
        <div class="auto-container">
            <div class="inner-container">

                <div class="row clearfix">

                    <!-- Content Column -->
                    <div class="content-column col-lg-7 col-md-12 col-sm-12">
                        <div class="inner-column">
                            <div class="border-line"></div>
                            <!-- Sec Title -->
                            <div class="sec-title">
                                <h2>{{ __('website.about_title') }} <br> {{ __('website.about_subtitle') }}</h2>
                                <div class="separator"></div>
                            </div>
                            <div class="text">
                                @if(app()->getLocale() === 'en')
                                    {{ __('website.about_default') }}
                                @else
                                    {{ optional($siteSetting)->about ?? __('website.about_default') }}
                                @endif
                            </div>
                            <a href="{{ route('home') }}#contact" class="theme-btn btn-style-one"><span class="txt">{{ __('website.contact') }}</span></a>
                        </div>
                    </div>

                    <!-- Image Column -->
                    <div class="image-column col-lg-5 col-md-12 col-sm-12">
                        <div class="inner-column wow fadeInRight" data-wow-delay="0ms" data-wow-duration="1500ms">
                            <div class="image hms-about-photo">
                                <img src="{{ asset('WebSite/images/hms/about.jpg') }}" alt="مرافق مستشفى الشام التخصصي"/>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>
    <!-- End Health Section -->

    <!-- Featured Section -->
    <section class="featured-section">
        <div class="auto-container">
            <div class="row clearfix">

                <!-- Feature Block -->
                <div class="feature-block col-lg-3 col-md-6 col-sm-12">
                    <div class="inner-box wow fadeInLeft" data-wow-delay="0ms" data-wow-duration="1500ms">
                        <div class="upper-box">
                            <div class="icon fas fa-stethoscope"></div>
                            <h3><a href="#appointment">العلاج الطبي</a></h3>
                        </div>
                        <div class="text">خطط علاجية مخصصة وتشخيص دقيق لكل حالة وفق أحدث البروتوكولات.</div>
                    </div>
                </div>

                <!-- Feature Block -->
                <div class="feature-block col-lg-3 col-md-6 col-sm-12">
                    <div class="inner-box wow fadeInLeft" data-wow-delay="250ms" data-wow-duration="1500ms">
                        <div class="upper-box">
                            <div class="icon fas fa-ambulance"></div>
                            <h3><a href="#contact">مساعدة الطوارئ</a></h3>
                        </div>
                        <div class="text">استجابة سريعة لحالات الطوارئ مع فريق إسعاف وكادر طبي جاهز على مدار الساعة.</div>
                    </div>
                </div>

                <!-- Feature Block -->
                <div class="feature-block col-lg-3 col-md-6 col-sm-12">
                    <div class="inner-box wow fadeInLeft" data-wow-delay="500ms" data-wow-duration="1500ms">
                        <div class="upper-box">
                            <div class="icon fas fa-user-md"></div>
                            <h3><a href="#doctors">أطباء مؤهلون</a></h3>
                        </div>
                        <div class="text">نخبة من الأطباء الاختصاصيين بخبرة عالية في مختلف الفروع الطبية.</div>
                    </div>
                </div>

                <!-- Feature Block -->
                <div class="feature-block col-lg-3 col-md-6 col-sm-12">
                    <div class="inner-box wow fadeInLeft" data-wow-delay="750ms" data-wow-duration="1500ms">
                        <div class="upper-box">
                            <div class="icon fas fa-briefcase-medical"></div>
                            <h3><a href="#departments">خدمات متكاملة</a></h3>
                        </div>
                        <div class="text">مختبرات وأشعة وصيدلية وعيادات تخصصية تحت سقف واحد في دمشق.</div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- End Featured Section -->

    <!-- Department Section Three -->
    <section class="department-section-three" id="departments">
        <div class="image-layer" style="background-image:url({{ asset('WebSite/images/hms/dept-bg.jpg') }})"></div>
        <div class="auto-container">
            <div class="department-tabs tabs-box">
                <div class="row clearfix">
                    <div class="col-lg-4 col-md-12 col-sm-12">
                        <div class="sec-title light">
                            <h2>{{ __('website.departments') }}</h2>
                            <div class="separator"></div>
                        </div>
                        <ul class="tab-btns tab-buttons clearfix">
                            @forelse($sections as $section)
                                @php
                                    $tabName = $section->name ?? '';
                                    $tabIcon = 'fas fa-hospital';
                                    if (str_contains($tabName, 'قلب') || str_contains($tabName, 'أوعية')) {
                                        $tabIcon = 'fas fa-heartbeat';
                                    } elseif (str_contains($tabName, 'مخ') || str_contains($tabName, 'عصب') || str_contains($tabName, 'دماغ')) {
                                        $tabIcon = 'fas fa-brain';
                                    } elseif (str_contains($tabName, 'أطفال')) {
                                        $tabIcon = 'fas fa-baby';
                                    } elseif (str_contains($tabName, 'عيون') || str_contains($tabName, 'عين')) {
                                        $tabIcon = 'fas fa-eye';
                                    } elseif (str_contains($tabName, 'جراح')) {
                                        $tabIcon = 'fas fa-user-md';
                                    } elseif (str_contains($tabName, 'باطن') || str_contains($tabName, 'هضم')) {
                                        $tabIcon = 'fas fa-stethoscope';
                                    }
                                @endphp
                                <li data-tab="#tab-section-{{ $section->id }}"
                                    class="tab-btn {{ $loop->first ? 'active-btn' : '' }}">
                                    <i class="{{ $tabIcon }} hms-dept-tab-icon"></i>
                                    <span>{{ $section->name }}</span>
                                </li>
                            @empty
                                <li class="tab-btn active-btn">لا توجد أقسام حالياً</li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="col-lg-8 col-md-12 col-sm-12">
                        <div class="tabs-content">
                            @forelse($sections as $section)
                                @php
                                    $name = $section->name ?? '';
                                    $deptImg = 'WebSite/images/hms/dept-internal.jpg';
                                    if (str_contains($name, 'قلب') || str_contains($name, 'أوعية')) {
                                        $deptImg = 'WebSite/images/hms/dept-cardio.jpg';
                                    } elseif (str_contains($name, 'مخ') || str_contains($name, 'عصب') || str_contains($name, 'دماغ')) {
                                        $deptImg = 'WebSite/images/hms/dept-neuro.jpg';
                                    } elseif (str_contains($name, 'أطفال')) {
                                        $deptImg = 'WebSite/images/hms/dept-pedia.jpg';
                                    } elseif (str_contains($name, 'عيون') || str_contains($name, 'عين')) {
                                        $deptImg = 'WebSite/images/hms/dept-eye.jpg';
                                    } elseif (str_contains($name, 'جراح')) {
                                        $deptImg = 'WebSite/images/hms/dept-surgery.jpg';
                                    }
                                @endphp
                                <div class="tab {{ $loop->first ? 'active-tab' : '' }}" id="tab-section-{{ $section->id }}">
                                    <div class="content">
                                        <div class="hms-dept-photo">
                                            <img src="{{ asset($deptImg) }}" alt="{{ $section->name }}"/>
                                        </div>
                                        <h2>{{ $section->name }}</h2>
                                        <div class="title">عدد الأطباء: {{ $section->doctors_count }}</div>
                                        <div class="text">
                                            <p>{{ $section->description ?: 'يقدم هذا القسم رعاية طبية متخصصة بإشراف كادر طبي مؤهل.' }}</p>
                                        </div>
                                        <div class="two-column row clearfix">
                                            <div class="column col-lg-6 col-md-6 col-sm-12">
                                                <h3>01 - خدمات القسم</h3>
                                                <div class="column-text">
                                                    تشخيص وعلاج ومتابعة المرضى ضمن تخصص {{ $section->name }}.
                                                </div>
                                            </div>
                                            <div class="column col-lg-6 col-md-6 col-sm-12">
                                                <h3>02 - حجز موعد</h3>
                                                <div class="column-text">
                                                    يمكنك حجز موعد مع أحد أطباء القسم من خلال نموذج الحجز أدناه.
                                                </div>
                                            </div>
                                        </div>
                                        <a href="#appointment" class="theme-btn btn-style-two"><span class="txt">احجز موعد</span></a>
                                    </div>
                                </div>
                            @empty
                                <div class="tab active-tab">
                                    <div class="content">
                                        <h2>لا توجد أقسام</h2>
                                        <div class="text"><p>يرجى إضافة الأقسام من لوحة التحكم.</p></div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Department Section -->

    <!-- Team Section -->
    <section class="team-section" id="doctors">
        <div class="auto-container">
            <div class="sec-title centered">
                <h2>الأخصائيون الطبيون</h2>
                <div class="separator"></div>
            </div>

            <div class="row clearfix">
                @forelse($doctors as $doctor)
                    <div class="team-block col-lg-3 col-md-6 col-sm-6 col-xs-12">
                        <div class="inner-box wow fadeInLeft" data-wow-delay="{{ ($loop->index % 4) * 250 }}ms" data-wow-duration="1500ms">
                            <div class="image">
                                @php
                                    $doctorImage = optional($doctor->image)->filename;
                                    $doctorImagePath = $doctorImage
                                        ? public_path('Dashboard/img/doctors/' . $doctorImage)
                                        : null;
                                    $fallbackIndex = ($loop->index % 8) + 1;
                                    $doctorSrc = ($doctorImagePath && file_exists($doctorImagePath) && filesize($doctorImagePath) > 20000)
                                        ? asset('Dashboard/img/doctors/' . $doctorImage)
                                        : asset('WebSite/images/hms/doctors/d' . $fallbackIndex . '.jpg');
                                @endphp
                                <img src="{{ $doctorSrc }}" alt="{{ $doctor->name }}"/>
                                <div class="overlay-box">
                                    <a href="#appointment" class="appointment">احجز موعد</a>
                                </div>
                            </div>
                            <div class="lower-content">
                                <h3><a href="#appointment">{{ $doctor->name }}</a></h3>
                                <div class="designation">{{ optional($doctor->section)->name ?? 'طبيب' }}</div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center">
                        <p>لا يوجد أطباء لعرضهم حالياً.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
    <!-- End Team Section -->

    <!-- Video Section -->
    <section class="video-section" style="background-image:url({{ asset('WebSite/images/hms/video-bg.jpg') }})">
        <div class="auto-container">
            <div class="content">
                <a href="https://www.youtube.com/watch?v=kxPCFljwJws" class="lightbox-image play-box"><span
                        class="flaticon-play-button"><i class="ripple"></i></span></a>
                <div class="text">نحن نهتم بصحتك<h2>نحن نهتم بك</h2>
                </div>
            </div>
    </section>
    <!-- End Video Section -->

    <!-- Appointment Section Two -->
    <section class="appointment-section-two" id="appointment">
        <div class="auto-container">
            <div class="inner-container">
                <div class="row clearfix">

                    <!-- Image Column -->
                    <div class="image-column col-lg-6 col-md-12 col-sm-12">
                        <div class="inner-column wow slideInLeft" data-wow-delay="0ms" data-wow-duration="1500ms">
                            <div class="image hms-appointment-visual">
                                <img src="{{ asset('WebSite/images/hms/appointment.jpg') }}" alt="استشارة طبية وحجز موعد"/>
                                <div class="hms-appointment-badge">
                                    <i class="fas fa-calendar-check"></i>
                                    <span>احجز موعدك بسهولة</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Column -->
                    <div class="form-column col-lg-6 col-md-12 col-sm-12">
                        <div class="inner-column">
                            <!-- Sec Title -->
                            <div class="sec-title">
                                <h2>حجز موعد</h2>
                                <div class="separator"></div>
                            </div>

                            <!-- Appointment Form -->
                            <div class="appointment-form">
                                @include('WebSite.partials.appointment-form')
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    @include('WebSite.partials.ambulance-form')

    <!-- Testimonial Section Two -->
    @if($testimonials->isNotEmpty())
    <section class="testimonial-section-two">
        <div class="auto-container">
            <div class="sec-title centered">
                <h2>{{ __('website.testimonials_title') }}</h2>
                <div class="separator"></div>
            </div>
            <div class="row clearfix hms-testimonials-grid">
                @include('WebSite.partials.testimonials-grid')
            </div>
        </div>
    </section>
    @endif
    <!-- End Testimonial Section Two -->

    <!-- Counter Section -->
    <section class="counter-section style-two" style="background-image: url({{ URL::asset('WebSite/images/background/pattern-3.png') }})">
        <div class="auto-container">
            <div class="fact-counter style-two">
                <div class="row clearfix">
                    <div class="column counter-column col-lg-3 col-md-6 col-sm-12">
                        <div class="inner wow fadeInLeft" data-wow-delay="0ms" data-wow-duration="1500ms">
                            <div class="content">
                                <div class="icon fas fa-user-injured"></div>
                                <div class="count-outer count-box">
                                    <span class="count-text" data-speed="2500" data-stop="{{ $stats['patients'] }}">0</span>
                                </div>
                                <h4 class="counter-title">المرضى</h4>
                            </div>
                        </div>
                    </div>

                    <div class="column counter-column col-lg-3 col-md-6 col-sm-12">
                        <div class="inner wow fadeInLeft" data-wow-delay="300ms" data-wow-duration="1500ms">
                            <div class="content">
                                <div class="icon fas fa-user-md"></div>
                                <div class="count-outer count-box alternate">
                                    <span class="count-text" data-speed="3000" data-stop="{{ $stats['doctors'] }}">0</span>
                                </div>
                                <h4 class="counter-title">فريق الأطباء</h4>
                            </div>
                        </div>
                    </div>

                    <div class="column counter-column col-lg-3 col-md-6 col-sm-12">
                        <div class="inner wow fadeInLeft" data-wow-delay="600ms" data-wow-duration="1500ms">
                            <div class="content">
                                <div class="icon fas fa-hospital"></div>
                                <div class="count-outer count-box">
                                    <span class="count-text" data-speed="3000" data-stop="{{ $stats['sections'] }}">0</span>
                                </div>
                                <h4 class="counter-title">الأقسام</h4>
                            </div>
                        </div>
                    </div>

                    <div class="column counter-column col-lg-3 col-md-6 col-sm-12">
                        <div class="inner wow fadeInLeft" data-wow-delay="900ms" data-wow-duration="1500ms">
                            <div class="content">
                                <div class="icon fas fa-calendar-check"></div>
                                <div class="count-outer count-box">
                                    <span class="count-text" data-speed="2500" data-stop="{{ $stats['appointments'] }}">0</span>
                                </div>
                                <h4 class="counter-title">مواعيد مؤكدة</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Counter Section -->

    <!-- Doctor Info Section -->
    <section class="doctor-info-section">
        <div class="auto-container">
            <div class="inner-container">
                <div class="row clearfix">

                    <!-- Doctor Block -->
                    <div class="doctor-block col-lg-4 col-md-6 col-sm-12">
                        <div class="inner-box wow fadeInLeft" data-wow-delay="0ms" data-wow-duration="1500ms">
                            <h3>ساعات العمل</h3>
                            <ul class="doctor-time-list">
                                <li>من الإثنين إلى الجمعة<span>8:00am–7:00pm</span></li>
                                <li>السبت <span>9:00am–5:00pm</span></li>
                                <li>الأحد<span>9:00am–3:00pm</span></li>
                            </ul>
                            <h4>حالات الطوارئ</h4>
                            <div class="phone" id="contact">اتصل بنا ! <strong>{{ $siteSetting->phone ?? '+963 11 334 2200' }}</strong></div>
                        </div>
                    </div>

                    <!-- Doctor Block -->
                    <div class="doctor-block col-lg-4 col-md-6 col-sm-12">
                        <div class="inner-box wow fadeInUp" data-wow-delay="0ms" data-wow-duration="1500ms">
                            <h3>جدول الأطباء</h3>
                            <div class="text">
                                ما يلي هو للإرشاد فقط لمساعدتك في التخطيط لموعدك
                                طبيب أو ممرضة مفضلة. لا تضمن توافر الأطباء أو الممرضات
                                قد يكون في بعض الأحيان يحضر إلى واجبات أخرى
                            </div>
                            <a href="#" class="detail">تفاصيل اكثر</a>
                        </div>
                    </div>

                    <!-- Doctor Block -->
                    <div class="doctor-block col-lg-4 col-md-6 col-sm-12">
                        <div class="inner-box wow fadeInRight" data-wow-delay="0ms" data-wow-duration="1500ms">
                            <h3>العناية الصحية الاولية</h3>
                            <div class="text">عندما تعلم أنك تستخدم أفضل مواهبك من أجل شيء تحبه ، فأنت
                                لا تستطيع ذلك. التواصل الفعال هو الأساس لبناء علامات تجارية صلبة مثل
                                علاقة السفن بالبناء مع عملائنا
                            </div>
                            <a href="#" class="detail">اتصل الآن</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!-- End Doctor Info Section -->

    <!-- News Section Two -->
    <section class="news-section-two" id="articles">
        <div class="auto-container">
            <div class="sec-title centered">
                <h2>{{ __('website.latest_news') }}</h2>
                <div class="separator style-three"></div>
            </div>
            <div class="row clearfix">
                @forelse($blogs as $blog)
                    @php $isLiked = in_array($blog->id, $likedIds ?? [], true); @endphp
                    <div class="news-block-two col-lg-6 col-md-12 col-sm-12">
                        <div class="inner-box hms-article-card">
                            <div class="image">
                                <a href="{{ route('blogs.show', $blog) }}">
                                    <img src="{{ $blog->imageUrl() }}" alt="{{ $blog->title }}" style="width:100%;height:280px;object-fit:cover"/>
                                </a>
                            </div>
                            <div class="lower-content">
                                <div class="hms-article-meta">
                                    <span class="hms-article-meta__item">
                                        <i class="fas fa-eye"></i>
                                        <span class="js-views">{{ $blog->views }}</span>
                                    </span>
                                    <button type="button"
                                            class="hms-like-btn {{ $isLiked ? 'is-liked' : '' }}"
                                            data-like-url="{{ route('blogs.like', $blog) }}"
                                            data-blog-id="{{ $blog->id }}"
                                            title="أعجبني">
                                        <i class="fas fa-heart"></i>
                                        <span class="js-likes">{{ $blog->likes }}</span>
                                    </button>
                                    <a href="{{ route('blogs.show', $blog) }}#comments"
                                       class="hms-article-meta__item hms-comment-open"
                                       data-comment-url="{{ route('blogs.show', $blog) }}#comments"
                                       title="تعليق">
                                        <i class="fas fa-comment"></i>
                                        <span>{{ $blog->comments_count ?? 0 }}</span>
                                    </a>
                                    <span class="hms-article-meta__item">
                                        <i class="far fa-calendar-alt"></i>
                                        {{ optional($blog->published_at)->translatedFormat('d F Y') }}
                                    </span>
                                </div>
                                <h3><a href="{{ route('blogs.show', $blog) }}">{{ $blog->title }}</a></h3>
                                <div class="text">{{ $blog->excerpt }}</div>
                                <a href="{{ route('blogs.show', $blog) }}" class="theme-btn btn-style-five"><span class="txt">اقرأ المزيد</span></a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center"><p>لا توجد مقالات حالياً</p></div>
                @endforelse
            </div>
            @if(isset($blogs) && $blogs->count())
                <div class="text-center hms-view-all-blogs-wrap" style="margin-top:25px">
                    <a href="{{ route('blogs.index') }}" class="theme-btn btn-style-one hms-view-all-blogs"><span class="txt">{{ __('website.view_all_articles') }}</span></a>
                </div>
            @endif
        </div>
    </section>

@endsection
