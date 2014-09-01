<?php
namespace Gram\SplitTest;

/**
 * Class Result
 *
 * @package Gram\SplitTest
 */
class Result
{
    /**
     * @var bool
     */
    protected $isHit = false;
    /**
     * @var string
     */
    protected $version;
    /**
     * @var string
     */
    protected $name;


    /**
     * @param $name
     * @param $isHit
     * @param $version
     */
    function __construct($name, $isHit, $version)
    {
        $this->name = $name;
        $this->isHit = $isHit;
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return boolean
     */
    public function getIsHit()
    {
        return $this->isHit;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * 注册百度统计脚本代码
     */
    function registerHmtScript()
    {
        if ($this->getIsHit()) {
            $script = '<script type="text/javascript">if(typeof _hmt !== "undefined" && _hmt !== null) {_hmt.push(["_setCustomVar", 1, "{name}", "{version}", 3]);}';
            $script = str_replace('{name}', $this->getName(), $script);
            $script = str_replace('{version}', $this->getVersion(), $script);
            echo $script;
        }
    }

    /**
     * 注册谷歌统计脚本代码
     */
    function registerGaqScript()
    {
        if ($this->getIsHit()) {
            $script = '<script type="text/javascript">if(typeof _gaq !== "undefined" && _gaq !== null) {_gaq.push(["_setCustomVar", 1, "{name}", "{version}", 3]);}';
            $script = str_replace('{name}', $this->getName(), $script);
            $script = str_replace('{version}', $this->getVersion(), $script);
            echo $script;
        }
    }
}