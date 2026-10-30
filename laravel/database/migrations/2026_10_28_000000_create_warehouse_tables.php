<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 40)->unique();
            $table->string('name', 100);
        });
        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 80)->unique();
            $table->string('name', 150);
        });
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->unsignedInteger('role_id');
            $table->unsignedInteger('permission_id');
            $table->unique(['role_id', 'permission_id']);
        });
        Schema::create('departments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->dateTime('created_at')->useCurrent();
        });
        Schema::create('employees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 20)->unique();
            $table->string('fullname', 100);
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->integer('department_id');
            $table->string('position', 100)->nullable();
            $table->string('status', 30)->default('working');
            $table->dateTime('created_at')->useCurrent();
        });
        Schema::create('devices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('device_code', 50)->unique();
            $table->string('type', 50);
            $table->string('brand', 50);
            $table->string('model', 100);
            $table->string('serial_number', 100)->nullable();
            $table->date('import_date');
            $table->date('warranty_expiry')->nullable();
            $table->string('condition_state', 30)->default('new');
            $table->string('status', 30)->default('in_stock');
            $table->integer('current_employee_id')->nullable();
            $table->integer('current_department_id')->nullable();
            $table->boolean('is_temporary')->default(false);
            $table->date('expected_return_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        Schema::create('device_specs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('device_id')->unique();
            $table->string('cpu', 100)->nullable();
            $table->string('mainboard', 100)->nullable();
            $table->integer('ram_gb')->nullable();
            $table->string('ram_type', 50)->nullable();
            $table->string('ssd_capacity', 50)->nullable();
            $table->string('hdd_capacity', 50)->nullable();
            $table->string('gpu', 100)->nullable();
            $table->string('screen_size', 50)->nullable();
            $table->text('notes')->nullable();
        });
        Schema::create('device_monitors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('device_id');
            $table->integer('monitor_no');
            $table->string('brand', 100);
            $table->string('model', 100)->nullable();
            $table->decimal('size', 4, 1)->nullable();
        });
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ticket_code', 50)->unique();
            $table->string('ticket_type', 30);
            $table->date('ticket_date');
            $table->integer('created_by');
            $table->integer('from_employee_id')->nullable();
            $table->integer('from_department_id')->nullable();
            $table->integer('to_employee_id')->nullable();
            $table->integer('to_department_id')->nullable();
            $table->boolean('is_temporary')->default(false);
            $table->date('expected_return_date')->nullable();
            $table->string('status', 30)->default('completed');
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('created_at')->useCurrent();
        });
        Schema::create('ticket_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ticket_id');
            $table->integer('device_id');
            $table->string('accessories', 255)->nullable();
            $table->string('returned_condition', 30)->nullable();
            $table->text('notes')->nullable();
            $table->unique(['ticket_id', 'device_id']);
        });
        Schema::create('device_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('device_id');
            $table->integer('ticket_id')->nullable();
            $table->string('action_type', 50);
            $table->dateTime('action_date')->useCurrent();
            $table->integer('performed_by');
            $table->integer('from_employee_id')->nullable();
            $table->integer('to_employee_id')->nullable();
            $table->integer('from_department_id')->nullable();
            $table->integer('to_department_id')->nullable();
            $table->text('description');
        });
        Schema::create('maintenances', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('device_id');
            $table->string('maintenance_type', 30)->default('repair');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('cost', 12, 2)->nullable();
            $table->string('provider', 100)->nullable();
            $table->text('description');
            $table->string('status', 30)->default('in_progress');
            $table->integer('created_by');
            $table->dateTime('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenances');
        Schema::dropIfExists('device_history');
        Schema::dropIfExists('ticket_details');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('device_monitors');
        Schema::dropIfExists('device_specs');
        Schema::dropIfExists('devices');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
