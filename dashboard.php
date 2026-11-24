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
// ---------- 3. BORROW ACTIVITY LAST 7 DAYS ----------

$dailyStmt = $pdo->query("
    SELECT DATE(borrow_date) AS day, COUNT(*) AS cnt
    FROM borrows
    WHERE borrow_date >= CURDATE() - INTERVAL 6 DAY
    GROUP BY DATE(borrow_date)
    ORDER BY day ASC
");

$borrowByDay = [];

while ($row = $dailyStmt->fetch()) {
    $borrowByDay[$row['day']] = (int) $row['cnt'];
}
// Generate labels and counts for the last 7 days
$dayLabels = [];
$dailyBorrows = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));

    $dayLabels[] = date('D', strtotime($date));
    $dailyBorrows[] = $borrowByDay[$date] ?? 0;
}

// ---------- 4. RECENT BORROWS ----------
$recentStmt = $pdo->query("
    SELECT
        s.student_first_name,
        s.student_last_name,
        b.book_title,
        br.borrow_return_date
    FROM borrows br
    INNER JOIN students s ON br.student_id = s.student_id
    INNER JOIN books b ON br.book_id = b.book_id
    ORDER BY br.borrow_id DESC
    LIMIT 5
");
$recentBorrows = $recentStmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library · Dashboard</title>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <link rel="stylesheet" href="dashboard.css">

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
                        <span class="material-icons">category collection</span>
                        
                    </span>
                </div>
                <canvas id="categoryChart" width="400" height="200"></canvas>
            </div>

            <!-- Bar Chart: Borrow Activity -->
            <div class="chart-card">
                <div class="chart-title">
                    Borrows · last 7 days

                    <span>
                        <span class="material-icons">trending_up activity</span>
                        
                    </span>
                </div>

                <canvas id="borrowChart" width="400" height="200"></canvas>
            </div>

        </div>
           <!-- RECENT BORROWS -->
    <div class="recent-borrows">
        <div class="recent-header">
            <h3>
                <span class="material-icons">receipt_long</span>
                Recent borrows
            </h3>
            <a href="#">
                view all
                <span class="material-icons">arrow_forward</span>
            </a>
        </div>
        <?php if (empty($recentBorrows)): ?>
            <div class="borrow-row" style="color:#9e9e9e; justify-content:center;">
                No borrow records yet.
            </div>
        <?php else: ?>
            <?php foreach ($recentBorrows as $row): ?>
                <div class="borrow-row">
                    <div class="borrow-student">
                        <span class="material-icons">person</span>
                        <?= htmlspecialchars($row['student_first_name'] . ' ' . $row['student_last_name']) ?>
                    </div>
                    <div class="borrow-book">
                        <span class="material-icons">book</span>
                        <?= htmlspecialchars($row['book_title']) ?>
                    </div>
                    <div class="borrow-status <?= $row['borrow_return_date'] ? 'returned' : 'borrowed' ?>">
                        <span class="material-icons">
                            <?= $row['borrow_return_date'] ? 'check_circle' : 'schedule' ?>
                        </span>
                        <?= $row['borrow_return_date'] ? 'Returned' : 'Borrowed' ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
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

            // ---------- BAR: BORROWS LAST 7 DAYS ----------

            const borrowCtx = document
                .getElementById('borrowChart')
                .getContext('2d');

            new Chart(borrowCtx, {
                type: 'bar',

                data: {
                    labels: <?= json_encode($dayLabels) ?>,

                    datasets: [{
                        label: 'Borrows',
                        data: <?= json_encode($dailyBorrows) ?>,
                        backgroundColor: colors.blue,
                        borderRadius: 0, // Material flat bars are sharp

                        barPercentage: 0.6,
                        categoryPercentage: 0.8
                    }]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: true,

                    plugins: {
                        legend: {
                            display: false
                        },

                        tooltip: {
                            backgroundColor: '#212121',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            padding: 10,
                            cornerRadius: 4,

                            callbacks: {
                                label: (ctx) => ` ${ctx.raw} borrows`
                            }
                        }
                    },

                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#eeeeee',
                                drawBorder: false
                            },
                            ticks: {
                                stepSize: 1,
                                color: '#757575',
                                font: {
                                    size: 11,
                                    family: 'Roboto'
                                }
                            }
                        },

                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#757575',
                                font: {
                                    size: 11,
                                    family: 'Roboto'
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
