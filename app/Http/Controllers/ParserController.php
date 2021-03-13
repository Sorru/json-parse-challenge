<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProcessPersonsJson;
use Illuminate\Support\Facades\Storage;


class ParserController extends Controller
{
    /**
     * Starts the parse job.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function run(Request $request)
    {
        // normally we would get this from a user upload or some other source
        $filename = 'challenge.json';

        if (Storage::disk('local')->missing($filename)) {
            return response('File missing from storage', 400);
        }

        $file_path = Storage::path($filename);
        ProcessPersonsJson::dispatch($file_path);

        return response('Job sent to processing', 200)->header('Content-Type', 'text/plain');
    }
}
