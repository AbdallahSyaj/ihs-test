@extends('admin.layouts.app')

@section('content')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-5 align-self-center">
                <h3 class="page-title">{{ __('dashboard.shared.modules.product_inquiry_requests') }}</h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">{{ __('dashboard.shared.modules.dashboard') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('dashboard.shared.modules.product_inquiry_requests') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if ($inquiries->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered display">
                                    <thead>
                                        <tr>
                                            <th>{{ __('dashboard.shared.name') }}</th>
                                            <th>{{ __('dashboard.shared.email') }}</th>
                                            <th>{{ __('dashboard.shared.phone') }}</th>
                                            <th>{{ __('dashboard.product_inquiry_requests.summary') }}</th>
                                            <th>{{ __('dashboard.product_inquiry_requests.status') }}</th>
                                            <th>{{ __('dashboard.product_inquiry_requests.received') }}</th>
                                            <th>{{ __('dashboard.shared.control') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($inquiries as $inquiry)
                                            <tr>
                                                <td>
                                                    {{ $inquiry->name }}
                                                    @if ($inquiry->read_at === null)
                                                        <span class="badge bg-info ms-1">{{ __('dashboard.product_inquiry_requests.unread') }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $inquiry->email }}</td>
                                                <td>{{ $inquiry->phone }}</td>
                                                <td>{{ $inquiry->productSummary() }}</td>
                                                <td>
                                                    @php
                                                        $statusKey = match ($inquiry->status) {
                                                            'replied' => 'status_replied',
                                                            'archived' => 'status_archived',
                                                            default => 'status_new',
                                                        };
                                                    @endphp
                                                    {{ __('dashboard.product_inquiry_requests.'.$statusKey) }}
                                                </td>
                                                <td>
                                                    @if ($inquiry->created_at)
                                                        {{ $inquiry->created_at->format('Y-m-d h:i A') }}
                                                        <br><small class="text-muted">{{ $inquiry->created_at->diffForHumans() }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('super_admin.product_inquiry_requests-show', $inquiry->id) }}"
                                                        class="btn waves-effect waves-light btn-secondary btn-sm"
                                                        title="{{ __('dashboard.product_inquiry_requests.view_details') }}">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3 d-flex justify-content-center">
                                {{ $inquiries->links('pagination::bootstrap-4') }}
                            </div>
                        @else
                            <p class="mb-0">{{ __('dashboard.product_inquiry_requests.no_inquiries') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
