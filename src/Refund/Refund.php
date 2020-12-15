<?php

namespace AllInPay\Refund;

use AllInPay\AllInPay\Exceptions\Exception;
use AllInPay\AllInPay\Http\AllInPayClient;


class Refund extends AllInPayClient
{
    protected $service = 'orderService';

    /**
     * 退款接口
     * @param array $request
     * @return array|bool
     * @throws Exception
     */
    public function refund(array $request)
    {
        $params = [
            'bizOrderNo'    => $request['bizOrderNo'],
            'oriBizOrderNo' => $request['oriBizOrderNo'],
            'bizUserId'     => $request['bizUserId'],
            'amount'        => $request['amount'],
            'backUrl'       => $request['backUrl'],
        ];

        $method = 'allinpay.yunst.orderService.refund';

        return $this->setBusiness('refund')->guzzleHttp($params, $method);
    }
}
