<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\tag\widgets\views
 * @category   CategoryName
 */

use lispa\amos\tag\AmosTag;

/**
 *
 * @var \lispa\amos\core\record\AmosRecordAudit $model
 * @var \lispa\amos\core\forms\ActiveForm $form
 * @var string $name
 * @var array $trees
 * @var array $limit_trees
 * @var bool $is_search
 * @var array $tags_selected
 * @var bool $hideHeader
 * @var string $id
 */

\lispa\amos\tag\assets\ModuleTagFormAsset::register($this); 

?>

    <div id="<?= $id ?>" class="body">
        <div class="intestazione-box">
            <?php if (!$hideHeader): ?>
                <?php
                if (!$is_search) {
                    echo \lispa\amos\core\helpers\Html::tag('h3', AmosTag::tHtml('amostag', 'Tags areas of interest'), [
                        'class' => 'tags-title'
                    ]);
                } else {
                    echo \lispa\amos\core\helpers\Html::tag('label', AmosTag::tHtml('amostag', 'Tags areas of interest'));
                }
                ?>
            <?php endif; ?>
        </div>
        <?php
        $data_trees = [];
        foreach ($trees as $tree):
            //dati dell'albero
            $id_tree = $tree['id'];
            $label_tree = $tree['nome'];
            $limit_tree = (array_key_exists("tree_" . $id_tree,
                $limit_trees) && $limit_trees["tree_" . $id_tree] ? $limit_trees["tree_" . $id_tree] : false);
            $tags_selected_tree = (array_key_exists("tree_" . $id_tree,
                $tags_selected) && $tags_selected["tree_" . $id_tree] ? $tags_selected["tree_" . $id_tree] : []);
            
            //inserisce i dati nell'array per gli eventi js
            $data_trees[] = [
                "id" => $id_tree,
                "limit" => $limit_tree,
                "tags_selected" => $tags_selected_tree
            ];
            ?>
            <div class="amos-tag-tree-container row">
                <?php if (!$is_search): ?>
                    <div id="remaining_tag_tree_<?= $id_tree ?>" class="remaining_tag_tree col-xs-12">
                        <?= AmosTag::tHtml('amostag', 'Scelte rimanenti:') ?>
                        <span class="tree-remaining-tag-number"></span>
                    </div>
                <?php endif; ?>
                <div id="tree_<?= $id_tree ?>" class="col-sm-12 col-md-8">
                    <?php
                    $model->setFocusRoot($id_tree);
                    $options = [
                        'id' => 'tree_obj_' . $id_tree,
                        'disabled' => false,
                        'name' => $model->formName() . '[tagValues][' . $id_tree . ']',
                    ];
                    if ($is_search) {
                        $options['value'] = !empty($tags_selected[$id_tree]) ? $tags_selected[$id_tree] : '';
                    }
                    echo $form->field($model, $name)->widget(\kartik\tree\TreeViewInput::className(), [
                        'query' => lispa\amos\tag\models\Tag::find()
                            ->andWhere(['root' => $id_tree])
                            ->addOrderBy('root, lft'),
                        'headingOptions' => ['label' => $label_tree],
                        'rootOptions' => [
                            'label' => '<i class="fa fa-tree text-success"></i>',
                            'class' => 'text-success hidden'
                        ],
                        'fontAwesome' => false,
                        'asDropdown' => false,
                        'multiple' => true,
                        'cascadeSelectChildren' => ($limit_tree ? false : true),
                        'options' => $options,
                    ])->label(false);
                    ?>
                </div>
                <div id="preview_tag_tree_<?= $id_tree ?>" class="preview_tag_tree col-sm-12 col-md-4"></div>
                <div class="clearfix"></div>
            </div>
            <?php
        endforeach;
        ?>
    </div>
<?php
$options = [
    'data_trees' => $data_trees,
    'error_limit_tags' => AmosTag::tHtml('amostag', 'Hai superato le scelte disponibili per questi descrittori.'),
    'tags_unlimited' => AmosTag::tHtml('amostag', 'illimitate'),
    'no_tags_selected' => AmosTag::tHtml('amostag', 'Nessun tag selezionato'),
    'icon_remove_tag' => \lispa\amos\core\icons\AmosIcons::show('close', [], 'am'),
];
$this->registerJs(
    "if (typeof TagFormVars === 'undefined' || TagFormVars === null) {
        TagFormVars = [];
    }
    TagFormVars.push(" . \yii\helpers\Json::htmlEncode($options) . ");",
    \yii\web\View::POS_HEAD
);
?>