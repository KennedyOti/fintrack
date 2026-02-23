<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\DebtsReceivable;
use App\Models\DebtsPayable;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller
{
    public function indexReceivable(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $status = $request->get('status');
        
        $receivables = DebtsReceivable::where('user_id', $user->id)
            ->with(['client', 'invoice'])
            ->when($search, function ($query) use ($search) {
                return $query->whereHas('client', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(10);
            
        return view('portal.debts.index', compact('receivables', 'search', 'status'));
    }

    public function indexPayable(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $status = $request->get('status');
        
        $payables = DebtsPayable::where('user_id', $user->id)
            ->when($search, function ($query) use ($search) {
                return $query->where('vendor_name', 'like', "%{$search}%");
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->paginate(10);
            
        return view('portal.debts.index', compact('payables', 'search', 'status'));
    }

    public function createReceivable()
    {
        $user = Auth::user();
        
        $clients = Client::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
            
        return view('portal.debts.create', compact('clients'));
    }

    public function createPayable()
    {
        return view('portal.debts.create');
    }

    public function storeReceivable(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'original_amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,partial,paid,overdue',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['paid_amount'] = 0;
        
        DebtsReceivable::create($validated);
        
        return redirect()->route('debts.receivable.index')
            ->with('success', 'Receivable created successfully.');
    }

    public function storePayable(Request $request)
    {
        $validated = $request->validate([
            'vendor_name' => 'required|string|max:150',
            'original_amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,partial,paid,overdue',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['paid_amount'] = 0;
        
        DebtsPayable::create($validated);
        
        return redirect()->route('debts.payable.index')
            ->with('success', 'Payable created successfully.');
    }

    public function showReceivable(DebtsReceivable $debt)
    {
        $this->authorizeReceivable($debt);
        
        $debt->load(['client', 'invoice', 'payments']);
        
        return view('portal.debts.show', compact('debt'));
    }

    public function showPayable(DebtsPayable $debt)
    {
        $this->authorizePayable($debt);
        
        return view('portal.debts.show', compact('debt'));
    }

    public function editReceivable(DebtsReceivable $debt)
    {
        $this->authorizeReceivable($debt);
        
        $user = Auth::user();
        
        $clients = Client::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
            
        return view('portal.debts.edit', compact('debt', 'clients'));
    }

    public function editPayable(DebtsPayable $debt)
    {
        $this->authorizePayable($debt);
        
        return view('portal.debts.edit', compact('debt'));
    }

    public function updateReceivable(Request $request, DebtsReceivable $debt)
    {
        $this->authorizeReceivable($debt);
        
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'original_amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,partial,paid,overdue',
            'notes' => 'nullable|string',
        ]);

        $debt->update($validated);
        
        return redirect()->route('debts.receivable.index')
            ->with('success', 'Receivable updated successfully.');
    }

    public function updatePayable(Request $request, DebtsPayable $debt)
    {
        $this->authorizePayable($debt);
        
        $validated = $request->validate([
            'vendor_name' => 'required|string|max:150',
            'original_amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|in:pending,partial,paid,overdue',
            'notes' => 'nullable|string',
        ]);

        $debt->update($validated);
        
        return redirect()->route('debts.payable.index')
            ->with('success', 'Payable updated successfully.');
    }

    public function destroyReceivable(DebtsReceivable $debt)
    {
        $this->authorizeReceivable($debt);
        
        $debt->delete();
        
        return redirect()->route('debts.receivable.index')
            ->with('success', 'Receivable deleted successfully.');
    }

    public function destroyPayable(DebtsPayable $debt)
    {
        $this->authorizePayable($debt);
        
        $debt->delete();
        
        return redirect()->route('debts.payable.index')
            ->with('success', 'Payable deleted successfully.');
    }

    private function authorizeReceivable($debt)
    {
        if ($debt->user_id !== Auth::id()) {
            abort(403);
        }
    }

    private function authorizePayable($debt)
    {
        if ($debt->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
