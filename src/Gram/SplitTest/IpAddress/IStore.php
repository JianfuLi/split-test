<?php

namespace Gram\SplitTest\IpAddress;

/**
 * Interface IStore
 *
 * @package Gram\SplitTest\IpAddress
 */
interface IStore
{
    /**
     * @param $offset
     * @param $length
     *
     * @return mixed
     */
    function read($offset, $length);
}