<!-- Deleted insurance -->
<div class="modal fade" id="approval{{ $appointment->id }}" tabindex="-1" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">تاكيد موعد المريض</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('appointments.approval', $appointment->id) }}" method="post">
                    @method('PUT')
                    @csrf
                    <input type="hidden" name="id" value="{{ $appointment->id }}">
                    <p class="mg-b-20"><strong>{{ $appointment->name }}</strong></p>
                    @if($appointment->preferred_date)
                        @php
                            $preferredTime = $appointment->preferred_time
                                ? substr((string) $appointment->preferred_time, 0, 5)
                                : '09:00';
                            $suggestedAt = \Carbon\Carbon::parse($appointment->preferred_date . ' ' . $preferredTime)->format('Y-m-d H:i');
                        @endphp
                        <div class="alert alert-info py-2 px-3 mb-3">
                            <strong>الموعد المطلوب من المريض:</strong>
                            {{ \Carbon\Carbon::parse($appointment->preferred_date)->format('Y-m-d') }}
                            الساعة {{ $preferredTime }}
                        </div>
                    @else
                        @php $suggestedAt = now()->format('Y-m-d H:i'); @endphp
                        <div class="alert alert-warning py-2 px-3 mb-3">لم يحدد المريض تاريخاً مفضلاً — اختر الموعد النهائي يدوياً.</div>
                    @endif
                    <!--div-->
                    <div class="col-md-12 col-xl-12 col-xs-12 col-sm-12">
                        <div class="card">
                            <div class="card-body">
                                    <label class="d-block mb-2">تاريخ ووقت الموعد النهائي</label>
                                    <div class="input-group col-md-12">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="typcn typcn-calendar-outline tx-24 lh--9 op-6"></i>
                                            </div>
                                        </div><input class="form-control" name="appointment" id="datetimepicker{{ $appointment->id }}" type="datetime-local" value="{{ $suggestedAt ? \Carbon\Carbon::parse($suggestedAt)->format('Y-m-d\TH:i') : '' }}">
                                    </div>

                            </div>
                        </div>
                    </div>
                    <!--/div-->

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ trans('insurance.close') }}</button>
                        <button class="btn btn-success">{{ trans('insurance.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
