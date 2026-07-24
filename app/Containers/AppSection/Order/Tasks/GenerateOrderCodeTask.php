<?php

namespace App\Containers\AppSection\Order\Tasks;

use App\Containers\AppSection\Order\Data\Repositories\OrderRepository;
use App\Ship\Exceptions\CreateResourceFailedException;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Illuminate\Support\Str;

class GenerateOrderCodeTask extends ParentTask
{
    public function __construct(
        protected OrderRepository $orderRepository
    ) {
    }

    /**
     * @return string
     * @throws CreateResourceFailedException
     */
    public function run(): string
    {
        $maxAttempts = 5;

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $datePrefix = date('Ymd');
            $randomSuffix = strtoupper(Str::random(4));
            $code = "ORD-{$datePrefix}-{$randomSuffix}";

            $exists = $this->orderRepository->findWhere(['code' => $code])->isNotEmpty();

            if (!$exists) {
                return $code;
            }
        }

        throw new CreateResourceFailedException('Unable to generate unique order code after maximum attempts.');
    }
}
