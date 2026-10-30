<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function dashboard(): View
    {
        return view('dashboard', [
            'counts' => [
                'devices' => DB::table('devices')->count(),
                'in_stock' => DB::table('devices')->where('status', 'in_stock')->count(),
                'allocated' => DB::table('devices')->where('status', 'allocated')->count(),
                'employees' => DB::table('employees')->where('status', 'working')->count(),
                'maintenance' => DB::table('maintenances')->where('status', 'in_progress')->count(),
            ],
            'recentHistory' => DB::table('device_history')->leftJoin('devices', 'devices.id', '=', 'device_history.device_id')
                ->orderByDesc('device_history.action_date')->limit(8)
                ->get(['device_history.*', 'devices.device_code', 'devices.brand', 'devices.model']),
        ]);
    }

    public function devices(Request $request): View
    {
        $devices = DB::table('devices')->leftJoin('employees', 'employees.id', '=', 'devices.current_employee_id')
            ->leftJoin('departments', 'departments.id', '=', 'devices.current_department_id')
            ->when($request->filled('q'), fn ($q) => $q->where(function ($query) use ($request) {
                $query->where('device_code', 'like', '%'.$request->q.'%')
                    ->orWhere('serial_number', 'like', '%'.$request->q.'%')
                    ->orWhere('brand', 'like', '%'.$request->q.'%')
                    ->orWhere('model', 'like', '%'.$request->q.'%');
            }))
            ->when($request->filled('status'), fn ($q) => $q->where('devices.status', $request->status))
            ->orderBy('device_code')
            ->paginate(20)->withQueryString();

        return view('warehouse.devices', compact('devices'));
    }

    public function exportDevices(Request $request)
    {
        if (! $request->filled('keyword') && $request->filled('q')) {
            $request->merge(['keyword' => $request->input('q')]);
        }

        return $this->exportReport($request, 'danh-sach-thiet-bi');
    }

    public function createDevice(): View
    {
        return view('warehouse.device-form', ['device' => null, 'spec' => null, 'monitors' => collect()]);
    }

    public function storeDevice(Request $request): RedirectResponse
    {
        $data = $this->validateDevice($request);
        $spec = $this->deviceSpec($data);
        $monitors = $data['monitors'] ?? [];
        unset($data['cpu'], $data['mainboard'], $data['ram_gb'], $data['ram_type'], $data['ssd_capacity'],
            $data['hdd_capacity'], $data['gpu'], $data['screen_size'], $data['spec_notes'], $data['monitors'], $data['has_monitors']);
        $id = DB::transaction(function () use ($data, $spec, $monitors, $request) {
            $id = DB::table('devices')->insertGetId($data + ['status' => 'in_stock']);
            if (array_filter($spec)) {
                DB::table('device_specs')->insert(['device_id' => $id] + $spec);
            }
            $this->replaceMonitors($id, $monitors);
            DB::table('device_history')->insert([
                'device_id' => $id, 'ticket_id' => null, 'action_type' => 'import',
                'performed_by' => $request->user()->id, 'description' => 'Nhập thiết bị mới vào kho.',
            ]);

            return $id;
        });

        return redirect()->route('devices.show', $id)->with('success', 'Đã thêm thiết bị mới vào kho.');
    }

    public function showDevice(int $id): View
    {
        $device = DB::table('devices')->leftJoin('employees', 'employees.id', '=', 'devices.current_employee_id')
            ->leftJoin('departments', 'departments.id', '=', 'devices.current_department_id')
            ->where('devices.id', $id)
            ->first(['devices.*', 'employees.fullname as current_employee_name', 'departments.name as current_department_name']);
        abort_unless($device, 404);
        $spec = DB::table('device_specs')->where('device_id', $id)->first();
        $monitors = DB::table('device_monitors')->where('device_id', $id)->orderBy('monitor_no')
            ->get()->map(fn ($monitor) => (array) $monitor)->all();
        $history = DB::table('device_history')->where('device_id', $id)->orderByDesc('action_date')->get();

        return view('warehouse.device-show', compact('device', 'spec', 'monitors', 'history'));
    }

    public function editDevice(int $id): View
    {
        $device = DB::table('devices')->where('id', $id)->first();
        abort_unless($device, 404);
        $spec = DB::table('device_specs')->where('device_id', $id)->first();
        $monitors = DB::table('device_monitors')->where('device_id', $id)->orderBy('monitor_no')
            ->get()->map(fn ($monitor) => (array) $monitor)->all();

        return view('warehouse.device-form', compact('device', 'spec', 'monitors'));
    }

    public function updateDevice(Request $request, int $id): RedirectResponse
    {
        abort_unless(DB::table('devices')->where('id', $id)->exists(), 404);
        $data = $this->validateDevice($request, $id);
        $spec = $this->deviceSpec($data);
        $monitors = $data['monitors'] ?? [];
        unset($data['cpu'], $data['mainboard'], $data['ram_gb'], $data['ram_type'], $data['ssd_capacity'],
            $data['hdd_capacity'], $data['gpu'], $data['screen_size'], $data['spec_notes'], $data['monitors'], $data['has_monitors']);
        DB::transaction(function () use ($data, $spec, $monitors, $id) {
            DB::table('devices')->where('id', $id)->update($data);
            if (DB::table('device_specs')->where('device_id', $id)->exists()) {
                DB::table('device_specs')->where('device_id', $id)->update($spec);
            } elseif (array_filter($spec)) {
                DB::table('device_specs')->insert(['device_id' => $id] + $spec);
            }
            $this->replaceMonitors($id, $monitors);
        });

        return redirect()->route('devices.show', $id)->with('success', 'Đã cập nhật thông tin thiết bị.');
    }

    public function deleteDevice(int $id): RedirectResponse
    {
        $device = DB::table('devices')->where('id', $id)->first();
        abort_unless($device, 404);
        abort_if($device->status !== 'in_stock' || $device->current_employee_id !== null
            || DB::table('device_history')->where('device_id', $id)->exists()
            || DB::table('ticket_details')->where('device_id', $id)->exists(), 422,
            'Thiết bị đã có lịch sử cấp phát; hãy giữ lại hồ sơ và đổi trạng thái thay vì xóa.');
        DB::transaction(function () use ($id) {
            DB::table('device_specs')->where('device_id', $id)->delete();
            DB::table('device_monitors')->where('device_id', $id)->delete();
            DB::table('devices')->where('id', $id)->delete();
        });

        return redirect()->route('devices.index')->with('success', 'Đã xóa thiết bị.');
    }

    public function departments(): View
    {
        return view('warehouse.departments', ['departments' => DB::table('departments')->orderBy('name')->get()]);
    }

    public function storeDepartment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:departments,code'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
        ]);
        DB::table('departments')->insert($data);

        return back()->with('success', 'Đã thêm phòng ban.');
    }

    public function updateDepartment(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('departments', 'code')->ignore($id)],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
        ]);
        DB::table('departments')->where('id', $id)->update($data);

        return back()->with('success', 'Đã cập nhật phòng ban.');
    }

    public function deleteDepartment(int $id): RedirectResponse
    {
        abort_if(DB::table('employees')->where('department_id', $id)->exists()
            || DB::table('devices')->where('current_department_id', $id)->exists()
            || DB::table('tickets')->where('from_department_id', $id)->orWhere('to_department_id', $id)->exists()
            || DB::table('device_history')->where('from_department_id', $id)->orWhere('to_department_id', $id)->exists(), 422,
            'Không thể xóa phòng ban đang được nhân viên hoặc thiết bị sử dụng.');
        DB::table('departments')->where('id', $id)->delete();

        return back()->with('success', 'Đã xóa phòng ban.');
    }

    public function employees(Request $request): View
    {
        $employees = DB::table('employees')
            ->leftJoin('departments', 'departments.id', '=', 'employees.department_id')
            ->leftJoin('devices', function ($join) {
                $join->on('devices.current_employee_id', '=', 'employees.id')
                    ->where('devices.status', '=', 'allocated');
            })
            ->select([
                'employees.*', 'departments.name as department_name', 'departments.code as department_code',
                DB::raw('COUNT(devices.id) as held_devices_count'),
            ])
            ->when($request->filled('status'), fn ($query) => $query->where('employees.status', $request->input('status')))
            ->when($request->filled('department_id'), fn ($query) => $query->where('employees.department_id', $request->integer('department_id')))
            ->when($request->filled('keyword'), fn ($query) => $query->where(function ($search) use ($request) {
                $term = '%'.$request->input('keyword').'%';
                $search->where('employees.fullname', 'like', $term)
                    ->orWhere('employees.code', 'like', $term)
                    ->orWhere('employees.email', 'like', $term)
                    ->orWhere('employees.phone', 'like', $term);
            }))
            ->groupBy('employees.id', 'departments.name', 'departments.code')
            ->orderByDesc('employees.id')
            ->paginate(10)
            ->withQueryString();

        return view('warehouse.employees', [
            'employees' => $employees,
            'departments' => DB::table('departments')->orderBy('name')->get(),
        ]);
    }

    public function showEmployee(int $id): View
    {
        $employee = DB::table('employees')
            ->join('departments', 'departments.id', '=', 'employees.department_id')
            ->where('employees.id', $id)
            ->first(['employees.*', 'departments.name as department_name', 'departments.code as department_code']);
        abort_unless($employee, 404);

        return view('warehouse.employee-show', [
            'employee' => $employee,
            'departments' => DB::table('departments')->orderBy('name')->get(),
            'heldDevices' => DB::table('devices')
                ->leftJoin('device_specs', 'device_specs.device_id', '=', 'devices.id')
                ->where('devices.current_employee_id', $id)
                ->where('devices.status', 'allocated')
                ->orderByDesc('devices.id')
                ->get([
                    'devices.*', 'device_specs.cpu', 'device_specs.ram_gb',
                    'device_specs.ssd_capacity', 'device_specs.gpu',
                ]),
        ]);
    }

    public function storeEmployee(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:employees,code'],
            'fullname' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'position' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(['working', 'resigned'])],
        ]);
        DB::table('employees')->insert($data);

        return back()->with('success', 'Đã thêm nhân viên.');
    }

    public function updateEmployee(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('employees', 'code')->ignore($id)],
            'fullname' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'position' => ['nullable', 'string', 'max:100'],
            'status' => ['required', Rule::in(['working', 'resigned'])],
        ]);
        DB::table('employees')->where('id', $id)->update($data);

        return back()->with('success', 'Đã cập nhật nhân viên.');
    }

    public function deleteEmployee(int $id): RedirectResponse
    {
        $hasDevices = DB::table('devices')->where('current_employee_id', $id)->exists();
        abort_if($hasDevices, 422, 'Nhân viên vẫn đang giữ thiết bị. Hãy lập phiếu thu hồi trước khi cho nghỉ.');
        DB::table('employees')->where('id', $id)->update(['status' => 'resigned']);

        return back()->with('success', 'Đã chuyển nhân viên sang trạng thái nghỉ việc; lịch sử được giữ nguyên.');
    }

    public function bulkRevokeEmployee(Request $request, int $id): RedirectResponse
    {
        $employee = DB::table('employees')->where('id', $id)->first();
        abort_unless($employee, 404);
        $devices = DB::table('devices')->where('current_employee_id', $id)->where('status', 'allocated')->get();
        if ($devices->isEmpty()) {
            return back()->with('success', 'Nhân viên không còn thiết bị cần thu hồi.');
        }
        $data = $request->validate([
            'returned_condition' => ['required', Rule::in(['good', 'broken', 'missing_accessories'])],
            'reason' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'mark_as_resigned' => ['nullable', 'boolean'],
        ]);
        $ticketId = DB::transaction(function () use ($devices, $employee, $request, $data) {
            $code = 'PTH-'.now()->format('Ymd').'-'.Str::upper(Str::random(4));
            $ticketId = DB::table('tickets')->insertGetId([
                'ticket_code' => $code, 'ticket_type' => 'revocation', 'ticket_date' => now()->toDateString(),
                'created_by' => $request->user()->id, 'from_employee_id' => $employee->id,
                'from_department_id' => $employee->department_id, 'status' => 'completed', 'is_temporary' => 0,
                'reason' => $data['reason'] ?: 'Thu hồi thiết bị khi nhân viên bàn giao/nghỉ việc.',
                'notes' => $data['notes'] ?? null,
            ]);
            foreach ($devices as $device) {
                $status = $data['returned_condition'] === 'broken' ? 'maintenance' : 'in_stock';
                $condition = match ($data['returned_condition']) {
                    'broken' => 'broken',
                    'missing_accessories' => 'used',
                    default => 'used',
                };
                $accessories = 'Đầy đủ phụ kiện theo máy';
                DB::table('ticket_details')->insert([
                    'ticket_id' => $ticketId, 'device_id' => $device->id, 'accessories' => $accessories,
                    'returned_condition' => $data['returned_condition'], 'notes' => $data['notes'] ?? null,
                ]);
                DB::table('devices')->where('id', $device->id)->update([
                    'status' => $status, 'condition_state' => $condition,
                    'current_employee_id' => null, 'current_department_id' => null,
                    'is_temporary' => 0, 'expected_return_date' => null,
                ]);
                DB::table('device_history')->insert([
                    'device_id' => $device->id, 'ticket_id' => $ticketId, 'action_type' => 'revoke',
                    'performed_by' => $request->user()->id, 'from_employee_id' => $employee->id,
                    'from_department_id' => $employee->department_id,
                    'description' => 'Thu hồi theo phiếu '.$code.'; tình trạng: '.$data['returned_condition']
                        .'; phụ kiện: '.$accessories.'; ghi chú: '.($data['notes'] ?? ''),
                ]);
            }
            if ($request->boolean('mark_as_resigned')) {
                DB::table('employees')->where('id', $employee->id)->update(['status' => 'resigned']);
            }

            return $ticketId;
        });

        return redirect()->route('tickets.show', $ticketId)
            ->with('success', 'Đã thu hồi toàn bộ thiết bị và tạo biên bản bàn giao.');
    }

    public function tickets(): View
    {
        return view('warehouse.tickets', [
            'tickets' => DB::table('tickets')->leftJoin('employees as receiver', 'receiver.id', '=', 'tickets.to_employee_id')
                ->orderByDesc('tickets.ticket_date')->orderByDesc('tickets.id')
                ->paginate(20, ['tickets.*', 'receiver.fullname as receiver_name']),
        ]);
    }

    public function createTicket(Request $request): View
    {
        return view('warehouse.ticket-form', [
            'type' => $request->query('type', 'allocation'),
            'selectedEmployeeId' => $request->integer('to_employee_id') ?: null,
            'selectedDepartmentId' => $request->integer('to_department_id') ?: null,
            'devices' => DB::table('devices')->whereIn('status', ['in_stock', 'allocated'])->orderBy('device_code')->get(),
            'employees' => DB::table('employees')->where('status', 'working')->orderBy('fullname')->get(),
            'departments' => DB::table('departments')->orderBy('name')->get(),
        ]);
    }

    public function storeTicket(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ticket_type' => ['required', Rule::in(['allocation', 'transfer', 'revocation'])],
            'ticket_date' => ['required', 'date'],
            'device_ids' => ['required', 'array', 'min:1'],
            'device_ids.*' => ['required', 'integer', 'distinct', 'exists:devices,id'],
            'to_employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'to_department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'expected_return_date' => ['nullable', 'date'],
            'reason' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'accessories' => ['nullable', 'string', 'max:255'],
            'returned_condition' => ['nullable', Rule::in(['good', 'broken', 'missing_accessories'])],
            'is_temporary' => ['nullable', 'boolean'],
        ]);
        if ($data['ticket_type'] !== 'revocation' && (! $data['to_employee_id'] || ! $data['to_department_id'])) {
            return back()->withErrors(['to_employee_id' => 'Cấp phát hoặc chuyển giao cần chọn nhân viên và phòng ban nhận.'])->withInput();
        }
        $ticketId = DB::transaction(function () use ($data, $request) {
            $ticketCode = 'PX-'.now()->format('Ymd-His').'-'.Str::upper(Str::random(4));
            $ticketId = DB::table('tickets')->insertGetId([
                'ticket_code' => $ticketCode, 'ticket_type' => $data['ticket_type'],
                'ticket_date' => $data['ticket_date'], 'created_by' => $request->user()->id,
                'from_employee_id' => null, 'from_department_id' => null,
                'to_employee_id' => $data['ticket_type'] === 'revocation' ? null : ($data['to_employee_id'] ?? null),
                'to_department_id' => $data['ticket_type'] === 'revocation' ? null : ($data['to_department_id'] ?? null),
                'is_temporary' => (int) ($data['is_temporary'] ?? false),
                'expected_return_date' => $data['expected_return_date'] ?? null,
                'status' => 'completed', 'reason' => $data['reason'] ?? null, 'notes' => $data['notes'] ?? null,
            ]);
            $origin = null;
            $consistentOrigin = true;
            foreach ($data['device_ids'] as $deviceId) {
                $device = DB::table('devices')->where('id', $deviceId)->lockForUpdate()->first();
                if (! $device || $device->status === 'maintenance' || $device->status === 'liquidated') {
                    abort(422, 'Có thiết bị không thể cấp phát hoặc thu hồi.');
                }
                if ($data['ticket_type'] === 'allocation' && $device->status !== 'in_stock') {
                    abort(422, 'Chỉ thiết bị trong kho mới có thể cấp phát.');
                }
                if ($data['ticket_type'] === 'transfer' && $device->status !== 'allocated') {
                    abort(422, 'Chỉ thiết bị đang cấp phát mới có thể chuyển giao.');
                }
                if ($origin === null) {
                    $origin = [$device->current_employee_id, $device->current_department_id];
                } elseif ($origin !== [$device->current_employee_id, $device->current_department_id]) {
                    $consistentOrigin = false;
                    if ($data['ticket_type'] === 'transfer') {
                        abort(422, 'Một phiếu chuyển giao chỉ được chọn thiết bị của cùng một người đang sử dụng.');
                    }
                }
                if ($data['ticket_type'] === 'revocation' && $device->status !== 'allocated') {
                    abort(422, 'Chỉ thiết bị đang cấp phát mới có thể thu hồi.');
                }
                $fromEmployee = $device->current_employee_id;
                $fromDepartment = $device->current_department_id;
                $newStatus = $data['ticket_type'] === 'revocation' ? 'in_stock' : 'allocated';
                DB::table('ticket_details')->insert([
                    'ticket_id' => $ticketId, 'device_id' => $deviceId, 'accessories' => $data['accessories'] ?? null,
                    'returned_condition' => $data['ticket_type'] === 'revocation' ? ($data['returned_condition'] ?? null) : null,
                    'notes' => null,
                ]);
                DB::table('devices')->where('id', $deviceId)->update([
                    'status' => $newStatus,
                    'condition_state' => ($data['ticket_type'] === 'revocation' && ($data['returned_condition'] ?? null) === 'broken')
                        ? 'broken' : $device->condition_state,
                    'current_employee_id' => $newStatus === 'allocated' ? $data['to_employee_id'] : null,
                    'current_department_id' => $newStatus === 'allocated' ? $data['to_department_id'] : null,
                    'is_temporary' => $newStatus === 'allocated' ? (int) ($data['is_temporary'] ?? false) : 0,
                    'expected_return_date' => $newStatus === 'allocated' ? ($data['expected_return_date'] ?? null) : null,
                ]);
                DB::table('device_history')->insert([
                    'device_id' => $deviceId, 'ticket_id' => $ticketId, 'action_type' => $data['ticket_type'],
                    'performed_by' => $request->user()->id, 'from_employee_id' => $fromEmployee,
                    'to_employee_id' => $newStatus === 'allocated' ? $data['to_employee_id'] : null,
                    'from_department_id' => $fromDepartment,
                    'to_department_id' => $newStatus === 'allocated' ? $data['to_department_id'] : null,
                    'description' => ucfirst($data['ticket_type']).' - '.$ticketCode,
                ]);
            }
            if ($consistentOrigin && $origin !== null && $data['ticket_type'] !== 'allocation') {
                DB::table('tickets')->where('id', $ticketId)->update([
                    'from_employee_id' => $origin[0], 'from_department_id' => $origin[1],
                ]);
            }

            return $ticketId;
        });

        return redirect()->route('tickets.show', $ticketId)->with('success', 'Đã lập phiếu và cập nhật trạng thái thiết bị.');
    }

    public function showTicket(int $id): View
    {
        $ticket = DB::table('tickets')->where('id', $id)->first();
        abort_unless($ticket, 404);

        return view('warehouse.ticket-show', [
            'ticket' => $ticket,
            'items' => DB::table('ticket_details')->join('devices', 'devices.id', '=', 'ticket_details.device_id')
                ->where('ticket_details.ticket_id', $id)->get(),
        ]);
    }

    public function printTicket(int $id): View
    {
        $ticket = DB::table('tickets')
            ->join('users as creator', 'creator.id', '=', 'tickets.created_by')
            ->leftJoin('employees as sender', 'sender.id', '=', 'tickets.from_employee_id')
            ->leftJoin('employees as receiver', 'receiver.id', '=', 'tickets.to_employee_id')
            ->leftJoin('departments as sender_department', 'sender_department.id', '=', 'tickets.from_department_id')
            ->leftJoin('departments as receiver_department', 'receiver_department.id', '=', 'tickets.to_department_id')
            ->where('tickets.id', $id)
            ->first([
                'tickets.*', 'creator.fullname as creator_name',
                'sender.fullname as from_employee_name', 'sender.position as from_employee_pos',
                'receiver.fullname as to_employee_name', 'receiver.position as to_employee_pos',
                'sender_department.name as from_department_name', 'receiver_department.name as to_department_name',
            ]);
        abort_unless($ticket, 404);

        return view('warehouse.ticket-print', [
            'ticket' => $ticket,
            'items' => DB::table('ticket_details')
                ->join('devices', 'devices.id', '=', 'ticket_details.device_id')
                ->leftJoin('device_specs', 'device_specs.device_id', '=', 'devices.id')
                ->where('ticket_details.ticket_id', $id)
                ->orderBy('ticket_details.id')
                ->get(),
        ]);
    }

    public function maintenance(): View
    {
        return view('warehouse.maintenance', [
            'records' => DB::table('maintenances')->join('devices', 'devices.id', '=', 'maintenances.device_id')
                ->orderByDesc('maintenances.start_date')->paginate(20, ['maintenances.*', 'devices.device_code', 'devices.brand', 'devices.model']),
            'devices' => DB::table('devices')->orderBy('device_code')->get(),
        ]);
    }

    public function storeMaintenance(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'device_id' => ['required', 'integer', 'exists:devices,id'],
            'maintenance_type' => ['required', Rule::in(['repair', 'upgrade', 'routine'])],
            'start_date' => ['required', 'date'], 'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'cost' => ['nullable', 'numeric', 'min:0'], 'provider' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string'], 'status' => ['required', Rule::in(['in_progress', 'completed', 'cannot_repair'])],
        ]);
        DB::transaction(function () use ($data, $request) {
            $maintenanceId = DB::table('maintenances')->insertGetId($data + ['created_by' => $request->user()->id]);
            if ($data['status'] === 'in_progress') {
                DB::table('devices')->where('id', $data['device_id'])->update([
                    'status' => 'maintenance', 'current_employee_id' => null, 'current_department_id' => null,
                    'is_temporary' => 0, 'expected_return_date' => null,
                ]);
            } elseif ($data['status'] === 'cannot_repair') {
                DB::table('devices')->where('id', $data['device_id'])->update([
                    'status' => 'liquidated', 'current_employee_id' => null, 'current_department_id' => null,
                    'is_temporary' => 0, 'expected_return_date' => null,
                ]);
            } else {
                DB::table('devices')->where('id', $data['device_id'])->where('status', 'maintenance')->update(['status' => 'in_stock']);
            }
            DB::table('device_history')->insert([
                'device_id' => $data['device_id'], 'ticket_id' => null, 'action_type' => 'maintenance',
                'performed_by' => $request->user()->id, 'description' => 'Phiếu bảo trì #'.$maintenanceId.': '.$data['description'],
            ]);
        });

        return back()->with('success', 'Đã ghi nhận bảo trì.');
    }

    public function updateMaintenance(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', Rule::in(['in_progress', 'completed', 'cannot_repair'])]]);
        $record = DB::table('maintenances')->where('id', $id)->first();
        abort_unless($record, 404);
        DB::transaction(function () use ($data, $record, $id) {
            DB::table('maintenances')->where('id', $id)->update($data);
            DB::table('devices')->where('id', $record->device_id)->update([
                'status' => match ($data['status']) {
                    'in_progress' => 'maintenance', 'cannot_repair' => 'liquidated', default => 'in_stock',
                },
                'current_employee_id' => null, 'current_department_id' => null,
                'is_temporary' => 0, 'expected_return_date' => null,
            ]);
        });

        return back()->with('success', 'Đã cập nhật trạng thái bảo trì.');
    }

    public function reports(Request $request): View
    {
        $this->validateReportFilters($request);
        $query = $this->filteredReportDevices($request);
        $stats = (clone $query)->selectRaw("
            COUNT(*) as total,
            COALESCE(SUM(status = 'in_stock'), 0) as in_stock,
            COALESCE(SUM(status = 'allocated'), 0) as allocated,
            COALESCE(SUM(status = 'maintenance'), 0) as maintenance,
            COALESCE(SUM(status = 'liquidated'), 0) as liquidated,
            COALESCE(SUM(type = 'PC'), 0) as pc_count,
            COALESCE(SUM(type = 'Laptop'), 0) as laptop_count,
            COALESCE(SUM(type NOT IN ('PC', 'Laptop')), 0) as other_count
        ")->first();
        $deptStats = (clone $query)
            ->leftJoin('departments as report_department', 'report_department.id', '=', 'devices.current_department_id')
            ->where('devices.status', 'allocated')
            ->select('report_department.id as department_id', 'report_department.name as department_name',
                'report_department.code as department_code', DB::raw('COUNT(devices.id) as device_count'))
            ->groupBy('report_department.id', 'report_department.name', 'report_department.code')
            ->orderByDesc('device_count')->get();
        $overdueDevices = (clone $query)
            ->leftJoin('employees as overdue_employee', 'overdue_employee.id', '=', 'devices.current_employee_id')
            ->leftJoin('departments as overdue_department', 'overdue_department.id', '=', 'devices.current_department_id')
            ->where('devices.is_temporary', 1)->where('devices.status', 'allocated')
            ->whereDate('devices.expected_return_date', '<', now()->toDateString())
            ->orderBy('devices.expected_return_date')
            ->get(['devices.*', 'overdue_employee.fullname as employee_name',
                'overdue_employee.phone as employee_phone', 'overdue_department.name as department_name']);
        $expiringDevices = (clone $query)
            ->whereNotNull('devices.warranty_expiry')
            ->whereDate('devices.warranty_expiry', '>=', now()->toDateString())
            ->whereDate('devices.warranty_expiry', '<=', now()->addDays(30)->toDateString())
            ->orderBy('devices.warranty_expiry')
            ->select('devices.*')
            ->get()
            ->map(function ($device) {
                $device->days_left = now()->startOfDay()->diffInDays(Carbon::parse($device->warranty_expiry)->startOfDay());

                return $device;
            });

        return view('warehouse.reports', [
            'stats' => $stats,
            'deptStats' => $deptStats,
            'overdueDevices' => $overdueDevices,
            'expiringDevices' => $expiringDevices,
            'departments' => DB::table('departments')->orderBy('name')->get(),
            'employees' => DB::table('employees')->orderBy('fullname')->get(['id', 'code', 'fullname']),
            'filters' => $request->only([
                'keyword', 'status', 'type', 'brand', 'condition_state', 'department_id', 'employee_id',
                'cpu', 'ram_gb', 'ssd_capacity', 'is_overdue', 'expiring_warranty',
            ]),
        ]);
    }

    public function exportReport(Request $request, string $filenamePrefix = 'bao-cao-thiet-bi')
    {
        $this->validateReportFilters($request);
        $devices = $this->filteredReportDevices($request)
            ->leftJoin('departments', 'departments.id', '=', 'devices.current_department_id')
            ->leftJoin('employees', 'employees.id', '=', 'devices.current_employee_id')
            ->orderBy('devices.device_code')
            ->select([
                'devices.*', 'employees.fullname as employee_name', 'departments.name as department_name',
                'report_specs.cpu', 'report_specs.mainboard', 'report_specs.ram_gb', 'report_specs.ram_type',
                'report_specs.ssd_capacity', 'report_specs.hdd_capacity', 'report_specs.gpu', 'report_specs.screen_size',
            ]);

        return response()->streamDownload(function () use ($devices) {
            $output = fopen('php://output', 'w');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, [
                'STT', 'STT', 'Loại', 'Hãng', 'Model', 'Số Serial', 'Trạng thái', 'Tình trạng',
                'CPU', 'Mainboard', 'RAM', 'SSD', 'HDD', 'GPU', 'Màn hình', 'Người đang giữ',
                'Phòng ban', 'Ngày nhập kho', 'Hết hạn bảo hành', 'Ghi chú',
            ]);
            $rowNumber = 0;
            $devices->chunk(500, function ($chunk) use ($output, &$rowNumber) {
                $monitorDetails = DB::table('device_monitors')
                    ->whereIn('device_id', $chunk->pluck('id'))
                    ->orderBy('monitor_no')
                    ->get()
                    ->groupBy('device_id');
                foreach ($chunk as $device) {
                    $screenDetails = [];
                    if ($device->screen_size) {
                        $screenDetails[] = 'Màn hình tích hợp: '.$device->screen_size;
                    }
                    foreach ($monitorDetails->get($device->id, collect()) as $monitor) {
                        $screenDetails[] = trim($monitor->brand.' '.$monitor->model.($monitor->size ? ' '.$monitor->size.' inch' : ''));
                    }
                    fputcsv($output, [
                        ++$rowNumber, $device->id, $device->type, $device->brand, $device->model,
                        $device->serial_number, [
                            'in_stock' => 'Trong kho', 'allocated' => 'Đang cấp phát',
                            'maintenance' => 'Bảo trì', 'liquidated' => 'Thanh lý',
                        ][$device->status] ?? $device->status, [
                            'new' => 'Mới', 'used' => 'Đang dùng', 'broken' => 'Hỏng',
                            'under_warranty' => 'Bảo hành',
                        ][$device->condition_state] ?? $device->condition_state,
                        $device->cpu, $device->mainboard,
                        trim(($device->ram_gb ?? '').'GB '.($device->ram_type ?? '')),
                        $device->ssd_capacity, $device->hdd_capacity, $device->gpu, implode('; ', $screenDetails),
                        $device->employee_name ?: 'Trong kho', $device->department_name,
                        $device->import_date, $device->warranty_expiry, $device->notes,
                    ]);
                }
            });
            fclose($output);
        }, $filenamePrefix.'-'.now()->format('Ymd_His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function filteredReportDevices(Request $request)
    {
        return DB::table('devices')
            ->leftJoin('device_specs as report_specs', 'report_specs.device_id', '=', 'devices.id')
            ->when($request->filled('keyword'), fn ($query) => $query->where(function ($search) use ($request) {
                $term = '%'.$request->input('keyword').'%';
                $search->where('devices.device_code', 'like', $term)
                    ->orWhere('devices.serial_number', 'like', $term)
                    ->orWhere('devices.type', 'like', $term)
                    ->orWhere('devices.brand', 'like', $term)
                    ->orWhere('devices.model', 'like', $term);
            }))
            ->when($request->filled('status'), fn ($query) => $query->where('devices.status', $request->input('status')))
            ->when($request->filled('type'), fn ($query) => $query->where('devices.type', $request->input('type')))
            ->when($request->filled('brand'), fn ($query) => $query->where('devices.brand', $request->input('brand')))
            ->when($request->filled('condition_state'), fn ($query) => $query->where('devices.condition_state', $request->input('condition_state')))
            ->when($request->filled('department_id'), fn ($query) => $query->where('devices.current_department_id', $request->integer('department_id')))
            ->when($request->filled('employee_id'), fn ($query) => $query->where('devices.current_employee_id', $request->integer('employee_id')))
            ->when($request->filled('cpu'), fn ($query) => $query->where('report_specs.cpu', 'like', '%'.$request->input('cpu').'%'))
            ->when($request->filled('ram_gb'), fn ($query) => $query->where('report_specs.ram_gb', '>=', $request->integer('ram_gb')))
            ->when($request->filled('ssd_capacity'), fn ($query) => $query->where('report_specs.ssd_capacity', 'like', '%'.$request->input('ssd_capacity').'%'))
            ->when($request->boolean('is_overdue'), fn ($query) => $query->where('devices.is_temporary', 1)
                ->where('devices.status', 'allocated')->whereDate('devices.expected_return_date', '<', now()->toDateString()))
            ->when($request->boolean('expiring_warranty'), fn ($query) => $query->whereNotNull('devices.warranty_expiry')
                ->whereDate('devices.warranty_expiry', '>=', now()->toDateString())
                ->whereDate('devices.warranty_expiry', '<=', now()->addDays(30)->toDateString()));
    }

    private function validateReportFilters(Request $request): void
    {
        $request->validate([
            'keyword' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['in_stock', 'allocated', 'maintenance', 'liquidated'])],
            'type' => ['nullable', 'string', 'max:50'],
            'brand' => ['nullable', 'string', 'max:50'],
            'condition_state' => ['nullable', Rule::in(['new', 'used', 'broken', 'under_warranty'])],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'cpu' => ['nullable', 'string', 'max:100'],
            'ram_gb' => ['nullable', 'integer', 'min:1', 'max:1024'],
            'ssd_capacity' => ['nullable', 'string', 'max:50'],
            'is_overdue' => ['nullable', 'boolean'],
            'expiring_warranty' => ['nullable', 'boolean'],
        ]);
    }

    private function validateDevice(Request $request, ?int $id = null): array
    {
        $data = $request->validate([
            'device_code' => ['required', 'string', 'max:50', Rule::unique('devices', 'device_code')->ignore($id)],
            'type' => ['required', 'string', 'max:50'], 'brand' => ['required', 'string', 'max:50'],
            'model' => ['required', 'string', 'max:100'], 'serial_number' => ['nullable', 'string', 'max:100'],
            'import_date' => ['required', 'date'], 'warranty_expiry' => ['nullable', 'date'],
            'condition_state' => ['required', Rule::in(['new', 'used', 'broken', 'under_warranty'])],
            'notes' => ['nullable', 'string'],
            'accessories' => ['nullable', 'string', 'max:255'],
            'returned_condition' => ['nullable', Rule::in(['good', 'broken', 'missing_accessories'])], 'cpu' => ['nullable', 'string', 'max:100'],
            'mainboard' => ['nullable', 'string', 'max:100'], 'ram_gb' => ['nullable', 'integer', 'min:0'],
            'ram_type' => ['nullable', 'string', 'max:50'], 'ssd_capacity' => ['nullable', 'string', 'max:50'],
            'hdd_capacity' => ['nullable', 'string', 'max:50'], 'gpu' => ['nullable', 'string', 'max:100'],
            'screen_size' => ['nullable', 'string', 'max:50'], 'spec_notes' => ['nullable', 'string'],
            'has_monitors' => ['nullable', 'boolean'],
            'monitors' => ['nullable', 'array', 'required_if:has_monitors,1'],
            'monitors.*.brand' => ['required', 'string', 'max:100'],
            'monitors.*.model' => ['nullable', 'string', 'max:100'],
            'monitors.*.size' => ['nullable', 'numeric', 'min:1', 'max:100'],
        ]);
        $data['monitors'] = $request->boolean('has_monitors') ? ($data['monitors'] ?? []) : [];

        return $data;
    }

    private function replaceMonitors(int $deviceId, array $monitors): void
    {
        DB::table('device_monitors')->where('device_id', $deviceId)->delete();
        foreach (array_values($monitors) as $index => $monitor) {
            DB::table('device_monitors')->insert([
                'device_id' => $deviceId, 'monitor_no' => $index + 1,
                'brand' => $monitor['brand'], 'model' => $monitor['model'] ?? null,
                'size' => $monitor['size'] ?? null,
            ]);
        }
    }

    private function deviceSpec(array $data): array
    {
        return [
            'cpu' => $data['cpu'] ?? null, 'mainboard' => $data['mainboard'] ?? null,
            'ram_gb' => $data['ram_gb'] ?? null, 'ram_type' => $data['ram_type'] ?? null,
            'ssd_capacity' => $data['ssd_capacity'] ?? null, 'hdd_capacity' => $data['hdd_capacity'] ?? null,
            'gpu' => $data['gpu'] ?? null, 'screen_size' => $data['screen_size'] ?? null,
            'notes' => $data['spec_notes'] ?? null,
        ];
    }
}
