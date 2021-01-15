<?php

namespace app\controllers;

use app\models\Category;
use app\models\News;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
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
     * {@inheritdoc}
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
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionNewsList($categoryId)
    {
        $sql = <<<SQL
WITH RECURSIVE `cte` AS (
    SELECT id
    FROM `categories`
    WHERE id = :id
    UNION ALL
    SELECT c2.id
    FROM `cte` AS c1
             JOIN `categories` AS c2 ON c1.id = c2.parent_id
)
SELECT * FROM news WHERE category_id IN (SELECT * FROM cte)
SQL;
        $query = News::findBySql($sql, [':id' => $categoryId]);
        $query->limit(10);
        $items = $query->all();

        return $this->asJson($items);
    }

    public function actionCategoryList()
    {
        $sql = <<<SQL
WITH RECURSIVE `cte` AS (
    SELECT *
    FROM `categories`
    WHERE parent_id IS NULL
    UNION ALL
    SELECT c2.*
    FROM `cte` AS c1
             JOIN `categories` AS c2 ON c1.id = c2.parent_id
)
SELECT *
FROM cte
SQL;

        $query = Category::findBySql($sql);
        $query->limit(100);

        $items = $query->all();

        return $this->asJson($items);
    }
}
