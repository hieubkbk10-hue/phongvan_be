<?php

namespace App\Ship\Parents\Commands;

use Illuminate\Support\Facades\File;
use Apiato\Core\Abstracts\Commands\ConsoleCommand as AbstractConsoleCommand;
use Exception;

abstract class ConsoleCommand extends AbstractConsoleCommand
{
    /**
     * Folder chứa file được tạo
     * 
     * @var string
     */
    protected $saveDir = '/Containers/';

    /**
     * Folder chứa file code mẫu
     * 
     * @var string
     */
    protected $stubDir = 'Ship/Stubs';

    /**
     * Đọc nội dung file stub
     * 
     * @param string $path
     * @return string
     */
    protected function getStubContent($path)
    {
        $path = app_path() . '/' . $this->stubDir . '/' . $path . '.stub';

        return File::get($path);
    }

    /**
     * Replace các tham số trong nội dung
     *
     * @param string $stub
     * @param array $data
     *
     * @return string|array
     */
    protected function parseStubContent($stub, $data)
    {
        return str_replace(array_map(fn($key) => '{{' . $key . '}}', array_keys($data)), array_values($data), $stub);
    }

    /**
     * If path is for a directory, create it otherwise do nothing
     *
     * @param $path
     */
    protected function createDirectory($path)
    {
        if (File::exists($path)) {
            return;
        }

        try {
            if (!File::isDirectory(dirname($path))) {
                File::makeDirectory(dirname($path), 0777, true, true);
            }
        } catch (Exception) {}
    }

    /**
     * Tạo file code theo đường dẫn và nội dung
     * 
     * @param string $path
     * @param string $content
     * @param string $extension
     * @return void
     */
    protected function generateFile($path, $content, $extension = '.php')
    {
        $path = app_path() . $this->saveDir . $path . $extension;
        if (File::exists($path)) {
            return false;
        }

        $this->createDirectory($path);

        File::put($path, $content);
    }
}
