<?php

/**
 * Class OgrnValidator
 *
 * Выполняет проверку 13-значного ОГРН или 15-значного ОГРНИП
 * по стандартному алгоритму
 */
class OgrnValidator extends CValidator
{
    public $allow13 = false;
    public $allow15 = false;

    protected function validateAttribute($object, $attribute)
    {
        $value = $object->$attribute;
        if (preg_match('/\D/', $value)) {
            $this->addError($object, $attribute, 'ОГРН должен содержать только цифры.');
            return;
        }
        if ($this->allow13 && (strlen($value) == 13)) {
            $checkValue = bcmod(substr($value, 0, 12), 11);
            $controlValue = substr($value, 12);
        } elseif ($this->allow15 && (strlen($value) == 15)) {
            $checkValue = bcmod(substr($value, 0, 14), 13);
            $controlValue = substr($value, 14);
        } else {
            $this->addError($object, $attribute, $this->errorMessage());
            return;
        }

        if (($checkValue % 10) != $controlValue) {
            $this->addError($object, $attribute, $this->errorMessage());
        }
    }

    private function errorMessage() {
        $variants = array();
        if ($this->allow13)
            $variants[] = 'ОГРН';

        if ($this->allow15)
            $variants[] = 'ОГРНИП';

        if (!count($variants))
            return 'Не настроен валидатор ОГРН.';

        return 'Неверный ' . implode(' или ', $variants) . '.';
    }
}
