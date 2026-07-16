<?php

namespace App\Http\Controllers\AttendanceAPIs;

use App\Http\Controllers\Controller;
use App\Models\UploadMobileApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MobileAppController extends Controller
{
    public function uploadMobileApp(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'application' => [
                'required',
                Rule::in(['attendance', 'call review'])
            ],


            'version_name' => [
                'required',
                Rule::unique('upload_mobile_apps')
                    ->where('application', $request->application),
            ],

            'version_code' => [
                'required',
                Rule::unique('upload_mobile_apps')
                    ->where('application', $request->application),
            ],

            'force_update' => 'nullable|boolean',

            'apk_url' => [
                'required',
                'file',
                'max:102400',
                function ($attribute, $value, $fail) {

                    $extension = strtolower(
                        $value->getClientOriginalExtension()
                    );

                    if ($extension !== 'apk') {
                        $fail('Only APK files are allowed.');
                    }
                },
            ],

            'update_message' => 'nullable|string',
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

            $file = $request->file('apk_url');

            $fileName = uniqid() . '.apk';

            $folder = $request->application === 'attendance'
                ? 'mobile-apps/attendance'
                : 'mobile-apps/call_review';

            $apkPath = $file->storeAs(
                $folder,
                $fileName,
                'public'
            );

            $uploadMobileApp = UploadMobileApp::create([

                'application' => $request->application,

                'version_name' => $request->version_name,

                'version_code' => $request->version_code,

                'force_update' => $request->boolean('force_update'),

                // Save relative path in DB
                'apk_url' => $apkPath,

                'update_message' => $request->update_message,

                'created_by' => Auth::id(),

                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            // dd(asset('storage/' . $uploadMobileApp->apk_url));

            return response()->json([
                'status' => true,
                'message' => 'Version uploaded successfully',
                'data' => [

                    'id' => $uploadMobileApp->id,

                    'application' => $uploadMobileApp->application,

                    'version_name' => $uploadMobileApp->version_name,

                    'version_code' => $uploadMobileApp->version_code,

                    'force_update' => $uploadMobileApp->force_update,

                    'apk_url' => asset('storage/' . $uploadMobileApp->apk_url),

                    'update_message' => $uploadMobileApp->update_message,

                    'created_at' => $uploadMobileApp->created_at,
                ]
            ], 201);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Upload failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function latest($application)
    {
        $apps = UploadMobileApp::where(
            'application',
            $application
        )
            ->latest()
            ->get()
            ->map(function ($app) {
                return [
                    'id' => $app->id,
                    'application' => $app->application,
                    'version_name' => $app->version_name,
                    'version_code' => $app->version_code,
                    'force_update' => $app->force_update,
                    'update_message' => $app->update_message,
                    'apk_url' => url(Storage::url($app->apk_url)),
                    'updated_at' => $app->updated_at,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $apps
        ]);
    }
}
