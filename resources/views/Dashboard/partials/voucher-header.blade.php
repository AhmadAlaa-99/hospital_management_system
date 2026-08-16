@php($site = \App\Models\SiteSetting::current())
<h6>{{ $site->hospital_name }}</h6>
<p>
    {{ $site->address }}@if($site->city)<br>{{ $site->city }}@endif
    @if($site->phone)<br>Tel: {{ $site->phone }}@endif
    @if($site->email)<br>Email: {{ $site->email }}@endif
</p>
