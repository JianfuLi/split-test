<?php
/**
 *
 *
 * @version   1.0
 * @author    ljf (jianfuli@cyou-inc.com)
 * @create    14-8-8 上午11:30
 * @copyright Copyright (c) 2003-2012 17173 (http://www.17173.com)
 */

namespace Gram\SplitTest\Tools;


class EncodeIpData
{
    protected $filePath;

    function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    function makeDatFile($savePath)
    {
        $count = 0;
        $min = 0;
        $max = 0;

        $write = fopen($savePath, 'w');
        $read = fopen($this->filePath, 'r');
        fwrite($write, pack('l3', $count, $min, $max));

        while (!feof($read)) {
            $line = trim(fgets($read));
            if (empty($line)) {
                continue;
            }
            $arr = explode(',', $line);
            if (count($arr) != 4) {
                continue;
            }

            $data = pack('l2', $arr[0], $arr[1]);
            $data .= pack('s2', $arr[2], $arr[3]);
            fwrite($write, $data);

            if ($count == 0) {
                $min = $arr[0];
            }
            $count++;
            $max = $arr[1];
        }
        fclose($read);

        fseek($write, 0, SEEK_SET);
        fwrite($write, pack('l3', $count, $min, $max));
        fclose($write);
    }
} 