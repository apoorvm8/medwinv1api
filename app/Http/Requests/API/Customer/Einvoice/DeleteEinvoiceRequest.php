<?php

namespace App\Http\Requests\API\Customer\Einvoice;

use App\Traits\HashIds;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class DeleteEinvoiceRequest extends FormRequest
{
    use HashIds;
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get data to be validated from the request.
     *
     * @return array
     */
    public function validationData()
    {
        $all = parent::validationData();
        $all["id"] = $this->route('id');
        //e.g you have a field which may be json string or array
        if (Arr::exists($all, 'id')) {
            $id = $this->decode(Arr::get($all, 'id'), "E-Invoice");
            $all['id'] = $id;
        }
        return $all;
    }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id' => ['required', 'exists:einvoice,id'],
        ];
    }

    public function messages() {
        return [
            'id.required' => 'Einvoice id is required',
            'id.exists' => 'Customer E-Invoice does not exist',
        ];
    }
}
