<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\Category;
use app\models\News;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";

        return ExitCode::OK;
    }

    public function actionSeedDatabase()
    {
        /** @var Category[] $categoryList */
        $categoryList = [];

        ($categoryList[] = $category1 = new Category(['title' => 'Общество']))->save();
        ($categoryList[] = $category2 = new Category(['title' => 'День города']))->save();
        ($categoryList[] = $category3 = new Category(['title' => '0-3 года']))->save();
        ($categoryList[] = $category4 = new Category(['title' => '3-7 года']))->save();
        ($categoryList[] = $category5 = new Category(['title' => 'Спорт']))->save();

        ($categoryList[] = $category6 = new Category(['title' => 'городская жизнь', 'parent_id' => $category1->id]))->save();
        ($categoryList[] = $category7 = new Category(['title' => 'выборы', 'parent_id' => $category1->id]))->save();

        ($categoryList[] = $category8 = new Category(['title' => 'салюты', 'parent_id' => $category2->id]))->save();
        ($categoryList[] = $category9 = new Category(['title' => 'детская площадка', 'parent_id' => $category2->id]))->save();

        $faker = \Faker\Factory::create();

        for ($i1 = 0; $i1 < 100; ++$i1) {
            $rows = [];

            for ($i2 = 0; $i2 < 10; ++$i2) {
                /** @var Category $cat */
                $cat = $faker->randomElement($categoryList);
                $rows[] = [$faker->text(500), $cat->id];
            }

            \Yii::$app->db->createCommand()->batchInsert(News::tableName(), ['title', 'category_id'], $rows)->execute();
        }

        return ExitCode::OK;
    }
}
