<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest as BaseFormRequest;

class FormRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request. API 表单验证基类
     *
     * @return bool
     */    
    public function authorize()
    {
        return true;
    }
}
