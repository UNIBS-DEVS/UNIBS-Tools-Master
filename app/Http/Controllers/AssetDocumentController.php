<?php

namespace App\Http\Controllers;

use App\Models\AssetMaster;
use App\Models\AssetDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssetDocumentController extends Controller
{
    /* ================= INDEX ================= */
    public function index()
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts', 'manager'])) {
            abort(403, 'Unauthorized access');
        }

        return redirect()->route('asset.index');
    }

    /* ================= CREATE ================= */
    public function create()
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts', 'manager'])) {
            abort(403, 'Unauthorized access');
        }

        $assets = AssetMaster::orderBy('asset_name')->get();

        return view('asset_documents.create', compact('assets'));
    }

    /* ================= STORE ================= */
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts', 'manager'])) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'asset_id'      => 'required|exists:asset_masters,id',
            'document_type' => 'required|string|max:50',
            'document_file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip|max:5120',
        ]);

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $originalName = $file->getClientOriginalName();
            // Store on local public storage disk
            $path = $file->store('asset_documents', 'public');

            AssetDocument::create([
                'asset_id'      => $request->asset_id,
                'document_type' => $request->document_type,
                'file_name'     => $originalName,
                'file_path'     => $path,
                'uploaded_on'   => now(),
                'created_by'    => Auth::id(),
                'updated_by'    => Auth::id()
            ]);

            return redirect()
                ->route('asset.show', $request->asset_id)
                ->with('success', 'Document uploaded successfully.');
        }

        return back()->withErrors(['document_file' => 'Failed to upload document file.']);
    }

    /* ================= EDIT ================= */
    public function edit(AssetDocument $document)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts', 'manager'])) {
            abort(403, 'Unauthorized access');
        }

        $assets = AssetMaster::orderBy('asset_name')->get();

        return view('asset_documents.edit', compact('document', 'assets'));
    }

    /* ================= UPDATE ================= */
    public function update(Request $request, AssetDocument $document)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts', 'manager'])) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'asset_id'      => 'required|exists:asset_masters,id',
            'document_type' => 'required|string|max:50',
            'document_file' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip|max:5120',
        ]);

        $data = [
            'asset_id'      => $request->asset_id,
            'document_type' => $request->document_type,
            'updated_by'    => Auth::id()
        ];

        if ($request->hasFile('document_file')) {
            // Delete old file
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $file = $request->file('document_file');
            $originalName = $file->getClientOriginalName();
            $path = $file->store('asset_documents', 'public');

            $data['file_name'] = $originalName;
            $data['file_path'] = $path;
        }

        $document->update($data);

        return redirect()
            ->route('asset.show', $request->asset_id)
            ->with('success', 'Document details updated successfully.');
    }

    /* ================= DESTROY ================= */
    public function destroy(AssetDocument $document)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts', 'manager'])) {
            abort(403, 'Unauthorized access');
        }

        // Delete physical file
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()
            ->route('asset.show', $document->asset_id)
            ->with('success', 'Document deleted successfully.');
    }

    /* ================= DOWNLOAD ================= */
    public function download(AssetDocument $document)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts', 'manager'])) {
            abort(403, 'Unauthorized access');
        }

        if (Storage::disk('public')->exists($document->file_path)) {
            return Storage::disk('public')->download($document->file_path, $document->file_name);
        }

        abort(404, 'File not found on storage disk.');
    }

    /* ================= VIEW ================= */
    public function view(AssetDocument $document)
    {
        if (!Auth::user()->hasRole(['admin', 'hr', 'accounts', 'manager'])) {
            abort(403, 'Unauthorized access');
        }

        if (Storage::disk('public')->exists($document->file_path)) {
            $path = Storage::disk('public')->path($document->file_path);
            $mime = Storage::disk('public')->mimeType($document->file_path);

            return response()->file($path, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="' . $document->file_name . '"'
            ]);
        }

        abort(404, 'File not found on storage disk.');
    }
}