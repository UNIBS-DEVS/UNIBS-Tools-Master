<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseCategoryController extends Controller
{
    public function index()
    { 
        $categories = ExpenseCategory::latest()->get(); 

        return view('expense_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('expense_categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|max:255'
        ]);

        ExpenseCategory::create([
            'category_name' => $request->category_name,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('expense-categories.index')
            ->with('success', 'Category Created Successfully');
    }

    public function edit($id)
    {
        $category = ExpenseCategory::findOrFail($id);

        return view('expense_categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|max:255'
        ]);

        $category = ExpenseCategory::findOrFail($id);

        $category->update([
            'category_name' => $request->category_name,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('expense-categories.index')
            ->with('success', 'Category Updated Successfully');
    }

    public function destroy($id)
    {
        ExpenseCategory::destroy($id);

        return redirect()->route('expense-categories.index')
            ->with('success', 'Category Deleted Successfully');
    }
}