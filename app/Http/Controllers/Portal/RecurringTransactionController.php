<?php

namespace App\Http\Controllers\Portal;

use App\Console\Commands\GenerateRecurringTransactions;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Client;
use App\Models\Project;
use App\Models\RecurringTransaction;
use App\Helpers\CurrencyHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class RecurringTransactionController extends Controller
{
    public function index(Request $request)
    {
        $user      = Auth::user();
        $type      = $request->get('type');
        $frequency = $request->get('frequency');
        $status    = $request->get('status');

        $recurring = RecurringTransaction::where('user_id', $user->id)
            ->with(['category', 'client', 'project'])
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($frequency, fn($q) => $q->where('frequency', $frequency))
            ->when($status === 'active', fn($q) => $q->where('is_active', true))
            ->when($status === 'paused', fn($q) => $q->where('is_active', false))
            ->orderByRaw('is_active DESC, next_due_date ASC')
            ->paginate(12);

        $today     = Carbon::today();
        $thisWeek  = Carbon::today()->endOfWeek();

        $totalActive  = RecurringTransaction::where('user_id', $user->id)->where('is_active', true)->count();
        $dueToday     = RecurringTransaction::where('user_id', $user->id)->where('is_active', true)->whereDate('next_due_date', $today)->count();
        $dueThisWeek  = RecurringTransaction::where('user_id', $user->id)->where('is_active', true)->whereBetween('next_due_date', [$today, $thisWeek])->count();
        $totalPaused  = RecurringTransaction::where('user_id', $user->id)->where('is_active', false)->count();

        $currencySymbol = CurrencyHelper::getSymbol($user->currency_code ?? 'USD');

        return view('portal.recurring.index', compact(
            'recurring', 'type', 'frequency', 'status',
            'totalActive', 'dueToday', 'dueThisWeek', 'totalPaused',
            'currencySymbol'
        ));
    }

    public function create()
    {
        $user = Auth::user();

        $categories = Category::where('user_id', $user->id)->orderBy('type')->orderBy('name')->get();
        $clients    = Client::where('user_id', $user->id)->where('status', 'active')->orderBy('name')->get();
        $projects   = Project::where('user_id', $user->id)->whereIn('status', ['planned', 'in_progress'])->orderBy('title')->get();

        $currencySymbol = CurrencyHelper::getSymbol($user->currency_code ?? 'USD');

        return view('portal.recurring.create', compact('categories', 'clients', 'projects', 'currencySymbol'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:150',
            'type'             => 'required|in:income,expense',
            'amount'           => 'required|numeric|min:0.01',
            'frequency'        => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'start_date'       => 'required|date',
            'end_date'         => 'nullable|date|after:start_date',
            'category_id'      => 'nullable|exists:categories,id',
            'client_id'        => 'nullable|exists:clients,id',
            'project_id'       => 'nullable|exists:projects,id',
            'vendor_name'      => 'nullable|string|max:150',
            'payment_method'   => 'required|in:cash,bank_transfer,mpesa,card,paypal,other',
            'reference_number' => 'nullable|string|max:100',
            'notes'            => 'nullable|string',
            'is_active'        => 'boolean',
        ]);

        $validated['user_id']      = Auth::id();
        $validated['next_due_date'] = $validated['start_date'];
        $validated['is_active']    = $request->boolean('is_active', true);

        RecurringTransaction::create($validated);

        return redirect()->route('recurring.index')
            ->with('success', 'Recurring transaction created successfully.');
    }

    public function show(RecurringTransaction $recurring)
    {
        $this->authorize($recurring);

        $recurring->load(['category', 'client', 'project']);

        $currencySymbol = CurrencyHelper::getSymbol(Auth::user()->currency_code ?? 'USD');

        return view('portal.recurring.show', compact('recurring', 'currencySymbol'));
    }

    public function edit(RecurringTransaction $recurring)
    {
        $this->authorize($recurring);

        $user = Auth::user();

        $categories = Category::where('user_id', $user->id)->orderBy('type')->orderBy('name')->get();
        $clients    = Client::where('user_id', $user->id)->where('status', 'active')->orderBy('name')->get();
        $projects   = Project::where('user_id', $user->id)->whereIn('status', ['planned', 'in_progress'])->orderBy('title')->get();

        $currencySymbol = CurrencyHelper::getSymbol($user->currency_code ?? 'USD');

        return view('portal.recurring.edit', compact('recurring', 'categories', 'clients', 'projects', 'currencySymbol'));
    }

    public function update(Request $request, RecurringTransaction $recurring)
    {
        $this->authorize($recurring);

        $validated = $request->validate([
            'name'             => 'required|string|max:150',
            'type'             => 'required|in:income,expense',
            'amount'           => 'required|numeric|min:0.01',
            'frequency'        => 'required|in:daily,weekly,monthly,quarterly,yearly',
            'start_date'       => 'required|date',
            'end_date'         => 'nullable|date|after:start_date',
            'category_id'      => 'nullable|exists:categories,id',
            'client_id'        => 'nullable|exists:clients,id',
            'project_id'       => 'nullable|exists:projects,id',
            'vendor_name'      => 'nullable|string|max:150',
            'payment_method'   => 'required|in:cash,bank_transfer,mpesa,card,paypal,other',
            'reference_number' => 'nullable|string|max:100',
            'notes'            => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $recurring->update($validated);

        return redirect()->route('recurring.show', $recurring)
            ->with('success', 'Recurring transaction updated successfully.');
    }

    public function destroy(RecurringTransaction $recurring)
    {
        $this->authorize($recurring);

        $recurring->delete();

        return redirect()->route('recurring.index')
            ->with('success', 'Recurring transaction deleted.');
    }

    /**
     * Toggle active / paused state.
     */
    public function toggle(RecurringTransaction $recurring)
    {
        $this->authorize($recurring);

        $recurring->update(['is_active' => !$recurring->is_active]);

        $label = $recurring->is_active ? 'resumed' : 'paused';

        return redirect()->back()->with('success', "Recurring transaction {$label}.");
    }

    /**
     * Immediately generate the record for a single recurring transaction.
     */
    public function generateNow(RecurringTransaction $recurring)
    {
        $this->authorize($recurring);

        if (!$recurring->is_active) {
            return redirect()->back()->with('error', 'Cannot generate a paused transaction. Resume it first.');
        }

        Artisan::call('recurring:generate', ['--id' => $recurring->id]);

        $recurring->refresh();

        return redirect()->route('recurring.show', $recurring)
            ->with('success', 'Transaction generated. Next due date is now ' . $recurring->next_due_date->format('M d, Y') . '.');
    }

    private function authorize(RecurringTransaction $recurring): void
    {
        if ($recurring->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
