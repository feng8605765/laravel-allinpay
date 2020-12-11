<?php

namespace Modules\Core\Services\AllInPay\Src\Refund;

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
        try {
            $backUrl = '/api/order/refund/notify';

            $params = [
                'bizOrderNo'    => $request['bizOrderNo'],
                'oriBizOrderNo' => $request['oriBizOrderNo'],
                'bizUserId'     => $request['bizUserId'],
                'amount'        => $request['amount'],
                'backUrl'       => $this->envBackUrl($backUrl),
            ];

            $method = 'allinpay.yunst.orderService.refund';

            return $this->setBusiness('refund')->guzzleHttp($params, $method);

        } catch (Exception $exception) {
            throw $exception;
        }
    }
}
