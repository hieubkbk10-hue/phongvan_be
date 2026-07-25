<?php

use App\Containers\AppSection\Order\Models\OrderItem;
use App\Containers\AppSection\Product\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table(OrderItem::getTableName(), function (Blueprint $table) {
                $table->dropForeign(['product_id']);
            });

            DB::statement('ALTER TABLE ' . OrderItem::getTableName() . ' MODIFY product_id BIGINT UNSIGNED NULL');

            Schema::table(OrderItem::getTableName(), function (Blueprint $table) {
                $table->foreign('product_id')
                    ->references('id')
                    ->on(Product::getTableName())
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table(OrderItem::getTableName(), function (Blueprint $table) {
                $table->dropForeign(['product_id']);
            });

            DB::statement('ALTER TABLE ' . OrderItem::getTableName() . ' MODIFY product_id BIGINT UNSIGNED NOT NULL');

            Schema::table(OrderItem::getTableName(), function (Blueprint $table) {
                $table->foreign('product_id')
                    ->references('id')
                    ->on(Product::getTableName());
            });
        }
    }
};
