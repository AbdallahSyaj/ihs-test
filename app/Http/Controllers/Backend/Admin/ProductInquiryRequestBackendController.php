<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\ProductInquiry\StoreProductInquiryReplyRequest;
use App\Jobs\SendProductInquiryReplyJob;
use App\Models\MailOutboundMessage;
use App\Models\ProductInquiryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductInquiryRequestBackendController extends Controller
{
    public function index(Request $request): View
    {
        $inquiries = ProductInquiryRequest::query()
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.product_inquiry_requests.index', compact('inquiries'));
    }

    public function show(int $id): View
    {
        $inquiry = ProductInquiryRequest::query()->findOrFail($id);
        $inquiry->markAsRead();

        $outboundMessages = MailOutboundMessage::query()
            ->where('source_type', MailOutboundMessage::SOURCE_PRODUCT_INQUIRY)
            ->where('source_id', $inquiry->id)
            ->orderByDesc('created_at')
            ->get();

        return view('admin.product_inquiry_requests.show', compact('inquiry', 'outboundMessages'));
    }

    public function storeReply(StoreProductInquiryReplyRequest $request, int $id): RedirectResponse
    {
        $inquiry = ProductInquiryRequest::query()->findOrFail($id);
        $admin = auth('super_admin')->user();

        $data = $request->validated();

        $outbound = MailOutboundMessage::query()->create([
            'admin_id' => $admin->id,
            'source_type' => MailOutboundMessage::SOURCE_PRODUCT_INQUIRY,
            'source_id' => $inquiry->id,
            'recipient_email' => $inquiry->email,
            'subject' => $data['subject'],
            'body' => $data['body'],
            'status' => MailOutboundMessage::STATUS_QUEUED,
        ]);

        SendProductInquiryReplyJob::dispatch($outbound->id);

        $outbound->refresh();

        if ($outbound->status === MailOutboundMessage::STATUS_FAILED) {
            return redirect()
                ->route('super_admin.product_inquiry_requests-show', $inquiry->id)
                ->withInput()
                ->with('danger', __('dashboard.product_inquiry_requests.reply_send_failed'));
        }

        if ($outbound->status === MailOutboundMessage::STATUS_SENT) {
            return redirect()
                ->route('super_admin.product_inquiry_requests-show', $inquiry->id)
                ->with('success', __('dashboard.product_inquiry_requests.reply_sent'));
        }

        return redirect()
            ->route('super_admin.product_inquiry_requests-show', $inquiry->id)
            ->with('success', __('dashboard.product_inquiry_requests.reply_queued'));
    }

    public function retryOutbound(int $inquiryId, int $outboundId): RedirectResponse
    {
        $inquiry = ProductInquiryRequest::query()->findOrFail($inquiryId);

        $outbound = MailOutboundMessage::query()
            ->whereKey($outboundId)
            ->where('source_type', MailOutboundMessage::SOURCE_PRODUCT_INQUIRY)
            ->where('source_id', $inquiry->id)
            ->firstOrFail();

        if (! $outbound->isRetryable()) {
            return redirect()
                ->route('super_admin.product_inquiry_requests-show', $inquiry->id)
                ->with('danger', __('dashboard.product_inquiry_requests.retry_not_allowed'));
        }

        $outbound->forceFill([
            'status' => MailOutboundMessage::STATUS_QUEUED,
            'error_message' => null,
            'failed_at' => null,
        ])->save();

        SendProductInquiryReplyJob::dispatch($outbound->id);

        return redirect()
            ->route('super_admin.product_inquiry_requests-show', $inquiry->id)
            ->with('success', __('dashboard.product_inquiry_requests.reply_retry_queued'));
    }
}
