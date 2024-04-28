@extends('admin.master_layout')
@section('title')
    <title>{{ $title }}</title>
@endsection
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ $title }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    {{-- Search filter --}}
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ url()->current() }}" method="GET"
                                    onchange="this.submit()" class="card-body">
                                    <div class="row">
                                        <div class="{{Route::is('admin.plan-transaction-history') ? 'col-md-4 ': 'col-md-6'}} form-group">
                                            <input type="text" name="keyword" value="{{ request()->get('keyword') }}"
                                                class="form-control" placeholder="{{ __('Search') }}">
                                        </div>
                                        @if (Route::is('admin.plan-transaction-history'))
                                            <div class="col-md-2 form-group">
                                                <select name="status" id="status" class="form-control">
                                                    <option value="">{{ __('Select Status') }}</option>
                                                    <option value="active"
                                                        {{ request('status') == 'active' ? 'selected' : '' }}>
                                                        {{ __('Active') }}
                                                    </option>
                                                    <option value="inactive"
                                                        {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                                        {{ __('In-Active') }}
                                                    </option>
                                                </select>
                                            </div>
                                        @endif

                                        <div class="col-md-2 form-group">
                                            <select name="user" id="user" class="form-control select2">
                                                <option value="">{{ __('Select user') }}</option>
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                        {{ $user->id == request('user') ? 'selected' : '' }}>
                                                        {{ $user->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <select name="order_by" id="order_by" class="form-control">
                                                <option value="">{{ __('Order By') }}</option>
                                                <option value="1" {{ request('order_by') == '1' ? 'selected' : '' }}>
                                                    {{ __('ASC') }}
                                                </option>
                                                <option value="0" {{ request('order_by') == '0' ? 'selected' : '' }}>
                                                    {{ __('DESC') }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <select name="par-page" id="par-page" class="form-control">
                                                <option value="">{{ __('Per Page') }}</option>
                                                <option value="10" {{ '10' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('10') }}
                                                </option>
                                                <option value="50" {{ '50' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('50') }}
                                                </option>
                                                <option value="100"
                                                    {{ '100' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('100') }}
                                                </option>
                                                <option value="all"
                                                    {{ 'all' == request('par-page') ? 'selected' : '' }}>
                                                    {{ __('All') }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tr>
                                            <th>{{ __('SN') }}</th>
                                            <th>{{ __('User') }}</th>
                                            <th>{{ __('Plan') }}</th>
                                            <th>{{ __('Price') }}</th>
                                            <th>{{ __('Payment') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>

                                        @forelse ($histories as $index => $history)
                                            <tr>
                                                <td>{{ ++$index }}</td>
                                                <td><a href="">{{ $history?->user?->name }}</a></td>
                                                <td>{{ $history->plan_name }}</td>

                                                <td>{{ currency($history->plan_price) }}</td>

                                                <td>
                                                    @if ($history->payment_status == 'success')
                                                        <div class="badge badge-success">{{ __('Success') }}</div>
                                                    @else
                                                        <div class="badge badge-danger">{{ __('Pending') }}</div>
                                                    @endif
                                                </td>

                                                <td>
                                                    <a href="{{ route('admin.purchase-history-show', $history->id) }}"
                                                        class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></a>
                                                    <a href=""
                                                        data-url="{{ route('admin.delete-plan-payment', $history->id) }}"
                                                        class="btn btn-danger btn-sm delete"><i class="fa fa-trash"></i></a>

                                                </td>
                                            </tr>
                                        @empty
                                            <x-empty-table :name="__('')" route="admin.subscription-plan.create"
                                                create="no" :message="__('No data found!')" colspan="6"></x-empty-table>
                                        @endforelse

                                    </table>
                                </div>
                                @if (request()->get('par-page') !== 'all')
                                    <div class="float-right">
                                        {{ $histories->onEachSide(0)->links() }}
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>


    <div class="modal fade" tabindex="-1" role="dialog" id="delete">
        <div class="modal-dialog" role="document">
            <form action="" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Delete Purchase History') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-danger">{{ __('Are You Sure to Delete this Plan?') }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('Yes, Delete') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>



    <script>
        $(function() {
            'use strict'

            $('.delete').on('click', function(e) {
                e.preventDefault();
                const modal = $('#delete');
                modal.find('form').attr('action', $(this).data('url'));
                modal.modal('show');
            })
        })
    </script>
@endsection
