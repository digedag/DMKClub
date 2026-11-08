<?php

namespace DMKClub\Bundle\MemberBundle\Model;

use DMKClub\Bundle\MemberBundle\Accounting\DefaultProcessor;

class AgePrice
{
    private $from;
    private $to;
    private $value;

    public function __construct(?array $priceData = null)
    {
        if (!empty($priceData)) {
            $this->setFeeAgeFrom($priceData[DefaultProcessor::OPTION_FEE_AGE_FROM]);
            $this->setFeeAgeTo($priceData[DefaultProcessor::OPTION_FEE_AGE_TO]);
            $this->setFeeAgeValue($priceData[DefaultProcessor::OPTION_FEE_AGE_VALUE]);
        }
    }

    public function getFeeAgeValue()
    {
      return $this->value;
    }

    public function setFeeAgeValue($value)
    {
      $this->value = $value;
    }

	/**
     * @return mixed
     */
	public function getFeeAgeFrom()
    {
        return $this->from;
    }

    /**
     * @param mixed $from
     */
    public function setFeeAgeFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @return mixed
     */
    public function getFeeAgeTo()
    {
        return $this->to;
    }

    /**
     * @param mixed $to
     */
    public function setFeeAgeTo($to)
    {
        $this->to = $to;
    }

    public function isEmpty()
    {
        return $this->value === null ||
            $this->to === null ||
            $this->from === null;
    }
}
