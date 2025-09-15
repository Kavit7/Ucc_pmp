<div class="container-fluid mt-4 body">
    <div class="row g-3">
        <!-- Cards -->
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6>Total Properties</h6>
                    <h3><?= $totalProperties ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6>Sale</h6>
                    <h3><?= $forSale ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6> Rent</h6>
                    <h3><?= $forRent ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6>Stored</h6>
                    <h3><?= $stored ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts -->
    <div class="row mt-4">
        <!-- Left Chart -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title text-center">Property Analytics (Types)</h5>
                    <canvas id="propertyChart1" style="height:220px;"></canvas>
                </div>
            </div>
        </div>
        <!-- Right Chart -->
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title text-center">Property Analytics (Status)</h5>
                    <canvas id="propertyChart2" style="height:220px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Translation Report -->
    <div class="mt-4">
        <!-- Toolbar -->
        <div class="d-flex justify-content-between align-items-center mb-3 p-3"
            style="gap:10px; background-color:#ffffff; border-radius:8px; flex-wrap:wrap;">
            <h3 class="mb-0">Translation Report</h3>
            <div class="d-flex align-items-center gap-2">
                <div class="position-relative" style="max-width: 250px;">
                    <i class="bi bi-search position-absolute"
                        style="left: 12px; top: 50%; transform: translateY(-50%); color: #939292ff;"></i>
                    <input type="text" id="searchInput" class="form-control ps-5"
                        placeholder="Search..."
                        style="background-color: #e2dedeff; border: 1px solid #c7c0c0ff; border-radius: 20px;">
                </div>

                <button id="exportBtn" class="btn" style="background-color:#e2dedeff; color:#000; border:1px solid #ccc;">
                    <i class="fas fa-file-export"></i> Export
                </button>
                <button id="printBtn" class="btn" style="background-color:#e2dedeff; color:#000; border:1px solid #ccc;">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="translationTable">
                <thead>
                    <tr>
                        <th>Lease ID</th>
                        <th>Customer Name</th>
                        <th>Property Name</th>
                        <th>Price</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($translationReport as $row): ?>
                        <tr>
                            <td><?= $row['lease_id'] ?></td>
                            <td><?= $row['tenant_name'] ?></td>
                            <td><?= $row['property_name'] ?></td>
                            <td><?= number_format($row['price'], 2) ?></td>
                            <td><?= $row['start_date'] ?></td>
                            <td><?= $row['end_date'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Chart 1: Property Types
        const ctx1 = document.getElementById('propertyChart1').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($analytics, 'type_name')) ?>,
                datasets: [{
                    label: 'Total',
                    data: <?= json_encode(array_column($analytics, 'total')) ?>,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                    borderRadius: 6,
                    barPercentage: 0.4, // default 0.9
                    categoryPercentage: 0.4

                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Chart 2: Property Status (Sale, Rent, Stored)
        const ctx2 = document.getElementById('propertyChart2').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: [' Sale', ' Rent', 'Stored'],
                datasets: [{
                    label: 'Total',
                    data: [<?= $forSale ?>, <?= $forRent ?>, <?= $stored ?>],
                    backgroundColor: ['#36b9cc', '#f6c23e', '#e74a3b'],
                    borderRadius: 6,
                    barPercentage: 0.4, // default 0.9
                    categoryPercentage: 0.4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
