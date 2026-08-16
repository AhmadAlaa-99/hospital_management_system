@extends('WebSite.layouts.master')

@section('content')
    <section class="page-title hms-page-hero" style="background-image:url({{ asset('WebSite/images/hms/blogs-hero.jpg') }})">
        <div class="auto-container">
            <h1>{{ $blog->title }}</h1>
            <ul class="page-breadcrumb">
                <li><a href="{{ route('home') }}">الرئيسية</a></li>
                <li><a href="{{ route('blogs.index') }}">المقالات</a></li>
                <li>{{ \Illuminate\Support\Str::limit($blog->title, 40) }}</li>
            </ul>
        </div>
    </section>

    <section class="news-detail-section" style="padding:70px 0">
        <div class="auto-container">
            <div class="row clearfix">
                <div class="col-lg-8 col-md-12 col-sm-12">
                    <div class="news-detail" style="background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,.06)">
                        <div class="image">
                            <img src="{{ $blog->imageUrl() }}" alt="{{ $blog->title }}" style="width:100%;max-height:420px;object-fit:cover">
                        </div>
                        <div class="lower-content" style="padding:28px">
                            <ul class="post-meta" style="list-style:none;padding:0;margin:0 0 15px;display:flex;gap:18px;flex-wrap:wrap;align-items:center;color:#666">
                                <li>{{ optional($blog->published_at)->translatedFormat('d F Y') }}</li>
                                <li>بواسطة: {{ $blog->author }}</li>
                                <li>{{ $blog->views }} مشاهدة</li>
                                <li>
                                    <button type="button"
                                            class="hms-like-btn {{ !empty($liked) ? 'is-liked' : '' }}"
                                            data-like-url="{{ route('blogs.like', $blog) }}"
                                            data-blog-id="{{ $blog->id }}"
                                            style="display:inline-flex;align-items:center;gap:6px">
                                        <i class="fas fa-heart"></i>
                                        <span class="js-likes">{{ $blog->likes }}</span>
                                        <span>إعجاب</span>
                                    </button>
                                </li>
                                <li>
                                    <i class="fas fa-comment"></i>
                                    <span class="js-comments-count">{{ $comments->count() }}</span> تعليق
                                </li>
                            </ul>
                            <h2 style="margin-bottom:18px;line-height:1.5;color:#163a3c">{{ $blog->title }}</h2>
                            <div class="text hms-article-body" style="line-height:1.95;color:#2a4547;white-space:pre-line">{{ $blog->body }}</div>
                            <div style="margin-top:30px">
                                <a href="{{ route('blogs.index') }}" class="theme-btn btn-style-five"><span class="txt">العودة للمقالات</span></a>
                                <a href="{{ route('home') }}#appointment" class="theme-btn btn-style-one" style="margin-inline-start:10px"><span class="txt">احجز موعد</span></a>
                            </div>
                        </div>
                    </div>

                    <div id="comments" class="hms-comments-box">
                        <h3>
                            <i class="far fa-comments"></i>
                            التعليقات (<span class="js-comments-count">{{ $comments->count() }}</span>)
                        </h3>

                        @php
                            $commentAvatar = asset('Dashboard/img/brand/hospital-logo.png');
                            $meAvatar = $commentAvatar;
                        @endphp

                        <form id="hmsCommentForm" class="hms-comment-composer" data-comment-url="{{ route('blogs.comments', $blog) }}" data-avatar="{{ $meAvatar }}">
                            @csrf
                            <div class="hms-comment-composer__row">
                                <div class="hms-comment-avatar hms-comment-avatar--logo" aria-hidden="true">
                                    <img src="{{ $meAvatar }}" alt="">
                                </div>
                                <div class="hms-comment-composer__fields">
                                    <textarea name="body" rows="1" required minlength="2" maxlength="1000" placeholder="اكتب تعليقاً..."></textarea>
                                    <button type="submit" class="hms-comment-post-btn">
                                        <i class="fas fa-paper-plane"></i>
                                        <span>نشر</span>
                                    </button>
                                </div>
                            </div>
                            @guest('patient')
                                <p class="hms-comment-hint">يجب تسجيل الدخول كمريض لنشر التعليق.</p>
                            @endguest
                        </form>

                        <div id="hmsCommentsList" class="hms-comments-list">
                            @forelse($comments as $comment)
                                @php
                                    $authorName = optional($comment->patient)->name ?: 'مريض';
                                    $initial = mb_substr($authorName, 0, 1, 'UTF-8');
                                @endphp
                                <div class="hms-fb-comment">
                                    <div class="hms-comment-avatar hms-comment-avatar--logo" title="{{ $authorName }}">
                                        <img src="{{ $commentAvatar }}" alt="{{ $authorName }}" onerror="this.style.display='none'">
                                        <span class="hms-comment-avatar__fallback">{{ $initial }}</span>
                                    </div>
                                    <div class="hms-fb-comment__main">
                                        <div class="hms-fb-bubble">
                                            <strong class="hms-fb-bubble__name">{{ $authorName }}</strong>
                                            <p class="hms-fb-bubble__text">{{ $comment->body }}</p>
                                        </div>
                                        <div class="hms-fb-meta">
                                            <span>{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <p id="hmsCommentsEmpty" class="hms-comments-empty">لا توجد تعليقات بعد. كن أول من يعلق.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-12 col-sm-12">
                    <div class="sidebar-widget" style="background:#fff;border-radius:12px;padding:22px;box-shadow:0 10px 30px rgba(0,0,0,.06);margin-bottom:20px">
                        <h3 style="margin-bottom:15px">تواصل معنا</h3>
                        <p style="margin-bottom:8px">{{ $siteSetting->hospital_name }}</p>
                        <p style="margin-bottom:8px">{{ $siteSetting->address }}</p>
                        <p style="margin-bottom:8px">{{ $siteSetting->city }}</p>
                        <p style="margin-bottom:8px"><a href="tel:{{ $siteSetting->phone }}">{{ $siteSetting->phone }}</a></p>
                        <p><a href="mailto:{{ $siteSetting->email }}">{{ $siteSetting->email }}</a></p>
                    </div>

                    <div class="sidebar-widget" style="background:#fff;border-radius:12px;padding:22px;box-shadow:0 10px 30px rgba(0,0,0,.06)">
                        <h3 style="margin-bottom:15px">مقالات ذات صلة</h3>
                        @forelse($related as $item)
                            <div style="display:flex;gap:12px;margin-bottom:16px">
                                <a href="{{ route('blogs.show', $item) }}" style="flex:0 0 90px">
                                    <img src="{{ $item->imageUrl() }}" alt="{{ $item->title }}" style="width:90px;height:70px;object-fit:cover;border-radius:8px">
                                </a>
                                <div>
                                    <h5 style="font-size:14px;line-height:1.5;margin:0 0 6px">
                                        <a href="{{ route('blogs.show', $item) }}">{{ \Illuminate\Support\Str::limit($item->title, 55) }}</a>
                                    </h5>
                                    <small class="text-muted">{{ optional($item->published_at)->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <p>لا توجد مقالات أخرى حالياً</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
