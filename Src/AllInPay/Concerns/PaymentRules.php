<?php

namespace AllInPay\AllInPay\Concerns;

use AllInPay\AllInPay\Exceptions\Exception;

trait PaymentRules
{
    /**
     * 停止支付
     *
     * @throws Exception
     */
    protected function stopPay()
    {
        if (config('allinpay.shut')) {
            throw new Exception('平台暂时停止支付,请联系客服人员');
        }
    }

    /**
     * 禁止黑名单支付
     * @throws Exception
     */
    protected function forbidBlackCompany()
    {
        $companyBlacks = config('allinpay.company_blacks');

        if ($this->inBlacksArray($companyBlacks)) {
            throw new Exception('企业已加入黑名单冻结中,请联系平台客服');
        }
    }

    /**
     * 禁止用户黑名单支付
     * @throws Exception
     */
    protected function forbidBlackUser()
    {
        $userBlacks = config('allinpay.user_blacks');

        if ($this->inBlacksArray($userBlacks)) {
            throw new Exception('您已经加入了黑名单,请联系平台客服！');
        }
    }

    /**
     * 判断是否在黑名单中
     *
     * @param $blacks
     *
     * @return bool
     */
    protected function inBlacksArray($blacks): bool
    {
        return ! empty($blacks) && in_array($this->bizUserId, $blacks);
    }

}
