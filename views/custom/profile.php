<?php
use yii\helpers\Html;

$this->title = 'My Profile';
?>

<div class="container mt-5">
    <h1 class="mb-4 text font-weight-bold"><?= Html::encode($this->title) ?></h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row">
                <!-- Left column: Profile picture and username -->
                <div class="col-md-4 text-center border-right">
                    <img src="<?= Html::encode($user->profile_picture ?? '/images/default-avatar.png') ?>" 
                         alt="Profile Picture" class="rounded-circle mb-3" style="width:150px; height:150px; object-fit:cover;">
                    <h3 class="font-weight-bold"><?= Html::encode($user->username) ?></h3>
                    <p class="text-muted"><?= Html::encode($user->full_name ?? '') ?></p>
                </div>

                <!-- Right column: Details -->
                <div class="col-md-8">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th><i class="fas fa-envelope text-primary mr-2"></i>Email</th>
                                <td><?= Html::encode($user->email) ?></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-phone-alt text-primary mr-2"></i>Phone Number</th>
                                <td><?= Html::encode($user->phone ?? 'N/A') ?></td>
                            </tr>
                        
                            <tr>
                                <th><i class="fas fa-map-marker-alt text-primary mr-2"></i>Location</th>
                                <td><?= Html::encode($user->location ?? 'N/A') ?></td>
                            </tr>
                            <!-- Ongeza fields zaidi unazotaka kuonyesha -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
