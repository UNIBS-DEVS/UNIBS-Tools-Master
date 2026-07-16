<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;

class SystemSettingController extends Controller
{
    public function index(Request $request)
    {
        $query = SystemSetting::with('user', 'update_by_user')->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('setting_parameter', 'like', "%{$request->search}%")
                    ->orWhere('setting_value', 'like', "%{$request->search}%");
            });
        }

        $settings = $query->paginate(10)->withQueryString();

        return view('settings.system_settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'setting_parameter' => 'required|string|unique:system_settings,setting_parameter|max:255',
            'setting_value' => 'required|string',
            'remarks' => 'nullable|string'
        ]);

        $data = $request->all();

        $data['user_id'] = Auth::id();

        $data['updated_by_user'] = Auth::id();

        $data['setting_parameter'] = strtoupper(str_replace(' ', '_', $data['setting_parameter']));

        $data['setting_value'] = $request->setting_value ? json_encode(explode(',', trim($request->setting_value))) : null;

        SystemSetting::create($data);

        return back()->with('success', 'System Setting added successfully.');
    }

    public function update(Request $request, SystemSetting $system_setting)
    {

        // return Auth::id();

        $request->validate([
            'setting_value' => 'required|string',
            'remarks' => 'nullable|string'
        ]);

        $data = $request->all();
        // $data['updated_by_user'] = Auth::id();
        // $data['updated_by_user'] = auth()->user()->id ?? Auth::id();
        $data['updated_by_user'] = auth()->id();

        $data['setting_value'] = $request->setting_value ? json_encode(explode(',', trim($request->setting_value))) : null;

        $system_setting->update($data);

        return back()->with('success', 'System Setting updated successfully.');
    }

    public function destroy(SystemSetting $system_setting)
    {
        $system_setting->delete();

        return back()->with('success', 'System Setting deleted successfully.');
    }
}
