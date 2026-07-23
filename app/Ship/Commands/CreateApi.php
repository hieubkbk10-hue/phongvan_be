<?php

namespace App\Ship\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use App\Ship\Parents\Commands\ConsoleCommand;

class CreateApi extends ConsoleCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-api {--type=private : private/public}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tạo code API tự động';

    private $type = 'private';
    private $section = '';
    private $container = '';
    private $docversion = 1;
    private $url = '';

    private function generateApi($method, $routeName)
    {
        $stubName = [
            'Create' => 'create',
            'Delete' => 'delete',
            'Find' => 'find',
            'GetAll' => 'getall',
            'Update' => 'update'
        ][$method];

        $model = $this->container;
        $methodName = Str::camel($routeName);
        $requestName = $routeName . 'Request';
        $controllerName = $routeName . 'Controller';
        $actionName = $routeName . 'Action';
        $taskName = $routeName . 'Task';

        $entity = Str::camel($this->container);
        $entities = Str::pluralStudly($entity);
        if ($stubName == 'getall') {
            $entity = $entities;
        }

        $permission = 'manage-' . Str::snake($entities, '-');

        // tạo Route
        $contentRoute = $this->getStubContent('routes/' . $stubName);
        $contentRoute = $this->parseStubContent($contentRoute, [
            'section-name' => $this->section,
            'container-name' => $this->container,
            'permission' => $permission,
            'controller-name' => $controllerName,
            'doc-api-name' => $routeName,
            'doc-endpoint-url' => '/v' . $this->docversion . '/' . $this->url,
            'endpoint-title' => Str::headline($methodName),
            'endpoint-version' => $this->docversion,
            'endpoint-url' => $this->url,
            'method-name' => $methodName,
            'model' => $model
        ]);
        $this->generateFile($this->section . '/' . $this->container . '/UI/API/Routes/' . $routeName . '.v' . $this->docversion . '.' . $this->type, $contentRoute);

        // tạo Request
        $contentRequest = $this->getStubContent('requests/' . $stubName);
        $contentRequest = $this->parseStubContent($contentRequest, [
            'section-name' => $this->section,
            'container-name' => $this->container,
            'class-name' => $requestName,
            'permission' => $permission
        ]);
        $this->generateFile($this->section . '/' . $this->container . '/UI/API/Requests/' . $requestName, $contentRequest);

        // tạo Controller
        $contentController = $this->getStubContent('controllers/' . $stubName);
        $contentController = $this->parseStubContent($contentController, [
            'section-name' => $this->section,
            'container-name' => $this->container,
            'class-name' => $controllerName,
            'model' => $model,
            'method-name' => $methodName,
            'action-name' => $actionName,
            'request-name' => $requestName,
            'entity' => $entity
        ]);
        $this->generateFile($this->section . '/' . $this->container . '/UI/API/Controllers/' . $controllerName, $contentController);

        // tạo Action
        $contentAction = $this->getStubContent('actions/' . $stubName);
        $contentAction = $this->parseStubContent($contentAction, [
            'section-name' => $this->section,
            'container-name' => $this->container,
            'class-name' => $actionName,
            'model' => $model,
            'task-name' => $taskName,
            'request-name' => $requestName,
        ]);
        $this->generateFile($this->section . '/' . $this->container . '/Actions/' . $actionName, $contentAction);

        // tạo Task
        $contentTask = $this->getStubContent('tasks/' . $stubName);
        $contentTask = $this->parseStubContent($contentTask, [
            'section-name' => $this->section,
            'container-name' => $this->container,
            'class-name' => $taskName,
            'model' => $model,
        ]);
        $this->generateFile($this->section . '/' . $this->container . '/Tasks/' . $taskName, $contentTask);

        $classes = [$routeName . ' (Route)', $requestName, $controllerName, $actionName, $taskName];
        $this->info('Đã tạo API thành công cho ' . $this->section . '/' . $this->container . ': ' . implode(', ', $classes));
    }

    private function generateFullApi()
    {
        $model = $this->container;
        $models = Str::pluralStudly($model);
        $entities = Str::lower($models);
        $configName = Str::camel($this->section) . '-' . Str::camel($this->container);
        $tableName = Str::snake(Str::pluralStudly($model));

        // create Reponse
        $contentResponse = $this->getStubContent('response');
        $contentResponse = $this->parseStubContent($contentResponse, [
            'model' => $model
        ]);
        $this->generateFile($this->section . '/' . $this->container . '/UI/API/Routes/_' . Str::snake($models) . '.v' . $this->docversion . '.public', $contentResponse);

        // create 4 API
        $this->generateApi('Create', 'Create' . $model);
        $this->generateApi('Delete', 'Delete' . $model);
        $this->generateApi('GetAll', 'GetAll' . $models);
        $this->generateApi('Update', 'Update' . $model);

        // create Config
        $contentConfig = $this->getStubContent('config');
        $this->generateFile($this->section . '/' . $this->container . '/Configs/' . $configName, $contentConfig);

        // create Factory
        $contentFactory = $this->getStubContent('factory');
        $contentFactory = $this->parseStubContent($contentFactory, [
            'section-name' => $this->section,
            'container-name' => $this->container,
            'class-name' => $model . 'Factory',
            'model' => $model
        ]);
        $this->generateFile($this->section . '/' . $this->container . '/Data/Factories/' . $model . 'Factory', $contentFactory);

        // create Migration
        $contentMigration = $this->getStubContent('migration');
        $contentMigration = $this->parseStubContent($contentMigration, [
            'table-name' => $tableName
        ]);
        $this->generateFile($this->section . '/' . $this->container . '/Data/Migrations/' . date('Y_m_d_His') . '_create_' . $tableName . '_table', $contentMigration);

        // create Repository
        $contentRepository = $this->getStubContent('repository');
        $contentRepository = $this->parseStubContent($contentRepository, [
            'section-name' => $this->section,
            'container-name' => $this->container,
            'class-name' => $model . 'Repository'
        ]);
        $this->generateFile($this->section . '/' . $this->container . '/Data/Repositories/' . $model . 'Repository', $contentRepository);

        // create Seeder
        $contentSeeder = $this->getStubContent('seeder');
        $contentSeeder = $this->parseStubContent($contentSeeder, [
            'section-name' => $this->section,
            'container-name' => $this->container,
            'class-name' => $model . 'Seeder',
            'model' => $model,
            'permission-name' => $entities
        ]);
        $this->generateFile($this->section . '/' . $this->container . '/Data/Seeders/' . $model . 'Seeder', $contentSeeder);

        // create Model
        $contentModel = $this->getStubContent('model');
        $contentModel = $this->parseStubContent($contentModel, [
            'section-name' => $this->section,
            'container-name' => $this->container,
            'class-name' => $model,
            'table-name' => $tableName
        ]);
        $this->generateFile($this->section . '/' . $this->container . '/Models/' . $model, $contentModel);

        // create Transformer
        $contentTransformer = $this->getStubContent('transformer');
        $contentTransformer = $this->parseStubContent($contentTransformer, [
            'section-name' => $this->section,
            'container-name' => $this->container,
            'class-name' => $model . 'Transformer',
            'model' => $model,
            'variable' => Str::camel($model)
        ]);
        $this->generateFile($this->section . '/' . $this->container . '/UI/API/Transformers/' . $model . 'Transformer', $contentTransformer);

        return $this->info('Tạo thành công RESTful API: ' . $this->section . '/' . $this->container);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->type = $this->option('type');
        $this->section = $this->ask('Nhập tên của Section', 'AppSection');
        $this->container = $this->ask('Nhập tên của Container');
        
        if (!$this->container) {
            return $this->error('Hủy yêu cầu');
        }

        $method = $this->choice('Chọn phương thức', [
            'Create', 'Delete', 'Find', 'GetAll', 'Update', 'RESTful'
        ], 0);

        $url = Str::pluralStudly(Str::snake($this->container, '-'));
        $this->url = $this->ask('Nhập endpoint', $url);

        if ($method == 'RESTful') {
            $this->generateFullApi();
        } else {
            $routeName = $method . $this->container;
            if ($method == 'GetAll') {
                $routeName = Str::pluralStudly($routeName);
            }
            $routeName = $this->ask('Nhập tên phương thức', $routeName);

            $this->generateApi($method, $routeName);
        }

        return Command::SUCCESS;
    }
}
