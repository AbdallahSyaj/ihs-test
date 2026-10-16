<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Slider\StoreSliderFormRequest;
use App\Http\Requests\Backend\Slider\UpdateSliderFormRequest;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\UploadImageTrait;


class SliderBackendController extends Controller
{
    use UploadImageTrait;
    // ========================================================================
    // =========================== index Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function index(Request $request)
    {
        $sliders = Slider::orderBy('created_at', 'desc')->get();
        return view('admin.sliders.index', compact('sliders'));
    }

    // ========================================================================
    // =========================== Create Function ============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function create()
    {
        // get the next autoincrement id :
        $statement = DB::select("SHOW TABLE STATUS LIKE 'sliders'");
        $nextId = $statement[0]->Auto_increment;
        return view('admin.sliders.create', compact('nextId'));
    }

    // ========================================================================
    // =========================== Store Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function store(StoreSliderFormRequest $request)
    {
        // Prepare Data :
        $created_data = [
            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,
            'status' => $request->status,
            'description_ar' => $request->description_ar,
            'description_en' => $request->description_en,
            'type' => $request->type
        ];

        // Upload Image Section :
        if (isset($request->image)) {
            $orginal_image = $request->file('image');
            $upload_location = 'images/sliders/';
            $last_image = $this->saveFile($orginal_image, $upload_location);
            $created_data['image'] = $last_image;
        }

        // Upload video Section :
        if (isset($request->video)) {
            $orginal_image = $request->file('video');
            $upload_location = 'videos/sliders/';
            $last_image = $this->saveVideoFile($orginal_image, $upload_location);
            $created_data['video'] = $last_image;
        }

        // Store in DB :
        DB::transaction(function () use ($created_data) {
            Slider::create($created_data);
        });

        return redirect()->route('super_admin.sliders-index')->with('success', __('dashboard.shared.added_successfully'));
    }

    // ========================================================================
    // ============================ Show Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function show($id)
    {
        $slider = Slider::find($id);
        if ($slider) {
            return view('admin.sliders.show', compact('slider'));
        } else {
            return redirect()->route('super_admin.sliders-index')->with('danger', __('dashboard.shared.record_not_found'));
        }
    }

    // ========================================================================
    // ============================ Edit Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function edit($id)
    {
        $slider = Slider::find($id);
        if ($slider) {
            return view('admin.sliders.edit', compact('slider'));
        } else {
            return redirect()->route('super_admin.sliders-index')->with('danger', __('dashboard.shared.record_not_found'));
        }
    }

    // ========================================================================
    // =========================== Update Function ============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function update($id, UpdateSliderFormRequest $request)
    {
        $slider = Slider::find($id);
        if ($slider) {
            // Prepare Data :
            $updated_data = [
                'title_ar' => $request->title_ar,
                'title_en' => $request->title_en,
                'status' => $request->status,
                'description_ar' => $request->description_ar,
                'description_en' => $request->description_en,
                'type' => $request->type
            ];

            // Upload Image Section :
            if (isset($request->image)) {
                $orginal_image = $request->file('image');
                $upload_location = 'images/sliders/';
                $last_image = $this->saveFile($orginal_image, $upload_location);
                $updated_data['image'] = $last_image;
            }


            // Upload video Section :
            if (isset($request->video)) {
                $orginal_image = $request->file('video');
                $upload_location = 'videos/sliders/';
                $last_image = $this->saveVideoFile($orginal_image, $upload_location);
                $updated_data['video'] = $last_image;
            }

            // Update in DB :
            DB::transaction(function () use ($updated_data, $slider) {
                $slider->update($updated_data);
            });

            return redirect()->route('super_admin.sliders-index')->with('success', __('dashboard.shared.updated_successfully'));
        } else {
            return redirect()->route('super_admin.sliders-index')->with('danger', __('dashboard.shared.record_not_found'));
        }
    }

    // ========================================================================
    // ======================== Soft Delete Function ==========================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function softDelete($id)
    {
        $slider = Slider::find($id);
        if ($slider) {
            DB::transaction(function () use ($slider) {
                $slider->delete();
            });
            return redirect()->route('super_admin.sliders-index')->with('success', __('dashboard.shared.deleted_successfully'));
        } else {
            return redirect()->back()->with('danger', __('dashboard.shared.record_not_found'));
        }
    }

    // ========================================================================
    // ====================== Show Soft Delete Function =======================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function showSoftDelete(Request $request)
    {
        $sliders = new Slider();
        $sliders = $sliders->onlyTrashed()->select('*')->orderBy('created_at', 'asc')->get();
        return view('admin.sliders.trashed', compact('sliders'));
    }

    // ========================================================================
    // ==================== Soft Delete Restore Function ======================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function softDeleteRestore($id)
    {
        $slider = Slider::onlyTrashed()->find($id);
        if ($slider) {
            DB::transaction(function () use ($slider) {
                $slider->restore();
            });
            return redirect()->route('super_admin.sliders-showSoftDelete')->with('success', __('dashboard.shared.restored_successfully'));
        } else {
            return redirect()->back()->with('danger', __('dashboard.shared.record_not_found'));
        }
    }

    // ========================================================================
    // ====================== Active/Inactive Single ==========================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function activeInactiveSingle($id)
    {
        $slider = Slider::find($id);
        if ($slider) {
            if ($slider->status == 'Active') {
                $slider->status = 2;  // 2 => Inactive
            } elseif ($slider->status == 'Inactive') {
                $slider->status = 1;  // 1 => Active
            }
            $slider->save();
            return redirect()->back()->with('success', __('dashboard.shared.process_successful'));
        } else {
            return redirect()->back()->with('danger', __('dashboard.shared.record_not_found'));
        }
    }

    // ========================================================================
    // ======================== Soft Delete Selected Function =================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function softDeleteSelected(Request $request)
    {
        $query = $request->selectedSliders;

        if ($query) {
            // Split the query into an array using the comma ","
            $selectedSliders = explode(',', $query);
            $sliders = Slider::whereIn('id', $selectedSliders)->get();
            if (isset($sliders) &&  $sliders->count() > 0) {
                DB::transaction(function () use ($selectedSliders) {
                    Slider::whereIn('id', $selectedSliders)->delete();
                });
                return redirect()->route('super_admin.sliders-index')->with('success', __('dashboard.shared.deleted_successfully'));
            } else {
                return redirect()->route('super_admin.sliders-index')->with('danger', __('dashboard.shared.select_at_least_one'));
            }
        } else {
            return redirect()->route('super_admin.sliders-index')->with('danger', __('dashboard.shared.select_at_least_one'));
        }
    }

    // ========================================================================
    // ======================== Soft Delete Restore Selected Function =========
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function softDeleteRestoreSelected(Request $request)
    {
        $query = $request->selectedSliders;
        if ($query) {
            // Split the query into an array using the comma ","
            $selectedSliders = explode(',', $query);
            $sliders = Slider::onlyTrashed()->whereIn('id', $selectedSliders)->get();
            if ($sliders) {
                DB::transaction(function () use ($selectedSliders) {
                    Slider::onlyTrashed()->whereIn('id', $selectedSliders)->restore();
                });
                return redirect()->route('super_admin.sliders-index')->with('success', __('dashboard.shared.process_successful'));
            } else {
                return redirect()->route('super_admin.sliders-index')->with('danger', __('dashboard.shared.select_at_least_one'));
            }
        } else {
            return redirect()->route('super_admin.sliders-index')->with('danger', __('dashboard.shared.select_at_least_one'));
        }
    }

    // ========================================================================
    // ======================== Active Selected Function ======================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function activeSelected(Request $request)
    {
        $query = $request->selectedSliders;

        if ($query) {
            // Split the query into an array using the comma ","
            $selectedSliders = explode(',', $query);
            $sliders = Slider::whereIn('id', $selectedSliders)->get();
            if (isset($sliders) &&  $sliders->count() > 0) {
                DB::transaction(function () use ($selectedSliders) {
                    Slider::whereIn('id', $selectedSliders)->update(['status' => '1']); // 1 => Active
                });
                return redirect()->route('super_admin.sliders-index')->with('success', __('dashboard.shared.process_successful'));
            } else {
                return redirect()->route('super_admin.sliders-index')->with('danger', __('dashboard.shared.select_at_least_one'));
            }
        } else {
            return redirect()->route('super_admin.sliders-index')->with('danger', __('dashboard.shared.select_at_least_one'));
        }
    }

    // ========================================================================
    // ======================== Inactive Selected Function ====================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function inactiveSelected(Request $request)
    {
        $query = $request->selectedSliders;

        if ($query) {
            // Split the query into an array using the comma ","
            $selectedSliders = explode(',', $query);
            $sliders = Slider::whereIn('id', $selectedSliders)->get();
            if (isset($sliders) &&  $sliders->count() > 0) {
                DB::transaction(function () use ($selectedSliders) {
                    Slider::whereIn('id',  $selectedSliders)->update(['status' => 2]); // 1 => Inactive
                });
                return redirect()->route('super_admin.sliders-index')->with('success', __('dashboard.shared.process_successful'));
            } else {
                return redirect()->route('super_admin.sliders-index')->with('danger', __('dashboard.shared.select_at_least_one'));
            }
        } else {
            return redirect()->route('super_admin.sliders-index')->with('danger', __('dashboard.shared.select_at_least_one'));
        }
    }
}
