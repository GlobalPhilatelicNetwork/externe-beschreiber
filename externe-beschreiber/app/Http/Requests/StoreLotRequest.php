<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageLots', $this->route('consignment'));
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string'],
            'catalog_type_id' => ['required', 'exists:catalog_types,id'],
            'catalog_number' => ['required', 'string', 'max:255'],
            'starting_price' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
