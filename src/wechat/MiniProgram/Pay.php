<?php
namespace jorudan\wechat\MiniProgram;

use jorudan\wechat\Kernel\Config;
use jorudan\wechat\Kernel\Util;

/**
 * 小程序支付处理
 *
 * @package jorudan\wechat\MiniProgram
 * @author yaobin
 * @since 1.0
 */
class Pay extends Config
{
    /**
     * 统一下单接口
     *
     * @param string $openid     用户ID
     * @param string $body       商品描述
     * @param string $tradeNo    商户订单号
     * @param float  $totalFee   总金额
     * @param string $notifyUrl  通知地址
     * @return null|string
     */
    public function unifiedorder($openid, $body, $tradeNo, $totalFee, $notifyUrl)
    {
        if (!isset($this->_config['appid']) || !isset($this->_config['mch_id']) || !isset($this->_config['pay_key']))
        {
            return [
                'errcode' => 1,
                'errmsg' => '参数异常',
            ];
        }

        $param = [
            'appid' => $this->_config['appid'],
            'mch_id' => $this->_config['mch_id'],
            'nonce_str' => Util::getNonceStr(),
            'body' => $body,
            'out_trade_no' => $tradeNo,
            'total_fee' => $totalFee,
            'spbill_create_ip' => Util::getClientIp(),
            'notify_url' => $notifyUrl,
            'trade_type' => 'JSAPI',
            'openid' => $openid,
        ];

        $param['sign'] = Util::generateSign($param, $this->_config['pay_key']);

        $url = "https://api.mch.weixin.qq.com/pay/unifiedorder";

        $return = Util::urlRequest($url, Util::arrayToXml($param));
        if (!$return)
        {
            return [
                'errcode' => 2,
                'errmsg' => '系统异常!',
            ];
        }

        $return = Util::xmlToArray($return);
        if (!isset($return['return_code']) || $return['return_code'] != 'SUCCESS'
            || !isset($return['result_code']) || $return['result_code'] != 'SUCCESS')
        {
            return [
                'errcode' => 3,
                'errmsg' => '系统异常!',
                'data' => $return,
            ];
        }

        $payParam = [
            'timeStamp' => $_SERVER['REQUEST_TIME'] . "",
            'nonceStr' => Util::getNonceStr(),
            'package' => 'prepay_id=' . $return['prepay_id'],
            'signType' => 'MD5',
        ];
        $payParam['paySign'] = $this->getPaySign($payParam);
        return $payParam;
    }

    protected function getPaySign($param)
    {
        $param['appId'] = $this->_config['appid'];
        return Util::generateSign($param, $this->_config['pay_key']);
    }
}