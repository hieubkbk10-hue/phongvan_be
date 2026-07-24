<?php

use App\Containers\AppSection\Customer\Models\Customer;
use App\Containers\AppSection\Order\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(Order::getTableName(), function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name_snapshot', 150);
            $table->string('customer_phone_snapshot', 20);
            $table->text('customer_address_snapshot');
            $table->date('delivery_date')->nullable();
            $table->string('shipping_carrier', 100)->nullable();
            $table->unsignedTinyInteger('payment_method');
            $table->unsignedSmallInteger('credit_days')->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('shipping_fee', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('advance_payment', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2)->default(0);
            $table->unsignedTinyInteger('status')->default(1);
            $table->string('cancel_reason', 255)->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('customer_id')->references('id')->on(Customer::getTableName())->nullOnDelete();

            // Indexes
            $table->index('customer_id');
            $table->index(['status', 'created_at']);
            $table->index(['payment_method', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Order::getTableName());
    }
};
