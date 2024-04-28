@extends('admin.master_layout')
@section('title')
    <title>{{ __('Club Point History') }}</title>
@endsection
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Club Point History') }}</h1>
            </div>

            <div class="section-body">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <th>{{ __('SN') }}</th>
                                            <th>{{ __('User') }}</th>
                                            <th>{{ __('Order Id') }}</th>
                                            <th>{{ __('Club Point') }}</th>
                                            <th>{{ __('Earned At') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </thead>

                                        @forelse ($histories as $index => $history)
                                            <tr>
                                                <td>{{ ++$index }}</td>
                                                <td><a
                                                        href="{{ route('admin.customer-show', $history->user_id) }}">{{ $history?->user?->name }}</a>
                                                </td>
                                                <td>#{{ $history?->order?->order_id }}</td>
                                                <td>{{ $history->club_point }}</td>
                                                <td>{{ $history->created_at->format('H:iA, d M Y') }}</td>
                                                <td>

                                                    @if ($history->status == 'pending')
                                                        <span class="badge badge-danger">{{ __('Pending') }}</span>
                                                    @else
                                                        <span class="badge badge-success">{{ __('Converted') }}</span>
                                                    @endif
                                                </td>

                                            </tr>
                                        @empty
                                            <x-empty-table :name="__('')" route="" create="no" :message="__('No data found!')"
                                                colspan="6"></x-empty-table>
                                        @endforelse


                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
