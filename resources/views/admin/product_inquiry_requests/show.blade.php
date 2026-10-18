@extends('admin.layouts.app')

@section('content')
    <div class="page-breadcrumb">
        <div class="row">
            <div class="col-md-8 align-self-center">
                <h3 class="page-title">{{ __('dashboard.product_inquiry_requests.inquiry_details') }}</h3>
                <div class="d-flex align-items-center">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('super_admin.dashboard') }}">{{ __('dashboard.shared.modules.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('super_admin.product_inquiry_requests-index') }}">{{ __('dashboard.shared.modules.product_inquiry_requests') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">#{{ $inquiry->id }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-7">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('dashboard.product_inquiry_requests.customer') }}</h5>
                        <ul class="list-unstyled mb-0">
                            <li><strong>{{ __('dashboard.shared.name') }}:</strong> {{ $inquiry->name }}</li>
                            <li><strong>{{ __('dashboard.shared.email') }}:</strong> {{ $inquiry->email }}</li>
                            <li><strong>{{ __('dashboard.shared.phone') }}:</strong> {{ $inquiry->phone }}</li>
                            <li>
                                <strong>{{ __('dashboard.product_inquiry_requests.received') }}:</strong>
                                @if ($inquiry->created_at)
                                    {{ $inquiry->created_at->format('Y-m-d h:i A') }}
                                    ({{ $inquiry->created_at->diffForHumans() }})
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('dashboard.product_inquiry_requests.products') }}</h5>
                        @php
                            $items = is_array($inquiry->items) ? $inquiry->items : [];
                        @endphp
                        @if (count($items) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('dashboard.product_inquiry_requests.product_name') }}</th>
                                            <th>{{ __('dashboard.product_inquiry_requests.model') }}</th>
                                            <th>{{ __('dashboard.product_inquiry_requests.quantity') }}</th>
                                            <th>{{ __('dashboard.product_inquiry_requests.options') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $item)
                                            @if (is_array($item))
                                                <tr>
                                                    <td>
                                                        @if (!empty($item['slug']))
                                                            <a href="{{ route('frontend.products.show', $item['slug']) }}" target="_blank" rel="noopener noreferrer">
                                                                {{ $item['name'] ?? '—' }}
                                                            </a>
                                                        @else
                                                            {{ $item['name'] ?? '—' }}
                                                        @endif
                                                    </td>
                                                    <td>{{ $item['model'] ?? '—' }}</td>
                                                    <td>{{ (int) ($item['quantity'] ?? 1) }}</td>
                                                    <td>
                                                        @php
                                                            $options = is_array($item['options'] ?? null) ? $item['options'] : [];
                                                        @endphp
                                                        @if (count($options) > 0)
                                                            <ul class="mb-0 ps-3">
                                                                @foreach ($options as $optionName => $optionValue)
                                                                    <li>{{ $optionName }}: {{ $optionValue }}</li>
                                                                @endforeach
                                                            </ul>
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="mb-0 text-muted">—</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('dashboard.product_inquiry_requests.reply') }}</h5>
                        <p class="text-muted small mb-3">
                            {{ __('dashboard.product_inquiry_requests.reply_to') }}:
                            <strong>{{ $inquiry->email }}</strong>
                        </p>
                        <form method="post" action="{{ route('super_admin.product_inquiry_requests-reply', $inquiry->id) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label" for="reply-subject">{{ __('dashboard.product_inquiry_requests.reply_subject') }}</label>
                                <input type="text" name="subject" id="reply-subject" class="form-control @error('subject') is-invalid @enderror"
                                    value="{{ old('subject') }}" maxlength="255" required>
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="reply-body">{{ __('dashboard.product_inquiry_requests.reply_body') }}</label>
                                <textarea name="body" id="reply-body" rows="6" class="form-control @error('body') is-invalid @enderror" required>{{ old('body') }}</textarea>
                                @error('body')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">{{ __('dashboard.product_inquiry_requests.reply') }}</button>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('dashboard.product_inquiry_requests.reply_history') }}</h5>
                        @if ($outboundMessages->count() > 0)
                            @foreach ($outboundMessages as $message)
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start gap-2">
                                        <strong>{{ $message->subject }}</strong>
                                        @php
                                            $outboundStatusKey = match ($message->status) {
                                                'sent' => 'outbound_status_sent',
                                                'failed' => 'outbound_status_failed',
                                                default => 'outbound_status_queued',
                                            };
                                        @endphp
                                        <span class="badge @if ($message->status === 'sent') bg-success @elseif ($message->status === 'failed') bg-danger @else bg-secondary @endif">
                                            {{ __('dashboard.product_inquiry_requests.'.$outboundStatusKey) }}
                                        </span>
                                    </div>
                                    <p class="small text-muted mb-2">{{ $message->recipient_email }}</p>
                                    <pre class="small bg-light p-2 rounded mb-2" style="white-space: pre-wrap;">{{ $message->body }}</pre>
                                    @if ($message->status === 'sent' && $message->sent_at)
                                        <p class="small mb-0"><strong>{{ __('dashboard.product_inquiry_requests.sent_at') }}:</strong> {{ $message->sent_at->format('Y-m-d h:i A') }}</p>
                                    @endif
                                    @if ($message->status === 'failed')
                                        @if ($message->failed_at)
                                            <p class="small mb-1"><strong>{{ __('dashboard.product_inquiry_requests.failed_at') }}:</strong> {{ $message->failed_at->format('Y-m-d h:i A') }}</p>
                                        @endif
                                        @if ($message->error_message)
                                            <p class="small text-danger mb-2"><strong>{{ __('dashboard.product_inquiry_requests.send_error') }}:</strong> {{ $message->error_message }}</p>
                                        @endif
                                        <form method="post" action="{{ route('super_admin.product_inquiry_requests-retry_outbound', ['inquiryId' => $inquiry->id, 'outboundId' => $message->id]) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('dashboard.product_inquiry_requests.retry_send') }}</button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted mb-0">—</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
