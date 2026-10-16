<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\PrivacyPolicy\StorePrivacyPolicyFormRequest;
use App\Http\Requests\Backend\PrivacyPolicy\UpdatePrivacyPolicyFormRequest;
use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrivacyPolicyBackendController extends Controller
{
    // ========================================================================
    // =========================== index Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function index(Request $request)
    {
        $privacyPolices = PrivacyPolicy::orderBy('created_at', 'desc')->get();
        return view('admin.privacy_polices.index', compact('privacyPolices'));
    }

    // ========================================================================
    // =========================== Create Function ============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function create()
    {
        // get the next autoincrement id :
        $statement = DB::select("SHOW TABLE STATUS LIKE 'privacy_policies'");
        $nextId = $statement[0]->Auto_increment;
        return view('admin.privacy_polices.create', compact('nextId'));
    }

    // ========================================================================
    // =========================== Store Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function store(StorePrivacyPolicyFormRequest $request)
    {
        // Prepare Data :
        $created_data = [
            'title_ar' => $request->title_ar,
            'title_en' => $request->title_en,
            'status' => $request->status,
            'description_ar' => $request->description_ar,
            'description_en' => $request->description_en,
        ];

        // Store in DB :
        DB::transaction(function () use ($created_data) {
            PrivacyPolicy::create($created_data);
        });

        return redirect()->route('super_admin.privacy_policies-index')->with('success', __('dashboard.shared.added_successfully'));
    }

    // ========================================================================
    // ============================ Show Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function show($id)
    {
        $privacyPolicy = PrivacyPolicy::find($id);
        if ($privacyPolicy) {
            return view('admin.privacy_polices.show', compact('privacyPolicy'));
        } else {
            return redirect()->route('super_admin.privacy_policies-index')->with('danger', __('dashboard.shared.record_not_found'));
        }
    }

    // ========================================================================
    // ============================ Edit Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function edit($id)
    {
        $privacyPolicy = PrivacyPolicy::find($id);
        if ($privacyPolicy) {
            return view('admin.privacy_polices.edit', compact('privacyPolicy'));
        } else {
            return redirect()->route('super_admin.privacy_policies-index')->with('danger', __('dashboard.shared.record_not_found'));
        }
    }

    // ========================================================================
    // =========================== Update Function ============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function update($id, UpdatePrivacyPolicyFormRequest $request)
    {
        $privacyPolicy = PrivacyPolicy::find($id);
        if ($privacyPolicy) {
            // Prepare Data :
            $updated_data = [
                'title_ar' => $request->title_ar,
                'title_en' => $request->title_en,
                'status' => $request->status,
                'description_ar' => $request->description_ar,
                'description_en' => $request->description_en,
            ];

            // Update in DB :
            DB::transaction(function () use ($updated_data, $privacyPolicy) {
                $privacyPolicy->update($updated_data);
            });

            return redirect()->route('super_admin.privacy_policies-index')->with('success', __('dashboard.shared.updated_successfully'));
        } else {
            return redirect()->route('super_admin.privacy_policies-index')->with('danger', __('dashboard.shared.record_not_found'));
        }
    }

    // ========================================================================
    // ======================== Soft Delete Function ==========================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function softDelete($id)
    {
        $privacyPolicy = PrivacyPolicy::find($id);
        if ($privacyPolicy) {
            DB::transaction(function () use ($privacyPolicy) {
                $privacyPolicy->delete();
            });
            return redirect()->route('super_admin.privacy_policies-index')->with('success', __('dashboard.shared.deleted_successfully'));
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
        $privacyPolices = new PrivacyPolicy();
        $privacyPolices = $privacyPolices->onlyTrashed()->select('*')->orderBy('created_at', 'asc')->get();
        return view('admin.privacy_polices.trashed', compact('privacyPolices'));
    }

    // ========================================================================
    // ==================== Soft Delete Restore Function ======================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function softDeleteRestore($id)
    {
        $privacyPolicy = PrivacyPolicy::onlyTrashed()->find($id);
        if ($privacyPolicy) {
            DB::transaction(function () use ($privacyPolicy) {
                $privacyPolicy->restore();
            });
            return redirect()->route('super_admin.privacy_policies-showSoftDelete')->with('success', __('dashboard.shared.restored_successfully'));
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
        $privacyPolicy = PrivacyPolicy::find($id);
        if ($privacyPolicy) {
            if ($privacyPolicy->status == 'Active') {
                $privacyPolicy->status = 2;  // 2 => Inactive
            } elseif ($privacyPolicy->status == 'Inactive') {
                $privacyPolicy->status = 1;  // 1 => Active
            }
            $privacyPolicy->save();
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
        $query = $request->selectedPrivaciesPolices;

        if ($query) {
            // Split the query into an array using the comma ","
            $selectedPrivaciesPolices = explode(',', $query);
            $privacyPolices = PrivacyPolicy::whereIn('id', $selectedPrivaciesPolices)->get();
            if (isset($privacyPolices) &&  $privacyPolices->count() > 0) {
                DB::transaction(function () use ($selectedPrivaciesPolices) {
                    PrivacyPolicy::whereIn('id', $selectedPrivaciesPolices)->delete();
                });
                return redirect()->route('super_admin.privacy_policies-index')->with('success', __('dashboard.shared.deleted_successfully'));
            } else {
                return redirect()->route('super_admin.privacy_policies-index')->with('danger', __('dashboard.shared.select_at_least_one'));
            }
        } else {
            return redirect()->route('super_admin.privacy_policies-index')->with('danger', __('dashboard.shared.select_at_least_one'));
        }
    }

    // ========================================================================
    // ======================== Soft Delete Restore Selected Function =========
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function softDeleteRestoreSelected(Request $request)
    {
        $query = $request->selectedPrivaciesPolices;
        if ($query) {
            $selectedPrivaciesPolices = explode(',', $query);
            $privacyPolices = PrivacyPolicy::onlyTrashed()->whereIn('id', $selectedPrivaciesPolices)->get();
            if ($privacyPolices) {
                DB::transaction(function () use ($selectedPrivaciesPolices) {
                    PrivacyPolicy::onlyTrashed()->whereIn('id', $selectedPrivaciesPolices)->restore();
                });
                return redirect()->route('super_admin.privacy_policies-index')->with('success',  __('dashboard.shared.process_successful'));
            } else {
                return redirect()->route('super_admin.privacy_policies-index')->with('danger', __('dashboard.shared.select_at_least_one'));
            }
        } else {
            return redirect()->route('super_admin.privacy_policies-index')->with('danger', __('dashboard.shared.select_at_least_one'));
        }
    }

    // ========================================================================
    // ======================== Active Selected Function ======================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function activeSelected(Request $request)
    {
        $query = $request->selectedPrivaciesPolices;

        if ($query) {
            $selectedPrivaciesPolices = explode(',', $query);
            $privacyPolices = PrivacyPolicy::whereIn('id', $selectedPrivaciesPolices)->get();
            if (isset($privacyPolices) &&  $privacyPolices->count() > 0) {
                DB::transaction(function () use ($selectedPrivaciesPolices) {
                    PrivacyPolicy::whereIn('id', $selectedPrivaciesPolices)->update(['status' => '1']); // 1 => Active
                });
                return redirect()->route('super_admin.privacy_policies-index')->with('success', __('dashboard.shared.process_successful'));
            } else {
                return redirect()->route('super_admin.privacy_policies-index')->with('danger', __('dashboard.shared.select_at_least_one'));
            }
        } else {
            return redirect()->route('super_admin.privacy_policies-index')->with('danger', __('dashboard.shared.select_at_least_one'));
        }
    }

    // ========================================================================
    // ======================== Inactive Selected Function ====================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function inactiveSelected(Request $request)
    {
        $query = $request->selectedPrivaciesPolices;

        if ($query) {
            $selectedPrivaciesPolices = explode(',', $query);
            $privacyPolices = PrivacyPolicy::whereIn('id', $selectedPrivaciesPolices)->get();
            if (isset($privacyPolices) &&  $privacyPolices->count() > 0) {
                DB::transaction(function () use ($selectedPrivaciesPolices) {
                    PrivacyPolicy::whereIn('id',  $selectedPrivaciesPolices)->update(['status' => 2]);
                });
                return redirect()->route('super_admin.privacy_policies-index')->with('success', __('dashboard.shared.process_successful'));
            } else {
                return redirect()->route('super_admin.privacy_policies-index')->with('danger', __('dashboard.shared.select_at_least_one'));
            }
        } else {
            return redirect()->route('super_admin.privacy_policies-index')->with('danger', __('dashboard.shared.select_at_least_one'));
        }
    }
}
