@if($testimonials->isNotEmpty())
    @foreach($testimonials as $testimonial)
        <div class="testimonial-block-two col-lg-6 col-md-6 col-sm-12">
            <div class="inner-box hms-testimonial-card">
                <div class="image">
                    <img src="{{ $testimonial->homepageAvatar($loop->index) }}" alt="{{ $testimonial->patientDisplayName() }}"/>
                </div>
                <div class="text">{{ $testimonial->comment }}</div>
                <div class="lower-box">
                    <div class="hms-testimonial-footer">
                        <div class="quote-icon fas fa-quote-right"></div>
                        <div class="author-info">
                            <h3>{{ $testimonial->patientDisplayName() }}</h3>
                            <div class="author">{{ $testimonial->patientRoleLabel() }}</div>
                            <div class="hms-testimonial-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star{{ $i <= $testimonial->rating ? '' : '-o' }}"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endif
