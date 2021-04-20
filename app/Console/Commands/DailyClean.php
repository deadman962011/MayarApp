<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

use App\MayarFile;

class DailyClean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Daily:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean google drive files on Root Folder';

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
        //


        //get Files From MayarFiles where OrderId is Null
        $getFiles=MayarFile::whereNull('OrderId')->get();

        //get FileOn On Cloud
        foreach ($getFiles as $fileOne ) {
        
            $recursive = false; // Get subdirectories also?
            $contents = collect(Storage::cloud()->listContents('/', $recursive));
            $file = $contents
                ->where('type', '=', 'file')
                ->where('basename', '=',$fileOne['BaseName']);

            //delete FileOne On Cloud
            Storage::delete($fileOne['BaseName']);  
            
            //Delete File Info From MayarFile
            $deleteFile=$fileOne->delete();

            error_log('Done');
        }



    }
}
