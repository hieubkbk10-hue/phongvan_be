<?php

namespace App\Containers\AppSection\Media\UI\API\Tests\Functional;

use App\Containers\AppSection\Media\UI\API\Tests\ApiTestCase;

/**
 * Class CreateMediaTest.
 *
 * @group media
 * @group api
 */
class CreateMediaTest extends ApiTestCase
{
    public function testLegacyRouteDisabled(): void
    {
        $response = $this->endpoint('post@v1/media')->makeCall();
        $this->assertTrue(in_array($response->getStatusCode(), [404, 405], true));
    }
}
