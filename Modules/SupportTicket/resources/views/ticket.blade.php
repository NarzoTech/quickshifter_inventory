@extends('admin.master_layout')
@section('title')
    <title>{{ __('Support Tickets') }}</title>
@endsection
@section('admin-content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ __('Support Tickets') }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ __('Support Tickets') }}</div>
                </div>
            </div>
            <div class="section-body">
                <div class="mt-4 row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between">
                                <h4>{{ __('Support Tickets') }}</h4>
                                <div>

                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive max-h-400">
                                    <table class="table table-striped report_table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('SN') }}</th>
                                                <th>{{ __('Ticket Info') }}</th>
                                                <th>{{ __('Unread Message') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Action') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($tickets as $ticket)
                                                <tr>
                                                    <td>{{ $loop->index + 1 }}</td>
                                                    <td>
                                                        <p>{{ __('Subject') }}: {{ html_decode($ticket->subject) }}
                                                        </p>
                                                        <p>{{ __('Ticket Id') }}: {{ $ticket->ticket_id }}</p>

                                                        <p>{{ __('Created') }}:
                                                            {{ $ticket->created_at->format('h:m A, d-M-Y') }}</p>
                                                    </td>

                                                    <td>
                                                        <span
                                                            class="badge badge-danger">{{ $ticket->unseen_for_user }}</span>
                                                    </td>
                                                    <td>
                                                        @if ($ticket->status == 'pending')
                                                            <span class="badge badge-danger">{{ __('Pending') }}</span>
                                                        @elseif ($ticket->status == 'in_progress')
                                                            <span
                                                                class="badge badge-success">{{ __('In Progress') }}</span>
                                                        @elseif ($ticket->status == 'closed')
                                                            <span class="badge badge-danger">{{ __('Closed') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('admin.support.ticket-show', $ticket->id) }}"
                                                            class="btn btn-primary btn-sm"><i class="fa fa-eye"
                                                                aria-hidden="true"></i></a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <x-empty-table :name="__('Support Tickets')" route="admin.support.ticket" create="no"
                                                    :message="__('No data found!')" colspan="5"></x-empty-table>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="float-right">
                                    {{ $tickets->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('css')
    <style>
        .dd-custom-css {
            position: absolute;
            will-change: transform;
            top: 0px;
            left: 0px;
            transform: translate3d(0px, -131px, 0px);
        }

        .max-h-400 {
            min-height: 400px;
        }
    </style>
@endpush
