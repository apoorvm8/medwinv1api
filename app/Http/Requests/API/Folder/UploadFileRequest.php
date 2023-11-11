<?php

namespace App\Http\Requests\API\Folder;

use App\Traits\HashIds;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class UploadFileRequest extends FormRequest
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
        //e.g you have a field which may be json string or array
        if (Arr::exists($all, 'id')) {
            $id = $this->decode(Arr::get($all, 'id'), "Folder");
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
            'id' => ['required', 'exists:folder_masters,id'],
            'folderFiles' => ['required'],
            'folderFiles.*' => ['file']
        ];
    }

    public function messages() {
        return [
            'id.required' => 'Folder id is required',
            'id.exists' => 'Folder does not exist',
            'folderFiles.required' => 'Please upload at least one file',
            'folderFiles.*.file' => 'Input must be a valid file',
        ];
    }
}
