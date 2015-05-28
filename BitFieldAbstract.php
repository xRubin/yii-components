<?php

abstract class BitFieldAbstract
{

    /** @var integer */
    private $value;

    /**
     * @param integer $value
     */
    public function __construct($value = 0)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->value;
    }

    /**
     * @param integer $n
     * @return bool
     */
    public function getBit($n)
    {
        return ($this->value & $n) == $n;
    }

    /**
     * @param integer $n
     */
    public function setBit($n)
    {
        $this->value |= $n;
    }

    /**
     * @param integer $n
     */
    public function clearBit($n)
    {
        $this->value &= ~$n;
    }

    /**
     * @return array
     */
    abstract public function labels();
}
