<?php
namespace jorudan\wechat\OfficialAccount;

use jorudan\wechat\Kernel\Config;
use jorudan\wechat\Kernel\Util;
use jorudan\wechat\Traits\AccessToken;

/**
 * 公众号素材管理
 *
 * @package jorudan\wechat\OfficialAccount
 * @author yaobin
 * @since 1.0
 */
class Material extends Config
{
    use AccessToken;

    /**
     *
     *
     * @param string $type
     * @param int $offset
     * @return array
     */
    public function batchget($type = 'news', $offset = 0)
    {
        if (!isset($this->_config['appid']) || !isset($this->_config['secret']))
        {
            return [
                'errcode' => 1,
                'errmsg' => '参数异常',
            ];
        }

        $accessToken = $this->getAccessToken($this->_config['appid'], $this->_config['secret']);

        if ($accessToken == null)
        {
            return [
                'errcode' => 2,
                'errmsg' => '系统异常',
            ];
        }

        $url = "https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=" . $accessToken['access_token'];
        return json_decode(Util::urlRequest($url, ['type'=>$type, 'offset'=>$offset, 'count'=>20]), true);
    }

    /**
     *
     *
     * @param string $id
     * @return array
     */
    public function get($id)
    {
        if (!isset($this->_config['appid']) || !isset($this->_config['secret']) || empty($id))
        {
            return [
                'errcode' => 1,
                'errmsg' => '参数异常',
            ];
        }

        $accessToken = $this->getAccessToken($this->_config['appid'], $this->_config['secret']);

        if ($accessToken == null)
        {
            return [
                'errcode' => 2,
                'errmsg' => '系统异常',
            ];
        }

        $url = "https://api.weixin.qq.com/cgi-bin/material/get_material?access_token=" . $accessToken['access_token'];
        return json_decode(Util::urlRequest($url, ['media_id'=>$id]), true);
    }

    /**
     *
     *
     * @param array $params
     * @return array
     */
    public function updateNews($params)
    {
        if (!isset($this->_config['appid']) || !isset($this->_config['secret']) || empty($params))
        {
            return [
                'errcode' => 1,
                'errmsg' => '参数异常',
            ];
        }

        $accessToken = $this->getAccessToken($this->_config['appid'], $this->_config['secret']);

        if ($accessToken == null)
        {
            return [
                'errcode' => 2,
                'errmsg' => '系统异常',
            ];
        }

        $url = "https://api.weixin.qq.com/cgi-bin/material/update_news?access_token=" . $accessToken['access_token'];
        return json_decode(Util::urlRequest($url, $params), true);
    }
}