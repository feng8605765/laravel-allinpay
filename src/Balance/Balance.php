<?php

namespace AllInPay\Balance;

use AllInPay\AllInPay\Exceptions\Exception;
use AllInPay\AllInPay\Http\AllInPayClient;


class Balance extends AllInPayClient
{

    /**
     * @var string
     */
    protected $service = 'orderService';

    /**
     * 查询余额
     *
     * @param array $request
     *
     * @throws Exception
     *
     * @return array|bool
     */
    public function balance(array $request)
    {
        $method = 'allinpay.yunst.merchantService.queryReserveFundBalance';

        $params = [
            'bizUserId'    => $request['bizUserId'],
            'accountSetNo' => $request['accountSetNo'],
        ];

        return $this->setBusiness('balance')->guzzleHttp($params, $method);
    }

    /**
     * 获取退款账户的余额
     *
     * @param $param
     * @return array|bool
     * @throws \AllInPay\AllInPay\Exceptions\Exception
     */
    public function queryReserveFundBalance($param)
    {
        $method = 'allinpay.yunst.merchantService.queryReserveFundBalance';

        $param['sysid'] = $this->config->getConf('sys_id');

        return $this->setBusiness('selectBalance')->guzzleHttp($param, $method);
    }
}
