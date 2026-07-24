<?php

namespace App\Containers\AppSection\Media\Tests\Unit;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\Tests\TestCase;

/**
 * Class MediaFactoryTest.
 *
 * @group media
 * @group unit
 */
class MediaFactoryTest extends TestCase
{
    public function testCreateMedia(): void
    {
        $media = Media::factory()->create();

        $this->assertInstanceOf(Media::class, $media);
        $this->assertModelExists($media);
    }
}
