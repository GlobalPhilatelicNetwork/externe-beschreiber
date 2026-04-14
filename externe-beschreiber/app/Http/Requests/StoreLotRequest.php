<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLotRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manageLots', $this->route('consignment'));
    }

    protected function prepareForValidation(): void
    {
        $allowedTags = '<b><i><u><s><strong><em><br><p><span>';
        if ($this->has('description')) {
            $this->merge(['description' => strip_tags($this->description, $allowedTags)]);
        }
        if ($this->has('provenance')) {
            $this->merge(['provenance' => strip_tags($this->provenance, $allowedTags)]);
        }
    }

    public function rules(): array
    {
        return [
            'lot_type' => ['required', 'in:single,collection'],
            'category_ids' => ['required', 'array', 'min:1'],
            'category_ids.*' => ['exists:categories,id'],
            'grouping_category_id' => [
                'nullable',
                'exists:grouping_categories,id',
                function ($attribute, $value, $fail) {
                    if ($value === null) return;
                    $consignment = $this->route('consignment');
                    if ($consignment->sale_id) {
                        $gc = \App\Models\GroupingCategory::find($value);
                        if (!$gc || $gc->sale_id !== $consignment->sale_id) {
                            $fail(__('validation.grouping_category_sale_mismatch'));
                        }
                    }
                },
            ],
            'condition_ids' => ['required', 'array', 'min:1'],
            'condition_ids.*' => ['exists:conditions,id'],
            'destination_ids' => ['nullable', 'array'],
            'destination_ids.*' => ['exists:destinations,id'],
            'description' => ['required', 'string', 'max:65535'],
            'provenance' => ['nullable', 'string', 'max:65535'],
            'epos' => ['nullable', 'string', 'max:255'],
            'starting_price' => ['required', 'numeric', 'min:0'],
            'is_bid_lot' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:255'],
            'catalog_entries' => ['nullable', 'array'],
            'catalog_entries.*.catalog_type_id' => ['required', 'exists:catalog_types,id'],
            'catalog_entries.*.catalog_number' => ['required', 'string', 'max:255'],
            'packages' => ['nullable', 'array'],
            'packages.*.pack_type_id' => ['required', 'exists:pack_types,id'],
            'packages.*.pack_number' => ['required', 'string', 'max:255'],
            'packages.*.pack_note' => ['nullable', 'string', 'max:255'],
        ];
    }
}
