@extends('WebSite.layouts.master')
@section('content')
<section class="hms-page-section hms-queue-track-page">
    <div class="auto-container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="hms-queue-card">
                    <div class="hms-queue-card__icon"><i class="fas fa-ticket-alt"></i></div>
                    <h2 class="hms-queue-card__title">{{ __('website.queue_title') }}</h2>
                    <p class="hms-queue-card__text">{{ __('website.queue_hint') }}</p>

                    <form action="{{ route('queue.track.lookup') }}" method="POST" class="hms-queue-form" id="hmsQueueTrackForm">
                        @csrf
                        <label for="ticket_number">{{ __('website.ticket_number') }}</label>
                        <input type="text" name="ticket_number" id="ticket_number"
                               class="hms-queue-input @error('ticket_number') is-invalid @enderror"
                               value="{{ old('ticket_number', $ticket_number ?? request('ticket_number')) }}"
                               required autofocus
                               placeholder="{{ __('website.ticket_placeholder') }}"
                               autocomplete="off">
                        @error('ticket_number')
                            <span class="hms-queue-error">{{ $message }}</span>
                        @enderror

                        <button type="submit" class="theme-btn btn-style-two hms-queue-submit" id="hmsQueueSubmitBtn">
                            <span class="txt"><i class="fas fa-search"></i> {{ __('website.query') }}</span>
                        </button>
                    </form>

                    @if(!empty($searched))
                        @if($ticket)
                            <div class="hms-queue-result hms-queue-result--ok">
                                <h3>{{ $ticket->ticket_number }}</h3>
                                <p><strong>{{ $ticket->patient_name }}</strong></p>
                                <p>{{ __('website.department') }}: {{ optional($ticket->section)->name }}</p>
                                @if($ticket->doctor)
                                    <p>{{ __('website.doctor') }}: {{ $ticket->doctor->name }}</p>
                                @endif
                                <p>
                                    {{ __('website.status') }}:
                                    <span class="hms-queue-badge">
                                        {{ \App\Models\QueueTicket::$statusLabels[$ticket->status] ?? $ticket->status }}
                                    </span>
                                </p>
                                @if($ticket->status === 'waiting')
                                    <p class="mb-0">{{ __('website.estimated_wait', ['minutes' => $ticket->estimated_wait_minutes]) }}</p>
                                @elseif(in_array($ticket->status, ['called', 'serving']))
                                    <p class="hms-queue-result__alert">{{ __('website.your_turn') }}</p>
                                @endif
                            </div>
                        @else
                            <div class="hms-queue-result hms-queue-result--empty" role="alert">
                                <i class="fas fa-exclamation-circle"></i>
                                <strong>{{ __('website.ticket_not_found') }}</strong>
                                <p>
                                    {{ __('website.ticket_not_found_text', ['number' => !empty($ticket_number) ? '«'.$ticket_number.'»' : '']) }}
                                </p>
                                <span>{{ __('website.ticket_not_found_hint') }}</span>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
