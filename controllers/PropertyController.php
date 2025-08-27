<?php 
namespace app\controllers;

use yii\web\Controller; // capital C
use Yii;

class PropertyController extends Controller
{
    // this will use views/layouts/custom.php

    public function actionIndex()
    {
         $this->layout=false;

        return $this->render('index');
    }
}
