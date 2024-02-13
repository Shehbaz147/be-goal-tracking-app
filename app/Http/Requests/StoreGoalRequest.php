<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGoalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:50',
            'baseline' => 'required|date',
            'deadline' => 'required|date',
            'target' => 'required|int',
            'unit' => 'required|string'
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'Please provide name for the goal!',
            'baseline.required' => 'Please provide baseline date for the goal!',
            'deadline.required' => 'Please provide deadline date for the goal!',
            'target.required' => 'Please provide target value for the goal!',
            'unit.required' => 'Please provide unit for the goal!'
        ];
    }
}
