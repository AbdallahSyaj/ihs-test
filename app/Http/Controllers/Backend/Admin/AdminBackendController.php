<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Admin\StoreAdminFormRequest;
use App\Http\Requests\Backend\Admin\UpdateAdminFormRequest;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Traits\UploadImageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminBackendController extends Controller
{
    use UploadImageTrait;
    // ========================================================================
    // =========================== index Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function index(Request $request)
    {
        $admins = Admin::orderBy('created_at', 'desc')->paginate(12);
        $activeAdminsCount = Admin::where('status', 1)->count();
        return view('admin.admins.index', compact('admins', 'activeAdminsCount'));
    }

    // ========================================================================
    // =========================== Create Function ============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function create()
    {
        // get the next autoincrement id :
        $statement = DB::select("SHOW TABLE STATUS LIKE 'admins'");
        $nextId = $statement[0]->Auto_increment;

        return view('admin.admins.create', compact('nextId'));
    }

    // ========================================================================
    // =========================== Store Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function store(StoreAdminFormRequest $request)
    {
        // Prepare Data :
        $created_data = [
            'name' => $request->name,
            'status' => $request->status,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),

        ];

        // Store in DB :
        DB::transaction(function () use ($created_data) {
            Admin::create($created_data);
        });

        return redirect()->route('super_admin.admins-index')->with('success', __('dashboard.shared.added_successfully'));
    }

    // ========================================================================
    // ============================ Show Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function show($id)
    {
        $admin = Admin::find($id);
        if ($admin) {
            return view('admin.admins.show', compact('admin'));
        } else {
            return redirect()->route('super_admin.admins-index')->with('danger', __('dashboard.shared.record_not_found'));
        }
    }

    // ========================================================================
    // ============================ Edit Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function edit($id)
    {
        $admin = Admin::find($id);
        if ($admin) {
            return view('admin.admins.edit', compact('admin'));
        } else {
            return redirect()->route('super_admin.admins-index')->with('danger', __('dashboard.shared.record_not_found'));
        }
    }

    // ========================================================================
    // =========================== Update Function ============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function update($id, UpdateAdminFormRequest $request)
    {
        $admin = Admin::find($id);
        if ($admin) {
            if ($admin->status == 'Active' && $request->status != 1 && Admin::where('status', 1)->where('id', '!=', $id)->count() === 0) {
                return redirect()->back()->withInput()->with('danger', __('dashboard.admins.cannot_deactivate_last_active'));
            }

            // Prepare Data :
            $updated_data = [
                'name' => $request->name,
                'status' => $request->status,
                'email' => $request->email,
                'phone' => $request->phone,
            ];

            // password change
            if ($request->filled('password')) {
                $updated_data['password'] = Hash::make($request->password);
            }

            // Update in DB :
            DB::transaction(function () use ($updated_data, $admin) {
                $admin->update($updated_data);
            });

            return redirect()->route('super_admin.admins-index')->with('success', __('dashboard.shared.updated_successfully'));
        } else {
            return redirect()->route('super_admin.admins-index')->with('danger', __('dashboard.shared.record_not_found'));
        }
    }

    // ========================================================================
    // ======================== Soft Delete Function ==========================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function softDelete($id)
    {
        $admin = Admin::find($id);
        if ($admin) {
            if ($admin->status === 'Active' && Admin::where('status', 1)->where('id', '!=', $id)->count() === 0) {
                return redirect()->back()->with('danger', __('dashboard.admins.cannot_archive_last_active'));
            }

            DB::transaction(function () use ($admin) {
                $admin->delete();
            });
            return redirect()->route('super_admin.admins-index')->with('success', __('dashboard.shared.deleted_successfully'));
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
        $admins = new Admin();
        $admins = $admins->onlyTrashed()->select('*')->orderBy('created_at', 'asc')->get();
        return view('admin.admins.trashed', compact('admins'));
    }

    // ========================================================================
    // ==================== Soft Delete Restore Function ======================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function softDeleteRestore($id)
    {
        $admin = Admin::onlyTrashed()->find($id);
        if ($admin) {
            DB::transaction(function () use ($admin) {
                $admin->restore();
            });
            return redirect()->route('super_admin.admins-showSoftDelete')->with('success', __('dashboard.shared.restored_successfully'));
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
        $admin = Admin::find($id);
        if ($admin) {
            if ($admin->status == 'Active') {
                if (Admin::where('status', 1)->where('id', '!=', $id)->count() === 0) {
                    return redirect()->back()->with('danger', __('dashboard.admins.cannot_deactivate_last_active'));
                }
                $admin->status = 2;  // 2 => Inactive
            } elseif ($admin->status == 'Inactive') {
                $admin->status = 1;  // 1 => Active
            }
            $admin->save();
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
        $query = $request->selectedAdmins;

        if ($query) {
            // Split the query into an array using the comma ","
            $selectedAdmins = explode(',', $query);
            $admins = Admin::whereIn('id', $selectedAdmins)->get();
            if (isset($admins) && $admins->count() > 0) {
                if (Admin::where('status', 1)->whereNotIn('id', $selectedAdmins)->count() === 0) {
                    return redirect()->route('super_admin.admins-index')->with('danger', __('dashboard.admins.cannot_archive_last_active'));
                }

                DB::transaction(function () use ($selectedAdmins) {
                    Admin::whereIn('id', $selectedAdmins)->delete();
                });
                return redirect()->route('super_admin.admins-index')->with('success', __('dashboard.shared.deleted_successfully'));
            } else {
                return redirect()->route('super_admin.admins-index')->with('danger', __('dashboard.shared.select_at_least_one'));
            }
        } else {
            return redirect()->route('super_admin.admins-index')->with('danger', __('dashboard.shared.select_at_least_one'));
        }
    }

    // ========================================================================
    // ======================== Soft Delete Restore Selected Function =========
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function softDeleteRestoreSelected(Request $request)
    {
        $query = $request->selectedAdmins;
        if ($query) {
            // Split the query into an array using the comma ","
            $selectedAdmins = explode(',', $query);
            $admins = Admin::onlyTrashed()->whereIn('id', $selectedAdmins)->get();
            if ($admins) {
                DB::transaction(function () use ($selectedAdmins) {
                    Admin::onlyTrashed()->whereIn('id', $selectedAdmins)->restore();
                });
                return redirect()->route('super_admin.admins-index')->with('success', __('dashboard.shared.process_successful'));
            } else {
                return redirect()->route('super_admin.admins-index')->with('danger', __('dashboard.shared.select_at_least_one'));
            }
        } else {
            return redirect()->route('super_admin.admins-index')->with('danger', __('dashboard.shared.select_at_least_one'));
        }
    }

    // ========================================================================
    // ======================== Active Selected Function ======================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function activeSelected(Request $request)
    {
        $query = $request->selectedAdmins;

        if ($query) {
            // Split the query into an array using the comma ","
            $selectedAdmins = explode(',', $query);
            $admins = Admin::whereIn('id', $selectedAdmins)->get();
            if (isset($admins) && $admins->count() > 0) {
                DB::transaction(function () use ($selectedAdmins) {
                    Admin::whereIn('id', $selectedAdmins)->update(['status' => '1']); // 1 => Active
                });
                return redirect()->route('super_admin.admins-index')->with('success', __('dashboard.shared.process_successful'));
            } else {
                return redirect()->route('super_admin.admins-index')->with('danger', __('dashboard.shared.select_at_least_one'));
            }
        } else {
            return redirect()->route('super_admin.admins-index')->with('danger', __('dashboard.shared.select_at_least_one'));
        }
    }

    // ========================================================================
    // ======================== Inactive Selected Function ====================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function inactiveSelected(Request $request)
    {
        $query = $request->selectedAdmins;

        if ($query) {
            // Split the query into an array using the comma ","
            $selectedAdmins = explode(',', $query);
            $admins = Admin::whereIn('id', $selectedAdmins)->get();
            if (isset($admins) && $admins->count() > 0) {
                if (Admin::where('status', 1)->whereNotIn('id', $selectedAdmins)->count() === 0) {
                    return redirect()->route('super_admin.admins-index')->with('danger', __('dashboard.admins.cannot_deactivate_last_active'));
                }

                DB::transaction(function () use ($selectedAdmins) {
                    Admin::whereIn('id', $selectedAdmins)->update(['status' => 2]); // 1 => Inactive
                });
                return redirect()->route('super_admin.admins-index')->with('success', __('dashboard.shared.process_successful'));
            } else {
                return redirect()->route('super_admin.admins-index')->with('danger', __('dashboard.shared.select_at_least_one'));
            }
        } else {
            return redirect()->route('super_admin.admins-index')->with('danger', __('dashboard.shared.select_at_least_one'));
        }
    }
}
