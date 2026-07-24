<?php

namespace App\Containers\AppSection\Media\UI\API\Tests\Functional;

use App\Containers\AppSection\Media\UI\API\Tests\ApiTestCase;

/**
 * Class UpdateMediaTest.
 *
 * @group media
 * @group api
 */
class UpdateMediaTest extends ApiTestCase
{
    public function testLegacyRouteDisabled(): void
    {
        $response = $this->endpoint('patch@v1/media/1')->makeCall();
        $this->assertTrue(in_array($response->getStatusCode(), [404, 405], true));
    }
}
