<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class WarehouseWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_allocation_and_revocation_update_device_and_audit_history(): void
    {
        $superAdmin = User::factory()->create(['id' => 6]);
        $departmentId = DB::table('departments')->insertGetId(['code' => 'IT', 'name' => 'Công nghệ']);
        $employeeId = DB::table('employees')->insertGetId([
            'code' => 'NV001', 'fullname' => 'Nguyễn An', 'department_id' => $departmentId, 'status' => 'working',
        ]);
        $newEmployeeId = DB::table('employees')->insertGetId([
            'code' => 'NV002', 'fullname' => 'Trần Bình', 'department_id' => $departmentId, 'status' => 'working',
        ]);
        $deviceId = DB::table('devices')->insertGetId([
            'device_code' => 'LT-001', 'type' => 'Laptop', 'brand' => 'Dell', 'model' => 'Latitude',
            'import_date' => '2026-01-01', 'condition_state' => 'new', 'status' => 'in_stock',
        ]);
        $this->actingAs($superAdmin);

        $this->post('/tickets', [
            'ticket_type' => 'allocation', 'ticket_date' => '2026-10-28',
            'device_ids' => [$deviceId], 'to_employee_id' => $employeeId, 'to_department_id' => $departmentId,
        ])->assertRedirect();
        $this->assertDatabaseHas('devices', [
            'id' => $deviceId, 'status' => 'allocated', 'current_employee_id' => $employeeId,
        ]);
        $this->assertDatabaseHas('device_history', ['device_id' => $deviceId, 'action_type' => 'allocation']);

        $this->post('/tickets', [
            'ticket_type' => 'transfer', 'ticket_date' => '2026-10-28',
            'device_ids' => [$deviceId], 'to_employee_id' => $newEmployeeId, 'to_department_id' => $departmentId,
        ])->assertRedirect();
        $this->assertDatabaseHas('device_history', [
            'device_id' => $deviceId, 'action_type' => 'transfer', 'from_employee_id' => $employeeId,
            'to_employee_id' => $newEmployeeId,
        ]);

        $this->post('/tickets', [
            'ticket_type' => 'revocation', 'ticket_date' => '2026-10-28', 'device_ids' => [$deviceId],
            'returned_condition' => 'broken',
        ])->assertRedirect();
        $this->assertDatabaseHas('devices', [
            'id' => $deviceId, 'status' => 'in_stock', 'current_employee_id' => null, 'condition_state' => 'broken',
        ]);
        $this->assertDatabaseHas('ticket_details', ['device_id' => $deviceId, 'returned_condition' => 'broken']);
        $this->assertDatabaseCount('tickets', 3);
        $this->assertDatabaseCount('device_history', 3);
    }

    public function test_new_device_saves_specs_and_attached_monitors(): void
    {
        $this->actingAs(User::factory()->create(['id' => 6]));

        $response = $this->post('/devices', [
            'device_code' => 'PC-100', 'type' => 'PC', 'brand' => 'Dell', 'model' => 'OptiPlex',
            'import_date' => '2026-10-28', 'condition_state' => 'new', 'cpu' => 'Intel Core i7',
            'ram_gb' => 16, 'has_monitors' => '1',
            'monitors' => [['brand' => 'Dell', 'model' => 'P2422H', 'size' => '24']],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('device_specs', ['cpu' => 'Intel Core i7', 'ram_gb' => 16]);
        $this->assertDatabaseHas('device_monitors', [
            'monitor_no' => 1, 'brand' => 'Dell', 'model' => 'P2422H', 'size' => 24,
        ]);
    }

    public function test_super_admin_can_render_all_primary_module_pages(): void
    {
        $this->actingAs(User::factory()->create(['id' => 6]));

        foreach ([
            '/dashboard', '/devices', '/devices/create', '/departments', '/employees',
            '/tickets', '/tickets/create', '/maintenance', '/reports', '/users', '/roles', '/profile',
        ] as $path) {
            $this->get($path)->assertOk();
        }
    }

    public function test_employee_detail_shows_assigned_devices_and_bulk_revoke_records_return_condition(): void
    {
        $superAdmin = User::factory()->create(['id' => 6]);
        $departmentId = DB::table('departments')->insertGetId(['code' => 'HR', 'name' => 'Nhân sự']);
        $employeeId = DB::table('employees')->insertGetId([
            'code' => 'NVH01', 'fullname' => 'Lê Hoa', 'email' => 'hoa@example.test',
            'phone' => '0900000000', 'department_id' => $departmentId, 'position' => 'Chuyên viên',
            'status' => 'working',
        ]);
        $deviceId = DB::table('devices')->insertGetId([
            'device_code' => 'LT-H01', 'type' => 'Laptop', 'brand' => 'Dell', 'model' => 'Latitude',
            'import_date' => '2025-01-01', 'condition_state' => 'used', 'status' => 'allocated',
            'current_employee_id' => $employeeId, 'current_department_id' => $departmentId, 'is_temporary' => 1,
            'expected_return_date' => '2026-10-30',
        ]);
        DB::table('device_specs')->insert(['device_id' => $deviceId, 'cpu' => 'Core i5', 'ram_gb' => 8]);

        $this->actingAs($superAdmin)->get('/employees/'.$employeeId)
            ->assertOk()->assertSee('Lê Hoa')->assertSee('Dell Latitude')->assertSee('Core i5');

        $response = $this->post('/employees/'.$employeeId.'/bulk-revoke', [
            'returned_condition' => 'broken', 'reason' => 'Bàn giao khi nghỉ việc',
            'notes' => 'Màn hình lỗi', 'mark_as_resigned' => '1',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('employees', ['id' => $employeeId, 'status' => 'resigned']);
        $this->assertDatabaseHas('devices', [
            'id' => $deviceId, 'status' => 'maintenance', 'condition_state' => 'broken',
            'current_employee_id' => null,
        ]);
        $this->assertDatabaseHas('ticket_details', [
            'device_id' => $deviceId, 'returned_condition' => 'broken', 'notes' => 'Màn hình lỗi',
        ]);
    }

    public function test_report_filters_and_csv_export_include_legacy_summary_data(): void
    {
        $this->actingAs(User::factory()->create(['id' => 6]));
        $departmentId = DB::table('departments')->insertGetId(['code' => 'IT', 'name' => 'Công nghệ']);
        $employeeId = DB::table('employees')->insertGetId([
            'code' => 'NVIT1', 'fullname' => 'Phạm An', 'department_id' => $departmentId, 'status' => 'working',
        ]);
        $reportDeviceId = DB::table('devices')->insertGetId([
            'device_code' => 'PC-RPT1', 'type' => 'PC', 'brand' => 'Lenovo', 'model' => 'ThinkCentre',
            'serial_number' => 'SR-RPT1', 'import_date' => now()->toDateString(),
            'warranty_expiry' => now()->addDays(10)->toDateString(), 'condition_state' => 'new',
            'status' => 'allocated', 'current_employee_id' => $employeeId, 'current_department_id' => $departmentId,
            'is_temporary' => 1, 'expected_return_date' => now()->subDay()->toDateString(),
        ]);
        DB::table('device_specs')->insert([
            'device_id' => $reportDeviceId, 'cpu' => 'Core i5-12400', 'ram_gb' => 16,
            'ram_type' => 'DDR4', 'ssd_capacity' => '512GB',
        ]);
        DB::table('device_monitors')->insert([
            'device_id' => $reportDeviceId, 'monitor_no' => 1, 'brand' => 'Dell', 'model' => 'P2422H', 'size' => 24,
        ]);
        DB::table('devices')->insert([
            'device_code' => 'LT-OTHER', 'type' => 'Laptop', 'brand' => 'Dell', 'model' => 'Other',
            'import_date' => now()->toDateString(), 'condition_state' => 'new', 'status' => 'in_stock',
        ]);

        $filterQuery = http_build_query([
            'type' => 'PC', 'brand' => 'Lenovo', 'condition_state' => 'new', 'department_id' => $departmentId,
            'employee_id' => $employeeId, 'cpu' => 'Core i5', 'ram_gb' => 8, 'ssd_capacity' => '512',
            'is_overdue' => 1,
        ]);
        $this->get('/reports?'.$filterQuery)
            ->assertOk()->assertSee('PC-RPT1')->assertDontSee('LT-OTHER')->assertSee('Thiết bị mượn tạm quá hạn');

        $response = $this->get('/reports/export?'.$filterQuery);
        $response->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $csv = $response->streamedContent();
        $this->assertStringContainsString('SR-RPT1', $csv);
        $this->assertStringContainsString('Core i5-12400', $csv);
        $this->assertStringContainsString('Dell P2422H 24 inch', $csv);
        $this->assertSame(2, count(array_filter(explode("\n", trim($csv)))));

        $inventoryExport = $this->get('/devices/export?q=PC-RPT1');
        $inventoryExport->assertOk();
        $inventoryCsv = $inventoryExport->streamedContent();
        $this->assertStringContainsString('SR-RPT1', $inventoryCsv);
        $this->assertSame(2, count(array_filter(explode("\n", trim($inventoryCsv)))));
    }

    public function test_print_route_renders_formal_ticket_document(): void
    {
        $user = User::factory()->create(['id' => 6]);
        $departmentId = DB::table('departments')->insertGetId(['code' => 'OPS', 'name' => 'Vận hành']);
        $employeeId = DB::table('employees')->insertGetId([
            'code' => 'NVOPS', 'fullname' => 'Đỗ Bình', 'department_id' => $departmentId,
            'position' => 'Kỹ thuật viên', 'status' => 'working',
        ]);
        $deviceId = DB::table('devices')->insertGetId([
            'device_code' => 'PC-PRINT', 'type' => 'PC', 'brand' => 'HP', 'model' => 'ProDesk',
            'serial_number' => 'PRINT-001', 'import_date' => '2026-01-01', 'condition_state' => 'new', 'status' => 'allocated',
        ]);
        DB::table('device_specs')->insert(['device_id' => $deviceId, 'cpu' => 'Core i7', 'ram_gb' => 16, 'ssd_capacity' => '512GB']);
        $ticketId = DB::table('tickets')->insertGetId([
            'ticket_code' => 'PX-PRINT-1', 'ticket_type' => 'allocation', 'ticket_date' => '2026-10-28',
            'created_by' => $user->id, 'to_employee_id' => $employeeId, 'to_department_id' => $departmentId,
            'is_temporary' => 1, 'expected_return_date' => '2026-11-28', 'status' => 'completed',
        ]);
        DB::table('ticket_details')->insert([
            'ticket_id' => $ticketId, 'device_id' => $deviceId, 'accessories' => 'Sạc, chuột',
        ]);

        $this->actingAs($user)->get('/tickets/'.$ticketId.'/print')
            ->assertOk()->assertSee('Biên bản bàn giao thiết bị CNTT')
            ->assertSee('Đỗ Bình')->assertSee('PC-PRINT')->assertSee('Core i7')
            ->assertSee('Sạc, chuột')->assertSee('BÊN GIAO')->assertSee('NGƯỜI LẬP PHIẾU');
    }

    public function test_employee_detail_report_and_print_routes_remain_permission_gated(): void
    {
        $user = User::factory()->create(['role' => 'warehouse']);

        $this->actingAs($user)->get('/employees/1')->assertForbidden();
        $this->get('/reports/export')->assertForbidden();
        $this->get('/tickets/1/print')->assertForbidden();
    }
}
