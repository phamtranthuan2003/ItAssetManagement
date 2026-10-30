<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminController extends Controller
{
    private const PERMISSIONS = [
        'dashboard.view' => 'Xem bảng điều khiển',
        'devices.view' => 'Xem kho thiết bị', 'devices.create' => 'Thêm thiết bị', 'devices.update' => 'Sửa thiết bị',
        'devices.delete' => 'Xóa thiết bị', 'devices.export' => 'Xuất danh sách thiết bị',
        'tickets.view' => 'Xem phiếu', 'tickets.create' => 'Lập phiếu', 'tickets.print' => 'In phiếu',
        'maintenance.view' => 'Xem bảo trì', 'maintenance.create' => 'Tạo phiếu bảo trì', 'maintenance.update' => 'Cập nhật bảo trì',
        'departments.view' => 'Xem phòng ban', 'departments.create' => 'Thêm phòng ban', 'departments.update' => 'Sửa phòng ban',
        'departments.delete' => 'Xóa phòng ban',
        'employees.view' => 'Xem nhân viên', 'employees.create' => 'Thêm nhân viên', 'employees.update' => 'Sửa nhân viên',
        'employees.delete' => 'Xóa nhân viên', 'employees.revoke' => 'Thu hồi thiết bị nhân viên',
        'reports.view' => 'Xem báo cáo', 'users.manage' => 'Quản lý tài khoản', 'roles.manage' => 'Quản lý vai trò và quyền',
    ];

    public function users(): View
    {
        $roles = DB::table('roles')->orderBy('name')->get();
        if (! auth()->user()->isSuperAdmin()) {
            $roles = $roles->reject(fn ($role) => $role->code === 'admin')->values();
        }

        return view('admin.users', [
            'users' => DB::table('users')->orderBy('id')->get(),
            'roles' => $roles,
        ]);
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6'],
            'fullname' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100'],
            'role' => ['required', 'string', 'exists:roles,code'],
            'status' => ['required', 'boolean'],
        ]);
        abort_if($data['role'] === 'admin' && ! auth()->user()->isSuperAdmin(), 403);
        $data['password'] = Hash::make($data['password']);
        DB::table('users')->insert($data);

        return back()->with('success', 'Đã tạo tài khoản.');
    }

    public function updateUser(Request $request, int $id): RedirectResponse
    {
        $target = DB::table('users')->where('id', $id)->first();
        abort_unless($target, 404);
        $actor = $request->user();
        abort_if(($id === 6 || $target->role === 'admin') && ! $actor->isSuperAdmin(), 403);
        $data = $request->validate([
            'fullname' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100'],
            'role' => ['required', 'string', 'exists:roles,code'],
            'status' => ['required', 'boolean'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);
        abort_if($data['role'] === 'admin' && ! $actor->isSuperAdmin(), 403);
        if ($id === 6 || $id === (int) $actor->id) {
            $data['role'] = $target->role;
            $data['status'] = (int) $target->status;
        }
        $password = $data['password'] ?? null;
        unset($data['password']);
        if ($password) {
            $data['password'] = Hash::make($password);
        }
        DB::table('users')->where('id', $id)->update($data);

        return back()->with('success', 'Đã cập nhật tài khoản.');
    }

    public function deactivateUser(Request $request, int $id): RedirectResponse
    {
        abort_if($id === 6, 403, 'Không thể vô hiệu hóa Super Admin.');
        abort_if($id === (int) $request->user()->id, 403, 'Bạn không thể tự vô hiệu hóa tài khoản.');
        DB::table('users')->where('id', $id)->update(['status' => 0]);

        return back()->with('success', 'Tài khoản đã được vô hiệu hóa; dữ liệu lịch sử được giữ nguyên.');
    }

    public function roles(): View
    {
        $roles = DB::table('roles')->orderBy('id')->get()->map(function ($role) {
            $role->permissions = DB::table('role_permissions')
                ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                ->where('role_permissions.role_id', $role->id)
                ->pluck('permissions.code')->all();

            return $role;
        });

        return view('admin.roles', ['roles' => $roles, 'catalog' => self::PERMISSIONS]);
    }

    public function storeRole(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'regex:/^[a-z][a-z0-9_]{1,39}$/', 'unique:roles,code'],
            'name' => ['required', 'string', 'max:100'],
        ]);
        abort_if($data['code'] === 'admin' && ! $request->user()->isSuperAdmin(), 403);
        DB::table('roles')->insert($data);

        return back()->with('success', 'Đã tạo vai trò. Hãy chọn quyền và lưu cấu hình.');
    }

    public function updateRolePermissions(Request $request, int $id): RedirectResponse
    {
        $role = DB::table('roles')->where('id', $id)->first();
        abort_unless($role, 404);
        abort_if($role->code === 'admin' && ! $request->user()->isSuperAdmin(), 403);
        $data = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);
        $codes = array_values(array_intersect($data['permissions'] ?? [], array_keys(self::PERMISSIONS)));
        DB::transaction(function () use ($id, $codes) {
            DB::table('role_permissions')->where('role_id', $id)->delete();
            $ids = DB::table('permissions')->whereIn('code', $codes)->pluck('id');
            foreach ($ids as $permissionId) {
                DB::table('role_permissions')->insert(['role_id' => $id, 'permission_id' => $permissionId]);
            }
        });

        return back()->with('success', 'Đã cập nhật quyền cho vai trò '.$role->name.'.');
    }

    public function deleteRole(int $id): RedirectResponse
    {
        $role = DB::table('roles')->where('id', $id)->first();
        abort_unless($role, 404);
        abort_if(in_array($role->code, ['admin', 'warehouse', 'customer'], true), 403);
        abort_if(DB::table('users')->where('role', $role->code)->exists(), 422, 'Không thể xóa vai trò đang được sử dụng.');
        DB::transaction(function () use ($id) {
            DB::table('role_permissions')->where('role_id', $id)->delete();
            DB::table('roles')->where('id', $id)->delete();
        });

        return back()->with('success', 'Đã xóa vai trò.');
    }
}
