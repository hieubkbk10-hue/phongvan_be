<?php

namespace App\Containers\AppSection\Order\Tasks;

use App\Containers\AppSection\Customer\Data\Repositories\CustomerRepository;
use App\Containers\AppSection\Customer\Models\Customer;
use App\Ship\Exceptions\ValidationFailedException;
use App\Ship\Parents\Tasks\Task as ParentTask;
use Exception;

class ResolveOrderCustomerTask extends ParentTask
{
    public function __construct(
        protected CustomerRepository $customerRepository
    ) {
    }

    /**
     * @param int|null $customerId
     * @param string|null $name
     * @param string|null $phone
     * @param string|null $address
     * @return array{customer_id: int, name_snapshot: string, phone_snapshot: string, address_snapshot: string}
     * @throws ValidationFailedException
     */
    public function run(?int $customerId, ?string $name = null, ?string $phone = null, ?string $address = null): array
    {
        if ($customerId !== null) {
            try {
                /** @var Customer $customer */
                $customer = $this->customerRepository->find($customerId);
            } catch (Exception) {
                throw (new ValidationFailedException('Customer not found.'))->withErrors(['customer_id' => ['Customer not found.']]);
            }

            return [
                'customer_id' => (int) $customer->id,
                'name_snapshot' => $name ?? $customer->name,
                'phone_snapshot' => $phone ?? $customer->phone,
                'address_snapshot' => $address ?? $customer->address,
            ];
        }

        if (empty($phone) || empty($name) || empty($address)) {
            throw (new ValidationFailedException('Customer name, phone, and address are required when customer_id is not provided.'))
                ->withErrors(['customer' => ['Customer name, phone, and address are required.']]);
        }

        // Clean phone for lookup
        $cleanPhone = str_replace([' ', '-', '(', ')'], '', $phone);

        // Check if customer exists (active or soft-deleted)
        $existingCustomer = Customer::withTrashed()->where('phone', $cleanPhone)->first();
        if ($existingCustomer) {
            throw (new ValidationFailedException('Customer phone number already exists.'))
                ->withErrors(['customer_phone_snapshot' => ['Customer phone number already exists.']]);
        }

        /** @var Customer $newCustomer */
        $newCustomer = $this->customerRepository->create([
            'name' => $name,
            'phone' => $cleanPhone,
            'address' => $address,
        ]);

        return [
            'customer_id' => (int) $newCustomer->id,
            'name_snapshot' => $name,
            'phone_snapshot' => $cleanPhone,
            'address_snapshot' => $address,
        ];
    }
}
