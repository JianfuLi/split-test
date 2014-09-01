<?php

namespace Gram\SplitTest;

/**
 * Class ExperimentSetting
 *
 * @package Gram\SplitTest
 */
class Setting
{
    /**
     * 实验名称
     *
     * @var string
     */
    public $name;
    /**
     * 导入的流量百分比，使用1-100的整数
     *
     * @var int
     */
    public $ratio;
    /**
     * 过滤器类名
     * @var string
     */
    public $filter;
    /**
     * 实验样本限制
     *
     * @var array
     */
    public $constraints;

    /**
     * @param string $name
     * @param int    $ratio
     * @param string $filter
     * @param array  $constraints
     */
    function __construct($name, $ratio, $filter, $constraints = array())
    {
        $this->name = $name;
        $this->ratio = $ratio;
        $this->filter = $filter;
        $this->constraints = $constraints;
    }
} 