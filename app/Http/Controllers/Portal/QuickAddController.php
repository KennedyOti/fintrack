<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuickAddController extends Controller
{
    /**
     * Quick-add an income record via AJAX.
     */
    public function storeIncome(Request $request)
    {
        $validated = $request->validate([
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'income_date'    => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,bank_transfer,mpesa,card,paypal,other'],
            'category_id'    => ['nullable', 'exists:categories,id'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        $income = Income::create([
            'user_id'        => Auth::id(),
            'amount'         => $validated['amount'],
            'income_date'    => $validated['income_date'],
            'payment_method' => $validated['payment_method'],
            'category_id'    => $validated['category_id'] ?? null,
            'notes'          => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Income recorded successfully!',
            'id'      => $income->id,
        ]);
    }

    /**
     * Quick-add an expense record via AJAX.
     */
    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'expense_date'   => ['required', 'date'],
            'payment_method' => ['required', 'in:cash,bank_transfer,mpesa,card,other'],
            'category_id'    => ['nullable', 'exists:categories,id'],
            'vendor_name'    => ['nullable', 'string', 'max:150'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ]);

        Expense::create([
            'user_id'        => Auth::id(),
            'amount'         => $validated['amount'],
            'expense_date'   => $validated['expense_date'],
            'payment_method' => $validated['payment_method'],
            'category_id'    => $validated['category_id'] ?? null,
            'vendor_name'    => $validated['vendor_name'] ?? null,
            'notes'          => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Expense recorded successfully!',
        ]);
    }
}
