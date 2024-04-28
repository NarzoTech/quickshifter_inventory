@extends('admin.master_layout')
@section('title')
    <title>{{ __('Subscription History') }}</title>
@endsection
@section('admin-content')

    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Subscription History') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <tr>
                                            <th>{{ __('Serial') }}</th>
                                            <th>{{ __('User') }}</th>
                                            <th>{{ __('Plan') }}</th>
                                            <th>{{ __('Expiration') }}</th>
                                            <th>{{ __('Remaining') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>

                                        @forelse ($histories as $index => $history)
                                            <tr>
                                                <td>{{ ++$index }}</td>
                                                <td><a href="">{{ $history?->user?->name }}</a></td>
                                                <td>{{ $history->plan_name }}</td>

                                                <td>{{ $history->expiration }}</td>
                                                <td>
                                                    @if ($history->expiration_date == 'lifetime')
                                                        {{ __('Lifetime') }}
                                                    @else
                                                        @php
                                                            $date1 = new DateTime(date('Y-m-d'));
                                                            $date2 = new DateTime($history->expiration_date);
                                                            $interval = $date1->diff($date2);
                                                            $remaining = $interval->days;
                                                        @endphp

                                                        @if ($remaining > 0)
                                                            {{ $remaining }} {{ __('Days') }}
                                                        @else
                                                            {{ __('Expired') }}
                                                        @endif
                                                    @endif
                                                </td>

                                                <td>

                                                    @if ($history->expiration_date == 'lifetime')
                                                        <div class="badge badge-success">{{ __('Active') }}</div>
                                                    @else
                                                        @if (date('Y-m-d') <= $history->expiration_date)
                                                            <div class="badge badge-success">{{ __('Active') }}</div>
                                                        @else
                                                            <div class="badge badge-danger">{{ __('Expired') }}</div>
                                                        @endif
                                                    @endif

                                                </td>

                                                <td>
                                                    <a href="{{ route('admin.purchase-history-show', $history->id) }}"
                                                        class="btn btn-primary btn-sm"><i class="fa fa-eye"></i></a>

                                                    <a href="javascript:;"
                                                        data-url="{{ route('admin.plan-renew', $history->id) }}"
                                                        class="btn btn-success btn-sm plan_renew"><i
                                                            class="fa fa-sync-alt"></i></a>

                                                    <a href=""
                                                        data-url="{{ route('admin.delete-plan-payment', $history->id) }}"
                                                        class="btn btn-danger btn-sm delete"><i class="fa fa-trash"></i></a>

                                                </td>
                                            </tr>
                                        @empty
                                            <x-empty-table :name="__('Subscription')" route="admin.subscription-plan.create"
                                                create="yes" :message="__('No data found!')" colspan="6"></x-empty-table>
                                        @endforelse
                                    </table>
                                </div>
                            </div>

                            @if ($histories->hasPages())
                                <div class="card-footer">
                                    {{ $histories->links('admin.paginate_box') }}
                                </div>
                            @endif
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
                        <h5 class="modal-title">{{ __('Delete subscription history') }}</h5>
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

    <div class="modal fade" tabindex="-1" role="dialog" id="plan_renew">
        <div class="modal-dialog" role="document">
            <form action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Subscription Renew') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="text-danger">{{ __('Are you really want to renew this subscription?') }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                        <button type="submit" class="btn btn-danger">{{ __('Yes, Renew') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    @push('js')
        <script>
            $(function() {
                'use strict'

                $('.delete').on('click', function(e) {
                    e.preventDefault();
                    const modal = $('#delete');
                    modal.find('form').attr('action', $(this).data('url'));
                    modal.modal('show');
                })

                $('.plan_renew').on('click', function(e) {
                    e.preventDefault();
                    const modal = $('#plan_renew');
                    modal.find('form').attr('action', $(this).data('url'));
                    modal.modal('show');
                })


            })
        </script>
    @endpush

@endsection
