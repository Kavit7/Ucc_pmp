 <?php 
 use yii\helpers\Html;
 ?>
<html>
<body>
    
 <table class="table" >
    <h1 class="text-center" style="color: #0c0a7e !important;">LIST SOURCE DETAILS</h1>
    <thead class="bg-light ">
        <tr>
          <th class="">ID</th>
          <th>UUID</th>
          <th>LIST NAME</th>
          <th>CODE</th>
          <th>CATEGORY</th>
          <th>PARENT ID</th>
          <th>DESCRIPTION</th>
          <th>CREATED</th>
          <th>UPDATED</th>
          <th>ACTION</th>
        </tr>
    </thead>
    
    <tbody>
         <?php foreach($sources as $source):?>
        <tr>
            <td style="color: #0c0a7e !important; ">#<?=$source->id ?></td>
            <td><?=$source->uuid ?></td>
            <td><?=$source->list_Name ?></td>
            <td><?=$source->code ?></td>
            <td><?=$source->category ?></td>
            <td><?=$source->parent_id ?></td>
            <td><?=$source->description ?></td>
            <td><?=$source->created_at ?></td>
            <td><?=$source->updated_at ?></td>
            <td>
                <?= \yii\helpers\Html::a(
    '<i class="bi bi-pencil-square"></i>',
    ['list-source/update', 'id' => $source->id],
    ['class' => 'me-4' ,'style'=>'color:#0c0a7e !important;']
) ?>

               <?= Html:: a( '<i class="bi bi-trash3 "></i>', ['list-source/delete','id'=>$source->id],['class' => 'text-danger','data'=>['confirm' =>'Are you sure You want to delete this Item?','method'=> 'post',]],
            );?>
               

            </td>
     </tr>
 <?php endforeach ?>
    </tbody>
 </table>
 
 </body>
 </html>