<?php

namespace App\Ship\Commands;

use Illuminate\Console\Command;
use App\Ship\Parents\Commands\ConsoleCommand;
use Illuminate\Support\Facades\Storage;
use Spatie\DbDumper\Compressors\Compressor;
use Spatie\DbDumper\Databases\MySql;

class BackupDatabase extends ConsoleCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (! Storage::exists('backup')) {
            Storage::makeDirectory('backup');
        }

        $filename = 'backup-' . date('Y-m-d') . '.sql.gz';
        $filepath = Storage::path('backup/' . $filename);
        $compressor = new class implements Compressor
        {
            public function useCommand(): string
            {
                return 'gzip';
            }

            public function useExtension(): string
            {
                return 'gz';
            }
        };

        MySql::create()
            ->setHost(config('database.connections.mysql.host'))
            ->setPort(config('database.connections.mysql.port'))
            ->setDbName(config('database.connections.mysql.database'))
            ->setUserName(config('database.connections.mysql.username'))
            ->setPassword(config('database.connections.mysql.password'))
            ->useCompressor($compressor)
            ->dumpToFile($filepath);

        return Command::SUCCESS;
    }
}