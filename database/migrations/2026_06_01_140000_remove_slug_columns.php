<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subservices', function (Blueprint $table): void {
            $table->dropUnique('subservices_service_id_slug_unique');
            $table->dropColumn('slug');
            $table->unique(['service_id', 'name']);
        });

        Schema::table('services', function (Blueprint $table): void {
            $table->dropUnique('services_company_id_slug_unique');
            $table->dropColumn('slug');
            $table->unique(['company_id', 'name']);
        });

        Schema::table('companies', function (Blueprint $table): void {
            $table->dropUnique('companies_slug_unique');
            $table->dropColumn('slug');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->string('slug')->nullable()->after('name');
            $table->unique('slug');
        });

        Schema::table('services', function (Blueprint $table): void {
            $table->dropUnique('services_company_id_name_unique');
            $table->string('slug')->nullable()->after('name');
            $table->unique(['company_id', 'slug']);
        });

        Schema::table('subservices', function (Blueprint $table): void {
            $table->dropUnique('subservices_service_id_name_unique');
            $table->string('slug')->nullable()->after('name');
            $table->unique(['service_id', 'slug']);
        });
    }
};
