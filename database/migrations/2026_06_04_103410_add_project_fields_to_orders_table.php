<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->foreignId('project_id')
                ->nullable()
                ->after('client_id')
                ->constrained()
                ->nullOnDelete();

            $table->date('deadline')->nullable()->after('status');
            $table->unsignedTinyInteger('progress')->default(0)->after('deadline');
        });

        DB::table('projects')
            ->whereNotNull('order_id')
            ->orderBy('id')
            ->get()
            ->each(function (object $project): void {
                DB::table('orders')
                    ->where('id', $project->order_id)
                    ->update(['project_id' => $project->id]);
            });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('project_id');
            $table->dropColumn(['deadline', 'progress']);
        });
    }
};
