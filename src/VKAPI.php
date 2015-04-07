<?php

namespace idsite\api_vk;

/**
 * Description of VKAPI
 *
 */
class VKAPI extends \yii\base\Object {

    public $client_id;
    public $version = '5.28';
    public $redirect_uri;
    public $token;
    public $client_secret;

    /**
     * Получение ссылки для перенаправление пользователя при авторизации
     * @link https://vk.com/dev/oauth_dialog
     * @param array $params
     */
    public function getUrlOAuth($params = []) {
        $paramsDefault = [
            'client_id' => $this->client_id,
            'redirect_uri' => $this->redirect_uri,
            'display' => 'popup',
            'response_type' => 'code',
            'v' => $this->version
        ];

        return 'https://oauth.vk.com/authorize?' . http_build_query(array_merge($paramsDefault, $params));
    }

    /**
     * получение токена
     * {"access_token":"...", "expires_in":<int>, '''user_id":<int>} 
     * @link https://vk.com/dev/auth_sites
     * @param string $code
     * @return array|bool
     */
    public function accessToken($code = null) {
        if ($code === null) {
            $code = \Yii::$app->getRequest()->getQueryParam('code');
            if ($code === null) {
                return false;
            }
        }

        $url = 'https://oauth.vk.com/access_token?' . http_build_query([
                    'client_id' => $this->client_id,
                    'client_secret' => $this->client_secret,
                    'code' => $code,
                    'redirect_uri' => $this->redirect_uri
        ]);
        $t = \yii\helpers\Json::decode($this->_getContent($url), true);
        if (isset($t['access_token'])) {
            $this->token = $t['access_token'];
            return $t;
        } else {
            return false;
        }
    }

    /**
     * @link https://vk.com/dev/methods список команд
     * @param type $method_name
     * @param array $params
     * @throws Exception
     */
    public function api($method_name, $params = []) {

        if ($this->token)
            $params['access_token'] = $this->token;

        $url = 'https://api.vk.com/method/' . $method_name . '?' . http_build_query($params);

        $result = $this->_getContent($url);

        return \yii\helpers\Json::decode($result);
    }

    /**
     * редирект с закрытием попапа
     * @param string $url
     */
    public function redirect($url) {
        echo \Yii::$app->view->renderFile(__DIR__ . '/_redirect.php', ['url' => $url], $this);
        \Yii::$app->end();
    }

    private function _getContent($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

}
