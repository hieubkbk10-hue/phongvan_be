<?php

use App\Containers\AppSection\Order\Models\Order;
use App\Containers\AppSection\Order\Models\OrderItem;
use App\Containers\AppSection\Product\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(OrderItem::getTableName(), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->string('product_name_snapshot', 255);
            $table->decimal('unit_price', 15, 2);
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('total_item_price', 15, 2);
            $table->timestamps();

            // Foreign keys
            $table->foreign('order_id')->references('id')->on(Order::getTableName())->cascadeOnDelete();
            $table->foreign('product_id')->references('id')->on(Product::getTableName());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(OrderItem::getTableName());
    }
};
