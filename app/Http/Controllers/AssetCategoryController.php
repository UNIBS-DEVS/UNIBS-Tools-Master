<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AssetCategory;
use Illuminate\Support\Facades\Auth;


class AssetCategoryController extends Controller
{
public function index()
{
    $assetcategories = AssetCategory::orderByDesc('id')->get();

    return view('asset_categories.index', [
        'assetcategories' => $assetcategories,
    ]);
}

    public function create()
    {
        return view('asset_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        AssetCategory::create([
            'category_name' => $request->category_name,
            'status' => $request->status,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);
    
        return redirect()->route('asset_categories.index')
            ->with('success', 'Category Created Successfully');
    }

    public function edit(AssetCategory $assetcategory)
    {
        return view('asset_categories.edit', compact('assetcategory'));
    }
    
    public function update(Request $request, AssetCategory $assetcategory)
    {
        $request->validate([
            'category_name' => 'required|max:255',
            'status' => 'required|in:active,inactive'
        ]);

        $assetcategory->update([
            'category_name' => $request->category_name,
            'status' => $request->status,
            'updated_by' => Auth::id(),
        ]);
    
        return redirect()->route('asset_categories.index')
            ->with('success', 'Category Updated Successfully');
    }

    public function destroy(AssetCategory $assetcategory)
    {
        $assetcategory->delete();

        return redirect()->route('asset_categories.index')
            ->with('success', 'Category Deleted Successfully');
    }
}