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
        $offset = (int)Yii::$app->request->getQueryParam('offset', 0);
        $params = [':id' => (int)$categoryId];

        $sqlWith = <<<SQL
WITH RECURSIVE `cte` AS (
    SELECT id
    FROM `categories`
    WHERE id = :id
    UNION ALL
    SELECT c2.id
    FROM `cte` AS c1
             JOIN `categories` AS c2 ON c1.id = c2.parent_id
)
SQL;
        $sqlItems = <<<SQL
SELECT * FROM news WHERE category_id IN (SELECT * FROM cte) LIMIT 10 OFFSET $offset
SQL;
        $sqlCount = <<<SQL
SELECT COUNT(*) FROM news WHERE category_id IN (SELECT * FROM cte)
SQL;

        $count = Yii::$app->db->createCommand($sqlWith . $sqlCount, $params)->queryScalar();
        $items = News::findBySql($sqlWith . $sqlItems, $params)->all();

        return $this->asJson(['items' => $items, 'count' => $count]);
    }

    public function actionCategoryList()
    {
        $offset = (int)Yii::$app->request->getQueryParam('offset', 0);

        $sqlWith = <<<SQL
WITH RECURSIVE `cte` AS (
    SELECT *
    FROM `categories`
    WHERE parent_id IS NULL
    UNION ALL
    SELECT c2.*
    FROM `cte` AS c1
             JOIN `categories` AS c2 ON c1.id = c2.parent_id
)
SQL;
        $sqlItems = <<<SQL
SELECT * FROM cte LIMIT 10 OFFSET $offset
SQL;
        $sqlCount = <<<SQL
SELECT COUNT(*) FROM cte
SQL;

        $sql = <<<SQL


SQL;

        $count = Yii::$app->db->createCommand($sqlWith . $sqlCount)->queryScalar();
        $items = Category::findBySql($sqlWith . $sqlItems)->all();

        return $this->asJson(['items' => $items, 'count' => $count]);
    }
}
