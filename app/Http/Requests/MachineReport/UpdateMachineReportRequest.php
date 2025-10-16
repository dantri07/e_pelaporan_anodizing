<?php

namespace App\Http\Requests\MachineReport;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMachineReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'machine_name' => ['required', 'string', 'max:255'],
            'report_description' => ['required', 'string'],
            'report_date' => ['required', 'date'],
            'action_id' => ['nullable', 'exists:actions,action_id'],
            'technician_id' => ['nullable', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'The user is required.',
            'user_id.exists' => 'The selected user is invalid.',
            'machine_name.required' => 'The machine name is required.',
            'report_description.required' => 'The report description is required.',
            'report_date.required' => 'The report date is required.',
            'action_id.exists' => 'The selected action is invalid.',
            'technician_id.exists' => 'The selected technician is invalid.'
        ];
    }
} 