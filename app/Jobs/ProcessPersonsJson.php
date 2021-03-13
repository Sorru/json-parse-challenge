<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use \JsonMachine\JsonMachine;
use App\Models\ProcessingLog;
use App\Jobs\StoreProcessedPersonData;
use Illuminate\Support\Facades\DB;


class ProcessPersonsJson implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $file_path;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $file_path)
    {
        $this->file_path = $file_path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $last_key_sent_for_processing = -1;
        $processing_log = DB::table('processing_logs')->where('file_path', $this->file_path)->first();
        if (!empty($processing_log)) {
            $last_key_sent_for_processing = $processing_log['last_key_sent_for_processing'];
        }

        if (empty($processing_log)) {
            DB::table('processing_logs')
                ->insert(['file_path' => $this->file_path, 'last_key_sent_for_processing' => -1]);
        }

        $persons = JsonMachine::fromFile($this->file_path);

        foreach ($persons as $key => $data) {

            if ($key <= $last_key_sent_for_processing) {
                continue;
            }

            StoreProcessedPersonData::dispatch($data);

            DB::table('processing_logs')
                ->where('file_path', $this->file_path)
                ->update(['last_key_sent_for_processing' => $key]);
        }
    }
}
