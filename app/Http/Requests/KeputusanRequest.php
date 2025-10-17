<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KeputusanRequest extends FormRequest
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
            'data' => 'required|array',
            'data.*.gejala_id' => 'nullable',
            'penyakit_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'data.required' => 'Data gejala tidak boleh kosong',
            'data.array' => 'Data gejala harus berupa array',
            'penyakit_id.required' => 'ID penyakit tidak boleh kosong',
        ];
    }
}
