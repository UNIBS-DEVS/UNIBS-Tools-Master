<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\Attachments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Reimbursement;
use Illuminate\Support\Facades\Storage;
use App\Models\ExpenseItem;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Expense::with([
            'employee',
            'items',
            'items.category',
            'items.attachments',
            'reimbursement',
            'advanceRequests'
        ])
            ->where('employee_id', $user->id);

        $expenses = $query
            ->when(
                $request->filled('status'),
                function ($query) use ($request) {
                    $query->where('status', $request->status);
                }
            )
            ->withSum('items', 'amount')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('expenses.index', [
            'expenses' => $expenses,
            'status'   => $request->status
        ]);
    }
 
    public function create()
    {
        $categories = ExpenseCategory::orderBy('category_name')->get();

        return view(
            'expenses.create',
            compact('categories')
        );
    }

    public function show($id)
    {
        $user = Auth::user();

        $expense = Expense::with([
            'employee',
            'items',
            'items.category',
            'items.attachments',
            'reimbursement',
            'advanceRequests'
        ])
            ->where('employee_id', $user->id)
            ->findOrFail($id);

        return view('expenses.show', [
            'expense' => $expense
        ]);
    }

    public function store(Request $request)
    {
        $employee = Auth::user();

        // dd($employee);

        $request->validate([
            'expense_reason' => 'required|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.category_id' => 'required|exists:expense_categories,id',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.expense_reason' => 'required|string|max:500',
            'items.*.expense_date' => 'required|date',
            'items.*.attachments' => 'nullable|array',
            'items.*.attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        try {
            DB::beginTransaction();

            $status = $request->input('action') === 'draft' ? 'Pending' : 'Submitted';

            $expense = Expense::create([
                'employee_id' => $employee->id,
                'user_remarks' => $request->expense_reason,
                'status' => $status,
                'created_by' => $employee->id,
                'updated_by' => $employee->id,
            ]);

            foreach ($request->items as $index => $item) {
                $expenseItem = ExpenseItem::create([
                    'expense_id' => $expense->id,
                    'category_id' => $item['category_id'],
                    'amount' => $item['amount'],
                    'expense_reason' => $item['expense_reason'],
                    'expense_date' => $item['expense_date'],
                    'created_by' => $employee->id,
                    'updated_by' => $employee->id,
                ]);

                if ($request->hasFile("items.$index.attachments")) {
                    foreach ($request->file("items.$index.attachments") as $file) {
                        $extension = $file->getClientOriginalExtension();

                        $fileName = 'EXP_' . $employee->id . '_' . $expense->id . '_' . now()->format('dmy') . '_' . Str::upper(Str::random(6)) . '.' . $extension;
                        $filePath = $file->storeAs(
                            'attachments/expenses/' . date('Y') . '/' . date('m'),
                            $fileName,
                            'public'
                        );

                        Attachments::create([
                            'expense_item_id' => $expenseItem->id,
                            'attachment_name' => $fileName,
                            'attachment_path' => $filePath,
                            'uploaded_at' => now(),
                            'created_by' => $employee->id,
                            'updated_by' => $employee->id,
                        ]);
                    }
                }
            }

            DB::commit();

            if ($status === 'Submitted') {
                try {
                    $expense->load('employee.manager');
                    if ($expense->employee && $expense->employee->manager && $expense->employee->manager->email) {
                        app(\App\Services\MailService::class)->send(
                            $expense->employee->manager->email,
                            new \App\Mail\ExpenseModuleMail($expense, 'expense', 'submitted')
                        );
                    }
                } catch (\Exception $e) {
                    Log::error('Expense submit mail failed in store: ' . $e->getMessage());
                }
            }

            return redirect()
                ->route('expenses.index')
                ->with(
                    'success',
                    'Expense request saved successfully.'
                );
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Expense save failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        $expense = Expense::with([
            'employee',
            'items',
            'items.category',
            'items.attachments'
        ])->findOrFail($id);

        if ($expense->status !== 'Pending' && $expense->status !== 'Rejected') {
            return redirect()
                ->route('expenses.index')
                ->with(
                    'error',
                    'Only pending or rejected expenses can be edited.'
                );
        }

        $categories = ExpenseCategory::orderBy('category_name')->get();

        return view('expenses.edit', compact('expense', 'categories'));
    }
 
    public function update(Request $request, $id)
    {
        $employee = Auth::user();

        if (!$employee) {
            return redirect('/login');
        }

        $validated = $request->validate([
            'expense_reason' => 'required|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.category_id' => 'required|exists:expense_categories,id',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.expense_reason' => 'required|string|max:500',
            'items.*.expense_date' => 'required|date',
            'items.*.attachments' => 'nullable|array',
            'items.*.attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        try {
            DB::beginTransaction();

            $expense = Expense::with(['items.attachments'])->findOrFail($id);

            $status = $request->input('action') === 'draft' ? 'Pending' : 'Submitted';

            $expense->update([
                'user_remarks' => $validated['expense_reason'],
                'status' => $status,
                'manager_action_at' => null,
                'manager_remarks' => null,
                'updated_by' => $employee->id,
            ]);

            foreach ($expense->items as $oldItem) {
                foreach ($oldItem->attachments as $attachment) {
                    if (
                        !empty($attachment->attachment_path) &&
                        Storage::disk('public')->exists($attachment->attachment_path)
                    ) {
                        Storage::disk('public')->delete($attachment->attachment_path);
                    }
                    $attachment->delete();
                }
            }

            $expense->items()->delete();

            foreach ($validated['items'] as $index => $item) {
                $expenseItem = ExpenseItem::create([
                    'expense_id' => $expense->id,
                    'category_id' => $item['category_id'],
                    'amount' => $item['amount'],
                    'expense_reason' => $item['expense_reason'],
                    'expense_date' => $item['expense_date'],
                    'created_by' => $employee->id,
                    'updated_by' => $employee->id,
                ]);

                if ($request->hasFile("items.$index.attachments")) {
                    foreach ($request->file("items.$index.attachments") as $file) {
                        $extension = $file->getClientOriginalExtension();
                        $fileName = 'EXP_' . $employee->id . '_' . $expense->id . '_' . now()->format('dmy') . '_' . Str::upper(Str::random(6)) . '.' . $extension;
                        $filePath = $file->storeAs(
                            'attachments/expenses/' . date('Y') . '/' . date('m'),
                            $fileName,
                            'public'
                        );

                        Attachments::create([
                            'expense_item_id' => $expenseItem->id,
                            'attachment_name' => $fileName,
                            'attachment_path' => $filePath,
                            'uploaded_at' => now(),
                            'created_by' => $employee->id,
                            'updated_by' => $employee->id,
                        ]);
                    }
                }
            }

            Reimbursement::where('expense_id', $expense->id)->delete();

            DB::commit();

            if ($status === 'Submitted') {
                try {
                    $expense->load('employee.manager');
                    if ($expense->employee && $expense->employee->manager && $expense->employee->manager->email) {
                        app(\App\Services\MailService::class)->send(
                            $expense->employee->manager->email,
                            new \App\Mail\ExpenseModuleMail($expense, 'expense', 'submitted')
                        );
                    }
                } catch (\Exception $e) {
                    Log::error('Expense submit mail failed in update: ' . $e->getMessage());
                }
            }

            return redirect()
                ->route('expenses.index')
                ->with(
                    'success',
                    'Expense updated successfully.'
                );
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Expense update failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function itemsIndex($expenseId, $itemId = null)
    {
        $expense = Expense::with([
            'employee',
            'items' => function ($query) {
                $query->latest('id');
            },
            'items.category',
            'items.attachments',
            'reimbursement'
        ])->findOrFail($expenseId);

        if ($expense->status !== 'Pending' && $expense->status !== 'Rejected') {
            return redirect()
                ->route('expenses.index')
                ->with(
                    'error',
                    'Only pending or rejected expenses can be modified.'
                );
        }

        $editingItem = null;

        if ($itemId !== null) {
            $editingItem = $expense->items->firstWhere('id', (int) $itemId);
            abort_if(!$editingItem, 404);
        }

        $categories = ExpenseCategory::orderBy('category_name')->get();

        return view(
            'expenses.items_index',
            [
                'expense'     => $expense,
                'items'       => $expense->items,
                'categories'  => $categories,
                'editingItem' => $editingItem
            ]
        );
    }

    public function storeItem(Request $request, $expenseId)
    {
        $employee = Auth::user();

        if (!$employee) {
            return redirect('/login');
        }

        $expense = Expense::findOrFail($expenseId);

        if ($expense->status !== 'Pending' && $expense->status !== 'Rejected') {
            return redirect()
                ->route('expenses.index')
                ->with(
                    'error',
                    'Only pending or rejected expenses can be modified.'
                );
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'expense_reason' => 'required|string|max:500',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        try {
            DB::beginTransaction();

            $expenseItem = ExpenseItem::create([
                'expense_id' => $expense->id,
                'category_id' => $validated['category_id'],
                'amount' => $validated['amount'],
                'expense_reason' => $validated['expense_reason'],
                'expense_date' => $validated['expense_date'],
                'created_by' => $employee->id,
                'updated_by' => $employee->id,
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $extension = $file->getClientOriginalExtension();
                    $fileName = 'EXP_' . $employee->id . '_' . $expense->id . '_' . now()->format('dmy') . '_' . Str::upper(Str::random(6)) . '.' . $extension;
                    $filePath = $file->storeAs(
                        'attachments/expenses/' . date('Y') . '/' . date('m'),
                        $fileName,
                        'public'
                    );

                    Attachments::create([
                        'expense_item_id' => $expenseItem->id,
                        'attachment_name' => $fileName,
                        'attachment_path' => $filePath,
                        'uploaded_at' => now(),
                        'created_by' => $employee->id,
                        'updated_by' => $employee->id,
                    ]);
                }
            }

            $expense->update([
                'updated_by' => $employee->id
            ]);

            DB::commit();

            return redirect()
                ->route('expenses.items.index', $expense->id)
                ->with(
                    'success',
                    'Expense item added successfully.'
                );
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Expense item save failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function updateItem(Request $request, $expenseId, $itemId)
    {
        $employee = Auth::user();

        if (!$employee) {
            return redirect('/login');
        }

        $expense = Expense::findOrFail($expenseId);

        if ($expense->status !== 'Pending' && $expense->status !== 'Rejected') {
            return redirect()
                ->route('expenses.index')
                ->with(
                    'error',
                    'Only pending or rejected expenses can be modified.'
                );
        }

        $expenseItem = ExpenseItem::with('attachments')
            ->where('expense_id', $expense->id)
            ->where('id', $itemId)
            ->firstOrFail();

        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'expense_reason' => 'required|string|max:500',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        try {
            DB::beginTransaction();

            $expenseItem->update([
                'category_id' => $validated['category_id'],
                'amount' => $validated['amount'],
                'expense_reason' => $validated['expense_reason'],
                'expense_date' => $validated['expense_date'],
                'updated_by' => $employee->id,
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $extension = $file->getClientOriginalExtension();
                    $fileName = 'EXP_' . $employee->id . '_' . $expense->id . '_' . now()->format('dmy') . '_' . Str::upper(Str::random(6)) . '.' . $extension;
                    $filePath = $file->storeAs(
                        'attachments/expenses/' . date('Y') . '/' . date('m'),
                        $fileName,
                        'public'
                    );

                    Attachments::create([
                        'expense_item_id' => $expenseItem->id,
                        'attachment_name' => $fileName,
                        'attachment_path' => $filePath,
                        'uploaded_at' => now(),
                        'created_by' => $employee->id,
                        'updated_by' => $employee->id,
                    ]);
                }
            }

            $expense->update([
                'updated_by' => $employee->id
            ]);

            DB::commit();

            return redirect()
                ->route('expenses.items.index', $expense->id)
                ->with(
                    'success',
                    'Expense item updated successfully.'
                );
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Expense item update failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function deleteItem($expenseId, $itemId)
    {
        $employee = Auth::user();

        if (!$employee) {
            return redirect('/login');
        }

        $expense = Expense::findOrFail($expenseId);

        if ($expense->status !== 'Pending' && $expense->status !== 'Rejected') {
            return redirect()
                ->route('expenses.index')
                ->with(
                    'error',
                    'Only pending or rejected expenses can be modified.'
                );
        }

        $expenseItem = ExpenseItem::with('attachments')
            ->where('expense_id', $expense->id)
            ->where('id', $itemId)
            ->firstOrFail();

        try {
            DB::beginTransaction();

            foreach ($expenseItem->attachments as $attachment) {
                if (
                    !empty($attachment->attachment_path) &&
                    Storage::disk('public')->exists($attachment->attachment_path)
                ) {
                    Storage::disk('public')->delete($attachment->attachment_path);
                }
                $attachment->delete();
            }

            $expenseItem->delete();

            $expense->update([
                'updated_by' => $employee->id
            ]);

            DB::commit();

            return redirect()
                ->route('expenses.items.index', $expense->id)
                ->with(
                    'success',
                    'Expense item deleted successfully.'
                );
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Expense item deletion failed: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    public function submit($id)
    {
        $employee = Auth::user();

        if (!$employee) {
            return redirect('/login');
        }

        $expense = Expense::with('items')->findOrFail($id);

        if ($expense->status !== 'Pending' && $expense->status !== 'Rejected') {
            return redirect()
                ->route('expenses.index')
                ->with(
                    'error',
                    'Only pending or rejected expenses can be submitted.'
                );
        }

        if ($expense->items->isEmpty()) {
            return back()->with('error', 'Cannot submit an expense request with no items.');
        }

        try {
            DB::beginTransaction();

            $expense->update([
                'status' => 'Submitted',
                'manager_action_at' => null,
                'manager_remarks' => null,
                'updated_by' => $employee->id
            ]);

            Reimbursement::where('expense_id', $expense->id)->delete();

            DB::commit();

            try {
                $expense->load('employee.manager');
                if ($expense->employee && $expense->employee->manager && $expense->employee->manager->email) {
                    app(\App\Services\MailService::class)->send(
                        $expense->employee->manager->email,
                        new \App\Mail\ExpenseModuleMail($expense, 'expense', 'submitted')
                    );
                }
            } catch (\Exception $e) {
                Log::error('Expense submit mail failed in submit: ' . $e->getMessage());
            }

            return redirect()
                ->route('expenses.index')
                ->with(
                    'success',
                    'Expense request submitted successfully.'
                );
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Expense submission failed: ' . $e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }
}
