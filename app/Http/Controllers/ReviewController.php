<?php

namespace App\Http\Controllers;

use App\Exports\ReviewReportExport;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReviewReportMail;
use App\Models\ReviewNotes;
use Illuminate\Support\Facades\Cache;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::select([
            'id',
            'from_number',
            'to_number',
            'call_date',
            'call_time',
            'duration',
            'type',
            'recording_name',
            'recording_path',
            'updated_by',
            'user_id',
        ])->with('userLatestNote');

        // Date Filter
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('call_date', [
                $request->from_date,
                $request->to_date
            ]);
        }

        // Employee Filter
        if ($request->filled('user_id')) {
            $query->whereIn('user_id', (array) $request->user_id);
        }

        // Type Filter
        if ($request->filled('types')) {
            $query->whereIn('type', (array) $request->types);
        }

        // Duration Filter
        if ($request->filled('duration') && $request->filled('duration_value')) {

            $value = (int) $request->duration_value;

            switch ($request->duration) {

                case 'Greater then':
                    $query->where('duration', '>', $value);
                    break;

                case 'Less then':
                    $query->where('duration', '<', $value);
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

        if ($request->ajax()) {

            $phoneMap = $this->getPhoneMap();

            $reviews = $query
                ->latest('call_date')
                ->latest('call_time')
                ->get();

            $data = $reviews->map(function ($review) use ($phoneMap) {

                return [
                    'id' => $review->id,

                    'from_number' => $review->from_number,
                    'to_number' => $review->to_number,

                    'from_name' => $phoneMap[$review->from_number] ?? null,
                    'to_name' => $phoneMap[$review->to_number] ?? null,

                    'call_date' => $review->call_date,
                    'call_time' => $review->call_time,
                    'duration' => $review->duration,
                    'type' => $review->type,

                    'recording_name' => $review->recording_name,
                    'recording_path' => $review->recording_path,

                    'updated_by' => $review->updated_by,

                    'notes' => optional($review->userLatestNote)->note ?? '',
                ];
            });

            return response()->json([
                'data' => $data
            ]);
        }

        $employees = User::where('status', 'active')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('reviews.index', compact('employees'));
    }

    private function getPhoneMap()
    {
        return Cache::remember('review_phone_map', now()->addHours(2), function () {

            $phoneMap = [];

            User::where('status', 'active')
                ->select('name', 'personal_mobile', 'offical_mobile')
                ->get()
                ->each(function ($user) use (&$phoneMap) {

                    if (!empty($user->personal_mobile)) {
                        $phoneMap[$user->personal_mobile] = $user->name;
                    }

                    if (!empty($user->offical_mobile)) {
                        $phoneMap[$user->offical_mobile] = $user->name;
                    }
                });

            return $phoneMap;
        });
    }

    private function filteredQuery(Request $request)
    {
        $query = Review::with('user');

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('call_date', [$request->from_date, $request->to_date]);
        }

        if ($request->filled('user_id')) {
            $query->whereIn('user_id', (array) $request->user_id);
        }

        if ($request->filled('types')) {
            $query->whereIn('type', (array) $request->types);
        }

        if ($request->filled('duration') && $request->filled('duration_value')) {
            $value = (int) $request->duration_value;

            switch ($request->duration) {
                case 'Greater then':
                    $query->where('duration', '>', $value);
                    break;

                case 'Less then':
                    $query->where('duration', '<', $value);
                    break;

                case 'Between':
                    $range = explode(',', $request->duration_value);
                    if (count($range) == 2) {
                        $query->whereBetween('duration', [$range[0], $range[1]]);
                    }
                    break;
            }
        }

        return $query;
    }

    public function exportExcel(Request $request)
    {
        $reviews = $this->filteredQuery($request)->get();

        $fileName = 'Reviews-Report-' . now()->format('Ymd-His') . '.xlsx';

        // ✅ Send email
        Mail::to(auth()->user()->email)
            ->later(now()->addSeconds(10), new ReviewReportMail($reviews, auth()->user(), 'excel'));

        // ✅ Download locally
        return Excel::download(
            new ReviewReportExport($reviews),
            $fileName
        );
    }

    public function exportPdf(Request $request)
    {
        $reviews = $this->filteredQuery($request)
            ->with(['user', 'contactUser']) // ✅ BOTH
            ->get();

        $fileName = 'Reviews-Report-' . now()->format('Ymd-His') . '.pdf';

        // ✅ Send email
        Mail::to(auth()->user()->email)
            ->later(now()->addSeconds(10), new ReviewReportMail($reviews, auth()->user(), 'pdf'));

        // ✅ Download locally
        return Pdf::loadView('reviews.pdf', [
            'reviews' => $reviews
        ])
            ->setPaper('a4', 'landscape')
            ->download($fileName);
    }

    public function history($id)
    {
        $history = ReviewNotes::with('user') // ✅ FIXED
            ->where('review_id', $id)
            ->latest()
            ->get()
            ->map(function ($item) {
                return [
                    'note' => $item->note,
                    'created_at' => $item->created_at->format('d M Y h:i A'),
                    'user' => optional($item->user)->name ?? 'System',
                ];
            });

        return response()->json($history);
    }

    public function saveNote(Request $request)
    {
        $userId = auth()->id();

        $request->validate([
            'review_id' => 'required|exists:reviews,id',
            'note' => 'nullable|string|max:1000',
        ]);

        $review = Review::findOrFail($request->review_id);

        // ✅ Save history
        ReviewNotes::create([
            'review_id' => $review->id,
            'note' => $request->note,
            'user_id' => $userId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Note saved successfully'
        ]);
    }
}
