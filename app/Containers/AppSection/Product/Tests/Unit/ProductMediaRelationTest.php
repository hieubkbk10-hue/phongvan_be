<?php

namespace App\Containers\AppSection\Product\Tests\Unit;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Product\Models\Product;
use App\Containers\AppSection\Product\Tests\TestCase;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Class ProductMediaRelationTest.
 *
 * @group product
 * @group unit
 */
class ProductMediaRelationTest extends TestCase
{
    public function testProductHasMorphManyMediaRelation(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();

        $this->assertInstanceOf(MorphMany::class, $product->media());

        /** @var Media $media */
        $media = Media::create([
            'disk' => 'public',
            'path' => 'products/sample.jpg',
            'filename' => 'sample.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 1024,
            'sort_order' => 1,
            'is_main' => true,
            'mediable_type' => Product::class,
            'mediable_id' => $product->id,
        ]);

        $this->assertTrue($product->media->contains($media));
        /** @var Product $mediable */
        $mediable = $media->mediable;
        $this->assertEquals($product->id, $mediable->id);
    }
}
