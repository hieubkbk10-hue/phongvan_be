<?php

namespace App\Containers\AppSection\Media\Tests\Unit;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\Tasks\DeleteMediaTask;
use App\Containers\AppSection\Media\Tests\TestCase;
use App\Ship\Exceptions\NotFoundException;

/**
 * Class DeleteMediaTaskTest.
 *
 * @group media
 * @group unit
 */
class DeleteMediaTaskTest extends TestCase
{
    public function testDeleteMedia(): void
    {
        $media = Media::factory()->create();

        $result = app(DeleteMediaTask::class)->run($media->id);

        $this->assertEquals(1, $result);
    }

    public function testDeleteMediaWithInvalidId(): void
    {
        $this->expectException(NotFoundException::class);

        $noneExistingId = 777777;

        app(DeleteMediaTask::class)->run($noneExistingId);
    }
}
