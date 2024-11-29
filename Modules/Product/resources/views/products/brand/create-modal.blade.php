<div class="modal fade" id="brandModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">{{ __('Create Brand') }}</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal">{{ __('×') }}</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <form action="{{ route('admin.brand.store') }}" method="post" enctype="multipart/form-data"
                    id="brandForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <div class="form-group">
                                <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" id="name" required
                                    value="{{ old('name') }}">
                                @error('name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-8 offset-md-2">
                            <div class="form-group">
                                <label for="slug">{{ __('Status') }}<span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1">
                                        {{ __('Active') }}</option>
                                    <option value="0">
                                        {{ __('Inactive') }}</option>
                                </select>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-8 offset-md-2">
                            <div class="form-group">
                                <label for="description">{{ __('Short Description') }}</label>
                                <textarea name="description" id="description" cols="30" rows="10"
                                    placeholder="{{ __('Enter Short Description') }}" class="form-control"></textarea>
                                @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal fade">{{ __('Close') }}</button>
                <button type="submit" class="btn btn-success" form="brandForm"><i class="fa fa-save"></i>
                    {{ __('Save') }}</button>

            </div>

        </div>
    </div>
</div>
