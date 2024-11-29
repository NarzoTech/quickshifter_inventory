<div class="modal" id="addCustomer">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">{{ __('Add Customer') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <form action="{{ route('admin.customers.store') }}" method="POST" id="add-customer-form">
                    @csrf
                    <div class="row">
                        <div class="form-group col-md-5">
                            <label for="name">{{ __('Customer Name') }}<span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="group_id">{{ __('Customer Group') }}</label>
                            <select name="group_id" id="group_id" class="form-control">
                                <option value="">{{ __('Select Group') }}</option>
                                @foreach ($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="due">{{ __('Due') }}</label>
                            <input type="text" class="form-control" id="due" name="due">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="phone">{{ __('Phone') }}</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="form-group col-md-4 ">
                            <label for="email">{{ __('Email') }}</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="area_id">{{ __('Area') }}</label>
                            <select name="area_id" id="area_id" class="form-control">
                                <option value="">{{ __('Select Area') }}</option>
                                @foreach ($areaList as $list)
                                    <option value="{{ $list->id }}">{{ $list->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4 ">
                            <label for="vehicle_id">{{ __('Vehicle') }}</label>
                            <select name="vehicle_id" id="vehicle_id" class="form-control select2"
                                data-dropdown-parent="#addCustomer" data-control="select2">
                                <option value="">{{ __('Select Vehicle') }}</option>
                                @foreach ($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">{{ $vehicle->name }} - {{ $vehicle->model }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="plate_number">{{ __('Plate Number') }}</label>
                            <input type="text" class="form-control" id="plate_number" name="plate_number">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="membership">{{ __('Membership') }}</label>
                            <input type="text" class="form-control" id="membership" name="membership">
                        </div>
                        <div class="form-group col-md-4 ">
                            <label for="date">{{ __('Date') }}</label>
                            <input type="text" class="form-control datepicker" id="date" name="date">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="status">{{ __('Status') }}</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1">{{ __('Active') }}</option>
                                <option value="0">{{ __('Inactive') }}</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4 d-flex justify-content-center align-items-center">
                            <label class="custom-switch mt-2">
                                <input type="checkbox" name="guest" class="custom-switch-input" value="1">
                                <span class="custom-switch-indicator"></span>
                                <label for="guest" class="ml-2">{{ __('Guest Customer') }}</label>
                            </label>
                        </div>
                        <div class="form-group col-md-12">
                            <label for="address">{{ __('Address') }}</label>
                            <textarea name="address" id="address" class="form-control height-80px" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" form="add-customer-form">Save</button>
            </div>

        </div>
    </div>
</div>
