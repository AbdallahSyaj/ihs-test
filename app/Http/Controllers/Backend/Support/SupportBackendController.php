<?php

namespace App\Http\Controllers\Backend\Support;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportBackendController extends Controller
{

    // ================================================================
    // ======================== index Function ========================
    // ================================================================
    public function index(Request $request)
    {
        $rows = new SupportTicket();
        $rows = $rows->select('*')->orderBy('id', 'asc')->get();
        return view('admin.support_tickets.index', compact('rows'));
    }

    // ================================================================
    // ======================= destroy Function =======================
    // ================================================================
    public function destroy($id)
    {
        $ticket = SupportTicket::find($id);
        if ($ticket) {
            $ticket->delete();
            return redirect()->route('super_admin.support_tickets-index')->with('success', __('dashboard.shared.deleted_successfully'));
        } else {
            return redirect()->route('super_admin.support_tickets-index')->with('danger', __('dashboard.shared.record_not_found'));
        }
    }
}
