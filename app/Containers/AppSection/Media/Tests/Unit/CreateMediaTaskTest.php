<?php

namespace App\Containers\AppSection\Media\Tests\Unit;

use App\Containers\AppSection\Media\Tasks\CreateMediaTask;
use App\Containers\AppSection\Media\Tests\TestCase;
use App\Ship\Exceptions\CreateResourceFailedException;

/**
 * Class CreateMediaTaskTest.
 *
 * @group media
 * @group unit
 */
class CreateMediaTaskTest extends TestCase
{
    public function testCreateMedia(): void
    {
        $data = [];

        $media = app(CreateMediaTask::class)->run($data);

        $this->assertModelExists($media);
    }

    // TODO TEST
//    public function testCreateMediaWithInvalidData(): void
//    {
//        $this->expectException(CreateResourceFailedException::class);
//
//        $data = [
//            // put some invalid data here
//            // 'invalid' => 'data',
//        ];
//
//        app(CreateMediaTask::class)->run($data);
//    }
}
