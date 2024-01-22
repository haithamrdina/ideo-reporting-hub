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
            'subdomain' => 'required|string|max:255|unique:domains,domain',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:tenants,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()]
        ];
    }
}
