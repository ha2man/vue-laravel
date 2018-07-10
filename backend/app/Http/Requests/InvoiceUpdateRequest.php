<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class InvoiceUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('invoice_edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => [
                'integer',
                'required',
            ],
            'type' => [
                'string',
                Rule::in(['shipping', 'dispatch']),
            ],
            'status' => [
                'string',
                Rule::in(['created', 'received', 'dispatched', 'collected']),
            ],
            'weight' => [
                'numeric',
                'required',
            ],
            'weight_unit' => [
                'string',
                Rule::in(['kg', 'pound']),
            ],
            'cost' => [
                'numeric',
                'required'
            ],
            'insurance' => [
                'numeric',
                'required'
            ],
            'notes' => [
                'json'
            ],
        ];
    }
}
