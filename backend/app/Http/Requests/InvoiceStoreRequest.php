<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class InvoiceStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::any(['invoice_create_shipping', 'invoice_create_dispatch']);
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
                Rule::in(['kg', 'lg']),
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
