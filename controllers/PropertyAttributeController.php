<?php
namespace app\controllers;

use yii\web\Controller;
use app\models\PropertyAttribute;
use app\models\ListSource;
use Yii;
use yii\helpers\ArrayHelper;

class PropertyAttributeController extends Controller
{
    public $layout = 'custom';

    public function actionCreate()
    {
        $model = new PropertyAttribute();

        $parentProperty = ListSource::find()
            ->where(['list_Name' => 'Property Type'])
            ->one();

        $parentDataType = ListSource::find()
            ->where(['list_Name' => 'Data Type'])
            ->one();

        $childDataType = [];
        $childProperty = [];

        if ($parentProperty) {
            $child = ListSource::find()
                ->where(['parent_id' => $parentProperty->id])
                ->all();
            $childProperty = ArrayHelper::map($child, 'id', 'list_Name');
        }

        if ($parentDataType) {
            $child = ListSource::find()
                ->where(['parent_id' => $parentDataType->id])
                ->all();
            $childDataType = ArrayHelper::map($child, 'id', 'list_Name');
        }

        if ($model->load(Yii::$app->request->post())) {

            if (empty($model->uuid)) {
                $lastUuid = PropertyAttribute::find()
                    ->select('uuid')
                    ->where(['like', 'uuid', 'Attr_%', false])
                    ->orderBy(['id' => SORT_DESC])
                    ->scalar();

                $model->uuid = $lastUuid
                    ? 'Attr_' . ((int)str_replace('Attr_', '', $lastUuid) + 1)
                    : 'Attr_1';
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Configuration added successfully!');
                return $this->redirect('create');
            } else {
                // If save fails, set flash with error messages
                Yii::$app->session->setFlash('error', implode('<br>', $this->formatErrors($model)));
            }
        }

        return $this->render('create', [
            'model' => $model,
            'childProperty' => $childProperty,
            'childDataType' => $childDataType,
        ]);
    }

    // Helper function to format model errors into array of strings
    protected function formatErrors($model)
    {
        $errors = [];
        foreach ($model->errors as $fieldErrors) {
            foreach ($fieldErrors as $error) {
                $errors[] = $error;
            }
        }
        return $errors;
    }
public function actionGetAttributes($id)
{
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

    $attributes = PropertyAttribute::find()
        ->where(['property_type_id' => $id])
        ->with(['propertyType', 'dataType'])
        ->all();

    $result = [];
    foreach ($attributes as $attr) {
        $listOptions = [];

        $parent = ListSource::find()
            ->where('parent_id IS NULL AND LOWER(category) = :cat', [':cat' => strtolower($attr->attribute_name)])
            ->one();

        if ($parent) {
            $children = ListSource::find()
                ->where(['parent_id' => $parent->id])
                ->all();
                
            // Return as array of objects instead of associative array
            foreach ($children as $child) {
                $listOptions[] = [
                    'id' => $child->id,
                    'list_Name' => $child->list_Name
                ];
            }
        }

        $result[] = [
            'id' => $attr->id,
            'attribute_name' => $attr->attribute_name,
            'attribute_datatype' => $attr->dataType ? strtolower($attr->dataType->list_Name) : null,
            'list_source' => $listOptions, 
        ];
    }

    return $result;
}




}
