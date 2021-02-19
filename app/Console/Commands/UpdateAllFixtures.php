<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\FbController;
use App\Http\Controllers\UpdateB;
use App\Competition;



class UpdateAllFixtures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'match:startUpdate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {

        $this->info('rob vana lastrus tarstagastinvastigalack');
        $fbcontroller = new FbController();
        $fbcontroller->TodayUpdateDoer();

        // $fbcontroller->testo();
        // $fbcontroller->putUpToDate();
        // $updateB = new UpdateB();
        // $updateB->testor4(Competition::class);
        
    }
}
