<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AboutRequest extends FormRequest
{
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
            'email' => 'nullable|string|email',
            'photo' => 'file|mimetypes:image/jpeg,image/png|nullable',
            'phone' => 'string|nullable|min:6|max:32',
            'address' => 'string|nullable',
            'description' => 'string|nullable',
            'cv' => 'file|mimetypes:application/pdf|nullable'
        ];
    }
}
