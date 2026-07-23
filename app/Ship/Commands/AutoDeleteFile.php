<?php

namespace App\Ship\Commands;

use Illuminate\Console\Command;
use App\Ship\Parents\Commands\ConsoleCommand;
use Illuminate\Support\Facades\Storage;

class AutoDeleteFile extends ConsoleCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto-delete-file {path : Đường dẫn thư mục cần xóa} {--day=5 : Khoảng cách ngày tạo so với hiện tại}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động xóa file trong storage/app';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = $this->argument('path');
        if (!$path) {
            return Command::FAILURE;
        }

        $disk = 'local';
        $day = $this->option('day');
        $max = $day * 86400;
        $timestamp = time();

        $files = Storage::disk($disk)->allFiles($path);
        foreach ($files as $file) {
            $time = Storage::disk($disk)->lastModified($file);
            $o = $timestamp - $time;
            if ($o >= $max) {
                Storage::disk($disk)->delete($file);
            }
        }

        return Command::SUCCESS;
    }
}
