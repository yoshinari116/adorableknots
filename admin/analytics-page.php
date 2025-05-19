<?php
session_start();
require_once '../database/db.php';

if (!isset($_SESSION['user']['user_id']) || $_SESSION['user']['user_type'] !== 'admin') {
    header("Location: ../signup-page.php");
    exit;
}

$sql = "
    SELECT c.category_name, IFNULL(SUM(oi.quantity * oi.price), 0) AS total_sales
    FROM category_tbl c
    LEFT JOIN product_tbl p ON p.category_id = c.category_id
    LEFT JOIN order_items_tbl oi ON oi.product_id = p.product_id
    LEFT JOIN orders_tbl o ON o.order_id = oi.order_id
    WHERE o.order_status = 'Delivered'
    GROUP BY c.category_id, c.category_name
    ORDER BY total_sales DESC
";
$stmt = $conn->prepare($sql);
$stmt->execute();
$salesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Adorable Knots - Analytics</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Afacad:ital,wght@0,400..700;1,400..700&family=Caveat:wght@400..700&family=Dosis:wght@200..800&display=swap" rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/navbar.css" />
    <link rel="stylesheet" href="../css/analytics-page.css" />
</head>
<body>
    <nav class="custom-navbar">
        <div class="custom-navbar-header">Admin Page</div>
        <div class="custom-navbar-contents">
            <div class="logo">
                <img src="../assets/web_img/ak-logo.png?v2" alt="Adorable Knots Logo" />
            </div>

            <div class="nav-links">
                <button><a href="admin-page.php">Products</a></button>
                <button><a href="admin-orders-page.php">Orders</a></button>
                <button class="active"><a href="analytics-page.php">Analytics</a></button>
            </div>

            <div class="logout">
                <a href="../login/logout.php">LOGOUT</a>
            </div>
        </div>
    </nav>

    <main class="chart-container my-4">
        <h1>Sales Analytics</h1>
        <div id="chart_div" style="width: 100%; height: 500px;"></div>
    </main>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', { packages: ['corechart', 'bar'] });
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Category', 'Sales Percentage', { role: 'style' }],
                <?php
                    $totalSales = array_sum(array_column($salesData, 'total_sales'));
                    $colors = ['#FF7EBC'];
                    $colorCount = count($colors);
                    foreach ($salesData as $index => $row) {
                        $percent = $totalSales > 0 ? round(($row['total_sales'] / $totalSales) * 100, 2) : 0;
                        $color = $colors[$index % $colorCount];
                        echo "['" . addslashes($row['category_name']) . "', " . $percent . ", '" . $color . "'],";
                    }
                ?>
            ]);

            var options = {
                title: 'Sales Percentage by Category',
                chartArea: { width: '50%' },
                hAxis: {
                    title: 'Percentage',
                    minValue: 0,
                    maxValue: 100,
                    format: '#\'%\''
                },
                vAxis: {
                    title: 'Category'
                },
                legend: { position: 'none' }
            };

            var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
    </script>
</body>
</html>
