<?php

namespace Gram\SplitTest\Filters;

use Gram\SplitTest\ShuntTag;

/**
 * Class RandomFilter
 *
 * @package Gram\SplitTest\Filters
 */
class RandomFilter implements IFilter
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
        return true;
    }

} 