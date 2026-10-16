<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\AboutUs\UpdateAboutUsFormRequest;
use App\Models\AboutUs;
use Illuminate\Http\Request;
use App\Traits\UploadImageTrait;
use Illuminate\Support\Facades\DB;

class AboutUsBackendController extends Controller
{
    use UploadImageTrait;
    // ========================================================================
    // =========================== index Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function index(Request $request)
    {
        $aboutUs = AboutUs::first();
        return view('admin.about_us.index', compact('aboutUs'));
    }

    // ========================================================================
    // ============================ Edit Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function edit($id)
    {
        $aboutUs = AboutUs::first();
        if ($aboutUs) {
            return view('admin.about_us.edit', compact('aboutUs'));
        } else {
            return redirect()->route('super_admin.about_us-index')->with('danger', __('dashboard.shared.record_not_found'));
        }
    }

    // ========================================================================
    // =========================== Update Function ============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function update($id, UpdateAboutUsFormRequest $request)
    {
        $aboutUs = AboutUs::first();
        if ($aboutUs) {
            // Prepare Data :
            $updated_data = [
                'title_ar' => $request->title_ar,
                'title_en' => $request->title_en,
                'description_ar' => $request->description_ar,
                'description_en' => $request->description_en,
            ];

            // Upload Image Section :

            // image
            if (isset($request->image)) {
                $orginal_image = $request->file('image');
                $upload_location = 'images/AboutUs/';
                $last_image = $this->saveFile($orginal_image, $upload_location, 1400, 0);
                $updated_data['image'] = $last_image;
            }

            // Update in DB :
            DB::transaction(function () use ($updated_data, $aboutUs) {
                $aboutUs->update($updated_data);
            });

            return redirect()->route('super_admin.about_us-index')->with('success', __('dashboard.shared.updated_successfully'));
        } else {
            return redirect()->route('super_admin.about_us-index')->with('danger', __('dashboard.shared.record_not_found'));
        }
    }
}
