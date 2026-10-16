<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\TermsConditoins\StoreTermsConditoinsFormRequest;
use App\Http\Requests\Backend\TermsConditoins\UpdateTermsConditoinsFormRequest;
use App\Models\TermsAndCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TermsConditionBackendController extends Controller
{
    // ========================================================================
    // =========================== index Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function index(Request $request)
    {
        $termsConditions = TermsAndCondition::orderBy('created_at', 'desc')->get();
        return view('admin.terms.index', compact('termsConditions'));
    }

    // ========================================================================
    // =========================== Create Function ============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function create()
    {
        // get the next autoincrement id :
        $statement = DB::select("SHOW TABLE STATUS LIKE 'terms_and_conditions'");
        $nextId = $statement[0]->Auto_increment;
        return view('admin.terms.create', compact('nextId'));
    }

    // ========================================================================
    // =========================== Store Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function store(StoreTermsConditoinsFormRequest $request)
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
            TermsAndCondition::create($created_data);
        });

        return redirect()->route('super_admin.terms_and_conditions-index')->with('success', __('dashboard.shared.added_successfully'));
    }

    // ========================================================================
    // ============================ Show Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function show($id)
    {
        $termCondition = TermsAndCondition::find($id);
        if ($termCondition) {
            return view('admin.terms.show', compact('termCondition'));
        } else {
            return redirect()->route('super_admin.terms_and_conditions-index')->with('danger', __('dashboard.shared.record_not_found'));
        }
    }

    // ========================================================================
    // ============================ Edit Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function edit($id)
    {
        $termCondition = TermsAndCondition::find($id);
        if ($termCondition) {
            return view('admin.terms.edit', compact('termCondition'));
        } else {
            return redirect()->route('super_admin.terms_and_conditions-index')->with('danger', __('dashboard.shared.record_not_found'));
        }
    }

    // ========================================================================
    // =========================== Update Function ============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function update($id, UpdateTermsConditoinsFormRequest $request)
    {
        $termCondition = TermsAndCondition::find($id);
        if ($termCondition) {
            // Prepare Data :
            $updated_data = [
                'title_ar' => $request->title_ar,
                'title_en' => $request->title_en,
                'status' => $request->status,
                'description_ar' => $request->description_ar,
                'description_en' => $request->description_en,
            ];

            // Update in DB :
            DB::transaction(function () use ($updated_data, $termCondition) {
                $termCondition->update($updated_data);
            });

            return redirect()->route('super_admin.terms_and_conditions-index')->with('success', __('dashboard.shared.updated_successfully'));
        } else {
            return redirect()->route('super_admin.terms_and_conditions-index')->with('danger', __('dashboard.shared.record_not_found'));
        }
    }

    // ========================================================================
    // ======================== Soft Delete Function ==========================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function softDelete($id)
    {
        $termCondition = TermsAndCondition::find($id);
        if ($termCondition) {
            DB::transaction(function () use ($termCondition) {
                $termCondition->delete();
            });
            return redirect()->route('super_admin.terms_and_conditions-index')->with('success', __('dashboard.shared.deleted_successfully'));
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
        $termConditions = new TermsAndCondition();
        $termConditions = $termConditions->onlyTrashed()->select('*')->orderBy('created_at', 'asc')->get();
        return view('admin.terms.trashed', compact('termConditions'));
    }

    // ========================================================================
    // ==================== Soft Delete Restore Function ======================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function softDeleteRestore($id)
    {
        $termCondition = TermsAndCondition::onlyTrashed()->find($id);
        if ($termCondition) {
            DB::transaction(function () use ($termCondition) {
                $termCondition->restore();
            });
            return redirect()->route('super_admin.terms_and_conditions-showSoftDelete')->with('success', __('dashboard.shared.restored_successfully'));
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
        $termCondition = TermsAndCondition::find($id);
        if ($termCondition) {
            if ($termCondition->status == 'Active') {
                $termCondition->status = 2;  // 2 => Inactive
            } elseif ($termCondition->status == 'Inactive') {
                $termCondition->status = 1;  // 1 => Active
            }
            $termCondition->save();
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
        $query = $request->selectedTerms;

        if ($query) {
            // Split the query into an array using the comma ","
            $selectedTerms = explode(',', $query);
            $termConditions = TermsAndCondition::whereIn('id', $selectedTerms)->get();
            if (isset($termConditions) && $termConditions->count() > 0) {
                DB::transaction(function () use ($selectedTerms) {
                    TermsAndCondition::whereIn('id', $selectedTerms)->delete();
                });
                return redirect()->route('super_admin.terms_and_conditions-index')->with('success', __('dashboard.shared.deleted_successfully'));
            } else {
                return redirect()->route('super_admin.terms_and_conditions-index')->with('danger', __('dashboard.shared.select_at_least_one'));
            }
        } else {
            return redirect()->route('super_admin.terms_and_conditions-index')->with('danger', __('dashboard.shared.select_at_least_one'));
        }
    }

    // ========================================================================
    // ======================== Soft Delete Restore Selected Function =========
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function softDeleteRestoreSelected(Request $request)
    {
        $query = $request->selectedTerms;
        if ($query) {
            $selectedTerms = explode(',', $query);
            $termConditions = TermsAndCondition::onlyTrashed()->whereIn('id', $selectedTerms)->get();
            if ($termConditions) {
                DB::transaction(function () use ($selectedTerms) {
                    TermsAndCondition::onlyTrashed()->whereIn('id', $selectedTerms)->restore();
                });
                return redirect()->route('super_admin.terms_and_conditions-index')->with('success', __('dashboard.shared.process_successful'));
            } else {
                return redirect()->route('super_admin.terms_and_conditions-index')->with('danger', __('dashboard.shared.select_at_least_one'));
            }
        } else {
            return redirect()->route('super_admin.terms_and_conditions-index')->with('danger', __('dashboard.shared.select_at_least_one'));
        }
    }

    // ========================================================================
    // ======================== Active Selected Function ======================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function activeSelected(Request $request)
    {
        $query = $request->selectedTerms;

        if ($query) {
            $selectedTerms = explode(',', string: $query);
            $termConditions = TermsAndCondition::whereIn('id', $selectedTerms)->get();
            if (isset($termConditions) && $termConditions->count() > 0) {
                DB::transaction(function () use ($selectedTerms) {
                    TermsAndCondition::whereIn('id', $selectedTerms)->update(['status' => '1']); // 1 => Active
                });
                return redirect()->route('super_admin.terms_and_conditions-index')->with('success', __('dashboard.shared.process_successful'));
            } else {
                return redirect()->route('super_admin.terms_and_conditions-index')->with('danger', __('dashboard.shared.select_at_least_one'));
            }
        } else {
            return redirect()->route('super_admin.terms_and_conditions-index')->with('danger', __('dashboard.shared.select_at_least_one'));
        }
    }

    // ========================================================================
    // ======================== Inactive Selected Function ====================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function inactiveSelected(Request $request)
    {
        $query = $request->selectedTerms;

        if ($query) {
            $selectedTerms = explode(',', string: $query);
            $termConditions = TermsAndCondition::whereIn('id', $selectedTerms)->get();
            if (isset($termConditions) && $termConditions->count() > 0) {
                DB::transaction(function () use ($selectedTerms) {
                    TermsAndCondition::whereIn('id', $selectedTerms)->update(['status' => 2]);
                });
                return redirect()->route('super_admin.terms_and_conditions-index')->with('success', __('dashboard.shared.process_successful'));
            } else {
                return redirect()->route('super_admin.terms_and_conditions-index')->with('danger', __('dashboard.shared.select_at_least_one'));
            }
        } else {
            return redirect()->route('super_admin.terms_and_conditions-index')->with('danger', __('dashboard.shared.select_at_least_one'));
        }
    }
}
