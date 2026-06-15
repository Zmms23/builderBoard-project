<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndex('company_user', 'user_id', 'company_user_user_id_index');

        $this->addIndex('services', ['company_id', 'is_active'], 'services_company_active_index');
        $this->addIndex('subservices', ['service_id', 'is_active'], 'subservices_service_active_index');

        $this->addIndex('clients', ['company_id', 'status'], 'clients_company_status_index');
        $this->addIndex('clients', ['company_id', 'type'], 'clients_company_type_index');
        $this->addIndex('clients', ['company_id', 'email'], 'clients_company_email_index');

        $this->addIndex('orders', ['company_id', 'project_id'], 'orders_company_project_index');
        $this->addIndex('orders', ['company_id', 'client_id'], 'orders_company_client_index');
        $this->addIndex('orders', ['company_id', 'status'], 'orders_company_status_index');
        $this->addIndex('orders', ['company_id', 'deadline'], 'orders_company_deadline_index');

        $this->addIndex('order_items', ['order_id', 'subservice_id'], 'order_items_order_subservice_index');

        $this->addIndex('projects', ['company_id', 'client_id'], 'projects_company_client_index');
        $this->addIndex('projects', ['company_id', 'status'], 'projects_company_status_index');
        $this->addIndex('projects', ['company_id', 'deadline'], 'projects_company_deadline_index');

        $this->addIndex('project_timeline_stages', ['project_id', 'status'], 'project_timeline_project_status_index');
        $this->addIndex('project_timeline_stages', ['project_id', 'sort'], 'project_timeline_project_sort_index');

        $this->addIndex('payments', ['company_id', 'order_id'], 'payments_company_order_index');
        $this->addIndex('payments', ['company_id', 'project_id'], 'payments_company_project_index');
        $this->addIndex('payments', ['company_id', 'client_id'], 'payments_company_client_index');

        $this->addIndex('proof_uploads', ['company_id', 'project_id'], 'proof_uploads_company_project_index');
        $this->addIndex('proof_uploads', ['company_id', 'is_client_visible'], 'proof_uploads_company_visible_index');
    }

    public function down(): void
    {
        $this->dropIndex('proof_uploads', 'proof_uploads_company_visible_index');
        $this->dropIndex('proof_uploads', 'proof_uploads_company_project_index');

        $this->dropIndex('payments', 'payments_company_client_index');
        $this->dropIndex('payments', 'payments_company_project_index');
        $this->dropIndex('payments', 'payments_company_order_index');

        $this->dropIndex('project_timeline_stages', 'project_timeline_project_sort_index');
        $this->dropIndex('project_timeline_stages', 'project_timeline_project_status_index');

        $this->dropIndex('projects', 'projects_company_deadline_index');
        $this->dropIndex('projects', 'projects_company_status_index');
        $this->dropIndex('projects', 'projects_company_client_index');

        $this->dropIndex('order_items', 'order_items_order_subservice_index');

        $this->dropIndex('orders', 'orders_company_deadline_index');
        $this->dropIndex('orders', 'orders_company_status_index');
        $this->dropIndex('orders', 'orders_company_client_index');
        $this->dropIndex('orders', 'orders_company_project_index');

        $this->dropIndex('clients', 'clients_company_email_index');
        $this->dropIndex('clients', 'clients_company_type_index');
        $this->dropIndex('clients', 'clients_company_status_index');

        $this->dropIndex('subservices', 'subservices_service_active_index');
        $this->dropIndex('services', 'services_company_active_index');

        $this->dropIndex('company_user', 'company_user_user_id_index');
    }

    /**
     * @param  array<int, string>|string  $columns
     */
    private function addIndex(string $table, array|string $columns, string $name): void
    {
        $columns = is_array($columns) ? $columns : [$columns];

        if (! Schema::hasTable($table) || $this->hasIndex($table, $name) || ! $this->hasColumns($table, $columns)) {
            return;
        }

        Schema::table($table, function (Blueprint $schemaTable) use ($columns, $name): void {
            $schemaTable->index($columns, $name);
        });
    }

    private function dropIndex(string $table, string $name): void
    {
        if (! Schema::hasTable($table) || ! $this->hasIndex($table, $name)) {
            return;
        }

        Schema::table($table, function (Blueprint $schemaTable) use ($name): void {
            $schemaTable->dropIndex($name);
        });
    }

    /**
     * @param  array<int, string>  $columns
     */
    private function hasColumns(string $table, array $columns): bool
    {
        foreach ($columns as $column) {
            if (! Schema::hasColumn($table, $column)) {
                return false;
            }
        }

        return true;
    }

    private function hasIndex(string $table, string $name): bool
    {
        foreach (Schema::getIndexes($table) as $index) {
            if (($index['name'] ?? null) === $name) {
                return true;
            }
        }

        return false;
    }
};
