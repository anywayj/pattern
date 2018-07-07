<?php

namespace frontend\controllers;

use Yii;
use frontend\models\Post;
use frontend\models\Category2_2;
use frontend\models\Lab2_2model;
use frontend\models\PostSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\models\EntryForm;
use frontend\models\UploadImage;
use yii\web\UploadedFile;



/**
 * Lab2_2Controller implements the CRUD actions for Post model.
 */
class Lab2_2Controller extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Post models.
     * @return mixed
     */

    


     public function actionLab2_2v()
    {  

// $modelCategory = new Category2_2();
// $cats = $modelCategory::find()->where('id=3')->all();
// $cats = $modelCategory::find()->where(['like', 'name', 'категор'])->all();
// $cats = Category2_2::find()->select(['*'])->where(['like', 'name', 'категор'])->andWhere('id<4')->all(); //orderBy('name ASC')
$cats = Category2_2::find()->select(['*'])->where(['like', 'name', 'категор'])->andWhere('id<4')->orderBy('name DESC')->all();
//$cats = Category2_2::find()->select(['id', 'name'])->limit(2)->all();
//$cats = Category2_2::find()->select(['id', 'name'])->limit(3)->offset(1)->all(); //начиная со второй,всего 3 штуки
//$cats = Category2_2::find()->one();
//$cats = Category2_2::find()->limit(1)->one();

        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $posts = Post::find()->all();

        return $this->render('lab2_2v', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'posts' => $posts,
            'cats' => $cats,   
        ]);  

    } 


}
