<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email' . ($userId ? ",{$userId}" : '')],
            'role' => ['required', 'in:admin,user'],
        ];
        if (!$userId) {
            $rules['password'] = ['required', Password::min(8)];
        } else {
            $rules['password'] = ['nullable', Password::min(8)];
        }
        return $rules;
    }
}
