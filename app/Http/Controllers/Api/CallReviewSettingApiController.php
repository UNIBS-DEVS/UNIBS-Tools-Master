<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CallReviewSetting;
use Illuminate\Http\Request;

class CallReviewSettingApiController extends Controller
{
    public function index(Request $request)
    {
        $query = CallReviewSetting::with(['user', 'update_by_user'])->latest();


        // 🔍 Search filter
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('setting_parameter', 'like', "%{$request->search}%")
                    ->orWhere('setting_value', 'like', "%{$request->search}%");
            });
        }

        // 📄 Pagination
        $settings = $query->paginate(10);

        // 🎯 Format response
        $data = $settings->getCollection()->map(function ($setting) {
            return [
                'id' => $setting->id,
                'parameter' => $setting->setting_parameter,
                'value' => $setting->setting_value
                    ? implode(', ', json_decode($setting->setting_value))
                    : null,
                'remarks' => $setting->remarks,
                'added_by' => $setting->user->name ?? null,
                'updated_by_user' => optional($setting->update_by_user)->name,
                'created_at' => $setting->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'status' => true,
            'message' => 'System settings fetched successfully',
            'current_page' => $settings->currentPage(),
            'last_page' => $settings->lastPage(),
            'per_page' => $settings->perPage(),
            'total' => $settings->total(),
            'data' => $data
        ]);
    }
}
