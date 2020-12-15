<?php

namespace AllInPay\SignContract;

use AllInPay\AllInPay\Http\AllInPayClient;

class SignContract extends AllInPayClient
{
    // 签约成功后的跳转地址
    const JUMP_URL = 'https://www.baidu.com';

    // 来源
    const PC_SOURCE = 2;

    /**
     * 服务对象
     *
     * @var string
     */
    protected $service = 'MemberService';

    /**
     * 签约接口的url返回
     *
     * @param array $request
     *
     * @return string
     */
    public function signContract(array $request)
    {
        $backUrl = '/api/finance/pay/sign-contract/notify';

        $params = [
            'bizUserId' => $request['bizUserId'],
            'jumpUrl'   => $request['jumpUrl'] ?? self::JUMP_URL,
            'backUrl'   => $request['backUrl'],
            'source'    => $request['source'] ?? self::PC_SOURCE,
            'method'    => 'allinpay.yunst.memberService.signContract',
        ];

        $request = $this->setBusiness('signContract')
            ->concatUrlParams($params);

        return $this->config->getConf('api_url').'?'.$request;
    }
}
