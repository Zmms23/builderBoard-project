<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('project_tasks');
    }

    public function down(): void
    {
        Schema::create('project_tasks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_to_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('status')->default('todo');
            $table->unsignedSmallInteger('sort')->default(0);
            $table->date('deadline')->nullable();
            $table->unsignedBigInteger('budget_amount')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
};
