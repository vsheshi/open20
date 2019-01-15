<?php
use \lispa\amos\cwh\AmosCwh;
/** @var \lispa\amos\core\record\AmosRecordAudit $model */
/** @var \lispa\amos\core\forms\ActiveForm $form */
/** @var string $name */
\lispa\amos\cwh\assets\ModuleAttivitaAsset::register($this);
?>

<div id="amos-cwh-tag">
    <?php
    $data_trees = [];
    ?>
    <div id="vista-semplice">
        <section class="section-data">
<!--            <h2 class="col-xs-6 nom-t">-->
<!--                < ?= \lispa\amos\core\icons\AmosIcons::show('bullhorn', [], 'dash'); ?>-->
<!--                < ?= \lispa\amos\cwh\AmosCwh::t('amoscwh', 'Semplice'); ?>-->
<!--            </h2>-->
<!--            <div class="col-xs-6 nopr">-->
<!--                <button type="button" id="bk-avanzata"-->
<!--                        class="btn bk-btnChangeView dropdown-toggle pull-right"-->
<!--                        data-toggle="dropdown" aria-expanded="false">-->
<!--                    <span class="am am-swap-vertical"></span>-->
<!--                    < ?= \lispa\amos\cwh\AmosCwh::t('amoscwh', 'Avanzata'); ?>-->
<!--                </button>-->
<!--            </div>-->
            <div class="col-xs-12 nop">

                <?php
                if(!count($contentsTreesSimple)): ?>
                    <div class="alert fade in kv-alert alert-warning">
                        Non sono stati inseriti tag per questo plugin.
                    </div>
                <?php
                endif;
                ?>



                <?php foreach ($contentsTreesSimple as $id_tree => $label_tree):
                $limit_tree = (array_key_exists("tree_".$id_tree, $limit_trees) && $limit_trees["tree_".$id_tree] ? $limit_trees["tree_".$id_tree] : false);
                $tags_selected_tree = (
                    array_key_exists('simple-choice', $tags_selected)
                    &&
                    $tags_selected['simple-choice']
                    &&
                    array_key_exists("tree_".$id_tree, $tags_selected['simple-choice'])
                    &&
                    $tags_selected['simple-choice']["tree_".$id_tree]
                ? $tags_selected['simple-choice']["tree_".$id_tree] : []);

                //verifica se esiste già il contenitore
                if(!array_key_exists("simple-choice", $data_trees)){
                    $data_trees["simple-choice"] = [];
                }

                //inserisce i dati nell'array per gli eventi js
                $data_trees["simple-choice"][] = [
                    "id" => $id_tree,
                    "limit" => $limit_tree,
                    "tags_selected" => $tags_selected_tree,
                ];
                ?>
                <div class="amos-cwh-tag-tree-container">
                    <div class="col-md-4 col-xs-12">
                        <div id="remaining_tag_tree_simple-choice_<?=$id_tree?>" class="remaining_tag_tree">
                            <?=AmosCwh::t('amoscwh', 'Scelte rimanenti:')?>
                            <span class="tree-remaining-tag-number"></span>
                        </div>
                        <div id="preview_tag_tree_simple-choice_<?=$id_tree?>" class="preview_tag_tree"></div>
                    </div>
                    <div id="tree_simple_<?=$id_tree?>" class="col-sm-12 col-md-8 nopr">
                        <?= \kartik\tree\TreeViewInput::widget([
                            'query' => lispa\amos\tag\models\Tag::find()
                                ->andWhere(['root' => $id_tree])
                                ->addOrderBy('root, lft'),
                            'headingOptions' => ['label' => $label_tree],
                            'rootOptions' => ['label' => '<i class="dash dash-bullhorn text-success"></i>', 'class' => 'text-success hidden'],
                            'fontAwesome' => false,
                            'asDropdown' => false,
                            'multiple' => true,
                            'cascadeSelectChildren' => ($limit_tree ? false : true),
                            'value' => $model->getInterestTagValues(null, 'simple-choice', $id_tree),
                            'name' => $model->formName() . '[' . $name . '][simple-choice][' . $id_tree . ']',
                            'options' => [
                                'disabled' => false,
                                'name' => $model->formName() . '[' . $name . '][simple-choice][' . $id_tree . ']',
                                'data' =>
                                    [
                                        'tree-attach' => $id_tree
                                    ]
                            ],
                            'id' => 'simple-choice-treeview-' . $id_tree,
                        ]); ?>
                    </div>
                    <div class="clearfix"></div>
                </div>
            <?php endforeach; ?>
            </div>
        </section>
    </div>

    <div id="vista-avanzata">
        <section class="section-data">
            <h2 class="col-xs-6 nom-t">
                <?= \lispa\amos\core\icons\AmosIcons::show('bullhorn', [], 'dash'); ?>
                <?= \lispa\amos\cwh\AmosCwh::t('amoscwh', 'Avanzata'); ?>
            </h2>
            <div class="col-xs-6 nopr">
                <button type="button" id="bk-semplice"
                        class="btn bk-btnChangeView dropdown-toggle pull-right"
                        data-toggle="dropdown" aria-expanded="false">
                    <span class="am am-swap-vertical"></span>
                    <?= \lispa\amos\cwh\AmosCwh::t('amoscwh', 'Semplice'); ?>
                </button>
            </div>
            <div class="col-xs-12 nopr">
                <?php
                foreach ($contentsTrees as $contentsTree): ?>
                    <hr class="col-xs-12"/>
                    <h3 class="col-xs-12"><?= $contentsTree['label'] ?></h3>
                    <?php
                    foreach ($contentsTree['trees'] as $tree):
                        //dati dell'albero
                        $id_tree = $tree['id'];
                        $label_tree = $tree['nome'];
                        $ref_tree = $contentsTree['classnameRef'];
                        $classname_tree = $contentsTree['classname'];
                        $limit_tree = (array_key_exists("tree_".$id_tree, $limit_trees) && $limit_trees["tree_".$id_tree] ? $limit_trees["tree_".$id_tree] : false);
                        $tags_selected_tree = (
                            array_key_exists($classname_tree, $tags_selected)
                            &&
                            $tags_selected[$classname_tree]
                            &&
                            array_key_exists("tree_".$id_tree, $tags_selected[$classname_tree])
                            &&
                            $tags_selected[$classname_tree]["tree_".$id_tree]
                        ? $tags_selected[$classname_tree]["tree_".$id_tree] : []);

                        //verifica se esiste già il contenitore
                        if(!array_key_exists($ref_tree, $data_trees)){
                            $data_trees[$ref_tree] = [];
                        }

                        //inserisce i dati nell'array per gli eventi js
                        $data_trees[$ref_tree][] = [
                            "id" => $id_tree,
                            "limit" => $limit_tree,
                            "tags_selected" => $tags_selected_tree,
                        ];
                        ?>
                        <div class="amos-cwh-tag-tree-container">
                            <div class="col-md-4 col-xs-12">
                                <div id="remaining_tag_tree_<?=$ref_tree?>_<?=$id_tree?>" class="remaining_tag_tree">
                                    <?=AmosCwh::t('amoscwh', 'Scelte rimanenti:')?>
                                    <span class="tree-remaining-tag-number"></span>
                                </div>
                                <div id="preview_tag_tree_<?=$ref_tree?>_<?=$id_tree?>" class="preview_tag_tree"></div>
                            </div>
                            <div id="tree_<?=$ref_tree?>_<?=$id_tree?>" class="col-sm-12 col-md-8 nopr">
                            <?php
                                echo \kartik\tree\TreeViewInput::widget([
                                    'query' => lispa\amos\tag\models\Tag::find()
                                        ->andWhere(['root' => $id_tree])
                                        ->addOrderBy('root, lft'),
                                    'headingOptions' => ['label' => $label_tree],
                                    'rootOptions' => ['label' => '<i class="dash dash-bullhorn text-success"></i>', 'class' => 'text-success hidden'],
                                    'fontAwesome' => false,
                                    'asDropdown' => false,
                                    'multiple' => true,
                                    'cascadeSelectChildren' => ($limit_tree ? false : true),
                                    'value' => $model->getInterestTagValues(null, $classname_tree, $id_tree),
                                    'name' => $model->formName() . '[' . $name . '][' . $classname_tree . '][' . $id_tree . ']',
                                    'options' => [
                                        'disabled' => false,
                                        'name' => $model->formName() . '[' . $name . '][' . $classname_tree . '][' . $id_tree . ']'
                                    ],
                                    'id' => 'advanced-choice-treeview-' . $id_tree . '-' . $ref_tree,
                                ]);
                            ?>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <?php
                    endforeach;
                endforeach;
                ?>
            </div>
        </section>
    </div>
</div>
<?php
$options = [
    'data_trees' => $data_trees,
    'error_limit_tags' => AmosCwh::t('amoscwh', 'Hai superato le scelte disponibili per questi descrittori.'),
    'tags_unlimited' => AmosCwh::t('amoscwh', 'illimitate'),
    'no_tags_selected' => AmosCwh::t('amoscwh', 'Nessun tag selezionato'),
    'icon_remove_tag' => \lispa\amos\core\icons\AmosIcons::show('close', [], 'am'),
];
$this->registerJs(
    "var AreeInteresseVars = ".\yii\helpers\Json::htmlEncode($options).";",
    \yii\web\View::POS_HEAD
);
?>