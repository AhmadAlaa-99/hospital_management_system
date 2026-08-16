<div class="modal fade" id="edit{{ $service->id }}" tabindex="-1" role="dialog" aria-labelledby="editServiceLabel{{ $service->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editServiceLabel{{ $service->id }}">{{trans('Services.edit_Service')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('Service.update', $service->id) }}" method="post" class="hms-form" autocomplete="off">
                @csrf
                @method('PATCH')
                <input type="hidden" name="id" value="{{$service->id}}">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_name{{ $service->id }}">{{trans('Services.name')}}</label>
                        <input type="text" name="name" id="edit_name{{ $service->id }}" value="{{$service->name}}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_price{{ $service->id }}">{{trans('Services.price')}}</label>
                        <input type="number" step="0.01" name="price" id="edit_price{{ $service->id }}" value="{{$service->price}}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_description{{ $service->id }}">{{trans('Services.description')}}</label>
                        <textarea class="form-control" name="description" id="edit_description{{ $service->id }}" rows="4">{{$service->description}}</textarea>
                    </div>
                    <div class="form-group mb-0">
                        <label for="edit_status{{ $service->id }}">{{trans('doctors.Status')}}</label>
                        <select class="form-control" id="edit_status{{ $service->id }}" name="status" required>
                            <option value="1" {{ $service->status == 1 ? 'selected' : '' }}>{{trans('doctors.Enabled')}}</option>
                            <option value="0" {{ $service->status == 0 ? 'selected' : '' }}>{{trans('doctors.Not_enabled')}}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{trans('Dashboard/sections_trans.Close')}}</button>
                    <button type="submit" class="btn btn-primary btn-hms-primary">{{trans('Dashboard/sections_trans.submit')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
