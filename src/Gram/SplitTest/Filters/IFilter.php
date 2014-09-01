<?php

namespace Gram\SplitTest\Filters;

use Gram\SplitTest\ShuntTag;

/**
 * Interface IFilter
 *
 * @package Gram\SplitTest\Filters
 */
interface IFilter
{
    /**
     * 判断是否符合样本条件
     *
     * @param array    $constraints 样本的约束条件
     * @param ShuntTag $shuntTag    分流标签
     *
     * @return bool
     */
    function isSample(array $constraints, ShuntTag $shuntTag);
} 