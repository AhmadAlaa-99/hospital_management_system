
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
