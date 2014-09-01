<?php
namespace Gram\SplitTest\Utilities;

/**
 * Class AesEncipher
 *
 * @package Gram\SplitTest\Utilities
 */
class AesEncipher
{
    protected $iv = '=7\+i%_^)H8^*@t&';
    protected $key = '0d7hT%&|<*/8Asjr';
    protected $mode = MCRYPT_MODE_CBC;
    protected $cipher_alg = MCRYPT_RIJNDAEL_128;

    function __construct($iv = null, $cipher_alg = null)
    {
        if (!empty($iv)) {
            $this->iv = $iv;
        }
        if (!empty($cipher_alg)) {
            $this->cipher_alg = $cipher_alg;
        }
    }

    /**
     * @param $value
     *
     * @return string
     */
    function encrypt($value)
    {
        $blockSize = 16;
        $pad = $blockSize - (strlen($value) % $blockSize);
        $value = $value . str_repeat(chr($pad), $pad);
        return bin2hex(mcrypt_encrypt($this->cipher_alg, $this->key, $value, $this->mode, $this->iv));
    }

    /**
     * @param $value
     *
     * @return string
     */
    function decrypt($value)
    {
        return mcrypt_decrypt($this->cipher_alg, $this->key, pack("H*", $value), $this->mode, $this->iv);
    }
} 