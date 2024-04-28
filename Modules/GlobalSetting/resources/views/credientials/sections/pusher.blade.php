<div class="tab-pane fade" id="pusher_tab" role="tabpanel">
    <form action="{{ route('admin.update-pusher') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <div class="form-group">
                <label for="">{{ __('Pusher Status') }}</label>
                <select name="pusher_status" class="form-control">
                    <option {{ $setting->pusher_status == 'active' ? 'selected' : '' }} value="active">
                        {{ __('Enable') }}</option>
                    <option {{ $setting->pusher_status == 'inactive' ? 'selected' : '' }} value="inactive">
                        {{ __('Disable') }}</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="">{{ __('Pusher App Id') }}</label>
            @if (env('APP_MODE') == 'DEMO')
                <input type="text" value="APP-DEMO-9348934-ID" class="form-control" name="pusher_app_id">
            @else
                <input type="text" value="{{ $setting->pusher_app_id }}" class="form-control"
                    name="pusher_app_id">
            @endif

        </div>
        <div class="form-group">
            <label for="">{{ __('Pusher App Key') }}</label>
            @if (env('APP_MODE') == 'DEMO')
                <input type="text" value="APP-DEMO-9348934-ID" class="form-control" name="pusher_app_key">
            @else
                <input type="text" value="{{ $setting->pusher_app_key }}" class="form-control"
                    name="pusher_app_key">
            @endif

        </div>

        <div class="form-group">
            <label for="">{{ __('Pusher App Secret') }}</label>
            @if (env('APP_MODE') == 'DEMO')
                <input type="text" value="APP-ID-SECRET-39343434" class="form-control" name="pusher_app_secret">
            @else
                <input type="text" value="{{ $setting->pusher_app_secret }}" class="form-control"
                    name="pusher_app_secret">
            @endif
        </div>

        <div class="form-group">
            <label for="">{{ __('Pusher App Cluster') }}</label>
            @if (env('APP_MODE') == 'DEMO')
                <input type="text" value="APP-ID-SECRET-39343434" class="form-control" name="pusher_app_key">
            @else
                <input type="text" value="{{ $setting->pusher_app_cluster }}" class="form-control"
                    name="pusher_app_cluster">
            @endif
        </div>

        <button class="btn btn-primary">{{ __('Update') }}</button>

    </form>
</div>
