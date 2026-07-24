<?php

namespace App\Containers\AppSection\Customer\Tasks;

use App\Containers\AppSection\Customer\Models\Customer;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Exceptions\ValidationFailedException;
use App\Ship\Parents\Tasks\Task as ParentTask;

class RestoreCustomerTask extends ParentTask
{
    /**
     * @throws NotFoundException
     * @throws ValidationFailedException
     */
    public function run($id): Customer
    {
        $customer = Customer::withTrashed()->find($id);

        if (!$customer) {
            throw new NotFoundException();
        }

        if (!$customer->trashed()) {
            throw new ValidationFailedException('Customer is not deleted.');
        }

        $customer->restore();

        return $customer;
    }
}
