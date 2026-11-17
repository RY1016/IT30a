<?php 
$host = 'localhost';
$db   = 'it30a_lab_db';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// ---------- 1. KPI COUNTS ----------
$studentCount  = $pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$bookCount     = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$totalBorrows  = $pdo->query("SELECT COUNT(*) FROM borrows")->fetchColumn();
$activeBorrows = $pdo->query("SELECT COUNT(*) FROM borrows WHERE borrow_return_date IS NULL")->fetchColumn();

// ---------- 2. BOOKS BY CATEGORY ----------
$catStmt = $pdo->query("
    SELECT book_category, COUNT(*) AS cnt
    FROM books
    GROUP BY book_category
    ORDER BY cnt DESC
");
$categories = [];
$categoryCounts = [];
while ($row = $catStmt->fetch()) {
    $categories[]     = $row['book_category'];
    $categoryCounts[] = (int) $row['cnt'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library · Dashboard</title>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* Materialize Base Typography */
        body {
            background: #f5f5f5; /* Material Grey 100 */
            font-family: Roboto, system-ui, -apple-system, sans-serif;
            color: #212121; /* Material Grey 900 */
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .dashboard { max-width: 1300px; width: 100%; }

        /* Materialize Standard Card */
        .stat-card, .chart-card, .recent-borrows {
            background: #ffffff;
            border-radius: 2px;
            box-shadow: 0 2px 2px 0 rgba(0,0,0,0.14),
                        0 3px 1px -2px rgba(0,0,0,0.12),
                        0 1px 5px 0 rgba(0,0,0,0.2);
            transition: box-shadow 0.3s;
        }
        .stat-card:hover, .chart-card:hover, .recent-borrows:hover {
            box-shadow: 0 4px 5px 0 rgba(0,0,0,0.14),
                        0 1px 10px 0 rgba(0,0,0,0.12),
                        0 2px 4px -1px rgba(0,0,0,0.3);
        }

        /* ---- KPI CARDS ---- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            border-top: 3px solid #1976d2; /* Material Blue Accent */
        }
        .stat-card:nth-child(2) { border-top-color: #4caf50; } /* Green */
        .stat-card:nth-child(3) { border-top-color: #ff9800; } /* Orange */
        .stat-card:nth-child(4) { border-top-color: #f44336; } /* Red */

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        .stat-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 500;
            color: #757575; /* Material Grey 600 */
        }
        .stat-header .material-icons {
            font-size: 1.8rem;
            color: #bdbdbd; /* Material Grey 400 */
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 400;
            color: #212121;
            line-height: 1.2;
        }
        .stat-sub {
            font-size: 0.85rem;
            color: #9e9e9e;
            margin-top: 0.3rem;
        }

        /* ---- CHARTS ---- */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .chart-card {
            padding: 1.5rem;
        }
        .chart-title {
            font-size: 1.1rem;
            font-weight: 500;
            color: #212121;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 0.8rem;
        }
        .chart-title span {
            font-weight: 500;
            color: #1976d2;
            font-size: 0.75rem;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        .chart-title span .material-icons {
            font-size: 1rem;
        }
        canvas {
            display: block;
            width: 100% !important;
            height: auto !important;
            max-height: 200px;
        }

        /* ---- RECENT BORROWS ---- */
        .recent-borrows {
            padding: 1.5rem;
        }
        .recent-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 0.8rem;
        }
        .recent-header h3 {
            font-weight: 500;
            font-size: 1.1rem;
            color: #212121;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .recent-header h3 .material-icons {
            color: #1976d2;
        }
        .recent-header a {
            font-size: 0.85rem;
            color: #1976d2;
            font-weight: 500;
            text-transform: uppercase;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.2rem;
        }
        .recent-header a:hover {
            text-decoration: underline;
        }
        .recent-header a .material-icons {
            font-size: 1rem;
        }

        .borrow-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #eeeeee;
            font-size: 0.95rem;
        }
        .borrow-row:last-child { border-bottom: none; }

        .borrow-student {
            font-weight: 500;
            color: #424242;
            width: 30%;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .borrow-book {
            color: #616161;
            width: 45%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .borrow-student .material-icons,
        .borrow-book .material-icons {
            font-size: 1.1rem;
            color: #9e9e9e;
        }

        /* Material Design Chips */
        .borrow-status {
            font-size: 0.75rem;
            padding: 0.3rem 0.8rem;
            border-radius: 16px; /* Chip border radius */
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        .borrow-status .material-icons {
            font-size: 0.9rem;
        }
        .borrow-status.returned {
            background: #e8f5e9; /* Green 50 */
            color: #2e7d32;      /* Green 800 */
        }
        .borrow-status.borrowed {
            background: #fff3e0; /* Orange 50 */
            color: #ef6c00;      /* Orange 800 */
        }

        .footer-note {
            margin-top: 2rem;
            text-align: center;
            font-size: 0.8rem;
            color: #9e9e9e;
            letter-spacing: 0.5px;
        }

        @media (max-width: 760px) {
            .charts-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .borrow-student,
            .borrow-book { width: auto; flex: 1; }
            .borrow-row { flex-wrap: wrap; gap: 0.5rem; }
        }
    </style>
</head>
<body>

    <div class="dashboard">
        <?php include 'includes/navbar.php'; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Students</span>
                    <span class="material-icons">school</span>
                </div>
                <div class="stat-number"><?= $studentCount ?></div>
                <div class="stat-sub">enrolled</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Books</span>
                    <span class="material-icons">auto_stories</span>
                </div>
                <div class="stat-number"><?= $bookCount ?></div>
                <div class="stat-sub">in collection</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Borrow records</span>
                    <span class="material-icons">history</span>
                </div>
                <div class="stat-number"><?= $totalBorrows ?></div>
                <div class="stat-sub">all time</div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <span class="stat-label">Active loans</span>
                    <span class="material-icons">pending_actions</span>
                </div>
                <div class="stat-number"><?= $activeBorrows ?></div>
                <div class="stat-sub">not returned</div>
            </div>
        </div>

        <!-- CHARTS -->
        <div class="charts-grid">

            <!-- Pie Chart - Book Categories-->
            <div class="chart-card">
                <div class="chart-title">
                    Books by category
                    <span>
                        <span class="material-icons">category</span>
                        collection
                    </span>
                </div>
                <canvas id="categoryChart" width="400" height="200"></canvas>
            </div>

        </div>
    </div>

    <script>
        (function() {
            // Materialize standard palette
            const colors = {
                blue: '#1976d2',
                green: '#4caf50',
                orange: '#ff9800',
                red: '#f44336',
                purple: '#9c27b0',
                grey: '#9e9e9e'
            };

            // ---------- DOUGHNUT : BOOKS BY CATEGORY ----------
            const catCtx = document.getElementById('categoryChart').getContext('2d');
            new Chart(catCtx, {
                type: 'doughnut',
                data: {
                    labels: <?= json_encode($categories) ?>,
                    datasets: [{
                        data: <?= json_encode($categoryCounts) ?>,
                        backgroundColor: [
                            colors.blue, colors.green, colors.orange, colors.red, colors.purple, colors.grey
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '65%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 12,
                                boxHeight: 12,
                                usePointStyle: false,
                                padding: 16,
                                font: { size: 12, family: 'Roboto' },
                                color: '#616161'
                            }
                        },
                        tooltip: {
                            backgroundColor: '#212121',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            padding: 10,
                            cornerRadius: 4,
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? ((value / total) * 100).toFixed(0) : 0;
                                    return ` ${label}: ${value} (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        })();
    </script>
    
</body>
</html>
