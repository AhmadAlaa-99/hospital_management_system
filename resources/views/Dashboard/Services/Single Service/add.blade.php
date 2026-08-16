<div class="modal fade" id="add" tabindex="-1" role="dialog" aria-labelledby="addServiceLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addServiceLabel">{{trans('Services.add_Service')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('Service.store') }}" method="post" autocomplete="off" class="hms-form">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="add_name">{{trans('Services.name')}}</label>
                        <input type="text" name="name" id="add_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="add_price">{{trans('Services.price')}}</label>
                        <input type="number" step="0.01" name="price" id="add_price" class="form-control" required>
                    </div>
                    <div class="form-group mb-0">
                        <label for="add_description">{{trans('Services.description')}}</label>
                        <textarea class="form-control" name="description" id="add_description" rows="4"></textarea>
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
