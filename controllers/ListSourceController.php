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

    // Get all top-level lists (parent_id = null)
    $topLists = ListSource::find()->where(['parent_id' => null])->all();

    // Convert objects to [id => list_Name] - ensure we get string values
    $topListOptions = [];
    foreach ($topLists as $list) {
        $topListOptions[$list->id] = (string)$list->list_Name;
    }

    // Check if form is submitted
    if ($model->load(Yii::$app->request->post())) {

        // Ensure parent_id is an integer or null
        if (!empty($model->parent_id)) {
            $model->parent_id = (int)$model->parent_id;
        } else {
            $model->parent_id = null; // top-level if no parent selected
        }

        // Generate UUID if empty
        if (empty($model->uuid)) {
            $lastUuid = static::find()
                ->select('uuid')
                ->where(['like','uuid','List_%',false])
                ->orderBy(['id'=>SORT_DESC])
                ->scalar();

            $model->uuid = $lastUuid ? 'List_'. ((int)str_replace('List_','',$lastUuid)+1) : 'List_1';
        }

        // Generate code if empty
        if (empty($model->code)) {
            $model->code = $model->generateCode();
        }

        // Generate sort_by if empty
        if (empty($model->sort_by)) {
            $model->sort_by = $model->generateSort();
        }

        // Save model
        if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Child configuration added successfully!');
            return $this->refresh();
        }
    }

    // Render form (for GET or if save fails)
    return $this->render('_formChild', [
        'model' => $model,
        'topListOptions' => $topListOptions,
    ]);
}


}

    /*public function actionTest()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => ListSource::find()->orderBy(['sort_by' => SORT_ASC]),
            'pagination' => ['pageSize' => 20],
        ]);

        $model = new ListSource();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->refresh();
        }

        $parents = ArrayHelper::map(
            ListSource::find()->all(),
            'id',
            function($m) { return $m->list_Name . ' - ' . $m->code; }
        );

        return $this->render('test', [
            'dataProvider' => $dataProvider,
            'model' => $model,
            'parents' => $parents
        ]);
    }*/

