<?php

namespace App\Containers\AppSection\Media\Tests\Unit;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\Tasks\FindMediaByIdTask;
use App\Containers\AppSection\Media\Tests\TestCase;
use App\Ship\Exceptions\NotFoundException;

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
        $media = Media::factory()->create();

        $foundMedia = app(FindMediaByIdTask::class)->run($media->id);

        $this->assertEquals($media->id, $foundMedia->id);
    }

    public function testFindMediaWithInvalidId(): void
    {
        $this->expectException(NotFoundException::class);

        $noneExistingId = 777777;

        app(FindMediaByIdTask::class)->run($noneExistingId);
    }
}
