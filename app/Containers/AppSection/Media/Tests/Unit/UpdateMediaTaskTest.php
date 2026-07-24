<?php

namespace App\Containers\AppSection\Media\Tests\Unit;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\Tasks\UpdateMediaTask;
use App\Containers\AppSection\Media\Tests\TestCase;

/**
 * Class UpdateMediaTaskTest.
 *
 * @group media
 * @group unit
 */
class UpdateMediaTaskTest extends TestCase
{
    public function testUpdateMedia(): void
    {
        /** @var Media $media */
        $media = Media::factory()->create();

        $updated = app(UpdateMediaTask::class)->run(['sort_order' => 99], $media->id);

        $this->assertEquals(99, $updated->sort_order);
    }
}
