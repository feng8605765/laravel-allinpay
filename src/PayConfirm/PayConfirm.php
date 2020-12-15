<?php

namespace AllInPay\PayConfirm;

use AllInPay\AllInPay\Http\AllInPayClient;

class PayConfirm extends AllInPayClient
{
    /**
     * 确认支付+验证码
     *
     * @param array $request
     *
     * @throws \AllInPay\AllInPay\Exceptions\Exception
     *
     * @return array|bool
     */
    public function payConfirm(array $request)
    {
        $params = [
            'bizOrderNo'       => $request['bizOrderNo'],
            'bizUserId'        => $request['bizUserId'],
            'verificationCode' => $request['verificationCode'],
            'consumerIp'       => $request['consumerIp'],
            'notifyUrl'        => $request['notifyUrl'],
        ];

        $method = 'allinpay.yunst.orderService.payByBackSMS';

        return $this->setBusiness('payByBackSMS')
            ->guzzleHttp($params, $method);
    }

    /**
     * 提现重发短信
     *
     * @param string $bizOrderNo
     *
     * @throws \AllInPay\AllInPay\Exceptions\Exception
     *
     * @return array|bool
     */
    public function resendPaySMS(string $bizOrderNo)
    {
        $method = 'allinpay.yunst.orderService.resendPaySMS';

        $params = [
            'bizOrderNo' => $bizOrderNo,
        ];

        return $this->setBusiness('resendPaySMS')->guzzleHttp($params, $method);
    }
}
