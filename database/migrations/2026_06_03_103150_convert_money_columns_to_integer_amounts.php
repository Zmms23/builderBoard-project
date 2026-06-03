<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subservices', function (Blueprint $table): void {
            $table->unsignedBigInteger('price_amount')->default(0);
        });

        DB::table('subservices')->get()->each(function (object $subservice): void {
            DB::table('subservices')
                ->where('id', $subservice->id)
                ->update(['price_amount' => (int) round(((float) $subservice->price) * 100)]);
        });

        Schema::table('subservices', function (Blueprint $table): void {
            $table->dropColumn('price');
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->unsignedBigInteger('estimated_price_amount')->default(0);
        });

        DB::table('orders')->get()->each(function (object $order): void {
            DB::table('orders')
                ->where('id', $order->id)
                ->update(['estimated_price_amount' => (int) round(((float) $order->estimated_price) * 100)]);
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn('estimated_price');
        });

        Schema::table('order_items', function (Blueprint $table): void {
            $table->unsignedBigInteger('unit_price_amount')->default(0);
            $table->unsignedBigInteger('total_price_amount')->default(0);
        });

        DB::table('order_items')->get()->each(function (object $item): void {
            DB::table('order_items')
                ->where('id', $item->id)
                ->update([
                    'unit_price_amount' => (int) round(((float) $item->unit_price) * 100),
                    'total_price_amount' => (int) round(((float) $item->total_price) * 100),
                ]);
        });

        Schema::table('order_items', function (Blueprint $table): void {
            $table->dropColumn(['unit_price', 'total_price']);
        });
    }

    public function down(): void
    {
        Schema::table('subservices', function (Blueprint $table): void {
            $table->decimal('price', 10, 2)->default(0);
        });

        DB::table('subservices')->get()->each(function (object $subservice): void {
            DB::table('subservices')
                ->where('id', $subservice->id)
                ->update(['price' => ((int) $subservice->price_amount) / 100]);
        });

        Schema::table('subservices', function (Blueprint $table): void {
            $table->dropColumn('price_amount');
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->decimal('estimated_price', 12, 2)->default(0);
        });

        DB::table('orders')->get()->each(function (object $order): void {
            DB::table('orders')
                ->where('id', $order->id)
                ->update(['estimated_price' => ((int) $order->estimated_price_amount) / 100]);
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn('estimated_price_amount');
        });

        Schema::table('order_items', function (Blueprint $table): void {
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
        });

        DB::table('order_items')->get()->each(function (object $item): void {
            DB::table('order_items')
                ->where('id', $item->id)
                ->update([
                    'unit_price' => ((int) $item->unit_price_amount) / 100,
                    'total_price' => ((int) $item->total_price_amount) / 100,
                ]);
        });

        Schema::table('order_items', function (Blueprint $table): void {
            $table->dropColumn(['unit_price_amount', 'total_price_amount']);
        });
    }
};