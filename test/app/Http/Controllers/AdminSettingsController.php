<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Gate::allows('admin-access')) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        try {
            $settings = Setting::pluck('value', 'key')->all();
            $users = User::orderBy('created_at', 'desc')->paginate(10);

            // Debug logging
            Log::info('Settings retrieved: ' . count($settings) . ' items');
            Log::info('Users retrieved: ' . $users->count() . ' items');

            return view('admin.settings', compact('settings', 'users'));
        } catch (\Exception $e) {
            Log::error('Error in AdminSettingsController@index: ' . $e->getMessage());

            // Fallback data
            $settings = [];
            $users = collect(); // Empty collection

            return view('admin.settings', compact('settings', 'users'))
                ->with('error', 'There was an issue loading the data. Please try again.');
        }
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'platform_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255',
            'default_language' => 'required|in:en,fr,sw,es',
            'enable_notifications' => 'nullable|boolean',
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return back()->with('success', 'Settings updated successfully!');
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('admin.settings')->with('success', 'User created successfully!');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.settings')->with('success', 'User updated successfully!');
    }

    public function destroyUser(User $user)
    {
        $user->delete();
        return redirect()->route('admin.settings')->with('success', 'User deleted successfully!');
    }
}
