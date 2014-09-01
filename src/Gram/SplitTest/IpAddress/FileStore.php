<?php

namespace Gram\SplitTest\IpAddress;

/**
 * Class FileStore
 *
 * @package Gram\SplitTest\IpAddress
 */
class FileStore implements IStore
{
    protected $file;

    /**
     * @param string $fileName
     */
    function __construct($fileName = 'ip.dat')
    {
        $this->file = fopen($fileName, 'r');
    }

    /**
     * @param $offset
     * @param $length
     *
     * @return string
     */
    function read($offset, $length)
    {
        fseek($this->file, $offset, SEEK_SET);
        return fread($this->file, $length);
    }

    /**
     *
     */
    function __destruct()
    {
        fclose($this->file);
    }
}