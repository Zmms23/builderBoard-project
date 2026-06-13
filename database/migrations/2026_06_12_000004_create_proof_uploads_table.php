<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proof_uploads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('photo_path');
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('expense_amount')->default(0);
            $table->boolean('is_client_visible')->default(true);
            $table->timestamps();

            $table->index(['company_id', 'order_id']);
            $table->index(['company_id', 'uploaded_by_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proof_uploads');
    }
};
