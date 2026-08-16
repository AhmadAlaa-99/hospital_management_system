@php
    $siteSetting = $siteSetting ?? \App\Models\SiteSetting::current();
    $footerSections = $footerSections ?? \App\Models\Section::with('translations')->take(6)->get();
    $footerBlogs = $footerBlogs ?? \App\Models\Blog::where('is_published', true)->latest('published_at')->take(2)->get();
@endphp
<footer class="main-footer style-two">
    <div class="auto-container">
        <div class="widgets-section">
            <div class="row clearfix">
                <div class="big-column col-lg-6 col-md-12 col-sm-12">
                    <div class="row clearfix">
                        <div class="footer-column col-lg-7 col-md-6 col-sm-12">
                            <div class="footer-widget logo-widget">
                                <div class="logo">
                                    <a href="{{ route('home') }}" class="hms-brand-link footer">
                                        <img src="{{ asset('Dashboard/img/brand/hospital-logo.png') }}" alt="{{ $siteSetting->hospital_name }}" class="hms-logo-img"/>
                                        <span class="hms-brand-text sticky">
                                            @if(app()->getLocale() === 'ar')
                                                نظام <span>إدارة</span> المستشفى
                                            @else
                                                Hospital <span>Management</span>
                                            @endif
                                        </span>
                                    </a>
                                </div>
                                <p style="color:#fff;opacity:.9;margin:12px 0 18px;line-height:1.7">
                                    {{ $siteSetting->about }}
                                </p>
                                <ul class="social-icons">
                                    @if($siteSetting->facebook)<li><a href="{{ $siteSetting->facebook }}" target="_blank"><span class="fab fa-facebook-f"></span></a></li>@endif
                                    @if($siteSetting->twitter)<li><a href="{{ $siteSetting->twitter }}" target="_blank"><span class="fab fa-twitter"></span></a></li>@endif
                                    @if($siteSetting->instagram)<li><a href="{{ $siteSetting->instagram }}" target="_blank"><span class="fab fa-instagram"></span></a></li>@endif
                                    @if($siteSetting->linkedin)<li><a href="{{ $siteSetting->linkedin }}" target="_blank"><span class="fab fa-linkedin"></span></a></li>@endif
                                </ul>

                                <div class="hms-footer-cta">
                                    <div class="hms-footer-cta__title">
                                        <i class="fas fa-bolt"></i>
                                        <span>{{ __('website.quick_links') }}</span>
                                    </div>
                                    <p class="hms-footer-cta__text">{{ __('website.quick_links_hint') }}</p>
                                    <div class="hms-footer-cta__actions">
                                        <a href="{{ route('home') }}#appointment" class="hms-footer-cta__btn hms-footer-cta__btn--primary">
                                            <i class="fas fa-calendar-check"></i>
                                            <span>{{ __('website.book_appointment') }}</span>
                                        </a>
                                        <a href="{{ route('queue.track') }}" class="hms-footer-cta__btn hms-footer-cta__btn--light">
                                            <i class="fas fa-ticket-alt"></i>
                                            <span>{{ __('website.track_ticket') }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="footer-column col-lg-5 col-md-6 col-sm-12">
                            <div class="footer-widget links-widget hms-footer-depts">
                                <div class="footer-title clearfix">
                                    <h2>{{ __('website.hospital_departments') }}</h2>
                                    <div class="separator"></div>
                                </div>
                                <p class="hms-footer-depts__hint">{{ __('website.hospital_departments_hint') }}</p>
                                <ul class="footer-list hms-footer-sections">
                                    @forelse($footerSections as $section)
                                        @php
                                            $fname = $section->name ?? '';
                                            $fIcon = 'fas fa-hospital';
                                            $fTone = 'teal';
                                            if (str_contains($fname, 'قلب') || str_contains($fname, 'أوعية')) {
                                                $fIcon = 'fas fa-heartbeat';
                                                $fTone = 'rose';
                                            } elseif (str_contains($fname, 'مخ') || str_contains($fname, 'عصب') || str_contains($fname, 'دماغ')) {
                                                $fIcon = 'fas fa-brain';
                                                $fTone = 'violet';
                                            } elseif (str_contains($fname, 'أطفال')) {
                                                $fIcon = 'fas fa-baby';
                                                $fTone = 'amber';
                                            } elseif (str_contains($fname, 'عيون') || str_contains($fname, 'عين')) {
                                                $fIcon = 'fas fa-eye';
                                                $fTone = 'sky';
                                            } elseif (str_contains($fname, 'جراح')) {
                                                $fIcon = 'fas fa-user-md';
                                                $fTone = 'emerald';
                                            } elseif (str_contains($fname, 'باطن') || str_contains($fname, 'هضم')) {
                                                $fIcon = 'fas fa-stethoscope';
                                                $fTone = 'orange';
                                            }
                                        @endphp
                                        <li>
                                            <a href="{{ route('home') }}#departments" class="hms-footer-dept hms-footer-dept--{{ $fTone }}">
                                                <i class="{{ $fIcon }}"></i>
                                                <span>{{ $section->name }}</span>
                                            </a>
                                        </li>
                                    @empty
                                        <li>
                                            <a href="{{ route('home') }}#departments" class="hms-footer-dept hms-footer-dept--teal">
                                                <i class="fas fa-hospital"></i>
                                                <span>{{ __('website.medical_departments') }}</span>
                                            </a>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="big-column col-lg-6 col-md-12 col-sm-12">
                    <div class="row clearfix">
                        <div class="footer-column col-lg-6 col-md-6 col-sm-12">
                            <div class="footer-widget news-widget">
                                <div class="footer-title clearfix">
                                    <h2>{{ __('website.latest_articles') }}</h2>
                                    <div class="separator"></div>
                                </div>
                                @forelse($footerBlogs as $blog)
                                    <div class="news-widget-block">
                                        <div class="widget-inner">
                                            <div class="image">
                                                <a href="{{ route('blogs.show', $blog) }}">
                                                    <img src="{{ $blog->imageUrl() }}" alt="{{ $blog->title }}" style="width:70px;height:70px;object-fit:cover;border-radius:8px"/>
                                                </a>
                                            </div>
                                            <h3><a href="{{ route('blogs.show', $blog) }}">{{ \Illuminate\Support\Str::limit($blog->title, 45) }}</a></h3>
                                            <div class="post-date">{{ optional($blog->published_at)->translatedFormat('d F Y') }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <p style="color:#fff">{{ __('website.no_articles') }}</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="footer-column col-lg-6 col-md-6 col-sm-12">
                            <div class="footer-widget contact-widget">
                                <div class="footer-title clearfix">
                                    <h2>{{ __('website.contact_us') }}</h2>
                                    <div class="separator"></div>
                                </div>
                                <ul class="contact-list">
                                    <li>
                                        <span class="icon flaticon-placeholder"></span>
                                        {{ $siteSetting->address }}<br>{{ $siteSetting->city }}
                                    </li>
                                    <li>
                                        <span class="icon flaticon-call"></span>
                                        {{ $siteSetting->working_hours }}<br>
                                        <a href="tel:{{ $siteSetting->phone }}">{{ $siteSetting->phone }}</a>
                                        @if($siteSetting->phone2)
                                            <br><a href="tel:{{ $siteSetting->phone2 }}">{{ $siteSetting->phone2 }}</a>
                                        @endif
                                    </li>
                                    <li>
                                        <span class="icon flaticon-message"></span>
                                        هل لديك سؤال؟
                                        <a href="mailto:{{ $siteSetting->email }}">{{ $siteSetting->email }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="auto-container">
            <div class="copyright">{{ $siteSetting->copyright ?: ($siteSetting->hospital_name . ' © جميع الحقوق محفوظة') }}</div>
        </div>
    </div>
</footer>
