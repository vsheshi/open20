<?php

/**
 * Lombardia Informatica S.p.A.
 * OPEN 2.0
 *
 *
 * @package    lispa\amos\organizzazioni
 * @category   CategoryName
 */

namespace lispa\amos\organizzazioni\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use lispa\amos\organizzazioni\models\Profilo;

/**
 * ProfiloSearch represents the model behind the search form about `lispa\amos\organizzazioni\models\Profilo`.
 */
class ProfiloSearch extends Profilo
{

    /**
     *
     * @return unknown
     */
    public function rules()
    {
        return [
            [['id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [['name', 'partita_iva', 'codice_fiscale', 'presentazione_della_organizzaz', 'tipologia_di_organizzazione', 'forma_legale', 'sito_web', 'facebook', 'twitter', 'linkedin', 'google', 'indirizzo', 'email', 'pec', 'la_sede_legale_e_la_stessa_del', 'sede_legale_indirizzo', 'sede_legale_email', 'sede_legale_pec', 'responsabile', 'rappresentante_legale', 'referente_operativo', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['telefono', 'fax', 'sede_legale_telefono', 'sede_legale_fax'], 'number'],
        ];
    }


    /**
     *
     * @return unknown
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }


    /**
     *
     * @param unknown $params
     * @return unknown
     */
    public function search($params)
    {
        $query = Profilo::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);


        $dataProvider->setSort([
            'attributes' => [
                'name' => [
                    'asc' => ['profilo.name' => SORT_ASC],
                    'desc' => ['profilo.name' => SORT_DESC],
                ],
                'partita_iva' => [
                    'asc' => ['profilo.partita_iva' => SORT_ASC],
                    'desc' => ['profilo.partita_iva' => SORT_DESC],
                ],
                'codice_fiscale' => [
                    'asc' => ['profilo.codice_fiscale' => SORT_ASC],
                    'desc' => ['profilo.codice_fiscale' => SORT_DESC],
                ],
                'presentazione_della_organizzaz' => [
                    'asc' => ['profilo.presentazione_della_organizzaz' => SORT_ASC],
                    'desc' => ['profilo.presentazione_della_organizzaz' => SORT_DESC],
                ],

                'tipologia_di_organizzazione' => [
                    'asc' => ['profilo.tipologia_di_organizzazione' => SORT_ASC],
                    'desc' => ['profilo.tipologia_di_organizzazione' => SORT_DESC],
                ],
                'forma_legale' => [
                    'asc' => ['profilo.forma_legale' => SORT_ASC],
                    'desc' => ['profilo.forma_legale' => SORT_DESC],
                ],
                'sito_web' => [
                    'asc' => ['profilo.sito_web' => SORT_ASC],
                    'desc' => ['profilo.sito_web' => SORT_DESC],
                ],
                'facebook' => [
                    'asc' => ['profilo.facebook' => SORT_ASC],
                    'desc' => ['profilo.facebook' => SORT_DESC],
                ],
                'twitter' => [
                    'asc' => ['profilo.twitter' => SORT_ASC],
                    'desc' => ['profilo.twitter' => SORT_DESC],
                ],
                'linkedin' => [
                    'asc' => ['profilo.linkedin' => SORT_ASC],
                    'desc' => ['profilo.linkedin' => SORT_DESC],
                ],
                'google' => [
                    'asc' => ['profilo.google' => SORT_ASC],
                    'desc' => ['profilo.google' => SORT_DESC],
                ],
                'indirizzo' => [
                    'asc' => ['profilo.indirizzo' => SORT_ASC],
                    'desc' => ['profilo.indirizzo' => SORT_DESC],
                ],
                'telefono' => [
                    'asc' => ['profilo.telefono' => SORT_ASC],
                    'desc' => ['profilo.telefono' => SORT_DESC],
                ],
                'fax' => [
                    'asc' => ['profilo.fax' => SORT_ASC],
                    'desc' => ['profilo.fax' => SORT_DESC],
                ],
                'email' => [
                    'asc' => ['profilo.email' => SORT_ASC],
                    'desc' => ['profilo.email' => SORT_DESC],
                ],
                'pec' => [
                    'asc' => ['profilo.pec' => SORT_ASC],
                    'desc' => ['profilo.pec' => SORT_DESC],
                ],
                'la_sede_legale_e_la_stessa_del' => [
                    'asc' => ['profilo.la_sede_legale_e_la_stessa_del' => SORT_ASC],
                    'desc' => ['profilo.la_sede_legale_e_la_stessa_del' => SORT_DESC],
                ],
                'sede_legale_indirizzo' => [
                    'asc' => ['profilo.sede_legale_indirizzo' => SORT_ASC],
                    'desc' => ['profilo.sede_legale_indirizzo' => SORT_DESC],
                ],
                'sede_legale_telefono' => [
                    'asc' => ['profilo.sede_legale_telefono' => SORT_ASC],
                    'desc' => ['profilo.sede_legale_telefono' => SORT_DESC],
                ],
                'sede_legale_fax' => [
                    'asc' => ['profilo.sede_legale_fax' => SORT_ASC],
                    'desc' => ['profilo.sede_legale_fax' => SORT_DESC],
                ],
                'sede_legale_email' => [
                    'asc' => ['profilo.sede_legale_email' => SORT_ASC],
                    'desc' => ['profilo.sede_legale_email' => SORT_DESC],
                ],
                'sede_legale_pec' => [
                    'asc' => ['profilo.sede_legale_pec' => SORT_ASC],
                    'desc' => ['profilo.sede_legale_pec' => SORT_DESC],
                ],
                'responsabile' => [
                    'asc' => ['profilo.responsabile' => SORT_ASC],
                    'desc' => ['profilo.responsabile' => SORT_DESC],
                ],
                'rappresentante_legale' => [
                    'asc' => ['profilo.rappresentante_legale' => SORT_ASC],
                    'desc' => ['profilo.rappresentante_legale' => SORT_DESC],
                ],
                'referente_operativo' => [
                    'asc' => ['profilo.referente_operativo' => SORT_ASC],
                    'desc' => ['profilo.referente_operativo' => SORT_DESC],
                ],
            ]]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }


        $query->andFilterWhere([
            'id' => $this->id,
            'telefono' => $this->telefono,
            'fax' => $this->fax,
            'sede_legale_telefono' => $this->sede_legale_telefono,
            'sede_legale_fax' => $this->sede_legale_fax,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'partita_iva', $this->partita_iva])
            ->andFilterWhere(['like', 'codice_fiscale', $this->codice_fiscale])
            ->andFilterWhere(['like', 'presentazione_della_organizzaz', $this->presentazione_della_organizzaz])
            ->andFilterWhere(['like', 'tipologia_di_organizzazione', $this->tipologia_di_organizzazione])
            ->andFilterWhere(['like', 'forma_legale', $this->forma_legale])
            ->andFilterWhere(['like', 'sito_web', $this->sito_web])
            ->andFilterWhere(['like', 'facebook', $this->facebook])
            ->andFilterWhere(['like', 'twitter', $this->twitter])
            ->andFilterWhere(['like', 'linkedin', $this->linkedin])
            ->andFilterWhere(['like', 'google', $this->google])
            ->andFilterWhere(['like', 'indirizzo', $this->indirizzo])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'pec', $this->pec])
            ->andFilterWhere(['like', 'la_sede_legale_e_la_stessa_del', $this->la_sede_legale_e_la_stessa_del])
            ->andFilterWhere(['like', 'sede_legale_indirizzo', $this->sede_legale_indirizzo])
            ->andFilterWhere(['like', 'sede_legale_email', $this->sede_legale_email])
            ->andFilterWhere(['like', 'sede_legale_pec', $this->sede_legale_pec])
            ->andFilterWhere(['like', 'responsabile', $this->responsabile])
            ->andFilterWhere(['like', 'rappresentante_legale', $this->rappresentante_legale])
            ->andFilterWhere(['like', 'referente_operativo', $this->referente_operativo]);

        return $dataProvider;
    }


}
