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
        return '000w'.date('YmdHis').mt_rand(1000, 9999).substr($id, -1, 2).$businessType;
    }
}
