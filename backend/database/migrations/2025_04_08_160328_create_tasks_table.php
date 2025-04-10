<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('employee_name');
            $table->text('task_description');
            $table->date('date');
            $table->decimal('hours_spent', 5, 2);
            $table->decimal('hourly_rate', 10, 2);
            $table->decimal('additional_charges', 10, 2)->nullable();
            $table->decimal('total_remuneration', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
