<?php

/**
 * Class InnValidator
 *
 * Выполняет проверку ИНН
 */
class InnValidator extends CValidator
{
    public $allow10 = false;
    public $allow12 = false;


    protected function validateAttribute($object, $attribute)
    {
        $value = $object->$attribute;
        if (preg_match('/\D/', $value)) {
            $this->addError($object, $attribute, 'ИНН должен содержать только цифры.');
            return;
        }
        $inn = (string)$value;

        if ($this->allow10 && (strlen($inn) == 10)) {
            if ($inn[9] != (string)(((
                            2 * $inn[0] + 4 * $inn[1] + 10 * $inn[2] +
                            3 * $inn[3] + 5 * $inn[4] + 9 * $inn[5] +
                            4 * $inn[6] + 6 * $inn[7] + 8 * $inn[8]
                        ) % 11) % 10)
            )
                $this->addError($object, $attribute, 'Некорректный ИНН.');
        } elseif ($this->allow12 && (strlen($inn) == 12)) {
            $num10 = (string)(((
                        7 * $inn[0] + 2 * $inn[1] + 4 * $inn[2] +
                        10 * $inn[3] + 3 * $inn[4] + 5 * $inn[5] +
                        9 * $inn[6] + 4 * $inn[7] + 6 * $inn[8] +
                        8 * $inn[9]
                    ) % 11) % 10);
            $num11 = (string)(((
                        3 * $inn[0] + 7 * $inn[1] + 2 * $inn[2] +
                        4 * $inn[3] + 10 * $inn[4] + 3 * $inn[5] +
                        5 * $inn[6] + 9 * $inn[7] + 4 * $inn[8] +
                        6 * $inn[9] + 8 * $inn[10]
                    ) % 11) % 10);

            if (($inn[11] != $num11) || ($inn[10] != $num10))
                $this->addError($object, $attribute, 'Некорректный ИНН.');
        } else {
            $this->addError($object, $attribute, $this->errorMessage());
        }
    }

    private function errorMessage() {
        $variants = array();
        if ($this->allow10)
            $variants[] = 10;

        if ($this->allow12)
            $variants[] = 12;

        if (!count($variants))
            return 'Не настроен валидатор ИНН.';

        return 'ИНН должен содержать ' . implode(' или ', $variants) . ' цифр.';
    }
}
