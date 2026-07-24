<?php

use App\Containers\AppSection\Order\Models\Order;
use App\Containers\AppSection\Order\Models\OrderItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table(OrderItem::getTableName(), function (Blueprint $table) {
            $table->decimal('product_price_snapshot', 15, 2)->after('product_name_snapshot');
            $table->string('price_override_reason', 255)->nullable()->after('unit_price');
        });

        Schema::table(Order::getTableName(), function (Blueprint $table) {
            $table->index(['created_at', 'id']);
            $table->index(['status', 'created_at', 'id']);
            $table->index(['payment_method', 'created_at', 'id']);
        });
    }

    public function down(): void
    {
        Schema::table(Order::getTableName(), function (Blueprint $table) {
            $table->dropIndex(['created_at', 'id']);
            $table->dropIndex(['status', 'created_at', 'id']);
            $table->dropIndex(['payment_method', 'created_at', 'id']);
        });

        Schema::table(OrderItem::getTableName(), function (Blueprint $table) {
            $table->dropColumn(['product_price_snapshot', 'price_override_reason']);
        });
    }
};
