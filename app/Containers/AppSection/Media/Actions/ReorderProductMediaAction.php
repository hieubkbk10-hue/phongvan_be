<?php

namespace App\Containers\AppSection\Media\Actions;

use App\Containers\AppSection\Media\Tasks\UpdateMediaTask;
use App\Ship\Exceptions\UpdateResourceFailedException;
use App\Ship\Parents\Actions\Action as ParentAction;
use Illuminate\Support\Facades\DB;
use Throwable;

class ReorderProductMediaAction extends ParentAction
{
    /**
     * Cập nhật thứ tự sắp xếp cho danh sách Media của sản phẩm.
     *
     * @param array<int, array{id: int, sort_order: int}> $items Danh sách object chứa id và sort_order
     * @return bool
     * @throws UpdateResourceFailedException
     * @throws Throwable
     */
    public function run(array $items): bool
    {
        return DB::transaction(function () use ($items) {
            foreach ($items as $item) {
                if (isset($item['id'], $item['sort_order'])) {
                    app(UpdateMediaTask::class)->run(
                        ['sort_order' => (int) $item['sort_order']],
                        $item['id']
                    );
                }
            }

            return true;
        });
    }
}
