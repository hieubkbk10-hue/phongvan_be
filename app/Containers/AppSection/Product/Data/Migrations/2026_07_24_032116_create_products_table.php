<?php

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
        Schema::create(Product::getTableName(), function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->decimal('price', 15, 2)->default(0.00);
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Product::getTableName());
    }
};
