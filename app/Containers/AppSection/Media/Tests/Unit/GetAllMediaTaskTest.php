<?php

namespace App\Containers\AppSection\Media\Tests\Unit;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\Tasks\GetAllMediaTask;
use App\Containers\AppSection\Media\Tests\TestCase;

/**
 * Class GetAllMediaTaskTest.
 *
 * @group media
 * @group unit
 */
class GetAllMediaTaskTest extends TestCase
{
    public function testGetAllMedia(): void
    {
        Media::factory()->count(2)->create();

        $result = app(GetAllMediaTask::class)->run();

        $this->assertGreaterThanOrEqual(2, count($result));
    }
}
