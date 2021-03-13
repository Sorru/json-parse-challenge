<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Person;
use Illuminate\Support\Facades\Validator;
use App\Rules\AgeBetween18and65orNull;
use App\Models\ProcessingLog;


class StoreProcessedPersonData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $person_data;
    public $last_key_sent_for_processing;
    public $file_path;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $person_data, int $last_key_sent_for_processing, string $file_path)
    {
        $this->person_data = $person_data;
        $this->last_key_sent_for_processing = $last_key_sent_for_processing;
        $this->file_path = $file_path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $processing_log = ProcessingLog::firstOrNew(
            ['file_path' => $this->file_path],
            ['file_path' => $this->file_path, 'last_key_sent_for_processing' => -1]
        );

        // store the parsed key of the given file, used for restarting the main job
        $processing_log->last_key_sent_for_processing = $this->last_key_sent_for_processing;
        $processing_log->save();

        // validate against requirements
        $rules = ['date_of_birth' => new AgeBetween18and65orNull];
        if (Validator::make($this->person_data, $rules)->fails()) {
            return response('Person data does not meet requirements', 403)->header('Content-Type', 'text/plain');
        }

        $date_of_birth = (isset($this->person_data['date_of_birth']))
                            ? date('Y-m-d H:i:s', strtotime(str_replace(array('\\', '/'), '-', $this->person_data['date_of_birth'])))
                            : null;

        $person = Person::create([
            'name' => $this->person_data['name'],
            'address' => $this->person_data['address'],
            'checked' => $this->person_data['checked'],
            'description' => $this->person_data['description'],
            'interest' => $this->person_data['interest'],
            'date_of_birth' => $date_of_birth,
            'email' => $this->person_data['email'],
            'account' => $this->person_data['account'],
            'credit_card_type' => $this->person_data['credit_card']['type'] ?? '',
            'credit_card_number' => $this->person_data['credit_card']['number'] ?? '',
            'credit_card_name' => $this->person_data['credit_card']['name'] ?? '',
            'credit_card_expiration_date' => $this->person_data['credit_card']['expirationDate'] ?? '',
        ]);

        return response('Person data saved to database', 200)->header('Content-Type', 'text/plain');
    }
}
