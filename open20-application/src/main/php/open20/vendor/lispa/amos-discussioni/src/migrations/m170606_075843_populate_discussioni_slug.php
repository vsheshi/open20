<?php

use yii\db\Migration;

class m170606_075843_populate_discussioni_slug extends Migration
{
    public function safeUp()
    {

        foreach (\lispa\amos\discussioni\models\DiscussioniTopic::find()
                     ->andWhere([
                         'slug' => null
                     ])
                     ->orderBy(['id' => SORT_ASC])
                     ->all() as $topic) {


            /**@var $topic \lispa\amos\discussioni\models\DiscussioniTopic */
            $topic->detachBehaviors();

            $topic->attachBehavior('slug', [
                'class' => \yii\behaviors\SluggableBehavior::className(),
                'attribute' => 'titolo',
                'slugAttribute' => 'slug',
                'ensureUnique' => true
            ]);

            $topic->validate(['slug']);
            $topic->save(false);

            \yii\helpers\Console::stdout("SLUG: {$topic->slug}\n\n");
        }

        return true;
    }

    public function safeDown()
    {

        return true;
    }
}
