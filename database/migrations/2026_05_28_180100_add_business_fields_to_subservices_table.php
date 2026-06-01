<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subservices', function (Blueprint $table): void {
            $table->string('pricing_type')->default('fixed')->after('price');
            $table->string('unit')->default('service')->after('pricing_type');
            $table->string('estimated_duration')->nullable()->after('unit');
        });
    }

    public function down(): void
    {
        Schema::table('subservices', function (Blueprint $table): void {
            $table->dropColumn([
                'pricing_type',
                'unit',
                'estimated_duration',
            ]);
        });
    }
};
