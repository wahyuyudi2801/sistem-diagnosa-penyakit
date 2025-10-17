<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PenyakitRequest extends FormRequest
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
            'nama_penyakit' => 'required|string',
            'solusi' => 'required|string'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'nama_penyakit.required' => 'Penyakit harus diisi.',
            'solusi.required' => 'Solusi harus diisi.'
        ];
    }
}
