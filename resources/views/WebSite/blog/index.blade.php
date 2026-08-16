@extends('WebSite.layouts.master')

@section('content')
    <section class="page-title hms-page-hero" style="background-image:url({{ asset('WebSite/images/hms/blogs-hero.jpg') }})">
        <div class="auto-container">
            <h1>المقالات والأخبار</h1>
            <ul class="page-breadcrumb">
                <li><a href="{{ route('home') }}">الرئيسية</a></li>
                <li>المقالات</li>
            </ul>
        </div>
    </section>

    <section class="news-section-two" style="padding:70px 0">
        <div class="auto-container">
            <div class="row clearfix">
                @forelse($blogs as $blog)
                    @php $isLiked = in_array($blog->id, $likedIds ?? [], true); @endphp
                    <div class="news-block-two col-lg-6 col-md-12 col-sm-12" style="margin-bottom:30px">
                        <div class="inner-box hms-article-card">
                            <div class="image">
                                <a href="{{ route('blogs.show', $blog) }}">
                                    <img src="{{ $blog->imageUrl() }}" alt="{{ $blog->title }}">
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
                                            data-blog-id="{{ $blog->id }}">
                                        <i class="fas fa-heart"></i>
                                        <span class="js-likes">{{ $blog->likes }}</span>
                                    </button>
                                    <a href="{{ route('blogs.show', $blog) }}#comments"
                                       class="hms-article-meta__item hms-comment-open"
                                       data-comment-url="{{ route('blogs.show', $blog) }}#comments">
                                        <i class="fas fa-comment"></i>
                                        <span>{{ $blog->comments_count ?? 0 }}</span>
                                    </a>
                                    <span class="hms-article-meta__item">
                                        <i class="far fa-calendar-alt"></i>
                                        {{ optional($blog->published_at)->translatedFormat('d F Y') }}
                                    </span>
                                </div>
                                <h3>
                                    <a href="{{ route('blogs.show', $blog) }}">{{ $blog->title }}</a>
                                </h3>
                                <div class="text">{{ $blog->excerpt }}</div>
                                <a href="{{ route('blogs.show', $blog) }}" class="theme-btn btn-style-five"><span class="txt">اقرأ المزيد</span></a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center"><p>لا توجد مقالات حالياً</p></div>
                @endforelse
            </div>
            <div class="d-flex justify-content-center">
                {{ $blogs->links() }}
            </div>
        </div>
    </section>
@endsection
