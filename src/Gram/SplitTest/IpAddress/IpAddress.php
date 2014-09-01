<?php

namespace Gram\SplitTest\IpAddress;

/**
 * Class IpAddress
 *
 * @package Gram\SplitTest\IpAddress
 */
class IpAddress
{
    /**
     * @var string
     */
    protected $city;
    /**
     * @var string
     */
    protected $province;

    /**
     * @param $province
     * @param $city
     */
    function __construct($province, $city)
    {
        $this->city = $city;
        $this->province = $province;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getProvince()
    {
        return $this->province;
    }
} 