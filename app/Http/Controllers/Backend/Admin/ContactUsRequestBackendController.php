<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactUsRequest;
use Illuminate\Http\Request;

class ContactUsRequestBackendController extends Controller
{
    // ========================================================================
    // =========================== index Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function index(Request $request)
    {
        $contactUsFormRequests = ContactUsRequest::paginate(10);
        return view('admin.contact_us_form_requests.index', compact('contactUsFormRequests'));
    }

    // ========================================================================
    // ======================= destroyMessage Function ========================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function destroyMessage($id)
    {
        $contactUsFormRequests = ContactUsRequest::find($id);
        if ($contactUsFormRequests) {
            $contactUsFormRequests->delete();
            return redirect()->route('super_admin.contact_us_requests-index')->with('success', __('dashboard.shared.deleted_successfully'));
        } else {
            return redirect()->route('super_admin.contact_us_requests-index')->with('danger', __('dashboard.shared.record_not_found'));
        }
    }
}
