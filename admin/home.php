<?php
include 'header.php';
include 'db_connect.php';

if (!isset($_SESSION['login_id'])) {
    header("Location: ../index.php");
    exit;
}

function getTotal($conn, $table)
{
    $result = $conn->query("SELECT COUNT(*) as total FROM $table");
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

$total_baptism = getTotal($conn, 'baptism_tbl');
$total_confirmation = getTotal($conn, 'confirmation_tbl');
$total_death = getTotal($conn, 'death_tbl');
$total_marriage = getTotal($conn, 'marriage_tbl');
?>

<head>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .custom-title {
            font-size: 1.75rem;
            font-weight: bold;
            text-align: center;
        }

        .custom-number {
            font-size: 2.5rem;
            font-weight: bold;
            text-align: center;
        }

        .card-icon {
            font-size: 2.5rem;
        }

        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            height: 100%;
            padding: 30px 20px;
        }

        .record-card {
            height: 180px !important;
        }

        #analyticsChartContainer {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto 60px;
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgb(0 0 0 / 0.1);
            height: 400px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #analyticsChart {
            max-width: 100%;
            max-height: 100%;
        }

        .chart-legend {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 15px;
            font-weight: 600;
            font-size: 1rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
        }

        .legend-info {
            user-select: none;
        }

        #chartTypeButtons {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px 0;
        }

        #chartTypeButtons button {
            margin: 0 8px;
            padding: 8px 16px;
            font-weight: 600;
            border-radius: 6px;
            border: 1.5px solid #0d6efd;
            background-color: white;
            color: #0d6efd;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        #chartTypeButtons button.active,
        #chartTypeButtons button:hover {
            background-color: #0d6efd;
            color: white;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <?php include 'navbar.php'; ?>
        <div class="content-wrapper" style="margin-top: 20px;">
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                    <h1 class="text-center fw-bold" style="font-size: 50px;">Welcome to Parish Records</h1>

                    <div class="container mt-5">
                        <div class="row g-4">
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="card record-card text-white bg-info h-100 rounded-4 shadow-sm">
                                    <div class="card-body">
                                        <i class="bi bi-water card-icon"></i>
                                        <h5 class="card-title custom-title mt-2">Baptism Records</h5>
                                        <p class="card-text custom-number"><?= $total_baptism ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="card record-card text-white bg-success h-100 rounded-4 shadow-sm">
                                    <div class="card-body">
                                        <i class="bi bi-shield-check card-icon"></i>
                                        <h5 class="card-title custom-title mt-2">Confirmation Records</h5>
                                        <p class="card-text custom-number"><?= $total_confirmation ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="card record-card text-white bg-danger h-100 rounded-4 shadow-sm">
                                    <div class="card-body">
                                        <i class="bi bi-person-x card-icon"></i>
                                        <h5 class="card-title custom-title mt-2">Death Records</h5>
                                        <p class="card-text custom-number"><?= $total_death ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="card record-card text-white bg-warning h-100 rounded-4 shadow-sm">
                                    <div class="card-body">
                                        <i class="bi bi-heart card-icon"></i>
                                        <h5 class="card-title custom-title mt-2">Marriage Records</h5>
                                        <p class="card-text custom-number"><?= $total_marriage ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="chartTypeButtons">
                        <button id="barBtn" class="active" aria-pressed="true" aria-label="Show Bar Chart">Bar Chart</button>
                        <button id="lineBtn" aria-pressed="false" aria-label="Show Line Chart">Line Chart</button>
                    </div>

                    <div id="analyticsChartContainer">
                        <canvas id="analyticsChart" aria-label="Parish Records Analytics Chart" role="img"></canvas>
                    </div>

                    <div class="chart-legend" id="chartLegend">
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #0dcaf0;"></div>
                            <div class="legend-info">Baptism</div>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #198754;"></div>
                            <div class="legend-info">Confirmation</div>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #dc3545;"></div>
                            <div class="legend-info">Death</div>
                        </div>
                        <div class="legend-item">
                            <div class="legend-color" style="background-color: #ffc107;"></div>
                            <div class="legend-info">Marriage</div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="policy" role="tabpanel" aria-labelledby="policy-tab">
                    <?php include 'policy.php'; ?>
                </div>
                <div class="tab-pane fade" id="tourist" role="tabpanel" aria-labelledby="tourist-tab">
                    <?php include 'tourist_spot.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('analyticsChart').getContext('2d');

        const labels = ['Baptism', 'Confirmation', 'Death', 'Marriage'];
        const dataValues = [<?= $total_baptism ?>, <?= $total_confirmation ?>, <?= $total_death ?>, <?= $total_marriage ?>];
        const colors = ['#0dcaf0', '#198754', '#dc3545', '#ffc107'];

        const legendContainer = document.getElementById('chartLegend');
        let analyticsChart;

        function getChartConfig(type) {
            const baseOptions = {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => ctx.parsed.y + ' records'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0
                        },
                        title: {
                            display: true,
                            text: 'Number of Records',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            display: false // Remove y-axis grid lines
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Record Types',
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        },
                        grid: {
                            display: false // Remove x-axis grid lines
                        }
                    }
                }
            };

            if (type === 'line') {
                return {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Records',
                            data: dataValues,
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13,110,253,0.2)',
                            fill: true,
                            tension: 0.3,
                            pointBackgroundColor: colors,
                            pointRadius: 6,
                            borderWidth: 3,
                        }]
                    },
                    options: baseOptions
                };
            } else {
                return {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total Records',
                            data: dataValues,
                            backgroundColor: colors,
                            borderRadius: 6,
                            barPercentage: 0.6
                        }]
                    },
                    options: baseOptions
                };
            }
        }

        function renderChart(type) {
            if (analyticsChart) analyticsChart.destroy();
            analyticsChart = new Chart(ctx, getChartConfig(type));
            legendContainer.style.display = 'flex';
        }

        const barBtn = document.getElementById('barBtn');
        const lineBtn = document.getElementById('lineBtn');

        [barBtn, lineBtn].forEach(btn => {
            btn.addEventListener('click', () => {
                [barBtn, lineBtn].forEach(b => {
                    b.classList.remove('active');
                    b.setAttribute('aria-pressed', 'false');
                });
                btn.classList.add('active');
                btn.setAttribute('aria-pressed', 'true');
                renderChart(btn.id.replace('Btn', '').toLowerCase());
            });
        });

        renderChart('bar');
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php include 'footer.php'; ?>