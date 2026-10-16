<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\User\StoreUserFormRequest;
use App\Http\Requests\Backend\User\UpdateUserFormRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\UploadImageTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserBackendController extends Controller
{
    use UploadImageTrait;
    // ========================================================================
    // =========================== index Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function index(Request $request)
    {
        $users = User::orderBy('created_at', 'desc')->paginate(12);
        return view('admin.users.index', compact('users'));
    }

    // ========================================================================
    // =========================== Create Function ============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function create()
    {
        // get the next autoincrement id :
        $statement = DB::select("SHOW TABLE STATUS LIKE 'users'");
        $nextId = $statement[0]->Auto_increment;

        return view('admin.users.create', compact('nextId'));
    }

    // ========================================================================
    // =========================== Store Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function store(StoreUserFormRequest $request)
    {
        // Prepare Data :
        $created_data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'status' => $request->status,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),

        ];

        // Store in DB :
        DB::transaction(function () use ($created_data) {
            User::create($created_data);
        });

        return redirect()->route('super_admin.users-index')->with('success', __('dashboard.shared.added_successfully'));
    }

    // ========================================================================
    // ============================ Show Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function show($id)
    {
        $user = User::find($id);
        if ($user) {
            return view('admin.users.show', compact('user'));
        } else {
            return redirect()->route('super_admin.users-index')->with('danger', __('dashboard.shared.record_not_found'));
        }
    }

    // ========================================================================
    // ============================ Edit Function =============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function edit($id)
    {
        $user = User::find($id);
        if ($user) {
            return view('admin.users.edit', compact('user'));
        } else {
            return redirect()->route('super_admin.users-index')->with('danger', __('dashboard.shared.record_not_found'));
        }
    }

    // ========================================================================
    // =========================== Update Function ============================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function update($id, UpdateUserFormRequest $request)
    {
        $user = User::find($id);
        if ($user) {
            // Prepare Data :
            $updated_data = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'status' => $request->status,
                'email' => $request->email,
                'phone' => $request->phone,
            ];

            // password change
            if ($request->filled('password')) {
                $updated_data['password'] = Hash::make($request->password);
            }

            // Update in DB :
            DB::transaction(function () use ($updated_data, $user) {
                $user->update($updated_data);
            });

            return redirect()->route('super_admin.users-index')->with('success', __('dashboard.shared.updated_successfully'));
        } else {
            return redirect()->route('super_admin.users-index')->with('danger', __('dashboard.shared.record_not_found'));
        }
    }

    // ========================================================================
    // ======================== Soft Delete Function ==========================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function softDelete($id)
    {
        $user = User::find($id);
        if ($user) {
            DB::transaction(function () use ($user) {
                $user->delete();
            });
            return redirect()->route('super_admin.users-index')->with('success', __('dashboard.shared.deleted_successfully'));
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
        $users = new User();
        $users = $users->onlyTrashed()->select('*')->orderBy('created_at', 'asc')->get();
        return view('admin.users.trashed', compact('users'));
    }

    // ========================================================================
    // ==================== Soft Delete Restore Function ======================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function softDeleteRestore($id)
    {
        $user = User::onlyTrashed()->find($id);
        if ($user) {
            DB::transaction(function () use ($user) {
                $user->restore();
            });
            return redirect()->route('super_admin.users-showSoftDelete')->with('success', __('dashboard.shared.restored_successfully'));
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
        $user = User::find($id);
        if ($user) {
            if ($user->status == 'Active') {
                $user->status = 2;  // 2 => Inactive
            } elseif ($user->status == 'Inactive') {
                $user->status = 1;  // 1 => Active
            }
            $user->save();
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
        $query = $request->selectedUsers;

        if ($query) {
            // Split the query into an array using the comma ","
            $selectedUsers = explode(',', $query);
            $users = User::whereIn('id', $selectedUsers)->get();
            if (isset($users) &&  $users->count() > 0) {
                DB::transaction(function () use ($selectedUsers) {
                    User::whereIn('id', $selectedUsers)->delete();
                });
                return redirect()->route('super_admin.users-index')->with('success', __('dashboard.shared.deleted_successfully'));
            } else {
                return redirect()->route('super_admin.users-index')->with('danger', __('dashboard.shared.select_at_least_one'));
            }
        } else {
            return redirect()->route('super_admin.users-index')->with('danger', __('dashboard.shared.select_at_least_one'));
        }
    }

    // ========================================================================
    // ======================== Soft Delete Restore Selected Function =========
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function softDeleteRestoreSelected(Request $request)
    {
        $query = $request->selectedUsers;
        if ($query) {
            // Split the query into an array using the comma ","
            $selectedUsers = explode(',', $query);
            $users = User::onlyTrashed()->whereIn('id', $selectedUsers)->get();
            if ($users) {
                DB::transaction(function () use ($selectedUsers) {
                    User::onlyTrashed()->whereIn('id', $selectedUsers)->restore();
                });
                return redirect()->route('super_admin.users-index')->with('success', __('dashboard.shared.process_successful'));
            } else {
                return redirect()->route('super_admin.users-index')->with('danger', __('dashboard.shared.select_at_least_one'));
            }
        } else {
            return redirect()->route('super_admin.users-index')->with('danger', __('dashboard.shared.select_at_least_one'));
        }
    }

    // ========================================================================
    // ======================== Active Selected Function ======================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function activeSelected(Request $request)
    {
        $query = $request->selectedUsers;

        if ($query) {
            // Split the query into an array using the comma ","
            $selectedUsers = explode(',', $query);
            $users = User::whereIn('id', $selectedUsers)->get();
            if (isset($users) &&  $users->count() > 0) {
                DB::transaction(function () use ($selectedUsers) {
                    User::whereIn('id', $selectedUsers)->update(['status' => '1']); // 1 => Active
                });
                return redirect()->route('super_admin.users-index')->with('success', __('dashboard.shared.process_successful'));
            } else {
                return redirect()->route('super_admin.users-index')->with('danger', __('dashboard.shared.select_at_least_one'));
            }
        } else {
            return redirect()->route('super_admin.users-index')->with('danger', __('dashboard.shared.select_at_least_one'));
        }
    }

    // ========================================================================
    // ======================== Inactive Selected Function ====================
    // =========================Created By :Qusai Alnablse ==========
    // ========================================================================
    public function inactiveSelected(Request $request)
    {
        $query = $request->selectedUsers;

        if ($query) {
            // Split the query into an array using the comma ","
            $selectedUsers = explode(',', $query);
            $users = User::whereIn('id', $selectedUsers)->get();
            if (isset($users) &&  $users->count() > 0) {
                DB::transaction(function () use ($selectedUsers) {
                    User::whereIn('id',  $selectedUsers)->update(['status' => 2]); // 1 => Inactive
                });
                return redirect()->route('super_admin.users-index')->with('success', __('dashboard.shared.process_successful'));
            } else {
                return redirect()->route('super_admin.users-index')->with('danger', __('dashboard.shared.select_at_least_one'));
            }
        } else {
            return redirect()->route('super_admin.users-index')->with('danger', __('dashboard.shared.select_at_least_one'));
        }
    }
}
