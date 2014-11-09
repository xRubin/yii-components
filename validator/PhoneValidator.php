<?php

class PhoneValidator extends CValidator
{
    public $length = 10;
    
    protected function validateAttribute($model, $attribute)
    {
        $phone = preg_replace('/\D/', '', $model->$attribute);
        if(strlen($phone) != $this->length)
            $model->addError($attribute, 'Некорректный номер телефона');
    }
}
