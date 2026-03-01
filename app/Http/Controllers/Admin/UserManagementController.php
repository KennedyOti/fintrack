<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    // ── Index ────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = User::query();

        // Include soft-deleted users if requested
        if ($request->boolean('trashed')) {
            $query->onlyTrashed();
        }

        // Search by name or email
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('business_name', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    // ── Show ─────────────────────────────────────────────────────────────────

    public function show(int $id)
    {
        $user = User::withTrashed()->with([
            'clients', 'invoices', 'projects', 'savingsAccounts',
        ])->findOrFail($id);

        $activityLogs = ActivityLog::where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhere(function ($q2) use ($user) {
                  $q2->where('subject_type', User::class)
                     ->where('subject_id', $user->id);
              });
        })
            ->latest()
            ->take(15)
            ->get();

        // Financial summary for this user
        $stats = [
            'income'    => $user->incomes()->sum('amount'),
            'expenses'  => $user->expenses()->sum('amount'),
            'savings'   => $user->savingsAccounts()->sum('current_balance'),
            'invoices'  => $user->invoices()->count(),
            'clients'   => $user->clients()->count(),
            'projects'  => $user->projects()->count(),
        ];

        return view('admin.users.show', compact('user', 'activityLogs', 'stats'));
    }

    // ── Edit ─────────────────────────────────────────────────────────────────

    public function edit(int $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    // ── Update ───────────────────────────────────────────────────────────────

    public function update(Request $request, int $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'         => ['nullable', 'string', 'max:30'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'role'          => ['required', Rule::in(['admin', 'user'])],
            'status'        => ['required', Rule::in(['active', 'suspended', 'inactive'])],
            'new_password'  => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Prevent admin from demoting themselves
        if ($user->id === Auth::id() && $validated['role'] !== 'admin') {
            return back()->withErrors(['role' => 'You cannot change your own role.']);
        }

        // Prevent admin from suspending themselves
        if ($user->id === Auth::id() && $validated['status'] !== 'active') {
            return back()->withErrors(['status' => 'You cannot suspend your own account.']);
        }

        $changes = [];
        if ($user->role !== $validated['role']) {
            $changes['role'] = ['from' => $user->role, 'to' => $validated['role']];
        }
        if ($user->status !== $validated['status']) {
            $changes['status'] = ['from' => $user->status, 'to' => $validated['status']];
        }

        $user->name          = $validated['name'];
        $user->email         = $validated['email'];
        $user->phone         = $validated['phone'];
        $user->business_name = $validated['business_name'];
        $user->role          = $validated['role'];
        $user->status        = $validated['status'];

        if (!empty($validated['new_password'])) {
            $user->password = Hash::make($validated['new_password']);
            $changes['password_reset'] = true;
        }

        $user->save();

        ActivityLog::log(
            'admin.user.updated',
            "Admin updated user profile: {$user->name} ({$user->email})",
            $user,
            $changes
        );

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', "User \"{$user->name}\" updated successfully.");
    }

    // ── Toggle Status (Suspend / Activate) ───────────────────────────────────

    public function toggleStatus(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot change your own account status.');
        }

        if ($user->status === 'suspended') {
            $user->status = 'active';
            $action = 'admin.user.activated';
            $msg    = "User \"{$user->name}\" has been activated.";
        } else {
            $user->status = 'suspended';
            $action = 'admin.user.suspended';
            $msg    = "User \"{$user->name}\" has been suspended.";
        }

        $user->save();

        ActivityLog::log($action, $msg, $user);

        return back()->with('success', $msg);
    }

    // ── Soft Delete ──────────────────────────────────────────────────────────

    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $userName = $user->name;
        $user->delete();

        ActivityLog::log(
            'admin.user.deleted',
            "Admin soft-deleted user: {$userName} (ID: {$id})"
        );

        return redirect()->route('admin.users.index')
            ->with('success', "User \"{$userName}\" has been deleted.");
    }

    // ── Restore Soft-Deleted User ─────────────────────────────────────────────

    public function restore(int $id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        $user->status = 'active';
        $user->save();

        ActivityLog::log(
            'admin.user.restored',
            "Admin restored deleted user: {$user->name} ({$user->email})",
            $user
        );

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', "User \"{$user->name}\" has been restored and activated.");
    }

    // ── Password Reset ────────────────────────────────────────────────────────

    public function resetPassword(Request $request, int $id)
    {
        $user = User::withTrashed()->findOrFail($id);

        $validated = $request->validate([
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

        ActivityLog::log(
            'admin.user.password_reset',
            "Admin reset password for user: {$user->name} ({$user->email})",
            $user
        );

        return back()->with('success', "Password for \"{$user->name}\" has been reset.");
    }
}
