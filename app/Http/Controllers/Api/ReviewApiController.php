<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewNotes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ReviewApiController extends Controller
{
    public function apiIndex(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $query = Review::with([
            'user',
            'userOldestNote'
        ]);

        // 🔐 Restrict non-admin users
        if (!$user->hasRole('admin')) {
            $query->where('user_id', $userId);
        }

        // Date Filter
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('call_date', [
                $request->from_date,
                $request->to_date
            ]);
        }

        // 👇 Only admin can filter by other users
        if ($user->hasRole('admin') && $request->filled('user_id')) {
            $query->whereIn('user_id', (array) $request->user_id);
        }

        // Call Type Filter
        if ($request->filled('types')) {
            $query->whereIn('type', (array) $request->types);
        }

        // Duration Filter
        if ($request->filled('duration') && $request->filled('duration_value')) {

            switch ($request->duration) {

                case 'Greater then':
                    $query->where('duration', '>', (int) $request->duration_value);
                    break;

                case 'Less then':
                    $query->where('duration', '<', (int) $request->duration_value);
                    break;

                case 'Between':
                    $range = explode(',', $request->duration_value);

                    if (count($range) == 2) {
                        $query->whereBetween('duration', [
                            (int) $range[0],
                            (int) $range[1]
                        ]);
                    }
                    break;
            }
        }

        $reviews = $query
            ->orderBy('id', 'desc')
            ->paginate(10);

        return response()->json($reviews);
    }

    public function saveCallLog(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'       => 'nullable|email',
            'from_number' => 'nullable|string',
            'to_number'   => 'nullable|string',
            'to_name'   => 'nullable|string',
            'call_date'   => 'nullable|date',
            'call_time'   => 'nullable',
            'duration'    => 'nullable|integer',
            'requirement_type' => 'nullable|in:sourcing,training,job seeker,microsoft,tally,google,zoho,software services,Razorpay,BGC,others',
            'type'        => 'nullable|in:incoming,outgoing,missed,rejected',
            'recording'   => 'nullable|file|mimes:mp3,wav,ogg,m4a,3gp,3gpp'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation Error',
                'errors'  => $validator->errors()
            ], 422);
        }

        // FILE UPLOAD + METADATA
        $recordingPath = null;
        $recordingName = null;
        $originalName  = null;
        $mimeType      = null;
        $sizeBytes     = null;

        if ($request->hasFile('recording')) {

            $file = $request->file('recording');

            $originalName = $file->getClientOriginalName();
            $mimeType     = $file->getClientMimeType();
            $sizeBytes    = $file->getSize();

            $recordingName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Folder only
            $folderPath = 'recordings/' . date('Y/m');

            // Store file
            $file->storeAs($folderPath, $recordingName, 'public');

            // Save ONLY folder
            $recordingPath = $folderPath;
        }

        // 🔹 Store Data
        $review = Review::create([
            'email'             => $request->email,
            'from_number'       => $request->from_number,
            'to_number'         => $request->to_number,
            'to_name'           => $request->to_name,
            'call_date'       => $request->call_date,
            'call_time'       => $request->call_time,
            'duration'        => $request->duration,
            'requirement_type' => $request->requirement_type,
            'type'            => $request->type,

            // FILE DATA (like your screenshot table)
            'recording_path'  => $recordingPath,
            'recording_name'  => $recordingName,
            'original_name'   => $originalName,
            'mime_type'       => $mimeType,
            'size_bytes'      => $sizeBytes,

            // user
            'user_id'           => Auth::id(),
            'added_by'          => Auth::id(),
            'updated_by'        => Auth::id()
        ]);

        // Save note in separate table
        if ($request->filled('note')) {
            ReviewNotes::create([
                'review_id' => $review->id,
                'note'      => $request->note,
                'user_id'   => Auth::id(),
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Review created successfully',
            'data'    => $review
        ]);
    }

    public function saveMultipleCallLogs(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'records' => 'required|array|min:1|max:500',

            'records.*.email'       => 'nullable|email',
            'records.*.from_number' => 'nullable|string',
            'records.*.to_number'   => 'nullable|string',
            'records.*.to_name'     => 'nullable|string',
            'records.*.call_date'   => 'nullable|date',

            'records.*.call_time'   => 'nullable|date_format:H:i:s',

            'records.*.duration'    => 'nullable|integer|min:0',

            'records.*.requirement_type' => [
                'nullable',
                Rule::in([
                    'sourcing',
                    'training',
                    'job seeker',
                    'microsoft',
                    'tally',
                    'google',
                    'zoho',
                    'software services',
                    'Razorpay',
                    'BGC',
                    'others'
                ]),
            ],

            'records.*.type' => [
                'nullable',
                Rule::in([
                    'incoming',
                    'outgoing',
                    'missed',
                    'rejected'
                ]),
            ],

            'records.*.recording' => 'nullable|file|mimes:mp3,wav,ogg,m4a,3gp,3gpp|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation Error',
                'errors'  => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {

            foreach ($request->records as $index => $record) {

                $recordingPath = null;
                $recordingName = null;
                $originalName  = null;
                $mimeType      = null;
                $sizeBytes     = null;

                $file = $request->file("records.$index.recording");

                if ($file) {

                    // Define folder
                    $folderPath = 'recordings/' . date('Y/m');

                    // Generate filename
                    $recordingName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

                    // Store file inside folder
                    $file->storeAs($folderPath, $recordingName, 'public');

                    // Save ONLY folder in DB
                    $recordingPath = $folderPath;

                    // Metadata
                    $originalName = $file->getClientOriginalName();
                    $mimeType     = $file->getClientMimeType();
                    $sizeBytes    = $file->getSize();
                }

                // Create Review
                $review = Review::create([
                    'email'            => $record['email'] ?? null,
                    'from_number'      => $record['from_number'] ?? null,
                    'to_number'        => $record['to_number'] ?? null,
                    'to_name'          => $record['to_name'] ?? null,
                    'call_date'        => $record['call_date'] ?? null,
                    'call_time'        => $record['call_time'] ?? null,
                    'duration'         => $record['duration'] ?? null,
                    'requirement_type' => $record['requirement_type'] ?? null,
                    'type'             => $record['type'] ?? null,

                    'recording_path'   => $recordingPath,
                    'recording_name'   => $recordingName,
                    'original_name'    => $originalName,
                    'mime_type'        => $mimeType,
                    'size_bytes'       => $sizeBytes,

                    'user_id'          => Auth::id(),
                    'added_by'         => Auth::id(),
                    'updated_by'       => Auth::id(),
                ]);

                // ✅ Save note in separate table
                if (!empty($record['note'])) {
                    $review->notes()->create([
                        'note'    => $record['note'],
                        'user_id' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Bulk insert successful',
                'count'   => count($request->records)
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => 'Error occurred',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function saveNote(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'requirement_type' => 'nullable|in:sourcing,training,job seeker,microsoft,tally,google,zoho,software services,Razorpay,BGC,others',
            'email' => 'nullable|email',
            'review_id' => 'required|exists:reviews,id',
            'note' => 'nullable|string|max:1000',
            'note_id' => 'nullable|exists:review_notes,id',
        ]);

        $review = Review::findOrFail($request->review_id);

        // ✅ Update Review
        $updateData = [];

        if ($request->filled('requirement_type')) {
            $updateData['requirement_type'] = $request->requirement_type;
        }

        if ($request->filled('email')) {
            $updateData['email'] = $request->email;
        }

        if (!empty($updateData)) {
            $updateData['updated_by'] = $userId;
            $review->update($updateData);
        }

        // 🚨 IMPORTANT CHECK
        if (!$request->filled('note_id') && !$request->filled('note')) {
            return response()->json([
                'success' => false,
                'message' => 'Note is required'
            ], 422);
        }

        if ($request->note_id) {

            // UPDATE
            $note = ReviewNotes::where('id', $request->note_id)
                ->where('review_id', $request->review_id)
                ->firstOrFail();

            $note->update([
                'note' => $request->note,
                'user_id' => $userId,
            ]);
        } else {

            // CREATE
            $note = ReviewNotes::create([
                'review_id' => $request->review_id,
                'note' => $request->note,
                'user_id' => $userId,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Note updated successfully',
            'data' => [
                'note' => $note,
                'review' => $review
            ]
        ]);
    }

    public function history($id)
    {
        $history = ReviewNotes::with('user')
            ->where('review_id', $id)
            ->latest()
            ->get()
            ->map(function ($item) {
                return [
                    'note' => $item->note,
                    'created_at' => $item->created_at->format('d M Y H:i'),
                    'user' => optional($item->user)->name ?? 'Unknown'
                ];
            });

        return response()->json($history);
    }
}
