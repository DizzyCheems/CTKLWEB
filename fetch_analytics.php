<?php
include 'config.php';

header('Content-Type: application/json');

$dateFrom = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : date('Y-m-d', strtotime('-30 days'));
$dateTo = isset($_GET['dateTo']) ? $_GET['dateTo'] : date('Y-m-d');

// Monthly User Visits
$monthlyVisits = [];
$startDate = new DateTime($dateFrom);
$endDate = new DateTime($dateTo);
$interval = DateInterval::createFromDateString('1 month');
$period = new DatePeriod($startDate, $interval, $endDate->modify('+1 month'));
foreach ($period as $dt) {
    $month = $dt->format('Y-m');
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT user_id) as visits FROM active_sessions WHERE DATE_FORMAT(last_active, '%Y-%m') = ? AND last_active BETWEEN ? AND ?");
    $stmt->execute([$month, $dateFrom, $dateTo]);
    $monthlyVisits[$month] = $stmt->fetchColumn();
}

// Daily User Traffic
$dailyTraffic = [];
$startDate = new DateTime($dateFrom);
$endDate = new DateTime($dateTo);
$interval = DateInterval::createFromDateString('1 day');
$period = new DatePeriod($startDate, $interval, $endDate->modify('+1 day'));
foreach ($period as $dt) {
    $day = $dt->format('Y-m-d');
    $stmt = $pdo->prepare("SELECT COUNT(*) as traffic FROM active_sessions WHERE DATE(last_active) = ? AND last_active BETWEEN ? AND ?");
    $stmt->execute([$day, $dateFrom, $dateTo]);
    $dailyTraffic[$day] = $stmt->fetchColumn();
}

$response = [
    'monthly' => [
        'labels' => array_keys($monthlyVisits),
        'data' => array_values($monthlyVisits)
    ],
    'daily' => [
        'labels' => array_keys($dailyTraffic),
        'data' => array_values($dailyTraffic)
    ]
];

echo json_encode($response);
?>