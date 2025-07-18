<?php

namespace App\Http\Requests\Central\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class StoreRequest extends FormRequest
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
            'company_code' => 'required|string|max:255|unique:tenants,company_code',
            'company_name' => 'required|string|max:255',
            'docebo_org_id' => 'required|string|max:255|unique:tenants,docebo_org_id',
            'zendesk_org_id'  => 'required|string|max:255',
            'subdomain' => 'required|string|max:255|unique:domains,domain'
        ];
    }
}
