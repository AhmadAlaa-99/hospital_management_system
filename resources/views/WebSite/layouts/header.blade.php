<div class="nav-outer clearfix hms-web-nav">
    <nav class="main-menu navbar-expand-md navbar-light">
        <div class="navbar-header d-md-none">
            <button class="navbar-toggler mobile-nav-toggler" type="button" aria-label="{{ __('website.hospital_menu') }}">
                <span class="icon flaticon-menu"></span>
            </button>
        </div>

        <div class="collapse navbar-collapse clearfix" id="navbarSupportedContent">
            <ul class="navigation clearfix">
                <li class="{{ request()->routeIs('home') ? 'current' : '' }}">
                    <a href="{{ route('home') }}">{{ __('website.home') }}</a>
                </li>
                <li><a href="{{ route('home') }}#departments">{{ __('website.departments') }}</a></li>
                <li><a href="{{ route('home') }}#doctors">{{ __('website.doctors') }}</a></li>
                <li class="{{ request()->routeIs('blogs.*') ? 'current' : '' }}">
                    <a href="{{ route('blogs.index') }}">{{ __('website.articles') }}</a>
                </li>
                <li><a href="{{ route('home') }}#appointment">{{ __('website.appointments') }}</a></li>
                <li class="{{ request()->routeIs('queue.track') ? 'current' : '' }}">
                    <a href="{{ route('queue.track') }}">{{ __('website.queue_track') }}</a>
                </li>
                <li><a href="{{ route('home') }}#contact">{{ __('website.contact') }}</a></li>
                <li class="dropdown hms-lang-dropdown">
                    <a href="#">{{ LaravelLocalization::getCurrentLocaleNative() }}</a>
                    <ul>
                        @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                            @if($localeCode !== LaravelLocalization::getCurrentLocale())
                                <li>
                                    <a rel="alternate" hreflang="{{ $localeCode }}"
                                       href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}">
                                        {{ $properties['native'] }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    <div class="outer-box clearfix hms-web-actions">
        <ul class="social-box clearfix">
            @if(!empty(optional($siteSetting)->facebook))
                <li><a href="{{ $siteSetting->facebook }}" target="_blank" rel="noopener" title="Facebook"><span class="fab fa-facebook-f"></span></a></li>
            @endif
            @if(!empty(optional($siteSetting)->instagram))
                <li><a href="{{ $siteSetting->instagram }}" target="_blank" rel="noopener" title="Instagram"><span class="fab fa-instagram"></span></a></li>
            @endif
            <li>
                <a title="{{ __('website.login') }}" href="{{ url('/login') }}">
                    <span class="fas fa-user"></span>
                </a>
            </li>
        </ul>
        <div class="nav-box">
            <div class="nav-btn nav-toggler navSidebar-button" title="{{ __('website.hospital_menu') }}">
                <span class="icon flaticon-menu-1"></span>
            </div>
        </div>
    </div>
</div>
