<?php 
namespace app\controllers;

use yii\web\Controller;
use Yii;
use app\models\Property;
use app\models\ListSource;
use app\models\PropertyAttributeAnswer;
use app\models\PropertyExtraData;
use yii\helpers\ArrayHelper;

class PropertyController extends Controller
{
    public $layout='custom';

    public function actionIndex()
    {
        return $this->render('index');
    }

  public function actionCreate123()
    {
        $model = new Property();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
           Yii::$app->session->setFlash('success','Data saved Successfully');
            return $this->refresh();
        }

        return $this->render('create' , [
        'model' => $model
    ]);
    }



    public function actionCreate()
    {
        $model = new Property();
        //find the usage type
         $parentUsage= ListSource::find()
         ->where(['parent_id'=>null,'category'=>'Usage Type'])->one();
         //find the property ype
         $parentProperty=ListSource::find()
         ->where(['parent_id' => null,'category'=>'Property Type'])->one();
         $parentOwnerShip=ListSource::find()
         ->where(['parent_id'=>null,'category'=>'Ownership'])->one();
         $parentStatus=ListSource::find()
         ->where(['parent_id'=>null,'category' => 'Status'])
         ->one();
          $childStatus=[];
         $childProperty=[];
         $childUsage=[];
         $childOwner=[];
         if ($parentUsage){
            $child=ListSource::find()
            ->where(['parent_id'=>$parentUsage->id])
            ->all();
            $childUsage=ArrayHelper::map($child,'id','list_Name');
         }
         if ($parentProperty){
            $child= ListSource::find()
            ->where(['parent_id'=>$parentProperty->id])
            ->all();
            $childProperty=ArrayHelper::map($child,'id','list_Name');

         }
         if ($parentOwnerShip){
            $child=ListSource::find()
            ->where(['parent_id' =>$parentOwnerShip->id])
            ->all();
            $childOwner=ArrayHelper::map($child,'id','list_Name');
         }

         if ($parentStatus){
            $child=ListSource::find()
            ->where(['parent_id'=>$parentStatus->id])
            ->all();
             $childStatus=ArrayHelper::map($child,'id','list_Name');
         }

         

if ($model->load(Yii::$app->request->post())) {
    if (empty($model->uuid)) {
        $lastUuid = Property::find()
            ->select('uuid')
            ->where(['like', 'uuid', 'Prop_%', false])
            ->orderBy(['id' => SORT_DESC])
            ->scalar();

        $model->uuid = $lastUuid
            ? 'Prop_' . ((int)str_replace('Prop_', '', $lastUuid) + 1)
            : 'Prop_1';
    }

    if ($model->save()) {
       $propertyId = $model->id;

    // 1️⃣ User input (free text)
    $attributes = Yii::$app->request->post('attributes', []);
    foreach ($attributes as $attrId => $value) {
        if (!empty($value)) {
            $extra = new PropertyExtraData();
            $extra->property_id = $propertyId;
            $extra->property_attribute_id = $attrId;
            $extra->attribute_answer_id = null; // hapana answer table
            $extra->attribute_answer_text = $value;
            $lastUuid = PropertyExtraData::find()
    ->select('uuid')
    ->orderBy(['id' => SORT_DESC])
    ->scalar();

$extra->uuid = $lastUuid
    ? 'PED_' . ((int)str_replace('PED_', '', $lastUuid) + 1)
    : 'PED_1';

            $extra->save(false);
        }
    }

 $answers = Yii::$app->request->post('answers', []);
foreach ($answers as $attrId => $listSourceId) {
    if (!empty($listSourceId)) {
        // 1. Save PropertyAttributeAnswer
        $ans = new PropertyAttributeAnswer();
        $ans->property_attribute_id = $attrId;
        $ans->answer_id = $listSourceId; // FK to ListSource
        $ans->status = null; // optional
        $lastUuid = PropertyAttributeAnswer::find()
            ->select('uuid')
            ->orderBy(['id'=>SORT_DESC])
            ->scalar();
        $ans->uuid = $lastUuid ? 'PAA_'.((int)str_replace('PAA_','',$lastUuid)+1) : 'PAA_1';
        $ans->save(false);

        // 2. Save reference kwenye PropertyExtraData
        $extra = new PropertyExtraData();
        $extra->property_id = $propertyId;
        $extra->property_attribute_id = $attrId;
        $extra->attribute_answer_id = $ans->id; // reference to PropertyAttributeAnswer
        $extra->attribute_answer_text = null; // sio lazima text
        $lastUuidExtra = PropertyExtraData::find()
            ->select('uuid')
            ->orderBy(['id' => SORT_DESC])
            ->scalar();
        $extra->uuid = $lastUuidExtra
            ? 'PED_' . ((int)str_replace('PED_', '', $lastUuidExtra) + 1)
            : 'PED_1';
        $extra->save(false);
    }
}


        Yii::$app->session->setFlash('success', 'Property added successfully!');
        return $this->refresh(); // badala ya redirect ili ujumbe uonekane
    }
}

         $childUsage=[0 =>'Select Usage Type'] + $childUsage;
        return $this->render('create', [
            'model' => $model, 
            'childUsage'=>$childUsage,
            'childProperty'=>$childProperty,
            'childOwner'=>$childOwner,
            'childStatus'=>$childStatus// Hapa ndo muhimu
        ]);
     }
}
