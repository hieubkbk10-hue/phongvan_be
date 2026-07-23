<?php

use App\Containers\AppSection\Media\Models\Media;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(Media::getTableName(), function (Blueprint $table) {
            $table->id();

            // Column quan hệ polymorphic (tự động tạo mediable_type, mediable_id và INDEX, không tạo foreign key)
            $table->nullableMorphs('mediable');

            // Column thông tin file
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('filename');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);

            // Column thứ tự và ảnh chính
            $table->integer('sort_order')->default(0);
            $table->boolean('is_main')->default(false);

            $table->timestamps();

            // Index sắp xếp
            $table->index('sort_order');
            $table->index('is_main');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Media::getTableName());
    }
};
