@component('mail::message')
# {{ $patientName }}

تذكير: موعد مراجعتك الطبية بتاريخ **{{ $reviewDate }}**@if($doctorName) مع **{{ $doctorName }}**@endif.

يرجى الحضور في الموعد المحدد أو التواصل مع العيادة لتعديل الموعد.

شكراً،<br>
{{ optional(\App\Models\SiteSetting::current())->hospital_name ?? config('app.name') }}
@endcomponent
