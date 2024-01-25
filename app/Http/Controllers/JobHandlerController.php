<?php
namespace  App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class JobHandlerController extends  Controller
{
    public static function dispatchJob($job)
    {
        // Create your job instance with delay, so we can back here within delay and take control in our hands.
    

        // Dispath your job with our custom_dispatch helper. This will return job id from jobs table
        $jobId = app(\Illuminate\Contracts\Bus\Dispatcher::class)->dispatch($job);

          return $jobId;
    }

 
}