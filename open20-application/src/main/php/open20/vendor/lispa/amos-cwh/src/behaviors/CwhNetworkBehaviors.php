<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\cwh\behaviors
 * @category   CategoryName
 */

namespace lispa\amos\cwh\behaviors;

use lispa\amos\cwh\AmosCwh;
use lispa\amos\cwh\models\CwhConfigContents;
use lispa\amos\cwh\models\CwhPubblicazioni;
use Yii;
use yii\base\Event;
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class CwhNetworkBehaviors
 * @package lispa\amos\cwh\behaviors
 */
class CwhNetworkBehaviors extends AttributeBehavior
{
    /**
     * @var array
     */
    public $tags = [
        'rootId' => 1
    ];
    
    public $regola_pubblicazione;
    
    public $tipologia_pubblicazione;
    
    public $destinatari = [];
    
    public $validatori = [];
    
    /**
     * @var array
     */
    public $targets = [];
    
    /**
     * @var array
     */
    public $draftingValidators = [];
    
    /**
     * @var int
     */
    public $publishingRule = null;
    
    /**
     * @var \lispa\amos\cwh\models\CwhPubblicazioni $pubblicazione ;
     */
    private $pubblicazione;
    
    /**
     * @var \lispa\amos\core\record\Record $sender ;
     */
    private $sender;
    
    /**
     * @return int
     */
    public function getPublishingRule()
    {
        return $this->publishingRule;
    }
    
    /**
     * @param int $publishingRule
     */
    public function setPublishingRule($publishingRule)
    {
        $this->publishingRule = $publishingRule;
    }
    
    /**
     * @return array
     */
    public function getTargets()
    {
        return $this->targets;
    }
    
    /**
     * @param array $targets
     */
    public function setTargets($targets)
    {
        $this->targets = $targets;
    }
    
    /**
     * @return array
     */
    public function getDraftingValidators()
    {
        return $this->draftingValidators;
    }
    
    /**
     * @param array $draftingValidators
     */
    public function setDraftingValidators($draftingValidators)
    {
        $this->draftingValidators = $draftingValidators;
    }
    
    /**
     * @return array list of targets publisher
     * @deprecated 2.1.0
     */
    public function getDestinatari()
    {
        return $this->destinatari;
    }
    
    /**
     * @param array $destinatari
     * @deprecated 2.1.0
     */
    public function setDestinatari($destinatari)
    {
        $this->destinatari = $destinatari;
    }
    
    /**
     * @return array
     * @deprecated 2.1.0
     */
    public function getValidatori()
    {
        return $this->validatori;
    }
    
    /**
     * @param array $validatori
     * @deprecated 2.1.0
     */
    public function setValidatori($validatori)
    {
        $this->validatori = $validatori;
    }
    
    /**
     * @return array
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_FIND => 'eventFind',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'eventSavePubblicazione',
            BaseActiveRecord::EVENT_AFTER_INSERT => 'eventSavePubblicazione',
            BaseActiveRecord::EVENT_BEFORE_VALIDATE => 'eventBeforeValidate',
            BaseActiveRecord::EVENT_BEFORE_DELETE => 'eventBeforeDelete'
        ];
    }
    
    
    /**
     * @return string
     * @deprecated 2.1.0
     */
    public function getRegolaPubblicazione()
    {
        if ($this->getPubblicazione() && $this->getPubblicazione()->cwhRegolePubblicazione) {
            return $this->getPubblicazione()->cwhRegolePubblicazione->nome;
        } else {
            return null;
        }
    }
    
    /**
     * @return CwhPubblicazioni
     */
    private function getPubblicazione()
    {
        return $this->pubblicazione;
    }
    
    /**
     * @return string
     */
    public function getNetworkPubblicazione()
    {
        if ($this->getPubblicazione() && $this->getPubblicazione()->destinatari) {
            $destinatariString = '';
            
            foreach ($this->getPubblicazione()->destinatari as $destinatari) {
                $destinatariString .= strlen($destinatariString) ? ' - ' . $destinatari->getText() : $destinatari->getText();
            }
            return $destinatariString;
        } else {
            return null;
        }
    }
    
    /**
     * @param Event $event
     */
    public function eventFind(Event $event)
    {
        $this->setSender($event->sender);
        $this->getSender()->regola_pubblicazione = null;
        
        if ($this->getSender() && !$this->getSender()->isNewRecord) {

            $cwhConfigContents = CwhConfigContents::findOne(['tablename' => $this->getSender()->tableName()]);

            /**
             * @var CwhPubblicazioni $Pubblicazione ;
             */
            $pubblicazione = CwhPubblicazioni::findOne([
                'content_id' => $this->getSender()->id,
                'cwh_config_contents_id' => $cwhConfigContents->id
            ]);

            $this->setPubblicazione($pubblicazione);
            
            if ($this->getPubblicazione()) {
                $this->getSender()->regola_pubblicazione = $this->getPubblicazione()->cwhRegolePubblicazione->getPrimaryKey();
                
                $targetsArrayMap = ArrayHelper::map($this->getPubblicazione()
                    ->getDestinatari()
                    ->all(), 'id', 'id');
                
                $this->getSender()->setDestinatari($targetsArrayMap);
                
                $this->getSender()->setTargets($targetsArrayMap);
                
                $this->getSender()->destinatari = $targetsArrayMap;
                
                $draftingValidatorsArrayMap = ArrayHelper::map($this->getPubblicazione()
                    ->getValidatori()
                    ->all(), 'id', 'id');
                
                $this->getSender()->setValidatori($draftingValidatorsArrayMap);
                
                $this->getSender()->setDraftingValidators($draftingValidatorsArrayMap);
                
                $this->getSender()->validatori = $draftingValidatorsArrayMap;
            }
        }
    }
    
    /**
     * @param \yii\db\ActiveRecord $sender
     */
    private function setSender($sender)
    {
        $this->sender = $sender;
    }
    
    /**
     * @return \lispa\amos\core\record\Record
     */
    private function getSender()
    {
        return $this->sender;
    }
    
    /**
     * @param CwhPubblicazioni $pubblicazione
     */
    private function setPubblicazione($pubblicazione)
    {
        $this->pubblicazione = $pubblicazione;
    }
    
    /**
     * @param Event $event
     */
    public function eventSavePubblicazione(Event $event)
    {
        $this->setSender($event->sender);
        
        if (isset($this->getSender()->deleted_at)) {
            return;
        }

        /**
         * @var CwhPubblicazioni $Pubblicazione
         */
        $Pubblicazione = $this->getPubblicazione();
        if (is_null($Pubblicazione)) {
            $cwhConfigContents = CwhConfigContents::findOne(['tablename' => $this->getSender()->tableName()]);
            $Pubblicazione = CwhPubblicazioni::findOne([
                'content_id' => $this->getSender()->id,
                'cwh_config_contents_id' => $cwhConfigContents->id
            ]);
            $this->setPubblicazione($Pubblicazione);
        }

        if (is_null($Pubblicazione)) {
            $Pubblicazione = new CwhPubblicazioni();
        }
        $Pubblicazione->aggiornaPubblicazione($this->getSender());
    }


    /**
     * @param Event $event
     *
     * Valida i campi obbligatori per la regola di pubblicazione
     */
    public function eventBeforeValidate(Event $event)
    {
        $this->setSender($event->sender);
        
        $post = [];
        
        if (isset($_POST[$this->getSender()->formName()])) {
            $post = $_POST[$this->getSender()->formName()];
        }
        
        if (count($post)) {
            if (array_key_exists('regola_pubblicazione', $post)) {
                $this->getSender()->regola_pubblicazione = $post['regola_pubblicazione'];
            }
            if (array_key_exists('destinatari', $post)) {
                $this->getSender()->destinatari = $post['destinatari'];
            }
            
            $config = AmosCwh::getInstance()->validateOnStatus[get_class($this->getSender())];

            //check from configuration if model status allows to change validator (or if model is new record to st it the first time)
            if (array_key_exists('validatori', $post)) {
                if (in_array($this->getSender()->{$config['attribute']}, $config['statuses']) || $this->getSender()->isNewRecord) {
                    $this->getSender()->validatori = $post['validatori'];
                }
            }
        }
        
        
        if (!isset($this->getSender()->regola_pubblicazione) || !strlen($this->getSender()->regola_pubblicazione)) {
            $this->getSender()->addError('regola_pubblicazione',
                AmosCwh::t('amoscwh', 'E\' necessario indicare la regola di pubblicazione'));
        } else {
            if (in_array($this->getSender()->regola_pubblicazione, [3, 4])) {
                if (!is_array($this->getSender()->destinatari)) {
                    $this->getSender()->addError('destinatari', AmosCwh::t('amoscwh', 'E\' necessario indicare verso quali ambiti pubblicare'));
                }
            }
        }

        if (!is_array($this->getSender()->validatori)) {
            if (!isset($this->getSender()->validatori) || !strlen($this->getSender()->validatori)) {
                if (\Yii::$app->getModule('cwh')->validatoriEnabled) {
                    $this->getSender()->addError('validatori', AmosCwh::t('amoscwh', 'E\' necessario indicare in quale ambito deve avvenire la validazione'));
                }
            }
        }
        
        // Retrieve form name from sender
        $formName = $this->getSender()->formName();
        
        // Check in post if at least one tag value is set for the model (necessary if publication rule 2 or 4 has been selected)
        if (isset($_POST[$formName])) {
            $_tagValues = [];
            if (isset($_POST[$formName]['tagValues'])) {
                $_tagValues = array_filter($_POST[$formName]['tagValues']);
            }
            if (in_array($this->getSender()->regola_pubblicazione, [2, 4]) && empty($_tagValues)) {
                $errorMessage = AmosCwh::t('amoscwh', 'E\' necessario indicare almeno una classificazione');
                $this->getSender()->addError('regola_pubblicazione', $errorMessage);
                $dangerFlashes = Yii::$app->session->getFlash('danger');
                if (!Yii::$app->session->hasFlash('danger') || !in_array($errorMessage, $dangerFlashes)) {
                    Yii::$app->getSession()->addFlash('danger', $errorMessage);
                }
            }
        }
    }

    /**
     * find the publication CwhPubblicazioni associated to the model
     * if exists delete related validators and editors and at last delete publication itself
     * @param Event $event
     */
    public function eventBeforeDelete(Event $event)
    {

        $this->setSender($event->sender);

        /**
         * @var CwhPubblicazioni $publication
         */
        $publication = $this->getPubblicazione();
        if (is_null($publication)) {
            $cwhConfigContents = CwhConfigContents::findOne(['tablename' => $this->getSender()->tableName()]);
            $publication = CwhPubblicazioni::findOne([
                'content_id' => $this->getSender()->id,
                'cwh_config_contents_id' => $cwhConfigContents->id
            ]);
            $this->setPubblicazione($publication);
        }
        if (!is_null($publication)) {
            $publication->deleteValidators();
            $publication->deleteEditors();
            $publication->delete();
        }

    }
}
