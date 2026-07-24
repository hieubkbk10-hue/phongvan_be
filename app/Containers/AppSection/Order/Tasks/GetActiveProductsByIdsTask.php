<?php

namespace App\Containers\AppSection\Order\Tasks;

use App\Containers\AppSection\Product\Models\Product;
use App\Ship\Exceptions\ValidationFailedException;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Database\Eloquent\Collection;

class GetActiveProductsByIdsTask extends ParentTask
{
    /**
     * @param array<int> $productIds
     * @return Collection<int, Product>
     * @throws ValidationFailedException
     */
    public function run(array $productIds): Collection
    {
        $uniqueIds = array_unique(array_map('intval', $productIds));

        if (empty($uniqueIds)) {
            throw (new ValidationFailedException('Items list cannot be empty.'))->withErrors(['items' => ['Items list cannot be empty.']]);
        }

        $products = Product::whereIn('id', $uniqueIds)
            ->where('status', Product::STATUS_ACTIVE)
            ->get()
            ->keyBy('id');

        if (count($products) !== count($uniqueIds)) {
            throw (new ValidationFailedException('One or more products are invalid or inactive.'))
                ->withErrors(['items' => ['One or more products are invalid or inactive.']]);
        }

        return $products;
    }
}
