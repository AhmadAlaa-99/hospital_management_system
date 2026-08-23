
@if (session('password_reset'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        تم تعيين كلمة مرور جديدة للمريض
        <strong>{{ session('password_reset_patient') }}</strong>:
        <code dir="ltr" style="font-size:1.1em;padding:2px 8px;">{{ session('password_reset') }}</code>
        <span class="d-block mt-1 text-muted small">انسخ الكلمة وسلّمها للمريض — لن تُعرض مرة أخرى.</span>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ session('error') }}
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ \App\Helpers\FriendlyError::message($error) }}</li>
            @endforeach
        </ul>
    </div>
@endif
    @if (session()->has('add'))
        <script>
            window.onload = function() {
                notif({
                    msg: "{{ trans('Dashboard/messages.add') }}",
                    type: "success"
                });
            }

        </script>
    @endif

    @if (session()->has('edit'))
        <script>
            window.onload = function() {
                notif({
                    msg: "{{ trans('Dashboard/messages.edit') }}",
                    type: "success"
                });
            }

        </script>
    @endif

    @if (session()->has('delete'))
        <script>
            window.onload = function() {
                notif({
                    msg: "{{ trans('Dashboard/messages.delete') }}",
                    type: "success"
                });
            }

        </script>
    @endif
