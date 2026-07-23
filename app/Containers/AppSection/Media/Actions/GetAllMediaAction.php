<?php

namespace App\Containers\AppSection\Media\Actions;

use Apiato\Core\Exceptions\CoreInternalErrorException;
use App\Containers\AppSection\Media\Tasks\GetAllMediaTask;
use App\Containers\AppSection\Media\UI\API\Requests\GetAllMediaRequest;
use App\Ship\Parents\Actions\Action as ParentAction;
use Prettus\Repository\Exceptions\RepositoryException;

class GetAllMediaAction extends ParentAction
{
    /**
     * @throws CoreInternalErrorException
     * @throws RepositoryException
     */
    public function run(GetAllMediaRequest $request): mixed
    {
        return app(GetAllMediaTask::class)->run();
    }
}
