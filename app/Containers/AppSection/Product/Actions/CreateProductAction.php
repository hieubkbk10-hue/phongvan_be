<?php

namespace App\Containers\AppSection\Product\Actions;

use Apiato\Core\Exceptions\IncorrectIdException;
use App\Containers\AppSection\Product\Models\Product;
use App\Containers\AppSection\Product\Tasks\CreateProductTask;
use App\Containers\AppSection\Product\UI\API\Requests\CreateProductRequest;
use App\Ship\Exceptions\CreateResourceFailedException;
use App\Ship\Parents\Actions\Action as ParentAction;

class CreateProductAction extends ParentAction
{
    /**
     * @param CreateProductRequest $request
     * @return Product
     * @throws CreateResourceFailedException
     * @throws IncorrectIdException
     */
    public function run(CreateProductRequest $request): Product
    {
        $data = $request->sanitizeInput([
            'name',
            'price',
            'status',
        ]);

        if (!isset($data['status'])) {
            $data['status'] = Product::STATUS_ACTIVE;
        }

        return app(CreateProductTask::class)->run($data);
    }
}
