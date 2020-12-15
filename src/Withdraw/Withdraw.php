<?php

namespace AllInPay\Withdraw;

use AllInPay\AllInPay\Http\AllInPayClient;
use AllInPay\AllInPay\Util\{Helper,IndustryCode,IndustryName};


class Withdraw extends AllInPayClient
{
    /**
     * 企业会员认证
     */
    const PC_SOURCE = 2;

    protected $service = 'OrderService';

    /**
     * 提现申请接口
     *
     * @param array $request 请求参数
     *
     * @throws
     *
     * @return array|bool
     */
    public function withdrawApply(array $request)
    {
        $method = 'allinpay.yunst.orderService.withdrawApply';

        $params = $this->formatCashRequest($request);

        return $this->setBusiness('withdraw')->guzzleHttp($params, $method);
    }

    /**
     * 格式化提现申请的参数
     *
     * @param array $request
     *
     * @return array
     */
    public function formatCashRequest(array $request): array
    {
        return [
            'bizOrderNo'   => $request['bizOrderNo'] ?: Helper::getBizOrderNo($request['bizUserId'], 'withdraw'),
            'bizUserId'    => $request['bizUserId'],
            'accountSetNo' => $request['accountSetNo'] ?? '',
            'amount'       => $request['amount'],
            'fee'          => $request['fee'],
            'backUrl'      => $request['backUrl'],
            'bankCardNo'   => $this->encryptAES($request['bankCardNo']),
            'industryCode' => IndustryCode::OTHER,
            'industryName' => IndustryName::OTHER,
            'source'       => $request['source'] ?? self::PC_SOURCE,
        ];
    }
}
