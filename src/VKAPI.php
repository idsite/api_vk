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
     * @param type $display
     * @param type $scope https://vk.com/dev/permissions
     * @param type $response_type
     */
    public function getUrlOAuth($display = 'popup', $scope = [], $response_type = 'code') {
        return 'https://oauth.vk.com/authorize?' . http_build_query([
                    'client_id' => $this->client_id,
                    'redirect_uri' => $this->redirect_uri,
                    'display' => $display,
                    'scope' => implode(',', $scope),
                    'response_type' => $response_type,
                    'v' => $this->version
        ]);
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
        $t = \yii\helpers\Json::decode(file_get_contents($url), true);
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

        $result = file_get_contents($url);

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

}
