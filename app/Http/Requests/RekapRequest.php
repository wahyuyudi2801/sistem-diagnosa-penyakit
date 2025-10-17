<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RekapRequest extends FormRequest
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
            'rekap_gejalas' => 'required|array',
            'rekap_gejalas.*' => 'numeric',
            'rekap_penyakits' => 'required|array',
            'rekap_penyakits.*.penyakit_id' => 'numeric',
            'rekap_penyakits.*.persentase' => 'numeric',
        ];
    }
}
