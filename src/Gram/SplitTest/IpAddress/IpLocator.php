<?php

namespace Gram\SplitTest\IpAddress;

/**
 * Class IpLocator
 */
class IpLocator
{
    private static $_HEAD_METADATA_LENGTH = 12;
    private static $_BODY_DATA_LENGTH = 12;
    private static $_CONTAINER = array();

    /**
     * @var IStore
     */
    protected $store;

    /**
     * @var IpLocator
     */
    private static $instance;

    /**
     * @param IStore $store
     */
    function __construct(IStore $store = null)
    {
        $this->store = is_null($store) ? new FileStore() : $store;
    }

    /**
     * 获取文件头数据，包含3个值：总记录数，开始范围，结束范围
     *
     * @return array
     */
    protected function getHeadMetadata()
    {
        $data = $this->store->read(0, self::$_HEAD_METADATA_LENGTH);
        return array_values(unpack('l3', $data));
    }

    /**
     * 获取文件数据，包含5个值：ip范围开始值，ip范围结束值，省份，城市，运营商
     *
     * @param  int $offset
     *
     * @return array
     */
    protected function getBodyData($offset)
    {
        $data = $this->store->read($offset, self::$_BODY_DATA_LENGTH);
        $part1 = unpack('l2', substr($data, 0, 8));
        $part2 = unpack('s2', substr($data, 8));
        return array_merge(array_values($part1), array_values($part2));
    }


    /**
     * 获取当前位置在文件中的偏移量
     *
     * @param int $position
     *
     * @return int
     */
    protected function getOffset($position)
    {
        return self::$_HEAD_METADATA_LENGTH + self::$_BODY_DATA_LENGTH * $position;
    }

    /**
     * 在文件中查找地址
     *
     * @param int $ip
     *
     * @return IpAddress|null
     */
    protected function search($ip)
    {
        list($count, $start, $end) = $this->getHeadMetadata();
        if ($ip < $start || $ip > $end) {
            return null;
        }
        return $this->binarySearch($ip, $count);
    }

    /**
     * 二分法快速查找ip地址
     *
     * @param int $ip
     * @param int $count
     *
     * @return IpAddress|null
     */
    protected function binarySearch($ip, $count)
    {
        $low = 0;
        $high = $count;
        $result = null;
        while ($low <= $high) {
            $position = floor(($high + $low) / 2);
            $offset = $this->getOffset($position);
            list($start, $end, $province, $city) = $this->getBodyData($offset);
            if ($ip < $start) {
                $high = $position - 1;
            } elseif ($ip > $end) {
                $low = $position + 1;
            } else {
                $result = new IpAddress($province, $city);
                break;
            }
        }
        return $result;
    }

    /**
     * @param $ipAddress
     *
     * @return IpAddress|null
     */
    protected function findInner($ipAddress)
    {
        $ip = self::ip2Int32($ipAddress);
        return $this->search($ip);
    }

    /**
     * 查找ip地址
     *
     * @param $ipAddress
     *
     * @return IpAddress|null
     * @throws \InvalidArgumentException
     */
    static function find($ipAddress)
    {
        if (empty($ipAddress)) {
            throw new \InvalidArgumentException('待获取的IP地址不能为空');
        }
        if (!isset(self::$_CONTAINER[$ipAddress])) {
            if (is_null(self::$instance)) {
                self::$instance = new self;
            }
            self::$_CONTAINER[$ipAddress] = self::$instance->findInner($ipAddress);
        }
        return self::$_CONTAINER[$ipAddress];
    }


    /**
     * 转换ip为有符号的32位整型
     *
     * @param string $ip
     *
     * @return int
     */
    static function ip2Int32($ip)
    {
        $ip = unpack('l', pack('l', ip2long($ip)));
        return array_shift($ip);
    }

    /**
     * @return string
     */
    static function getClientIp()
    {
        $ip = false;
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip) {
                array_unshift($ips, $ip);
                $ip = false;
            }
            for ($i = (count($ips) - 1); $i >= 0; $i--) {
                if (!preg_match('/^(10|172\.16|192\.168)\./', trim($ips[$i]))) {
                    $ip = $ips[$i];
                    break;
                }
            }
        }
        $ip = ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
        return $ip;
    }
}