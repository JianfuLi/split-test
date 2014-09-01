<?php
namespace Gram\SplitTest\Tools;

/**
 * Class IpConverter
 *
 * @package Gram\SplitTest\Tools
 */
class IpConverter
{
    protected $reserved_words
        = array(
            '中国',
            '北京',
            '天津',
            '重庆',
            '上海',
            '河北',
            '山西',
            '辽宁',
            '吉林',
            '黑龙',
            '江苏',
            '浙江',
            '安徽',
            '福建',
            '江西',
            '山东',
            '河南',
            '湖北',
            '湖南',
            '广东',
            '海南',
            '四川',
            '贵州',
            '云南',
            '陕西',
            '甘肃',
            '青海',
            '台湾',
            '内蒙',
            '广西',
            '宁夏',
            '新疆',
            '西藏',
            '香港',
            '澳门',
            '长江',
            '中央',
            '对外',
            '清华',
            '宁波',
            '长沙',
            '南开',
            '西华',
            '中山',
            '北方',
            '东华',
            '东北',
            '东南',
            '华中',
            '华东',
            '华北',
            '华南',
            '大连',
            '哈尔',
            '南京',
            '郑州',
            '大庆',
            '长春',
            '西安',
            '首都',
            '青海',
            '宁波',
            '福州',
            '成都',
            '南京',
            '佳木',
            '太原',
            '中北',
            '厦门',
            '南开',
            '武汉',
            '西华',
            '中南'
        );

    protected $czFilePath;

    function __construct($czFilePath)
    {
        $this->czFilePath = $czFilePath;
    }

    protected function ip2Int32($ip)
    {
        $ip = unpack('l', pack('l', ip2long($ip)));
        return array_shift($ip);
    }

    protected function getIpInfo($ipAddress)
    {
        $json = file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip=' . $ipAddress);
        $res = json_decode($json, 1);
        if ($res['code'] == 0) {
            return $res['data'];
        } else {
            return array();
        }
    }

    protected function splitLine($text)
    {
        $idx = strpos($text, ' ');
        $start = substr($text, 0, $idx);
        $text = ltrim(substr($text, $idx));
        $idx = strpos($text, ' ');
        $end = substr($text, 0, $idx);
        $area = trim(substr($text, $idx));
        if (strlen($area) < 2) {
            return array();
        }

        $prefix = mb_substr($area, 0, 2, 'utf-8');
        if (!in_array($prefix, $this->reserved_words)) {
            return array();
        }

        return array(
            'start' => $start,
            'end'   => $end
        );
    }

    protected function processFileContent($fileName, $func)
    {
        $rf = fopen($fileName, 'r');
        while (!feof($rf)) {
            $line = fgets($rf);
            if (empty($line)) {
                continue;
            }
            call_user_func($func, $line);
        }
        fclose($rf);
    }

    function convert($saveFilePath)
    {
        $file = fopen($saveFilePath, 'w');
        $this->processFileContent(
            $this->czFilePath,
            function ($text) use ($file) {
                $arr = $this->splitLine($text);
                if (count($arr) == 0) {
                    return;
                }

                $start = $this->ip2Int32($arr['start']);
                $end = $this->ip2Int32($arr['end']);
                $info = $this->getIpInfo($arr['start']);
                fwrite(
                    $file,
                    sprintf(
                        '%u,%u,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s%s',
                        $start,
                        $end,
                        $info['country'],
                        $info['country_id'],
                        $info['area'],
                        $info['area_id'],
                        $info['region'],
                        $info['region_id'],
                        $info['city'],
                        $info['city_id'],
                        $info['county'],
                        $info['county_id'],
                        $info['isp'],
                        $info['isp_id'],
                        PHP_EOL
                    )
                );
            }
        );
        fclose($file);
    }
}