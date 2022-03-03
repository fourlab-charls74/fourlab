<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class HDMgrCopy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hd:mgrcopy {pname}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new mgr program';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Name:' . $this->argument('pname'));

//        $destinationPath=public_path()."/UploadFolderName/FileName.ext";
//        $success = \File::copy(base_path('test.text'),$destinationPath);

        return 0;
    }
}
