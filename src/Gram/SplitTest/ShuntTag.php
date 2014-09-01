<?php
namespace Gram\SplitTest;

use Gram\SplitTest\IpAddress\IpLocator;
use Gram\SplitTest\Utilities\AesEncipher;
use Gram\SplitTest\Utilities\CookieHelper;
use Gram\SplitTest\Utilities\HashCode;

/**
 * Class ShuntTag
 *
 * @package Gram\SplitTest
 */
class ShuntTag
{
    private static $_COOKIE_NAME = 'ab_s_t';
    private static $_COOKIE_EXPIRE = 31536000;

    /**
     * @var string ip地址
     */
    public $ipAddress;

    /**
     * @var int 访问时间戳
     */
    public $timestamp;

    /**
     * @var int 随机值
     */
    public $salt;

    function __construct()
    {
        if ($this->restoreFromCookie()) {
            return;
        }

        $this->initialize();
        $this->saveToCookie();
    }

    function __toString()
    {
        return implode(
            ':',
            array(
                 $this->ipAddress,
                 $this->timestamp,
                 $this->salt
            )
        );
    }

    /**
     * 初始化新的分流标签
     */
    protected function initialize()
    {
        $this->ipAddress = IpLocator::getClientIp();
        $this->timestamp = time();
        $this->salt = rand(1, 100);
    }

    /**
     * 保存数据到Cookie
     */
    protected function saveToCookie()
    {
        $aesEncipher = new AesEncipher();
        $encrypted = $aesEncipher->encrypt(strval($this));
        CookieHelper::set(self::$_COOKIE_NAME, $encrypted, time() + self::$_COOKIE_EXPIRE);
    }

    /**
     * 从Cookie中还原数据
     *
     * @return bool|null
     */
    protected function restoreFromCookie()
    {
        $encrypted = CookieHelper::get(self::$_COOKIE_NAME);
        if (empty($encrypted)) {
            return null;
        }
        $aesEncipher = new AesEncipher();
        $decrypted = $aesEncipher->decrypt($encrypted);
        $arr = explode(':', $decrypted);
        if (count($arr) != 4) {
            return false;
        }

        list($ipAddress, $timestamp, $salt) = $arr;
        $this->ipAddress = $ipAddress;
        $this->timestamp = intval($timestamp);
        $this->salt = intval($salt);
        return true;
    }

    function getHashCode()
    {
        return HashCode::getHashCode($this);
    }
}