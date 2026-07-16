<?php

namespace App\Observers;

use App\Models\Candidate;
use App\Models\CjmChange;
use Illuminate\Support\Facades\Auth;

class CandidateObserver
{
    /**
     * Candidate Created
     */
    public function created(Candidate $candidate): void
    {
        CjmChange::create([
            'candidate_id' => $candidate->id,
            'changed_field' => 'status',
            'old_value' => null,
            'new_value' => $candidate->status,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        if (!empty($candidate->interview_level)) {

            CjmChange::create([
                'candidate_id' => $candidate->id,
                'changed_field' => 'interview_level',
                'old_value' => null,
                'new_value' => $candidate->interview_level,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }
    }

    /**
     * Candidate Updated
     */
    public function updated(Candidate $candidate): void
    {
        // Status changed
        if ($candidate->wasChanged('status')) {

            CjmChange::create([
                'candidate_id' => $candidate->id,
                'changed_field' => 'status',
                'old_value' => $candidate->getOriginal('status'),
                'new_value' => $candidate->status,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        // Interview level changed
        if ($candidate->wasChanged('interview_level')) {

            CjmChange::create([
                'candidate_id' => $candidate->id,
                'changed_field' => 'interview_level',
                'old_value' => $candidate->getOriginal('interview_level'),
                'new_value' => $candidate->interview_level,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }
    }
}
