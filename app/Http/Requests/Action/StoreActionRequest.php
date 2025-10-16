<?php

namespace App\Http\Requests\Action;

use Illuminate\Foundation\Http\FormRequest;

class StoreActionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'date' => ['required', 'date'],
            'spare_part_id' => ['nullable', 'exists:spare_parts,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'The status is required.',
            'description.required' => 'The description is required.',
            'date.required' => 'The action date is required.',
            'spare_part_id.exists' => 'The selected spare part is invalid.',
            'quantity.integer' => 'The quantity must be a number.',
            'quantity.min' => 'The quantity must be at least 1.',
            'images.*.image' => 'Each file must be an image.',
            'images.*.mimes' => 'Each image must be a file of type: jpeg, png, jpg, gif, svg.',
            'images.*.max' => 'Each image may not be greater than 2MB.'
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        \Log::error('StoreActionRequest validation failed', $validator->errors()->toArray());
        parent::failedValidation($validator);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $sparePartId = $this->input('spare_part_id');
            $quantity = $this->input('quantity');
            if ($sparePartId && $quantity) {
                $sparePart = \App\Models\SparePart::find($sparePartId);
                if ($sparePart && $quantity > $sparePart->quantity) {
                    $validator->errors()->add('quantity', 'The quantity exceeds available stock (' . $sparePart->quantity . ').');
                }
            }
        });
    }
} 