<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class PropertySearch extends Property
{
    public function rules()
    {
        return [
            [['property_name', 'ownership_type_id', 'property_status_id'], 'safe'],
        ];
    }

    public function search($params)
    {
        $query = Property::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 9],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // Filters
        $query->andFilterWhere(['like', 'property_name', $this->property_name])
              ->andFilterWhere(['ownership_type_id' => $this->ownership_type_id])
              ->andFilterWhere(['property_status_id' => $this->property_status_id]);

        return $dataProvider;
    }
     public function getPropertyStatus(){
    return $this->hasOne(ListSource::class,['id'=>'property_status_id']);
  }
  public function getPropertyOwnerShip()
    {
        return $this->hasOne(ListSource::class, ['id' => 'ownership_type_id']);
    }
}
