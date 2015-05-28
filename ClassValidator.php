<?php

/**
 * Class ClassValidator
 */
class ClassValidator extends CValidator
{
    public $className = 'stdClass';
    public $allowEmpty = true;

    /**
     * @param CModel $object the object being validated
     * @param string $attribute the attribute being validated
     */
    protected function validateAttribute($object, $attribute)
    {
        $value = $object->$attribute;
        if ($this->allowEmpty && $this->isEmpty($value))
            return;

        if (!$this->validateValue($value)) {
            $message = $this->message !== null ? $this->message : Yii::t('yii', '{attribute} must be {class}.');
            $this->addError($object, $attribute, $message, array('{class}' => $this->className));
        }
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function validateValue($value)
    {
        return $value instanceof $this->className;
    }
}
