<?php

namespace App\Containers\AppSection\Product\UI\API\Controllers;

use App\Containers\AppSection\Product\Actions\DeleteProductAction;
use App\Containers\AppSection\Product\UI\API\Requests\DeleteProductRequest;
use App\Ship\Exceptions\DeleteResourceFailedException;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

class DeleteProductController extends ApiController
{
    /**
     * @param DeleteProductRequest $request
     * @return JsonResponse
     * @throws DeleteResourceFailedException
     * @throws NotFoundException
     */
    public function deleteProduct(DeleteProductRequest $request): JsonResponse
    {
        app(DeleteProductAction::class)->run($request);

        return $this->noContent();
    }
}
