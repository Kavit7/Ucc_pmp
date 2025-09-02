<?php 
namespace app\controllers;

use yii\web\Controller; // capital C
use Yii;

class PropertyController extends Controller
{
    public $layout='custom';
    // this will use views/layouts/custom.php

    public function actionIndex()
    {

        return $this->render('index');
    }
}
