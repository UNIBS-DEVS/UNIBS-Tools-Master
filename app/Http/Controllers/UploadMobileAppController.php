<?php

namespace App\Http\Controllers;

use App\Models\UploadMobileApp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UploadMobileAppController extends Controller
{
    public function index()
    {
        $uploadMobileApps = UploadMobileApp::with([
            'creator',
            'updater'
        ])
            ->latest()
            ->paginate(10);

        return view(
            'mobile_app.index',
            compact('uploadMobileApps')
        );
    }

    public function create()
    {
        return view('mobile_app.create');
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'application' => 'required|in:attendance,call review',

                'version_name' => [
                    'required',
                    'max:100',
                    Rule::unique('upload_mobile_apps')
                        ->where(fn($query) => $query->where(
                            'application',
                            $request->application
                        )),
                ],

                'version_code' => [
                    'required',
                    'max:50',
                    Rule::unique('upload_mobile_apps')
                        ->where(fn($query) => $query->where(
                            'application',
                            $request->application
                        )),
                ],

                'force_update' => 'nullable|boolean',

                'apk_url' => [
                    'required',
                    'file',
                    'max:102400',
                    function ($attribute, $value, $fail) {
                        if ($value->getClientOriginalExtension() !== 'apk') {
                            $fail('Only APK files are allowed.');
                        }
                    },
                ],

                'update_message' => 'nullable|string',
            ],
            [
                'version_name.unique' => 'This Version Name already exists for this application.',
                'version_code.unique' => 'This Version Code already exists for this application.',
            ]
        );

        $apkPath = null;

        if ($request->hasFile('apk_url')) {

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
        }

        UploadMobileApp::create([
            'application'    => $request->application,
            'version_name'   => $request->version_name,
            'version_code'   => $request->version_code,
            'force_update'   => $request->boolean('force_update'),

            // Store relative path in DB
            'apk_url'        => $apkPath,

            'update_message' => $request->update_message,

            'created_by'     => Auth::id(),
            'updated_by'     => Auth::id(),
        ]);

        return redirect()
            ->route('upload-mobile-app.index')
            ->with(
                'success',
                'Mobile application version uploaded successfully.'
            );
    }

    public function show(UploadMobileApp $uploadMobileApp)
    {
        return view(
            'mobile_app.show',
            compact('uploadMobileApp')
        );
    }

    public function edit(UploadMobileApp $uploadMobileApp)
    {
        return view(
            'mobile_app.edit',
            compact('uploadMobileApp')
        );
    }

    public function update(Request $request, UploadMobileApp $uploadMobileApp)
    {
        $request->validate([
            'application' => 'required|in:attendance,call review',

            'version_name' => [
                'required',
                'max:100',
                Rule::unique('upload_mobile_apps')
                    ->where(function ($query) use ($request) {
                        return $query->where(
                            'application',
                            $request->application
                        );
                    })
                    ->ignore($uploadMobileApp->id),
            ],

            'version_code' => [
                'required',
                'max:50',
                Rule::unique('upload_mobile_apps')
                    ->where(function ($query) use ($request) {
                        return $query->where(
                            'application',
                            $request->application
                        );
                    })
                    ->ignore($uploadMobileApp->id),
            ],

            'apk_url' => [
                'nullable',
                'file',
                'max:102400',
                function ($attribute, $value, $fail) {
                    if (
                        $value &&
                        strtolower($value->getClientOriginalExtension()) !== 'apk'
                    ) {
                        $fail('Only APK files are allowed.');
                    }
                },
            ],

            'update_message' => 'nullable|string',
        ], [
            'version_name.unique' =>
            'This Version Name already exists for this application.',

            'version_code.unique' =>
            'This Version Code already exists for this application.',
        ]);

        $apkPath = $uploadMobileApp->apk_url;

        if ($request->hasFile('apk_url')) {

            // Delete old APK
            if (
                !empty($uploadMobileApp->apk_url) &&
                Storage::disk('public')->exists($uploadMobileApp->apk_url)
            ) {
                Storage::disk('public')->delete(
                    $uploadMobileApp->apk_url
                );
            }

            $file = $request->file('apk_url');

            $fileName = sprintf(
                '%s_%s_%s.%s',
                str_replace(' ', '_', $request->application),
                $request->version_code,
                time(),
                $file->getClientOriginalExtension()
            );

            $folder = $request->application === 'attendance'
                ? 'mobile-apps/attendance'
                : 'mobile-apps/call_review';

            $apkPath = $file->storeAs(
                $folder,
                $fileName,
                'public'
            );
        }

        $uploadMobileApp->update([
            'application'    => $request->application,
            'version_name'   => $request->version_name,
            'version_code'   => $request->version_code,
            'apk_url'        => $apkPath,
            'update_message' => $request->update_message,
            'force_update'   => $request->boolean('force_update'),
            'updated_by'     => Auth::id(),
        ]);

        return redirect()
            ->route('upload-mobile-app.index')
            ->with(
                'success',
                'Record Updated Successfully.'
            );
    }

    public function destroy(UploadMobileApp $uploadMobileApp)
    {
        if (
            $uploadMobileApp->apk_url &&
            Storage::disk('public')->exists(
                $uploadMobileApp->apk_url
            )
        ) {
            Storage::disk('public')
                ->delete($uploadMobileApp->apk_url);
        }

        $uploadMobileApp->delete();

        return redirect()
            ->route('upload-mobile-app.index')
            ->with(
                'success',
                'Record Deleted Successfully.'
            );
    }
}
