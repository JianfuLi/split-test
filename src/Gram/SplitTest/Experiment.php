<?php
namespace Gram\SplitTest;

use Gram\SplitTest\Filters\IFilter;
use Gram\SplitTest\Utilities\HashCode;

/**
 * Class Experiment
 *
 * @package Gram\SplitTest
 */
class Experiment
{
    private static $_SETTINGS = array();
    private static $_FILTERS = array();

    /**
     * @var \Gram\SplitTest\Filters\IFilter
     */
    protected $filter;

    /**
     * @var \Gram\SplitTest\Setting
     */
    protected $setting;

    /**
     * @var array
     */
    protected $versions = array();

    /**
     * @param string $Name 实验名称
     *
     * @throws \InvalidArgumentException
     */
    protected function __construct($Name)
    {
        if (count(self::$_SETTINGS) == 0) {
            throw new \InvalidArgumentException('必须先初始化实验设置');
        }
        if (empty($Name)) {
            throw new \InvalidArgumentException('实验名称不能为空');
        }

        $this->setting = self::$_SETTINGS[$Name];
        if (empty($this->setting)) {
            throw new \InvalidArgumentException('实验名称未初始化');
        }
        $this->filter = $this->createFilter($this->setting->filter);
    }


    /**
     * 创建分流标签过滤器
     *
     * @param $filter
     *
     * @return IFilter
     * @throws \InvalidArgumentException
     */
    protected function createFilter($filter)
    {
        if (empty(self::$_FILTERS[$filter])) {
            if (!class_exists($filter)) {
                throw new \InvalidArgumentException('分流过滤器不存在');
            }
            self::$_FILTERS[$filter] = new $filter;
        }
        return self::$_FILTERS[$filter];
    }

    /**
     * 获取版本命中结果
     *
     * @return Result
     * @throws \InvalidArgumentException
     */
    protected function getResult()
    {
        if (empty($this->versions) || count($this->versions) < 2) {
            throw new \InvalidArgumentException('请先设置至少2个实验版本');
        }

        $versionKeys = array_keys($this->versions);
        $shuntTag = $this->getShuntTag();

        if (!$this->checkSample($shuntTag) || !$this->checkRatio($shuntTag)) {
            $version = array_shift($versionKeys);
            return new Result($this->setting->name, false, $version);
        }

        $count = count($this->versions);
        $version = $versionKeys[$shuntTag->salt % $count];
        return new Result($this->setting->name, true, $version);
    }

    /**
     * @return ShuntTag
     */
    protected function getShuntTag()
    {
        return new ShuntTag();
    }

    /**
     * 验证请求是否符合样式约束条件
     *
     * @param ShuntTag $shuntTag
     *
     * @return bool
     */
    protected function checkSample(ShuntTag $shuntTag)
    {
        return $this->filter->isSample($this->setting->constraints, $shuntTag);
    }

    /**
     * 验证请求是否符合分流比例
     *
     * @param ShuntTag $shuntTag
     *
     * @return bool
     */
    protected function checkRatio(ShuntTag $shuntTag)
    {
        $hashCode = $shuntTag->getHashCode();
        $remainder = $hashCode % 100;
        return $remainder < $this->setting->ratio;
    }

    /**
     * @param string   $version
     * @param callable $callback
     *
     * @return $this
     */
    function version($version, \Closure $callback)
    {
        $this->versions[$version] = $callback;
        return $this;
    }

    /**
     *
     */
    function run()
    {
        $result = $this->getResult();
        call_user_func($this->versions[$result->getVersion()], $result);
    }


    /**
     * 创建实验
     *
     * @param string $name 实验名称
     *
     * @return Experiment
     */
    static function create($name)
    {
        return new self($name);
    }

    /**
     * @param Setting $setting
     *
     * @throws \OutOfRangeException
     * @throws \InvalidArgumentException
     */
    static function init(Setting $setting)
    {
        if (empty($setting->name)) {
            throw new \InvalidArgumentException('实验名称不能为空');
        }
        if ($setting->ratio < 1 || $setting->ratio > 100) {
            throw new \OutOfRangeException('实验流量百分比只能是1-100');
        }
        if (empty($setting->filter)) {
            throw new \InvalidArgumentException('实验分流过滤器不能为空');
        }
        self::$_SETTINGS[$setting->name] = $setting;
    }
} 