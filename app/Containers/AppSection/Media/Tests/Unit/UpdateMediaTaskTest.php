<?php

namespace App\Containers\AppSection\Media\Tests\Unit;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\Tasks\UpdateMediaTask;
use App\Containers\AppSection\Media\Tests\TestCase;
use App\Ship\Exceptions\NotFoundException;

/**
 * Class UpdateMediaTaskTest.
 *
 * @group media
 * @group unit
 */
class UpdateMediaTaskTest extends TestCase
{
    // TODO TEST
    public function testUpdateMedia(): void
    {
        $media = Media::factory()->create([
            // 'some_field' => 'new_field_value',
        ]);
        $data = [
            // 'some_field' => 'new_field_value',
        ];

        $updatedMedia = app(UpdateMediaTask::class)->run($data, $media->id);

        $this->assertEquals($media->id, $updatedMedia->id);
        // assert if fields are updated
        // $this->assertEquals($data['some_field'], $updatedMedia->some_field);
    }

    public function testUpdateMediaWithInvalidId(): void
    {
        $this->expectException(NotFoundException::class);

        $noneExistingId = 777777;

        app(UpdateMediaTask::class)->run([], $noneExistingId);
    }
}
