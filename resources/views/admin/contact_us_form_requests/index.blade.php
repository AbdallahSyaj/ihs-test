@extends('admin.layouts.app')

@section('content')
    {{-- =========================================================== --}}
    {{-- =================== Page Header Section =================== --}}
    {{-- =========================================================== --}}
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-5 align-self-center">
                <h3 class="page-title">{{ __('dashboard.shared.modules.contact_us_requests') }}</h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">{{ __('dashboard.shared.modules.dashboard') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('dashboard.shared.modules.contact_us_requests') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    {{-- ========================================================== --}}
    {{-- ==================== Page Body Section =================== --}}
    {{-- ========================================================== --}}
    <div class="container-fluid">
        <div class="card">
            @if (isset($contactUsFormRequests) && $contactUsFormRequests->count() > 0)
                <div class="card-body">
                    <div class="row">
                        @if (isset($contactUsFormRequests))
                            @foreach ($contactUsFormRequests as $contactUsFormRequest)
                                <div class="col-md-12">
                                    <div class="card groove-container glow">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ __('dashboard.contact_us_requests.message_details') }}</h5>
                                            <ul class="list-unstyled">
                                                <li><strong>{{ __('dashboard.shared.name') }}:</strong> {{ $contactUsFormRequest->name ?? '-------' }}
                                                </li>
                                                <li><strong>{{ __('dashboard.shared.email') }}:</strong> {{ $contactUsFormRequest->email ?? '-------' }}
                                                </li>
                                                <li><strong>{{ __('dashboard.contact_us_requests.message') }}:</strong>
                                                    {{ $contactUsFormRequest->message ?? '-------' }}</li>
                                                <li>
                                                    <strong>{{ __('dashboard.contact_us_requests.received') }}:</strong>
                                                    @if (isset($contactUsFormRequest->created_at))
                                                        {{ $contactUsFormRequest->created_at->format('Y-m-d h:i A') }}
                                                        ({{ $contactUsFormRequest->created_at->diffForHumans() }})
                                                    @else
                                                        <span style="color: blue;">----------</span>
                                                    @endif
                                                </li>
                                            </ul>
                                            <div class="text-right">
                                                <a href="{{ route('super_admin.contact_us_requests-destroyMessage', $contactUsFormRequest->id) }}"
                                                    class="btn btn-danger btn-sm cool-button mr-2 delete-btn" title="{{ __('dashboard.shared.delete') }}"
                                                    onclick="return confirm('{{ __('dashboard.contact_us_requests.confirm_delete_message') }}')">
                                                    <i class="fas fa-trash-alt"></i> {{ __('dashboard.shared.delete') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="mt-3 d-flex justify-content-center">
                    {{ $contactUsFormRequests->links('pagination::bootstrap-4') }}
                </div>
            @else
                <h1>{{ __('dashboard.contact_us_requests.no_messages') }}</h1>
            @endif
        </div>
    </div>
@endsection

@section('extra_js')
@endsection
