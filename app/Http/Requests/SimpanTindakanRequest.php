<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SimpanTindakanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->isJson()) {
            $this->merge($this->json()->all());
        }
    }

    public function validationData(): array
    {
        return $this->all();
    }

    public function rules(): array
    {
        return [
            'idpelayanan' => 'required',
            'kode_tindakan' => 'required',
            'nama_tindakan' => 'required',
            'loketId' => 'required',
            'nama_tindakan_ind' => 'nullable',
            'keterangan' => 'nullable',
            'kdPoli' => 'nullable',
        ];
    }

    protected function passedValidation()
    {
        dd('VALIDATOR DIBUAT');
    }
}
// class SimpanTindakanRequest extends FormRequest
// {
//     /**
//      * Determine if the user is authorized to make this request.
//      */
//     public function authorize(): bool
//     {
//         return true;
//     }

//     protected function prepareForValidation(): void
//     {
//         $this->merge(
//             json_decode($this->getContent(), true) ?? []
//         );
//     }

//     /**
//      * Get the validation rules that apply to the request.
//      *
//      * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
//      */


//     public function rules(): array
//     {
//         return [
//             'idpelayanan' => 'required',
//             'kode_tindakan' => 'required',
//             'nama_tindakan' => 'required',
//             'loketId' => 'required',
//             'nama_tindakan_ind' => 'nullable',
//             'keterangan' => 'nullable',
//         ];
//     }
// }
