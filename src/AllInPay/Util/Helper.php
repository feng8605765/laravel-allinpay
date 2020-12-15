<?php

namespace AllInPay\AllInPay\Util;

class Helper
{
    /**
     * @param $id
     * @param string $businessType 业务类型
     *
     * @return string
     */
    public static function getBizOrderNo($id, $businessType)
    {
        return "TL" . date("Ymdhis").$businessType;
    }
}
