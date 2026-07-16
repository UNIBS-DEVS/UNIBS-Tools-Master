<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceCalculator
{
    public function calculate(Collection $punches): array
    {
        $punches = $punches->sortBy('punch_at')->values();

        $firstIn = null;
        $lastOut = null;

        $currentIn = null;

        $totalMinutes = 0;

        $sessions = [];

        foreach ($punches as $punch) {

            // -----------------------------
            // IN Punch
            // -----------------------------
            if ($punch->punch_type === 'in') {

                // Ignore duplicate INs until an OUT arrives
                if (!$currentIn) {

                    $currentIn = $punch->punch_at;

                    if (!$firstIn) {
                        $firstIn = $currentIn;
                    }
                }

                continue;
            }

            // -----------------------------
            // OUT Punch
            // -----------------------------
            if ($punch->punch_type === 'out') {

                // Ignore OUT without IN
                if (!$currentIn) {
                    continue;
                }

                $minutes = $currentIn->diffInMinutes($punch->punch_at);

            
                $totalMinutes += $minutes;

                $lastOut = $punch->punch_at;

                $sessions[] = [
                    'in'      => $currentIn,
                    'out'     => $punch->punch_at,
                    'minutes' => $minutes,
                ];

                // Reset for next session
                $currentIn = null;
            }
        }

        return [
            'first_in' => $firstIn,
            'last_out' => $lastOut,
            'minutes'  => $totalMinutes,
            'hours'    => $this->formatMinutes($totalMinutes),
            'sessions' => $sessions,
        ];
    }

    private function formatMinutes(int $minutes): string
    {
        $hours = floor($minutes / 60);

        $mins = $minutes % 60;

        return "{$hours}h {$mins}m";
    }
}
