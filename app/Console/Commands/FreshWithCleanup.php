<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class FreshWithCleanup extends Command
{
    protected $signature = 'migrate:fresh-clean';
    protected $description = 'Run migrate:fresh and clean up old QR codes';

    public function handle()
    {
        $this->info('Running migrate:fresh...');
        Artisan::call('migrate:fresh');
        
        if (Storage::disk('public')->exists('qrcodes')) {
            Storage::disk('public')->deleteDirectory('qrcodes');
        }
        
    }
}
