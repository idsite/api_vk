# api_vk
Класс для работы с API vk

# Компонент в конфиге:
'vk'=>
        [
            'class'=>'\idsite\api_vk\VKAPI',
            'client_id'=>'...',
            'client_secret'=>'...',
            'redirect_uri'=>'...'
        ],

# Ссылка:
  <a href="<?=Yii::$app->vk->getUrlOAuth()?>" onclick="window.open(this.href,'Вход через VK','width=600,height=500,resizable=yes,scrollbars=yes,status=yes');return false">Войти через вк</a>

# Дейсвие:
    public function actionLogin() {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $vk = Yii::$app->vk;

        /* @var $vk \idsite\api_vk\VKAPI */
        if (($data = $vk->accessToken())) {
            //токен получен 
           // авторизация или регистрация пользователя
        }
        $vk->redirect(Yii::$app->getUser()->getReturnUrl('/'));
    }