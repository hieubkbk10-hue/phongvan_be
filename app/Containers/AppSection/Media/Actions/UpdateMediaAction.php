<?php

namespace App\Containers\AppSection\Media\Actions;

use Apiato\Core\Exceptions\IncorrectIdException;
use App\Containers\AppSection\Media\Models\Media;
use App\Containers\AppSection\Media\Tasks\UpdateMediaTask;
use App\Containers\AppSection\Media\UI\API\Requests\UpdateMediaRequest;
use App\Ship\Exceptions\NotFoundException;
use App\Ship\Exceptions\UpdateResourceFailedException;
use App\Ship\Parents\Actions\Action as ParentAction;

class UpdateMediaAction extends ParentAction
{
    /**
     * @param UpdateMediaRequest $request
     * @return Media
     * @throws UpdateResourceFailedException
     * @throws IncorrectIdException
     * @throws NotFoundException
     */
    public function run(UpdateMediaRequest $request): Media
    {
        $data = $request->sanitizeInput([
            // add your request data here
        ]);

        return app(UpdateMediaTask::class)->run($data, $request->id);
    }
}
