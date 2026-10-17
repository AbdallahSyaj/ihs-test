<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\ContactUs\UpdateContactUsFormRequest;
use App\Models\ContactUs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactUsBackendController extends Controller
{
    // ========================================================================
    // =========================== index Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function index(Request $request)
    {
        $contactUs = ContactUs::first();
        return view('admin.contact_us.index', compact('contactUs'));
    }

    // ========================================================================
    // ============================ Edit Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function edit($id)
    {
        $contactUs = ContactUs::first();
        if ($contactUs) {
            return view('admin.contact_us.edit', compact('contactUs'));
        } else {
            return redirect()->route('super_admin.contact_us-index')->with('danger', __('dashboard.shared.record_not_found'));
        }
    }

    // ========================================================================
    // =========================== Update Function ============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function update($id, UpdateContactUsFormRequest $request)
    {
        $contactUs = ContactUs::first();
        if ($contactUs) {
            // Prepare Data :
            $validated = $request->validated();
            $hours = $validated['hours'] ?? null;
            $pageTitleEn = $validated['page_title_en'] ?? null;
            $pageIntroEn = $validated['page_intro_en'] ?? null;

            $updated_data = [
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address_ar' => $validated['address_ar'],
                'address_en' => $validated['address_en'],
                'whatsapp' => $validated['whatsapp'],
                'facebook' => $validated['facebook'] ?? null,
                'twitter' => $validated['twitter'] ?? null,
                'instagram' => $validated['instagram'] ?? null,
                'linkedin' => $validated['linkedin'] ?? null,
                'youtube' => $validated['youtube'] ?? null,
                'snapchat' => $validated['snapchat'] ?? null,
                'tiktok' => $validated['tiktok'] ?? null,
                'telegram' => $validated['telegram'] ?? null,
                'hours' => blank($hours) ? null : $hours,
                'page_title_en' => blank($pageTitleEn) ? null : $pageTitleEn,
                'page_intro_en' => blank($pageIntroEn) ? null : $pageIntroEn,
            ];

            // Update in DB :
            DB::transaction(function () use ($updated_data, $contactUs) {
                $contactUs->update($updated_data);
            });

            return redirect()->route('super_admin.contact_us-index')->with('success', __('dashboard.shared.updated_successfully'));
        } else {
            return redirect()->route('super_admin.contact_us-index')->with('danger', __('dashboard.shared.record_not_found'));
        }
    }
}
