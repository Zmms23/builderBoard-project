<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->foreignId('assigned_user_id')
                ->nullable()
                ->after('client_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->index(['company_id', 'assigned_user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropIndex(['company_id', 'assigned_user_id']);
            $table->dropConstrainedForeignId('assigned_user_id');
        });
    }
};
