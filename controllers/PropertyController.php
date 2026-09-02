<?php 
namespace app\controllers;

use yii\web\Controller;
use Yii;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use app\models\Property;
use app\models\ListSource;
use app\models\PropertyAttributeAnswer;
use app\models\PropertyExtraData;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use app\models\PropertySearch;
use app\models\Street;
use app\models\Lease;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
class PropertyController extends Controller
{
    public $layout='custom';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            $role = Yii::$app->user->identity->role ?? null;
                            return $role === 'admin' || Yii::$app->user->can('create');
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'upload-photo', 'delete-photo'],
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            $role = Yii::$app->user->identity->role ?? null;
                            return $role === 'admin' || Yii::$app->user->can('edit');
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            $role = Yii::$app->user->identity->role ?? null;
                            return $role === 'admin' || Yii::$app->user->can('delete');
                        },
                    ],
                    [
                        'allow' => true,
                        'actions' => ['search-street', 'index', 'document', 'rented', 'stores', 'sales'],
                        'roles' => ['@'], // just needs to be logged in - browsing/searching, not mutating anything
                    ],
                ],
                'denyCallback' => function () {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['login/login']);
                    }
                    throw new \yii\web\ForbiddenHttpException('You do not have permission to do that.');
                },
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'upload-photo' => ['post'],
                    'delete-photo' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Searchable street lookup for the location Select2 field: type any
     * combination of street/district/region ("masaki dar es salaam") and
     * get matching streets back, so property location entry is a search
     * instead of scrolling a 16,000+ option dropdown.
     */
    public function actionSearchStreet()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $q = trim(Yii::$app->request->get('q', ''));
        $query = Street::find()->alias('s')
            ->select(['s.street_id AS id', "CONCAT(s.street_name, ' — ', d.district_name, ', ', r.name) AS text"])
            ->innerJoin('district d', 'd.district_id = s.district_id')
            ->innerJoin('region r', 'r.region_id = s.region_id')
            ->orderBy(['s.street_name' => SORT_ASC])
            ->limit(30);

        foreach (preg_split('/\s+/', $q, -1, PREG_SPLIT_NO_EMPTY) as $word) {
            $query->andWhere(['or',
                ['like', 's.street_name', $word],
                ['like', 'd.district_name', $word],
                ['like', 'r.name', $word],
            ]);
        }

        return ['results' => $query->asArray()->all()];
    }

 public function actionIndex()
{
    $searchModel = new PropertySearch(); 

    // pata id ya parent OwnerShip
    $parentOwner = ListSource::find()
        ->select('id')
        ->where(['list_name' => 'OwnerShip'])
        ->scalar(); // inarudisha single value badala ya array
    //pata id ya parent status
    $parentStatus = ListSource::find()
        ->select('id')
        ->where(['list_name' => 'Status'])
        ->scalar(); 
    // pata watoto wake
    $childOwner = [];
    $childStatus=[];
    if ($parentOwner) {
        $childOwner = ArrayHelper::map(
            ListSource::find()->where(['parent_id' => $parentOwner])->all(),
            'id',
            'list_Name'
        );
    }
         if ($parentStatus) {
        $childStatus = ArrayHelper::map(
            ListSource::find()->where(['parent_id' => $parentStatus])->all(),
            'id',
            'list_Name'
        );
    }

    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
    $dataProvider->pagination->pageSize = 12;

    $totalProperties = Property::find()->count();
    $activeLeaseStatusId = ListSource::find()
        ->where(['list_Name' => 'Active', 'category' => 'Lease Status'])
        ->select('id')->scalar();
    $occupiedCount = $activeLeaseStatusId
        ? Lease::find()->select('property_id')->distinct()->where(['status' => $activeLeaseStatusId])->count()
        : 0;

    return $this->render('index', [
        'searchModel'  => $searchModel,
        'dataProvider' => $dataProvider,
        'childOwner'   => $childOwner,
        'childStatus' =>$childStatus,
        'totalProperties' => (int) $totalProperties,
        'occupiedCount' => (int) $occupiedCount,
    ]);
}

   public function actionCreate()
    {
        $model = new Property();

        // find the usage type
        $parentUsage= ListSource::find()->where(['parent_id'=>null,'category'=>'Usage Type'])->one();
        $parentProperty=ListSource::find()->where(['parent_id' => null,'category'=>'Property Type'])->one();
        $parentOwnerShip=ListSource::find()->where(['parent_id'=>null,'category'=>'Ownership'])->one();
        $parentStatus=ListSource::find()->where(['parent_id'=>null,'category' => 'Status'])->one();

        $childStatus=[]; $childProperty=[]; $childUsage=[]; $childOwner=[];
        if ($parentUsage){
            $child=ListSource::find()->where(['parent_id'=>$parentUsage->id])->all();
            $childUsage=ArrayHelper::map($child,'id','list_Name');
        }
        if ($parentProperty){
            $child= ListSource::find()->where(['parent_id'=>$parentProperty->id])->all();
            $childProperty=ArrayHelper::map($child,'id','list_Name');
        }
        if ($parentOwnerShip){
            $child=ListSource::find()->where(['parent_id' =>$parentOwnerShip->id])->all();
            $childOwner=ArrayHelper::map($child,'id','list_Name');
        }
        if ($parentStatus){
            $child=ListSource::find()->where(['parent_id'=>$parentStatus->id])->all();
            $childStatus=ArrayHelper::map($child,'id','list_Name');
        }

        if ($model->load(Yii::$app->request->post())) {

            $model->documentFile = UploadedFile::getInstance($model, 'documentFile');

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

            // Validate (including the file's extension/size) before writing
            // anything to disk - previously this saved whatever was
            // uploaded, extension included, with no check at all.
            if (!$model->validate()) {
                Yii::$app->session->setFlash('error', 'Please fix the highlighted fields.');
                return $this->render('create', [
                    'model' => $model,
                    'childUsage' => $childUsage,
                    'childProperty' => $childProperty,
                    'childOwner' => $childOwner,
                    'childStatus' => $childStatus,
                ]);
            }

            if ($model->documentFile) {
                $uploadDir = Yii::getAlias('@webroot/uploads/');
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $filePath = 'uploads/' . Yii::$app->security->generateRandomString() . '.' . $model->documentFile->extension;
                if ($model->documentFile->saveAs(Yii::getAlias('@webroot/' . $filePath))) {
                    $model->document_url = $filePath;
                }
            }

            if ($model->save(false)) {
                $propertyId = $model->id;

                // free text attributes
                $attributes = Yii::$app->request->post('attributes', []);
                foreach ($attributes as $attrId => $value) {
                    if (!empty($value)) {
                        $extra = new PropertyExtraData();
                        $extra->property_id = $propertyId;
                        $extra->property_attribute_id = $attrId;
                        $extra->attribute_answer_id = null;
                        $extra->attribute_answer_text = $value;
                        $lastUuid = PropertyExtraData::find()->select('uuid')->orderBy(['id' => SORT_DESC])->scalar();
                        $extra->uuid = $lastUuid ? 'PED_' . ((int)str_replace('PED_', '', $lastUuid) + 1) : 'PED_1';
                        $extra->save(false);
                    }
                }

                // answers (dropdowns)
                $answers = Yii::$app->request->post('answers', []);
                foreach ($answers as $attrId => $listSourceId) {
                    if (!empty($listSourceId)) {
                        $ans = new PropertyAttributeAnswer();
                        $ans->property_attribute_id = $attrId;
                        $ans->answer_id = $listSourceId;
                        $ans->status = null;
                        $lastUuid = PropertyAttributeAnswer::find()->select('uuid')->orderBy(['id'=>SORT_DESC])->scalar();
                        $ans->uuid = $lastUuid ? 'PAA_'.((int)str_replace('PAA_','',$lastUuid)+1) : 'PAA_1';
                        $ans->save(false);

                        $extra = new PropertyExtraData();
                        $extra->property_id = $propertyId;
                        $extra->property_attribute_id = $attrId;
                        $extra->attribute_answer_id = $ans->id;
                        $extra->attribute_answer_text = null;
                        $lastUuidExtra = PropertyExtraData::find()->select('uuid')->orderBy(['id' => SORT_DESC])->scalar();
                        $extra->uuid = $lastUuidExtra ? 'PED_' . ((int)str_replace('PED_', '', $lastUuidExtra) + 1) : 'PED_1';
                        $extra->save(false);
                    }
                }

                Yii::$app->session->setFlash('success', 'Property added successfully!');
                return $this->redirect('index');
            }
        }

        
        return $this->render('create', [
            'model' => $model, 
            'childUsage'=>$childUsage,
            'childProperty'=>$childProperty,
            'childOwner'=>$childOwner,
            'childStatus'=>$childStatus
        ]);
    }
  public function actionUpdate($id)
{
    $model = $this->findModel($id);

    // parent-child lists (same as before)
    $parentUsage = ListSource::find()->where(['parent_id' => null, 'category' => 'Usage Type'])->one();
    $parentProperty = ListSource::find()->where(['parent_id' => null, 'category' => 'Property Type'])->one();
    $parentOwnerShip = ListSource::find()->where(['parent_id' => null, 'category' => 'Ownership'])->one();
    $parentStatus = ListSource::find()->where(['parent_id' => null, 'category' => 'Status'])->one();

    $childStatus = $childProperty = $childUsage = $childOwner = [];
    if ($parentUsage) {
        $childUsage = ArrayHelper::map(ListSource::find()->where(['parent_id' => $parentUsage->id])->all(), 'id', 'list_Name');
    }
    if ($parentProperty) {
        $childProperty = ArrayHelper::map(ListSource::find()->where(['parent_id' => $parentProperty->id])->all(), 'id', 'list_Name');
    }
    if ($parentOwnerShip) {
        $childOwner = ArrayHelper::map(ListSource::find()->where(['parent_id' => $parentOwnerShip->id])->all(), 'id', 'list_Name');
    }
    if ($parentStatus) {
        $childStatus = ArrayHelper::map(ListSource::find()->where(['parent_id' => $parentStatus->id])->all(), 'id', 'list_Name');
    }

    // Load existing extra data into a map for easy lookup
    $existingExtraData = PropertyExtraData::find()->where(['property_id' => $id])->all();
    $extraMap = [];
    foreach ($existingExtraData as $ex) {
        $extraMap[$ex->property_attribute_id] = $ex; // store record object
    }

    if ($model->load(Yii::$app->request->post())) {
        // handle file upload (keep previous if none)
        $model->documentFile = UploadedFile::getInstance($model, 'documentFile');
        $oldFile = $model->getOldAttribute('document_url');

        if (!$model->documentFile) {
            $model->document_url = $oldFile;
        }

        // Validate (including the file's extension/size, if a new one was
        // provided) before writing anything to disk.
        if (!$model->validate()) {
            Yii::$app->session->setFlash('error', 'Please fix the highlighted fields.');
            return $this->render('update', [
                'model' => $model,
                'childUsage' => $childUsage,
                'childProperty' => $childProperty,
                'childOwner' => $childOwner,
                'childStatus' => $childStatus,
                'existingAttributes' => array_map(function ($e) { return $e->attribute_answer_text; }, $extraMap),
                'existingAnswers' => array_map(function ($e) { return $e->attribute_answer_id; }, $extraMap),
            ]);
        }

        if ($model->documentFile) {
            $uploadDir = Yii::getAlias('@webroot/uploads/');
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filePath = 'uploads/' . Yii::$app->security->generateRandomString() . '.' . $model->documentFile->extension;
            if ($model->documentFile->saveAs(Yii::getAlias('@webroot/' . $filePath))) {
                if ($oldFile) {
                    $oldFullPath = Yii::getAlias('@webroot/' . $oldFile);
                    if (file_exists($oldFullPath)) {
                        @unlink($oldFullPath);
                    }
                }
                $model->document_url = $filePath;
            }
        }

        if ($model->save(false)) {
            $propertyId = $model->id;
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // 1) Process free-text attributes (posted as attributes[attribute_id] => text)
                $postedAttributes = Yii::$app->request->post('attributes', []);
                foreach ($postedAttributes as $attrId => $textValue) {
                    // trim and treat empty string as "no value"
                    $textValue = is_string($textValue) ? trim($textValue) : $textValue;
                    $existing = $extraMap[$attrId] ?? null;

                    if ($textValue === '' || $textValue === null) {
                        // if user cleared the field, delete existing extra data (if any)
                        if ($existing) {
                            // optionally remove associated PropertyAttributeAnswer if orphaning desired
                            $existing->delete();
                            unset($extraMap[$attrId]);
                        }
                        continue;
                    }

                    if ($existing) {
                        // update existing record: set text and clear attribute_answer_id
                        $existing->attribute_answer_text = $textValue;
                        $existing->attribute_answer_id = null;
                        $existing->save(false);
                    } else {
                        // create new PropertyExtraData
                        $newExtra = new PropertyExtraData();
                        $newExtra->property_id = $propertyId;
                        $newExtra->property_attribute_id = $attrId;
                        $newExtra->attribute_answer_text = $textValue;
                        $newExtra->attribute_answer_id = null;
                        $lastUuid = PropertyExtraData::find()->select('uuid')->orderBy(['id' => SORT_DESC])->scalar();
                        $newExtra->uuid = $lastUuid ? 'PED_' . ((int)str_replace('PED_', '', $lastUuid) + 1) : 'PED_1';
                        $newExtra->save(false);
                        $extraMap[$attrId] = $newExtra;
                    }
                }

                // 2) Process dropdown answers (posted as answers[attribute_id] => value)
                // The posted value may be one of two formats depending on your form:
                // - a PropertyAttributeAnswer.id (when you remapped options to use that id)
                // - a ListSource.id (when options use list source ids)
                //
                // We'll detect which it is and behave accordingly.
                $postedAnswers = Yii::$app->request->post('answers', []);
                foreach ($postedAnswers as $attrId => $postedValue) {
                    // normalize
                    if ($postedValue === '' || $postedValue === null) {
                        // user cleared dropdown => delete existing extra data for this attr
                        if (isset($extraMap[$attrId])) {
                            $extraMap[$attrId]->delete();
                            unset($extraMap[$attrId]);
                        }
                        continue;
                    }

                    // if numeric string -> cast int
                    $postedValueInt = is_numeric($postedValue) ? (int)$postedValue : $postedValue;

                    // 2.a If postedValue matches existing PropertyAttributeAnswer id, use it
                    $ansRecord = PropertyAttributeAnswer::findOne($postedValueInt);
                    if ($ansRecord && $ansRecord->property_attribute_id == $attrId) {
                        // ensure PropertyExtraData exists and points to this attribute_answer_id
                        $existing = $extraMap[$attrId] ?? null;
                        if ($existing) {
                            $existing->attribute_answer_id = $ansRecord->id;
                            $existing->attribute_answer_text = null;
                            $existing->save(false);
                        } else {
                            $extra = new PropertyExtraData();
                            $extra->property_id = $propertyId;
                            $extra->property_attribute_id = $attrId;
                            $extra->attribute_answer_id = $ansRecord->id;
                            $extra->attribute_answer_text = null;
                            $lastUuidExtra = PropertyExtraData::find()->select('uuid')->orderBy(['id' => SORT_DESC])->scalar();
                            $extra->uuid = $lastUuidExtra ? 'PED_' . ((int)str_replace('PED_', '', $lastUuidExtra) + 1) : 'PED_1';
                            $extra->save(false);
                            $extraMap[$attrId] = $extra;
                        }
                        continue;
                    }

                    // 2.b If not an existing answer id, treat postedValue as ListSource.id (selected option)
                    $listSource = ListSource::findOne($postedValueInt);
                    if ($listSource) {
                        // try to find existing PropertyAttributeAnswer for this pair (attrId + listSource.id)
                        $existingAns = PropertyAttributeAnswer::find()
                            ->where(['property_attribute_id' => $attrId, 'answer_id' => $listSource->id])
                            ->one();

                        if (!$existingAns) {
                            // create new PropertyAttributeAnswer
                            $existingAns = new PropertyAttributeAnswer();
                            $existingAns->property_attribute_id = $attrId;
                            $existingAns->answer_id = $listSource->id;
                            $existingAns->status = null;
                            $lastUuidAns = PropertyAttributeAnswer::find()->select('uuid')->orderBy(['id' => SORT_DESC])->scalar();
                            $existingAns->uuid = $lastUuidAns ? 'PAA_' . ((int)str_replace('PAA_', '', $lastUuidAns) + 1) : 'PAA_1';
                            $existingAns->save(false);
                        }

                        // ensure PropertyExtraData exists and points to this PropertyAttributeAnswer.id
                        $existingExtra = $extraMap[$attrId] ?? null;
                        if ($existingExtra) {
                            $existingExtra->attribute_answer_id = $existingAns->id;
                            $existingExtra->attribute_answer_text = null;
                            $existingExtra->save(false);
                        } else {
                            $extra = new PropertyExtraData();
                            $extra->property_id = $propertyId;
                            $extra->property_attribute_id = $attrId;
                            $extra->attribute_answer_id = $existingAns->id;
                            $extra->attribute_answer_text = null;
                            $lastUuidExtra = PropertyExtraData::find()->select('uuid')->orderBy(['id' => SORT_DESC])->scalar();
                            $extra->uuid = $lastUuidExtra ? 'PED_' . ((int)str_replace('PED_', '', $lastUuidExtra) + 1) : 'PED_1';
                            $extra->save(false);
                            $extraMap[$attrId] = $extra;
                        }

                        continue;
                    }

                    // fallback: if we couldn't resolve posted value, skip
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Property updated successfully!');
                return $this->redirect(['index']);
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', 'Error updating property: ' . $e->getMessage());
            }
        }
    }

    return $this->render('update', [
        'model' => $model,
        'childUsage' => $childUsage,
        'childProperty' => $childProperty,
        'childOwner' => $childOwner,
        'childStatus' => $childStatus,
        'existingAttributes' => array_map(function($e){ return $e->attribute_answer_text; }, $extraMap),
        'existingAnswers' => array_map(function($e){ return $e->attribute_answer_id; }, $extraMap),
    ]);
}
public function actionDocument($id)
{
    $model = $this->findModel($id);

    $extraData = \app\models\PropertyExtraData::find()
        ->where(['property_id' => $id])
        ->with(['propertyAttribute', 'attributeAnswer'])
        ->all();

    return $this->render('document', [
        'model' => $model,
        'extraData' => $extraData,
    ]);
}

/**
 * Adds a photo to a property's gallery.
 */
public function actionUploadPhoto($id)
{
    $this->findModel($id); // 404s if the property doesn't exist

    $photo = new \app\models\PropertyPhoto();
    $photo->property_id = $id;
    $photo->created_by = Yii::$app->user->id;
    $photo->photoFile = UploadedFile::getInstanceByName('photoFile');

    if ($photo->upload()) {
        Yii::$app->session->setFlash('success', 'Photo added.');
    } else {
        $errors = $photo->getFirstErrors();
        Yii::$app->session->setFlash('error', $errors ? reset($errors) : 'Please choose a valid image (png, jpg, jpeg, max 5MB).');
    }

    return $this->redirect(['document', 'id' => $id]);
}

/**
 * Removes a single photo from a property's gallery.
 */
public function actionDeletePhoto($photoId)
{
    $photo = \app\models\PropertyPhoto::findOne($photoId);
    if (!$photo) {
        throw new NotFoundHttpException('Photo not found.');
    }
    $propertyId = $photo->property_id;
    $photo->delete();

    Yii::$app->session->setFlash('success', 'Photo removed.');
    return $this->redirect(['document', 'id' => $propertyId]);
}

/**
 * Deletes a property, but only if it has no real business history
 * attached - leases/bills would otherwise cascade-delete silently
 * (the DB's FKs are set to CASCADE), wiping financial records with no
 * warning. A property that's actually been leased should be marked
 * inactive instead of deleted.
 */
public function actionDelete($id)
{
    $model = $this->findModel($id);

    if (Lease::find()->where(['property_id' => $id])->exists()) {
        Yii::$app->session->setFlash('error', 'This property has lease history and cannot be deleted. Mark it inactive instead.');
        return $this->redirect(['index']);
    }

    if (\app\models\MaintenanceRequest::find()->where(['property_id' => $id])->exists()) {
        Yii::$app->session->setFlash('error', 'This property has maintenance request history and cannot be deleted. Mark it inactive instead.');
        return $this->redirect(['index']);
    }

    if ($model->document_url) {
        $fullPath = Yii::getAlias('@webroot/' . $model->document_url);
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }

    $model->delete();
    Yii::$app->session->setFlash('success', 'Property deleted successfully.');
    return $this->redirect(['index']);
}

protected function findModel($id)
{
    if (($model = Property::findOne($id)) !== null) {
        return $model;
    }
    
    throw new NotFoundHttpException('The requested property does not exist.');
}


public function actionRented(){
    $Rents = Property::find()
        ->where(['usage_type_id' => $this->usageTypeId('rented')])
        ->all();
    return $this->render('rented',[
       'Rents'=>$Rents
    ]);

}
public function actionStores(){
    $stores = Property::find()
        ->where(['usage_type_id' => $this->usageTypeId('storage')])
        ->all();
    return $this->render('Stores',[
       'stores'=>$stores
    ]);

}
public function actionSales(){
    $sales = Property::find()
        ->where(['usage_type_id' => $this->usageTypeId('sale')])
        ->all();
    return $this->render('sales',[
       'sales'=>$sales
    ]);

}

/**
 * Resolves a "Usage Type" list_source id by its (case-insensitive) name,
 * e.g. usageTypeId('rented') -> the id of the "Rented" child under the
 * "Usage Type" parent category.
 */
private function usageTypeId($name)
{
    $parentUsage = ListSource::find()->where(['parent_id' => null, 'category' => 'Usage Type'])->one();
    if (!$parentUsage) {
        return null;
    }

    $children = ListSource::find()->where(['parent_id' => $parentUsage->id])->all();
    foreach ($children as $child) {
        if (strtolower($child->list_Name) === strtolower($name)) {
            return $child->id;
        }
    }

    return null;
}

}