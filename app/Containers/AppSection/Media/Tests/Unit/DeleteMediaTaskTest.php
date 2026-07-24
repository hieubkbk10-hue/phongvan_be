<?php

namespace App\Containers\AppSection\Media\Tests\Unit;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\Tests\TestCase;

/**
 * Class DeleteMediaTaskTest.
 *
 * @group media
 * @group unit
 */
class DeleteMediaTaskTest extends TestCase
{
    public function testDeleteMediaRecord(): void
    {
        /** @var Media $media */
        $media = Media::factory()->create();

        $result = $media->delete();

        $this->assertTrue((bool) $result);
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }
}
