<?php
namespace frontend\controllers;

use Yii;
use GuzzleHttp\Client; 
use yii\helpers\Url;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\models\EntryForm;
use frontend\models\UploadImage;
use yii\web\UploadedFile;
use frontend\models\Post;
use frontend\models\Category2_2;
use frontend\models\News;
use frontend\models\Beststudgroup;
use frontend\models\Prokat;
//use frontend\models\Client;
/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout','index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */


    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
       // $model = Post::find()->with('category2_2')->where(['id'=>$id])->one();
    //$modelCategory = new Category2_2();
   // $cats = Category2_2::find()->select(['*'])->where('id<4')->all();

  //  $cats = Post::find()->one()->getCategory()->all();
     $posts = Post::find()->all();
     $news = News::find()->all();
     $dd = Prokat::find()->all();
    // $kk = Client::find()->all();
     
     $client = new Client();
        // отправляем запрос к странице Яндекса
        $res = $client->request('GET', 'http://www.dgma.donetsk.ua/');
        // получаем данные между открывающим и закрывающим тегами body
        $body = $res->getBody();

     //$modelCategory = new Category2_2();
     //$cats = Category2_2::find()->all();

     //$posts = Post::find()->joinWith('category2_2')->all(); 
// подключаем phpQuery
        $document = \phpQuery::newDocumentHTML($body);
        //Смотрим html страницы Яндекса, определяем внешний класс списка и считываем его командой find
        $news = $document->find(".center_coloumn"); 
    // return $this->render('index', compact('posts'));
     //return $this->render('index', compact('cats'));
      return $this->render('index', [
          //  'searchModel' => $searchModel,
           // 'dataProvider' => $dataProvider,
            'posts' => $posts,
            'news' => $news,
            'beststudgroup' => $beststudgroup,
            'dd' => $dd,
          //  'kk' => $kk,
            'body' => $body,
            'news' => $news,
            //'cats' => $cats,
            //'model' => $model,
        ]);  
    }


    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        $beststudgroup = Beststudgroup::find()->all();
        return $this->render('about', [
          //  'searchModel' => $searchModel,
           // 'dataProvider' => $dataProvider,
          
            'beststudgroup' => $beststudgroup,
            
        ]); 
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */

    public function actionSay1($message = 'Hello my great world')
    {
        return $this->render('Hello123', ['message' => $message]);
    }

    public function actionForm()
    {
        $model = new EntryForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            // данные в $model удачно проверены

            // делаем что-то полезное с $model ...
 
            return $this->render('entry-confirm', ['model' => $model]);
        } else {
            // либо страница отображается первый раз, либо есть ошибка в данных
            return $this->render('form', ['model' => $model]);
        }
    }



    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    public function actionUpload(){ //загрузка изображения
        $model = new UploadImage();
        if(Yii::$app->request->isPost){
        $model->image = UploadedFile::getInstance($model, 'image');
        $model->upload();
        return $this->render('upload', ['model' => $model]);
        }
        return $this->render('upload', ['model' => $model]);
    }



   



    


}
















