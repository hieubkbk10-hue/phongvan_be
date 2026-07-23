<?php

namespace App\Containers\AppSection\Media\Tests\Unit;

use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\Tasks\GetAllMediaTask;
use App\Containers\AppSection\Media\Tests\TestCase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
        Media::factory()->count(3)->create();

        $foundMedia = app(GetAllMediaTask::class)->run();

        $this->assertCount(3, $foundMedia);
        $this->assertInstanceOf(LengthAwarePaginator::class, $foundMedia);
    }
}
