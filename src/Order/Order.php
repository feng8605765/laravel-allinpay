<?php

namespace AllInPay\Order;

use AllInPay\AllInPay\Http\AllInPayClient;

class Order extends AllInPayClient
{

    /**
     * [depositApply 充值申请]
     * type: 默认 原生小程序支付
     * fast_payment 快捷支付
     * cashier_gateway 收银宝网关
     * wechat_applet 微信小程序
     * cashiers_pay_by_credit_card 收银宝刷卡支付
     * cash_register_is_sweeping 收银宝正扫码
     *
     * @param array $request
     * @param $type
     * @return array|bool
     * @throws \AllInPay\AllInPay\Exceptions\Exception
     */
    public function depositApply(array $request, $type = 'default')
    {
        $param["bizOrderNo"] = $request['bizOrderNo'] ?: "TL".date("Ymdhis");
        $param["bizUserId"] = $request['bizUserId'];
        $param["accountSetNo"] = $request['accountSetNo'];
        $param["amount"] = $request['amount'];
        $param["fee"] = $request['fee'];
        $param["validateType"] = $request['validateType'];
        $param["frontUrl"] = $request['frontUrl'];
        $param["backUrl"] = $request['backUrl'];

        switch ($type) {
            //快捷支付
            case 'fast_payment':
                $payParam["amount"] = $request['amount'];
                $payParam["bankCardNo"] = $this->encryptAES($request['bankCardNo']);
                $payMethod["QUICKPAY_VSP"] = $payParam;
                break;
            case 'cashier_gateway': // 收银宝网关
                $payParam["amount"] = $request['amount'];
                $payParam["bankCardNo"] = $request['paytype'] ?? "B2C,B2B";
                $payMethod['GATEWAY_VSP'] = $payParam;
                break;
            case "wechat_applet": // 微信小程序
                $payParam['amount'] = $request['amount'];
                $payParam['limitPay'] = $request['limitPay'] ?? "no_credit";
                $payParam['acct'] = $request['acct'] ?? "oUU99wefa2BWRDmoIqUjMTFrxMGY";
                $payMethod["WECHATPAY_MINIPROGRAM"] = $payParam;
                break;
            case "cashiers_pay_by_credit_card": // 收银宝刷卡支付
                $payParam['amount'] = $request['amount'];
                $payParam['limitPay'] = $request['limitPay'] ?? "no_credit";
                $payParam['authcode'] = $request['authcode'] ?? "oUU99wefa2BWRDmoIqUjMTFrxMGY";
                $payMethod['CODEPAY_VSP'] = $payParam;
                break;
            case "cash_register_is_sweeping": // 收银宝正扫
                $payParam['amount'] = $request['amount'];
                $payParam['limitPay'] = $request['limitPay'] ?? "no_credit";
                $payMethod['SCAN_ALIPAY'] = $payParam;
                break;
            default:  // 微信原生小程序
                $payParam['amount'] = $request['amount'];
                $payParam['wxAppId'] = $request['wxAppId'] ?? 'wx806d3df873b1e9fc';
                $payParam['wxMchtId'] = $request['wxMchtId'] ?? '1550008971';
                $payParam['limitPay'] = $request['limitPay'] ?? "no_credit";
                $payParam['acct'] = $request['acct'] ?? "olRPt4pZRC04UilIX8GehfLj";
                $payParam['cusip'] = $request['cusip'] ?? "10.168.1.70";
                $payMethod["WECHATPAY_MINIPROGRAM_OPEN"] = $payParam;
        }

        $param["payMethod"] = $payMethod;
        $param["industryCode"] = $request["industryCode"];
        $param["industryName"] = $request["industryName"];
        $param["source"] = $request['source'];
        $method = "allinpay.yunst.orderService.depositApply";

        return $this->setBusiness('depositApply')
            ->guzzleHttp($param, $method);
    }

    /**
     * 查询储备资金余额

     * @param string $sysId
     * @return array|bool
     * @throws \AllInPay\AllInPay\Exceptions\Exception
     */
    public function queryReserveFundBalance(string $sysId)
    {
        $params["sysid"] = $sysId ?: "2002141050372732927";
        $method = "allinpay.yunst.merchantService.queryReserveFundBalance";
        return $this->setBusiness('queryReserveFundBalance')
            ->guzzleHttp($params, $method);
    }

    /**
     * [payBySMS 确认支付（前台+短信验证码确认）]
     * @param array $request
     * @return string
     */
    public function payBySMS(array $request)
    {
        $param["bizUserId"] = $request['bizUserId'];
        $param["bizOrderNo"] = $request['bizOrderNo'];
        $param["verificationCode"] = $request['verificationCode'];
        $param["consumerIp"] = $request['consumerIp'];
        $method = "allinpay.yunst.orderService.payBySMS";
        $result = $yunClient->concatUrlParams($method,$param);
        $url = $this->config->getConf('api_url').'?'.$result;
        $this->log->info("[前台+短信验证码确认URL]".$url);

//        $this->logIns->logMessage("[前台+短信验证码确认URL]",Log::INFO,$url);
        return $url;
        //header("Location:$url");
    }

    /**
     * [consumeApply 消费申请]
     * @param array $request
     * @param string $type
     * type：
     * BALANCE 余额支付
     * QUICKPAY_VSP 快捷支付
     * GATEWAY_VSP 收银宝网关
     * WECHATPAY_MINIPROGRAM 微信小程序
     * WECHATPAY_MINIPROGRAM_OPEN 微信原生小程序
     * CODEPAY_VSP 刷卡支付
     * H5_CASHIER_VSP H5支付
     * @param array $splitParam 分账数据
     * @return array|bool
     * @throws \AllInPay\AllInPay\Exceptions\Exception
     */
    public function consumeApply(array $request, $type="WECHATPAY_MINIPROGRAM_OPEN", $splitParam = [])
    {
        $param["payerId"] = $request['payerId'];
        $param["recieverId"] = $request['recieverId'];
        $param["amount"] = $request['amount'];
        $param["fee"] = $request['fee'];
        $param["bizOrderNo"] = $request['bizOrderNo'] ?? "TL".date("Ymdhis");
        $param["validateType"] = $request['validateType'] ?? '';
        $param["backUrl"] = $request['backUrl'] ?? '';
        $param["frontUrl"] = $request['frontUrl'] ?? '';

        switch ($type) {
            case "BALANCE": // 余额支付
                $payParam[0]['accountSetNo'] = $request['accountSetNo'] ?: '400193';
                $payParam[0]["amount"] = $request['amount'];
                $payMethod["BALANCE"] = $payParam;
                break;
            case "QUICKPAY_VSP": // 快捷支付
                $payParam["amount"] = $request['amount'];
                $payParam["bankCardNo"] = $this->encryptAES($request['bankCardNo']);
                $payMethod["QUICKPAY_VSP"] = $payParam;
            case "GATEWAY_VSP": // 收银宝网关
                $payParam['amount'] = $request['amount'];
                $payParam['paytype'] = $request['paytype'] ?? "B2B";
                $payMethod["GATEWAY_VSP"] = $payParam;
                break;
            case "WECHATPAY_MINIPROGRAM": // 微信小程序
                $payParam["amount"] = $request["amount"];
                $payParam["limitPay"] = $request["limitPay"] ?? "no_credit";
                $payParam["acct"] = $request["acct"] ?? "oUU99wefa2BWRDmoIqUjMTFrxMGY";
                $payMethod["WECHATPAY_MINIPROGRAM"] = $payParam;
                break;
            case "WECHATPAY_MINIPROGRAM_OPEN": // 微信原生小程序
                $payParam["amount"] = $request["amount"];
                $payParam["wxAppId"] = $request["wxAppId"] ?? "wx806d3df873b1e9fc";
                $payParam["wxMchtId"] = $request["wxMchtId"] ?? "1550008971";
                $payParam["limitPay"] = $request["limitPay"] ?: "no_credit";
                $payParam["acct"] = $request["acct"] ?? "oUU99wefa2BWRDmoIqUjMTFrxMGY";
                $payParam["cusip"] = $request["cusip"] ?? "10.168.1.70";
                $payMethod = $payParam;
                break;
            case "CODEPAY_VSP": //刷卡支付
                $payParam["amount"] = $request["amount"];
                $payParam["limitPay"] = $request["limitPay"] ?? "no_credit";
                $payParam["authcode"] = $request["authcode"] ?? "135022012029210261";
                $payMethod["CODEPAY_VSP"] = $payParam;
                break;
            case "H5_CASHIER_VSP": // H5支付
                $payParam["amount"] = $request["amount"];
                $payParam["limitPay"] = $request["limitPay"];
                $payMethod = $payParam;
                break;
        }


        // // 二级分账
        // $splitRuleList[0]["bizUserId"]="testtlzf02";
        // $splitRuleList[0]["accountSetNo"]="400193";
        // $splitRuleList[0]["amount"]=1;
        // $splitRuleList[0]["fee"]=0;
        // $splitRuleList[0]["remark"]="消费二级分账";

        // //一级分账
        // $splitParam[0]["bizUserId"]="testtlzf01";
        // $splitParam[0]["accountSetNo"]="400193";
        // $splitParam[0]["amount"]=1;
        // $splitParam[0]["fee"]=0;
        // $splitParam[0]["remark"]="消费一级分账";
        // $splitParam[0]["splitRuleList"]=$splitRuleList;
//         $param["splitRule"] = $splitParam;
        if (isset($splitParam)) {
            $payParam["splitRule"] = $splitParam;
        }
        $param["payMethod"] = $payMethod ?? '';
        $param["industryCode"] = $request["industryCode"];
        $param["industryName"] = $request["industryName"];
        $param["source"] = $request["source"];
        $method = "allinpay.yunst.orderService.consumeApply";
        return $this->setBusiness('consumeApply')
            ->guzzleHttp($param, $method);
    }

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
