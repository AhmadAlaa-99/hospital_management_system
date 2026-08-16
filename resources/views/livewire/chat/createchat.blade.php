<div>
    <div class="row row-sm">
        <div class="col-xl-12">
            <div class="card hms-table-card">
                <div class="card-header pb-0">
                    <h5 class="card-title mb-0">
                        {{ auth()->guard('patient')->check() ? 'ابدأ محادثة مع طبيب' : 'ابدأ محادثة مع مريض' }}
                    </h5>
                </div>
                <div class="card-body">
                    @if($flashMessage)
                        <div class="alert alert-info">{{ $flashMessage }}</div>
                    @endif
                    <div class="table-responsive">
                        <table class="table text-md-nowrap hms-table text-center">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ auth()->guard('patient')->check() ? 'اسم الطبيب' : 'اسم المريض' }}</th>
                                <th>البريد</th>
                                <th>العمليات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-hms-primary"
                                                wire:click="createConversation('{{ $user->email }}')">
                                            بدء المحادثة
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
