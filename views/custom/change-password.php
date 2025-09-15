<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
$this->title = 'Change Password';
?>

<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Card style - light border, subtle shadow */
    .card {
        border: 1px solid #e0e0e0;
        border-radius: 0.75rem;
        box-shadow: 0 4px 12px rgb(0 0 0 / 0.05);
    }
    .card-header {
        background-color: #f7f9fc;
        border-bottom: 1px solid #e0e0e0;
        font-weight: 600;
        font-size: 1.3rem;
        text-align: center;
        color: #2c3e50;
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
        padding: 1rem 1.5rem;
    }

    /* Input group */
    .input-group-text {
        background-color: #f0f3f7;
        border: 1px solid #ced4da;
        border-right: none;
        color: #6c757d;
        border-radius: 0.5rem 0 0 0.5rem;
        font-size: 1.1rem;
    }
    #change-password-form .form-control {
        border: 1px solid #ced4da;
        border-left: none;
        border-radius: 0 0.5rem 0.5rem 0;
        padding: 0.625rem 0.75rem;
        font-size: 1rem;
        transition: border-color 0.3s ease;
    }
    #change-password-form .form-control:focus {
        border-color: #4a90e2;
        box-shadow: none;
    }

    /* Feedback text */
    #currentPasswordFeedback, #passwordStrengthFeedback {
        font-size: 0.85rem;
        min-height: 1.2em;
        margin-top: 0.3rem;
        color: #6c757d;
        font-style: italic;
    }
    .feedback-error {
        color: #e74c3c !important; /* soft red */
        font-weight: 500;
    }
    .feedback-success {
        color: #27ae60 !important; /* soft green */
        font-weight: 500;
    }

    /* Submit button */
    #change-password-form button[type="submit"] {
        background-color: #4a90e2;
        border: none;
        font-weight: 600;
        border-radius: 0.5rem;
        padding: 0.75rem 1.25rem;
        font-size: 1.1rem;
        color: #fff;
        transition: background-color 0.3s ease;
        box-shadow: 0 4px 8px rgb(74 144 226 / 0.3);
    }
    #change-password-form button[type="submit"]:hover,
    #change-password-form button[type="submit"]:focus {
        background-color: #357ABD;
        box-shadow: 0 6px 12px rgb(53 122 189 / 0.5);
        outline: none;
    }
</style>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <?= Html::encode($this->title) ?>
                </div>
                <div class="card-body p-4">
                    <?php $form = ActiveForm::begin(['id'=>'change-password-form']); ?>

                    <!-- Current Password -->
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                            <?= $form->field($model, 'currentPassword')->passwordInput([
                                'placeholder'=>'Current password',
                                'id'=>'currentPassword',
                                'class'=>'form-control',
                                'onkeyup'=>'validateCurrentPassword()',
                                'aria-describedby'=>'currentPasswordFeedback'
                            ])->label(false) ?>
                        </div>
                        <div id="currentPasswordFeedback"></div>
                    </div>

                    <!-- New Password -->
                    <div class="mb-3">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-key"></i></span>
                            <?= $form->field($model, 'newPassword')->passwordInput([
                                'placeholder'=>'New password',
                                'id'=>'newPassword',
                                'class'=>'form-control',
                                'onkeyup'=>'checkPasswordStrength()',
                                'aria-describedby'=>'passwordStrengthFeedback'
                            ])->label(false) ?>
                        </div>
                        <div id="passwordStrengthFeedback"></div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-lock-keyhole"></i></span>
                            <?= $form->field($model, 'confirmPassword')->passwordInput([
                                'placeholder'=>'Confirm new password',
                                'id'=>'confirmPassword',
                                'class'=>'form-control',
                            ])->label(false) ?>
                        </div>
                    </div>

                    <div class="d-grid mt-3">
                        <?= Html::submitButton('Change Password', [
                            'class'=>'btn btn-lg',
                            'onclick'=>'return confirmChangePassword()'
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Live validation for current password
function validateCurrentPassword() {
    const pwd = document.getElementById('currentPassword').value.trim();
    const feedback = document.getElementById('currentPasswordFeedback');

    if (!pwd) {
        feedback.textContent = '';
        feedback.className = '';
        return;
    }

    fetch('<?= \yii\helpers\Url::to(["user/check-current-password"]) ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= Yii::$app->request->getCsrfToken() ?>'
        },
        body: JSON.stringify({ currentPassword: pwd })
    })
    .then(res => res.json())
    .then(data => {
        if (data.valid) {
            feedback.textContent = 'Password is correct';
            feedback.className = 'feedback-success';
        } else {
            feedback.textContent = 'Password is incorrect';
            feedback.className = 'feedback-error';
        }
    })
    .catch(() => {
        feedback.textContent = 'Error checking password';
        feedback.className = 'feedback-error';
    });
}

// Strong password check
function checkPasswordStrength() {
    const pwd = document.getElementById('newPassword').value.trim();
    const feedback = document.getElementById('passwordStrengthFeedback');
    const pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

    if (!pwd) {
        feedback.textContent = '';
        feedback.className = '';
        return false;
    }

    if (!pattern.test(pwd)) {
        feedback.textContent = 'Weak password. Must have uppercase, lowercase, number & special character, min 8 chars';
        feedback.className = 'feedback-error';
        return false;
    } else {
        feedback.textContent = 'Strong password';
        feedback.className = 'feedback-success';
        return true;
    }
}

// SweetAlert confirmation before submit
function confirmChangePassword() {
    const currentPassword = document.getElementById('currentPassword').value.trim();
    const newPassword = document.getElementById('newPassword').value.trim();
    const confirmPassword = document.getElementById('confirmPassword').value.trim();

    if (!currentPassword || !newPassword || !confirmPassword) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'All fields are required',
            confirmButtonColor: '#e74c3c'
        });
        return false;
    }

    if (!checkPasswordStrength()) return false;

    if (newPassword !== confirmPassword) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'New password and confirm password do not match',
            confirmButtonColor: '#e74c3c'
        });
        return false;
    }

    Swal.fire({
        title: 'Confirm Password Change',
        text: "Are you sure you want to change your password?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4a90e2',
        cancelButtonColor: '#e74c3c',
        confirmButtonText: 'Yes, change it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('change-password-form').submit();
        }
    });

    return false; // prevent normal submit
}

// Flash messages
<?php if (Yii::$app->session->hasFlash('success')): ?>
Swal.fire({
    icon: 'success',
    title: 'Success!',
    text: '<?= Yii::$app->session->getFlash('success') ?>',
    confirmButtonColor: '#4a90e2'
});
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error')): ?>
Swal.fire({
    icon: 'error',
    title: 'Error!',
    text: '<?= Yii::$app->session->getFlash('error') ?>',
    confirmButtonColor: '#e74c3c'
});
<?php endif; ?>
</script>
