<?php

namespace App\Containers\AppSection\Media\Tests\Unit;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\Tasks\FindMediaByIdTask;
use App\Containers\AppSection\Media\Tests\TestCase;

/**
 * Class FindMediaByIdTaskTest.
 *
 * @group media
 * @group unit
 */
class FindMediaByIdTaskTest extends TestCase
{
    public function testFindMediaById(): void
    {
        /** @var Media $media */
        $media = Media::factory()->create();

        $found = app(FindMediaByIdTask::class)->run($media->id);

        $this->assertEquals($media->id, $found->id);
    }
}
