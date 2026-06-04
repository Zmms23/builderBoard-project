<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_tasks', function (Blueprint $table): void {
            $table->foreignId('order_id')
                ->nullable()
                ->after('project_id')
                ->constrained()
                ->nullOnDelete();
        });

        DB::table('project_tasks')
            ->whereNull('order_id')
            ->orderBy('id')
            ->get()
            ->each(function (object $task): void {
                $orderId = DB::table('orders')
                    ->where('project_id', $task->project_id)
                    ->orderBy('id')
                    ->value('id');

                if (! $orderId) {
                    return;
                }

                DB::table('project_tasks')
                    ->where('id', $task->id)
                    ->update(['order_id' => $orderId]);
            });
    }

    public function down(): void
    {
        Schema::table('project_tasks', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('order_id');
        });
    }
};
