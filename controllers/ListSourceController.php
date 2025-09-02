<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use app\models\ListSource;

class ListSourceController extends Controller
{
    public $layout = 'custom';

    public function actionCreate()
    {
        $model = new ListSource();
        $sources = ListSource::find()->all();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
           Yii::$app->session->setFlash('success','Data saved Successfully');
            return $this->refresh();
        }

        return $this->render('create' , [
        'model' => $model,
        'sources' => $sources
    ]);
    }


    public function actionSource(){
        $sources= ListSource::find()->all();

       return $this->render('create',['sources'=>$sources]);
    }

public function actionDelete($id){
    $item=ListSource::findOne($id);
    if ($item !== null){
        $item->delete();
        Yii::$app->session->setFlash('success','The data delete successfully');
        return $this->redirect('create');
    }

}
public function actionUpdate($id)
{
    // Find the existing record
    $item = ListSource::findOne($id);

    if ($item === null) {
        throw new \yii\web\NotFoundHttpException("No List Found");
    }

    // Load POST data and save
    if ($item->load(Yii::$app->request->post()) && $item->save()) {
        Yii::$app->session->setFlash('success', 'List Updated Successfully');
        return $this->redirect(['create']); // redirect to create page
    }

    // Render the update form and pass the existing model
    return $this->render('update', [
        'model' => $item,
    ]);
}
public function actionAddChild()
{
    $model = new ListSource();

    // Get all top-level lists
    $topLists = ListSource::find()->where(['parent_id' => null])->all();
    $topListOptions = \yii\helpers\ArrayHelper::map($topLists, 'id', 'list_Name');

    // Handle POST
    if ($model->load(Yii::$app->request->post())) {

        // parent_id can be null
        $model->parent_id = $model->parent_id ?: null;

        // Generate UUID if empty
        if (empty($model->uuid)) {
            $lastUuid = ListSource::find()
                ->select('uuid')
                ->where(['like','uuid','List_%',false])
                ->orderBy(['id'=>SORT_DESC])
                ->scalar();

            $model->uuid = $lastUuid
                ? 'List_'. ((int)str_replace('List_','',$lastUuid)+1)
                : 'List_1';
        }

        // Generate code if empty
        if (empty($model->code)) {
            $model->code = $model->generateCode();
        }

        // Generate sort_by if empty
        if (empty($model->sort_by)) {
            $model->sort_by = $model->generateSort();
        }

        // Try to save
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Child configuration added successfully!');
            // PRG: redirect to same page or another
            return $this->redirect(['list-source/create']);
        }
        // If save fails, errors will be shown in errorSummary of form
    }

    return $this->render('_formChild', [
        'model' => $model,
        'topListOptions' => $topListOptions,
    ]);
}





}

    