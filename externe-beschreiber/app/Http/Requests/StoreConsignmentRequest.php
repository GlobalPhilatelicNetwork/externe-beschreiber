<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsignmentRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()->isAdmin(); }

    public function rules(): array
    {
        return [
            'consignor_number' => ['required', 'string', 'max:255'],
            'internal_nid' => ['required', 'string', 'max:255'],
            'sale_id' => ['nullable', 'string', 'max:255'],
            'start_number' => ['required', 'integer', 'min:1'],
            'catalog_part_id' => ['required', 'exists:catalog_parts,id'],
            'user_id' => ['required', 'exists:users,id'],
        ];
    }
}
