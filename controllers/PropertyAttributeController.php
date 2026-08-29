<?php
namespace app\controllers;

use yii\web\Controller;
use app\models\PropertyAttribute;
use app\models\ListSource;
use Yii;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;

class PropertyAttributeController extends Controller
{
    public $layout = 'custom';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return (Yii::$app->user->identity->role ?? null) === 'admin';
                        },
                    ],
                ],
                'denyCallback' => function () {
                    return Yii::$app->response->redirect(['login/login']);
                },
            ],
        ];
    }

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

    // Fetch every top-level category once and index by lowercased name,
    // instead of running a separate "find parent" query per attribute.
    $parentsByCategory = [];
    foreach (ListSource::find()->where(['parent_id' => null])->all() as $parent) {
        $parentsByCategory[strtolower($parent->category)] = $parent;
    }

    // Batch-fetch all children for the matched categories in one query,
    // instead of a separate query per attribute.
    $neededParentIds = [];
    foreach ($attributes as $attr) {
        if (isset($parentsByCategory[strtolower($attr->attribute_name)])) {
            $neededParentIds[] = $parentsByCategory[strtolower($attr->attribute_name)]->id;
        }
    }

    $childrenByParentId = [];
    if ($neededParentIds) {
        foreach (ListSource::find()->where(['parent_id' => $neededParentIds])->all() as $child) {
            $childrenByParentId[$child->parent_id][] = $child;
        }
    }

    $result = [];
    foreach ($attributes as $attr) {
        $listOptions = [];
        $parent = $parentsByCategory[strtolower($attr->attribute_name)] ?? null;

        if ($parent) {
            foreach ($childrenByParentId[$parent->id] ?? [] as $child) {
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
