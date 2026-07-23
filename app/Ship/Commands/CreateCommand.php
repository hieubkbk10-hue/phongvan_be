<?php

namespace App\Ship\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Ship\Parents\Commands\ConsoleCommand;

class CreateCommand extends ConsoleCommand
{
    /**
     * Folder chứa file được tạo
     * 
     * @var string
     */
    protected $saveDir = '/Ship/Commands/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-command {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');

        $content = $this->getStubContent('command');
        $content = $this->parseStubContent($content, [
            'class-name' => $name
        ]);
        $this->generateFile($name, $content);
        
        return Command::SUCCESS;
    }
}