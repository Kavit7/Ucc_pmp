<?php 
use yii\helpers\Html; 

$this->title = 'Leases'; 
?>

<!-- Include SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container mt-4">
    <!-- Header & Create button -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap">
            <h1 class="mb-2 mb-md-0"><?= Html::encode($this->title) ?></h1>
            <?= Html::a('Create Lease', ['custom/create-lease'], ['class'=>'btn btn-success mb-2 mb-md-0']) ?>
        </div>
    </div>

    <!-- Leases Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover shadow-sm rounded">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Property</th>
                    <th>Tenant</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Duration (Months)</th>
                    <th>Document</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($leases as $lease): ?>
                <tr>
                    <td><?= $lease->id ?></td>
                    <td><?= Html::encode($lease->property->property_name ?? 'N/A') ?></td>
                    <td><?= Html::encode($lease->tenant->name ?? 'N/A') ?></td>
                    <td><?= Html::encode($lease->propertyPrice->unit_amount ?? 'N/A') ?></td>
                    <td>
                        <?php 
                        switch($lease->status){
                            case 0: echo 'Pending'; break;
                            case 1: echo 'Active'; break;
                            case 2: echo 'Terminated'; break;
                        } 
                        ?>
                    </td>
                    <td><?= Html::encode($lease->lease_start_date ?? '-') ?></td>
                    <td><?= Html::encode($lease->lease_end_date ?? '-') ?></td>
                    <td>
                        <?php 
                        if(isset($lease->lease_start_date, $lease->lease_end_date)){
                            $duration = (new DateTime($lease->lease_start_date))->diff(new DateTime($lease->lease_end_date))->m;
                            echo $duration;
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                    <td>
                        <?php if($lease->lease_doc_url): ?>
                            <a href="<?= $lease->lease_doc_url ?>" target="_blank">View Doc</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm delete-lease-btn" data-id="<?= $lease->id ?>">
                            Delete
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- SweetAlert2 Delete Script -->
<script>
document.querySelectorAll('.delete-lease-btn').forEach(button => {
    button.addEventListener('click', function() {
        const leaseId = this.dataset.id;

        Swal.fire({
            title: 'Are you sure?',
            text: "This lease will be permanently deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'post';
                form.action = '<?= \yii\helpers\Url::to(["custom/delete-lease"]) ?>?id=' + leaseId;

                // CSRF token
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '<?= Yii::$app->request->csrfParam ?>';
                csrf.value = '<?= Yii::$app->request->csrfToken ?>';
                form.appendChild(csrf);

                document.body.appendChild(form);
                form.submit();
            }
        })
    });
});
</script>

<style>
/* Small screen adjustments */
@media (max-width: 576px) {
    .table th, .table td {
        white-space: nowrap;
        font-size: 0.85rem;
    }
}
</style>
