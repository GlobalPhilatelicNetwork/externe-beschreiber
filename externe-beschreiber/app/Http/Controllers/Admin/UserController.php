<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Mail\CredentialsMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::withCount('consignments')->orderBy('name')->get();
        return view('admin.users.index', compact('users'));
    }

    public function store(StoreUserRequest $request)
    {
        $plainPassword = $request->password;
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($plainPassword),
            'role' => $request->role,
            'locale' => 'de',
        ]);

        if ($request->boolean('send_credentials')) {
            Mail::to($user)->send(new CredentialsMail($user, $plainPassword, route('login')));
        }

        return redirect()->route('admin.users.index')->with('success', __('messages.user_created'));
    }

    public function update(StoreUserRequest $request, User $user)
    {
        $data = $request->only(['name', 'email', 'role']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }
        $user->update($data);
        return redirect()->route('admin.users.index')->with('success', __('messages.user_updated'));
    }

    public function sendCredentials(User $user)
    {
        $plainPassword = Str::random(10);
        $user->update(['password' => Hash::make($plainPassword)]);
        Mail::to($user)->send(new CredentialsMail($user, $plainPassword, route('login')));
        return redirect()->route('admin.users.index')->with('success', __('messages.credentials_sent'));
    }
}
