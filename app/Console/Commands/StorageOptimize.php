<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Xpressengine\Storage\Storage;

class StorageOptimize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '사용되지 않는 파일들을 삭제합니다.';

    protected $storage;

    protected $bag = [];

    /**
     * Create a new command instance.
     *
     * @param Storage $storage Storage instance
     */
    public function __construct(Storage $storage)
    {
        parent::__construct();

        $this->storage = $storage;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        if ($this->getOutput()->getVerbosity() > 1) {
            $this->intercept();
        }

        $loop = 0;
        try {
            while(true) {
                $files = $this->storage->paginate(['useCount' => 0, 'parentId' => null], 20);
                if (count($files) < 1) {
                    break;
                }

                foreach ($files as $file) {
                    $this->storage->remove($file);
                }

                sleep(1);
                $loop++;


                if ($loop > 100) {
                    throw new \Exception('loop over 100 times');
                }
            }

            $this->info('Done..');
        } catch (\Exception $e) {
            $this->error('Process error, stopped command');
            throw $e;
        } finally {
            $this->output();
        }

    }

    private function intercept()
    {
        intercept('Storage@remove', 'storage.optimize.log', function ($target, $file) {

            $this->bag[] = $file;

            return $target($file);
        });
    }

    private function output()
    {
        $verbosity = $this->getOutput()->getVerbosity();

        if ($verbosity > 1) {
            $this->comment('Removed:');
        }

        foreach ($this->bag as $file) {
            switch ($verbosity) {
                case 2:
                    if ($file->parentId === null) {
                        $this->info("\t" . $file->clientname);
                    }
                    break;
                case 3:
                    if ($file->parentId === null) {
                        $this->line("\t" .
                            $file->disk . "\t" .
                            "<info>" . $file->clientname . "</info>\t" .
                            bytes($file->size)
                        );
                    }
                    break;
                case 4:
                    $this->line("\t" .
                        $file->disk . "\t" .
                        "<info>" . $file->clientname . "</info>\t" .
                        bytes($file->size)
                    );
                    break;

            }
        }
    }
}
