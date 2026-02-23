<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\Client;
use App\Models\Project;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $category_id = $request->get('category_id');
        $client_id = $request->get('client_id');
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        
        $incomes = Income::where('user_id', $user->id)
            ->with(['client', 'category'])
            ->when($search, function ($query) use ($search) {
                return $query->where('notes', 'like', "%{$search}%")
                    ->orWhere('reference_number', 'like', "%{$search}%");
            })
            ->when($category_id, function ($query) use ($category_id) {
                return $query->where('category_id', $category_id);
            })
            ->when($client_id, function ($query) use ($client_id) {
                return $query->where('client_id', $client_id);
            })
            ->when($date_from, function ($query) use ($date_from) {
                return $query->whereDate('income_date', '>=', $date_from);
            })
            ->when($date_to, function ($query) use ($date_to) {
                return $query->whereDate('income_date', '<=', $date_to);
            })
            ->latest()
            ->paginate(10);
            
        $categories = Category::where('user_id', $user->id)
            ->where('type', 'income')
            ->orderBy('name')
            ->get();
            
        $clients = Client::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
            
        return view('portal.income.index', compact('incomes', 'categories', 'clients', 'search', 'category_id', 'client_id', 'date_from', 'date_to'));
    }

    public function create()
    {
        $user = Auth::user();
        
        $categories = Category::where('user_id', $user->id)
            ->where('type', 'income')
            ->orderBy('name')
            ->get();
            
        $clients = Client::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
            
        $projects = Project::where('user_id', $user->id)
            ->whereIn('status', ['planned', 'in_progress'])
            ->orderBy('title')
            ->get();
            
        return view('portal.income.create', compact('categories', 'clients', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'category_id' => 'nullable|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'income_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,mpesa,card,paypal,other',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = Auth::id();
        
        Income::create($validated);
        
        return redirect()->route('income.index')
            ->with('success', 'Income recorded successfully.');
    }

    public function edit(Income $income)
    {
        $this->authorizeIncome($income);
        
        $user = Auth::user();
        
        $categories = Category::where('user_id', $user->id)
            ->where('type', 'income')
            ->orderBy('name')
            ->get();
            
        $clients = Client::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
            
        $projects = Project::where('user_id', $user->id)
            ->orderBy('title')
            ->get();
            
        return view('portal.income.edit', compact('income', 'categories', 'clients', 'projects'));
    }

    public function update(Request $request, Income $income)
    {
        $this->authorizeIncome($income);
        
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'category_id' => 'nullable|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'income_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,mpesa,card,paypal,other',
            'reference_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $income->update($validated);
        
        return redirect()->route('income.index')
            ->with('success', 'Income updated successfully.');
    }

    public function show(Income $income)
    {
        $this->authorizeIncome($income);
        
        return view('portal.income.show', compact('income'));
    }

    public function destroy(Income $income)
    {
        $this->authorizeIncome($income);
        
        $income->delete();
        
        return redirect()->route('income.index')
            ->with('success', 'Income deleted successfully.');
    }

    private function authorizeIncome($income)
    {
        if ($income->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
