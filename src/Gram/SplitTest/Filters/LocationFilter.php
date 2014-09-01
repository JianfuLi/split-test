<?php

namespace Gram\SplitTest\Filters;

use Gram\SplitTest\IpAddress\IpLocator;
use Gram\SplitTest\ShuntTag;

/**
 * Class LocationFilter
 *
 * @package Gram\SplitTest\Filters
 */
class LocationFilter implements IFilter
{
    /**
     * 判断是不否符合样本条件
     *
     * @param array    $constraints 样本的约束条件
     * @param ShuntTag $shuntTag    分流标签
     *
     * @return bool
     */
    function isSample(array $constraints, ShuntTag $shuntTag)
    {
        $ip = $this->getIpAddress($shuntTag->ipAddress);
        if (is_null($ip)) {
            return false;
        }
        if (isset($constraints['provinces'])) {
            if (in_array($ip->getProvince(), $constraints['provinces'])) {
                return true;
            }
        }
        if (isset($constraints['cities'])) {
            if (in_array($ip->getCity(), $constraints['cities'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $ipAddress
     *
     * @return \Gram\SplitTest\IpAddress\IpAddress|null
     */
    protected function getIpAddress($ipAddress)
    {
        return IpLocator::find($ipAddress);
    }
}