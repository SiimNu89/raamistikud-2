<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Mail\Timetable;

class TimetableNotification extends Command
{
    protected $signature = 'app:timetable-notification';
    protected $description = 'Fetch timetable events and send via email';

    public function handle()
    {
        $startDate = now()->startOfWeek();
        $endDate   = now()->endOfWeek();

        $response = Http::get('https://tahveltp.edu.ee/hois_back/timetableevents/timetableSearch', [
            'from' => $startDate->toIsoString(),
            'thru' => $endDate->toIsoString(),
            'lang' => 'ET',
            'page' => 0,
            'size' => 50,
            'schoolId' => 38,
            'studentGroups' => '39248890-7051-4182-9a9a-8a82259b862b',
        ]);

        
        $timetableEvents = collect($response['content'] ?? [])
            ->sortBy(fn($event) => $event['date'] . ' ' . $event['timeStart'])
            ->groupBy(fn($event) =>
                Carbon::parse($event['date'])
                    ->locale('et_EE')
                    ->dayName
            );

        
        Mail::to('test@test.ee')->send(
            new Timetable($timetableEvents, $startDate, $endDate)
        );

        $this->info('Tunniplaan saadetud!');

        return 0;
    }
}
