<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('subservices', 'slug')) {
            Schema::table('subservices', function (Blueprint $table): void {
                $table->dropUnique('subservices_service_id_slug_unique');
                $table->dropColumn('slug');
            });
        }

        if (! $this->hasIndex('subservices', 'subservices_service_id_name_unique')) {
            Schema::table('subservices', function (Blueprint $table): void {
                $table->unique(['service_id', 'name']);
            });
        }

        if (Schema::hasColumn('services', 'slug')) {
            Schema::table('services', function (Blueprint $table): void {
                $table->dropUnique('services_company_id_slug_unique');
                $table->dropColumn('slug');
            });
        }

        if (! $this->hasIndex('services', 'services_company_id_name_unique')) {
            Schema::table('services', function (Blueprint $table): void {
                $table->unique(['company_id', 'name']);
            });
        }

        if (Schema::hasColumn('companies', 'slug')) {
            Schema::table('companies', function (Blueprint $table): void {
                $table->dropUnique('companies_slug_unique');
                $table->dropColumn('slug');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('companies', 'slug')) {
            Schema::table('companies', function (Blueprint $table): void {
                $table->string('slug')->nullable()->after('name');
                $table->unique('slug');
            });
        }

        if ($this->hasIndex('services', 'services_company_id_name_unique')) {
            Schema::table('services', function (Blueprint $table): void {
                $table->dropUnique('services_company_id_name_unique');
            });
        }

        if (! Schema::hasColumn('services', 'slug')) {
            Schema::table('services', function (Blueprint $table): void {
                $table->string('slug')->nullable()->after('name');
                $table->unique(['company_id', 'slug']);
            });
        }

        if ($this->hasIndex('subservices', 'subservices_service_id_name_unique')) {
            Schema::table('subservices', function (Blueprint $table): void {
                $table->dropUnique('subservices_service_id_name_unique');
            });
        }

        if (! Schema::hasColumn('subservices', 'slug')) {
            Schema::table('subservices', function (Blueprint $table): void {
                $table->string('slug')->nullable()->after('name');
                $table->unique(['service_id', 'slug']);
            });
        }
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        foreach (Schema::getIndexes($table) as $index) {
            if (($index['name'] ?? null) === $indexName) {
                return true;
            }
        }

        return false;
    }
};
