<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="{{ asset('Dashboard/css/queue-display.css') }}">
</head>
<body>
@php
    /** @var \App\Services\QueueService $queueService */
    $current = $data['current'];
    $statusLabels = \App\Models\QueueTicket::$statusLabels;
    $priorityLabels = \App\Models\QueueTicket::$priorityLabels;
    $hospitalName = optional(\App\Models\SiteSetting::current())->hospital_name ?? 'مستشفى الشام التخصصي للعيادات الشاملة التخصصية';
    $activeSectionId = $scope === 'doctor' ? ($sectionId ?? $scopeId) : $scopeId;
    $currentParts = $current ? $queueService->parseTicketDisplay($current->ticket_number) : ['code' => '', 'number' => '—'];
    $sectionLabel = $queueService->sectionTicketLabel($activeSectionId);
@endphp

<div class="queue-screen">
    <header class="queue-topbar">
        <div class="queue-brand">
            <img src="{{ asset('Dashboard/img/brand/hospital-logo.png') }}" alt="logo">
            <div class="queue-brand-text">
                <h1 id="page-title">{{ $title }}</h1>
                <p>{{ $hospitalName }}</p>
            </div>
        </div>
        <div class="queue-meta">
            <div class="queue-live"><span class="queue-live-dot"></span> تحديث مباشر</div>
            <button type="button" class="queue-sound-btn" id="sound-toggle" title="تفعيل/كتم صوت النداء">🔊</button>
            <span class="queue-badge"><span id="waiting-count">{{ $data['waiting_count'] }}</span> بالانتظار</span>
            <div class="queue-clock" id="clock"></div>
        </div>
    </header>

    @if($scope === 'section' && isset($sections) && $sections->count() > 1)
    <nav class="queue-section-nav" id="section-nav">
        @foreach($sections as $sec)
            <button type="button"
                    class="queue-section-btn {{ (int) $sec['id'] === (int) $activeSectionId ? 'active' : '' }}"
                    data-section-id="{{ $sec['id'] }}"
                    data-section-name="{{ $sec['name'] }}"
                    data-section-url="{{ $sec['url'] }}"
                    title="{{ $sec['name'] }}">
                <span class="sec-code">{{ $sec['code'] }}</span>
                <span class="sec-label">{{ $sec['label'] }}</span>
            </button>
        @endforeach
    </nav>
    @endif

    <div class="queue-main">
        <aside class="queue-sidebar">
            <div class="queue-sidebar-header">
                <h2>التالي في الانتظار</h2>
                <small id="sidebar-section-label">{{ $sectionLabel }}</small>
            </div>
            <div class="queue-waiting-list" id="waiting-list">
                @forelse($data['waiting'] as $ticket)
                    @php $parts = $queueService->parseTicketDisplay($ticket->ticket_number); @endphp
                    <div class="queue-wait-item">
                        <div class="ticket-split ticket-split--sm">
                            <span class="ticket-code">{{ $parts['code'] }}</span>
                            <span class="ticket-num">{{ $parts['number'] }}</span>
                        </div>
                        <div class="queue-wait-info">
                            <div class="queue-wait-name">{{ $ticket->patient_name }}</div>
                            <div class="queue-wait-priority {{ $ticket->priority }}">
                                {{ $priorityLabels[$ticket->priority] ?? $ticket->priority }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="queue-empty-msg">لا يوجد مرضى بالانتظار</div>
                @endforelse
            </div>
        </aside>

        <section class="queue-current {{ $current ? '' : 'empty' }}" id="current-panel">
            <div class="queue-current-label">الرقم الحالي — <span id="current-section-label">{{ $sectionLabel }}</span></div>
            <div class="ticket-split ticket-split--lg" id="current-ticket-wrap">
                <span class="ticket-code" id="current-code">{{ $currentParts['code'] }}</span>
                <span class="ticket-num" id="current-number">{{ $currentParts['number'] }}</span>
            </div>
            <div class="queue-current-name" id="current-name">
                {{ $current ? $current->patient_name : 'في انتظار النداء' }}
            </div>
            <div class="queue-current-doctor" id="current-doctor">
                @if($current && $current->doctor)
                    د. {{ $current->doctor->name }}
                @endif
            </div>
            <div class="queue-current-status" id="current-status">
                @if($current)
                    {{ $statusLabels[$current->status] ?? $current->status }}
                @else
                    يرجى الانتظار في منطقة الاستراحة
                @endif
            </div>
        </section>
    </div>

    <footer class="queue-footer">
        <span class="queue-footer-label">تمت معالجتهم:</span>
        <div class="queue-footer-list" id="recent-list">
            @forelse($data['recent'] as $ticket)
                @php $parts = $queueService->parseTicketDisplay($ticket->ticket_number); @endphp
                <span class="queue-done-chip">
                    <span class="chip-code">{{ $parts['code'] }}</span>{{ $parts['number'] }}
                </span>
            @empty
                <span class="queue-footer-label">—</span>
            @endforelse
        </div>
    </footer>
</div>

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
(function () {
    let sectionId = {{ (int) $activeSectionId }};
    const doctorId = {{ $scope === 'doctor' ? (int) $scopeId : 'null' }};
    const scope = @json($scope);
    const sectionsMeta = @json($sections ?? []);
    const dataBaseUrl = @json(route('queue.data'));
    const pusherKey = @json(config('broadcasting.connections.pusher.key'));
    const pollMs = 3000;
    let lastNumber = document.getElementById('current-number').textContent.trim();
    let lastAnnounceKey = '';
    let skipNextAnnounce = true;
    let soundEnabled = localStorage.getItem('queueDisplaySound') !== 'off';
    let speechReady = false;
    let pusher = null;
    let subscribedChannels = [];

    const priorityLabels = @json(\App\Models\QueueTicket::$priorityLabels);
    const sectionLabels = {};
    sectionsMeta.forEach(function (s) { sectionLabels[s.id] = s.label; });

    function dataUrl() {
        let url = dataBaseUrl + '?section_id=' + sectionId;
        if (doctorId) url += '&doctor_id=' + doctorId;
        return url;
    }

    function updateClock() {
        const el = document.getElementById('clock');
        if (el) {
            el.textContent = new Date().toLocaleTimeString('ar-SY', {
                hour: '2-digit', minute: '2-digit', second: '2-digit'
            });
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    function ticketParts(t) {
        if (t.display_code && t.display_number) {
            return { code: t.display_code, number: t.display_number };
        }
        const full = t.ticket_number || '';
        const i = full.indexOf('-');
        if (i > -1) return { code: full.slice(0, i), number: full.slice(i + 1) };
        return { code: '', number: full };
    }

    function ticketSplitHtml(parts, size) {
        return '<div class="ticket-split ticket-split--' + size + '">' +
            (parts.code ? '<span class="ticket-code">' + parts.code + '</span>' : '') +
            '<span class="ticket-num">' + parts.number + '</span></div>';
    }

    function renderWaiting(list) {
        const container = document.getElementById('waiting-list');
        if (!list || !list.length) {
            container.innerHTML = '<div class="queue-empty-msg">لا يوجد مرضى بالانتظار</div>';
            return;
        }
        container.innerHTML = list.map(function (t) {
            const parts = ticketParts(t);
            const pr = t.priority || 'normal';
            const prLabel = priorityLabels[pr] || pr;
            return '<div class="queue-wait-item">' +
                ticketSplitHtml(parts, 'sm') +
                '<div class="queue-wait-info">' +
                '<div class="queue-wait-name">' + t.patient_name + '</div>' +
                '<div class="queue-wait-priority ' + pr + '">' + prLabel + '</div>' +
                '</div></div>';
        }).join('');
    }

    function renderRecent(list) {
        const container = document.getElementById('recent-list');
        if (!list || !list.length) {
            container.innerHTML = '<span class="queue-footer-label">—</span>';
            return;
        }
        container.innerHTML = list.map(function (t) {
            const parts = ticketParts(t);
            return '<span class="queue-done-chip">' +
                (parts.code ? '<span class="chip-code">' + parts.code + '</span>' : '') +
                parts.number + '</span>';
        }).join('');
    }

    function updateSoundButton() {
        const btn = document.getElementById('sound-toggle');
        if (!btn) return;
        btn.textContent = soundEnabled ? '🔊' : '🔇';
        btn.classList.toggle('muted', !soundEnabled);
    }

    function playCallChime() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.value = 880;
            gain.gain.value = 0.08;
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start();
            osc.stop(ctx.currentTime + 0.25);
        } catch (e) {}
    }

    function speakArabic(text) {
        if (!soundEnabled || !window.speechSynthesis) return;
        window.speechSynthesis.cancel();
        const utter = new SpeechSynthesisUtterance(text);
        utter.lang = 'ar-SA';
        utter.rate = 0.92;
        utter.pitch = 1;
        const voices = window.speechSynthesis.getVoices();
        const arVoice = voices.find(function (v) {
            return v.lang && (v.lang.indexOf('ar') === 0);
        });
        if (arVoice) utter.voice = arVoice;
        window.speechSynthesis.speak(utter);
    }

    function announceCurrent(current) {
        if (!current || !['called', 'serving'].includes(current.status)) return;
        if (skipNextAnnounce) return;

        const key = String(current.id) + '|' + (current.called_at || current.ticket_number || '');
        if (key === lastAnnounceKey) return;
        lastAnnounceKey = key;

        const parts = ticketParts(current);
        const ticketLabel = parts.code
            ? parts.code + ' ' + parts.number
            : (current.ticket_number || parts.number);
        const section = current.section_label || '';
        const doctor = current.doctor ? current.doctor.name : '';
        let msg = 'المريض ' + (current.patient_name || '') + '، رقم ' + ticketLabel;
        if (doctor) {
            msg += '، يرجى التوجه إلى عيادة الدكتور ' + doctor;
        } else if (section) {
            msg += '، يرجى التوجه إلى قسم ' + section;
        } else {
            msg += '، يرجى التوجه إلى العيادة';
        }

        playCallChime();
        setTimeout(function () { speakArabic(msg); }, 350);
    }

    function initSpeech() {
        if (!window.speechSynthesis) return;
        speechSynthesis.getVoices();
        speechReady = true;
    }

    if (window.speechSynthesis) {
        initSpeech();
        window.speechSynthesis.onvoiceschanged = initSpeech;
    }

    updateSoundButton();
    document.getElementById('sound-toggle').addEventListener('click', function () {
        soundEnabled = !soundEnabled;
        localStorage.setItem('queueDisplaySound', soundEnabled ? 'on' : 'off');
        updateSoundButton();
        if (soundEnabled) {
            speakArabic('تم تفعيل صوت النداء');
        } else {
            window.speechSynthesis.cancel();
        }
    });

    document.body.addEventListener('click', function enableAudioOnce() {
        if (window.speechSynthesis && !speechReady) initSpeech();
        document.body.removeEventListener('click', enableAudioOnce);
    }, { once: true });

    function render(payload) {
        if (!payload) return;

        const current = payload.current;
        const parts = current ? ticketParts(current) : { code: '', number: '—' };
        const numEl = document.getElementById('current-number');
        const codeEl = document.getElementById('current-code');
        const panel = document.getElementById('current-panel');

        if (parts.number !== lastNumber) {
            numEl.classList.remove('flash');
            void numEl.offsetWidth;
            numEl.classList.add('flash');
            lastNumber = parts.number;
        }

        if (current) {
            announceCurrent(current);
        } else {
            lastAnnounceKey = '';
        }

        codeEl.textContent = parts.code;
        numEl.textContent = parts.number;
        document.getElementById('current-name').textContent = current
            ? current.patient_name : 'في انتظار النداء';
        document.getElementById('current-doctor').textContent = (current && current.doctor)
            ? 'د. ' + current.doctor.name : '';
        document.getElementById('current-status').textContent = current
            ? (current.status_label || current.status) : 'يرجى الانتظار في منطقة الاستراحة';
        document.getElementById('waiting-count').textContent = payload.waiting_count || 0;

        if (current && current.section_label) {
            document.getElementById('current-section-label').textContent = current.section_label;
            document.getElementById('sidebar-section-label').textContent = current.section_label;
        }

        panel.classList.toggle('empty', !current);
        renderWaiting(payload.waiting);
        renderRecent(payload.recent);
        skipNextAnnounce = false;
    }

    function fetchData() {
        fetch(dataUrl(), { headers: { 'Accept': 'application/json' }, cache: 'no-store' })
            .then(function (r) { return r.json(); })
            .then(render)
            .catch(function () {});
    }

    function subscribePusher() {
        if (!pusherKey) return;
        try {
            if (!pusher) {
                pusher = new Pusher(pusherKey, {
                    cluster: @json(config('broadcasting.connections.pusher.options.cluster')),
                    encrypted: true
                });
            }
            subscribedChannels.forEach(function (ch) { pusher.unsubscribe(ch); });
            subscribedChannels = [];
            const bind = function (channelName) {
                subscribedChannels.push(channelName);
                pusher.subscribe(channelName).bind('queue-updated', function (data) {
                    if (data && data.payload) render(data.payload);
                });
            };
            bind('queue.section.' + sectionId);
            if (doctorId) bind('queue.doctor.' + doctorId);
        } catch (e) {}
    }

    function switchSection(id, name, label) {
        sectionId = parseInt(id, 10);
        document.querySelectorAll('.queue-section-btn').forEach(function (btn) {
            btn.classList.toggle('active', parseInt(btn.dataset.sectionId, 10) === sectionId);
        });
        const title = document.getElementById('page-title');
        if (title && name) title.textContent = 'شاشة الانتظار — ' + name;
        const lbl = label || sectionLabels[sectionId] || '';
        document.getElementById('current-section-label').textContent = lbl;
        document.getElementById('sidebar-section-label').textContent = lbl;
        lastNumber = '';
        lastAnnounceKey = '';
        skipNextAnnounce = true;
        subscribePusher();
        fetchData();
        history.replaceState(null, '', sectionsMeta.find(function (s) { return s.id === sectionId; })?.url || location.href);
    }

    document.querySelectorAll('.queue-section-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            switchSection(btn.dataset.sectionId, btn.dataset.sectionName, btn.querySelector('.sec-label')?.textContent);
        });
    });

    subscribePusher();
    fetchData();
    setInterval(fetchData, pollMs);
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) fetchData();
    });
})();
</script>
</body>
</html>
