<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone', 30)->unique();
            $table->string('company');
            $table->unsignedInteger('company_employee_count');
            $table->text('address');
            $table->decimal('project_budget', 15, 2);
            $table->decimal('service_charge', 16, 2);
            $table->decimal('discount_value', 16, 2);
            $table->decimal('final_project_value', 16, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
