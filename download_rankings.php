<?php
include_once "./crest/crest.php";
include_once "./crest/settings.php";
include_once "./data/fetch_deals.php";
include_once "./data/fetch_users.php";
include_once "./controllers/calculate_agent_rank.php";

// Get parameters from URL
$selected_year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$type = isset($_GET['type']) ? $_GET['type'] : 'weekly';
$current_user = getCurrentUser();
$current_user_id = $current_user['ID'];
$global_ranking = calculateAgentRank();

// Define ranking type mappings
$ranking_map = [
    'weekly' => 'weekwise_rank',
    'monthly' => 'monthwise_rank',
    'quarterly' => 'quarterly_rank',
    'yearly' => 'yearly_rank'
];

// Validate type
if (!isset($ranking_map[$type])) {
    die("Invalid ranking type");
}

$ranking_data = $global_ranking[$selected_year][$ranking_map[$type]] ?? [];

// Filter rankings for the current user
function filterRankings($rank_data, $user_id)
{
    $ranking = [];
    foreach ($rank_data as $period => $agents) {
        foreach ($agents as $agent) {
            if ($agent['id'] == $user_id) {
                $ranking[$period] = [
                    'rank' => $agent['rank'] ?? 0,
                    'gross_comms' => $agent['gross_comms'] ?? 0
                ];
            }
        }
    }
    return $ranking;
}

$filtered_rankings = filterRankings($ranking_data, $current_user_id);

// Prepare CSV content
header('Content-Type: text/csv; charset=utf-8');
header("Content-Disposition: attachment; filename={$type}_rankings_{$selected_year}.csv");

$output = fopen('php://output', 'w');
fputcsv($output, [ucfirst($type), 'Rank', 'Opportunity Value']);

foreach ($filtered_rankings as $period => $data) {
    fputcsv($output, [$period, $data['rank'], number_format($data['gross_comms'], 2) . ' AED']);
}

fclose($output);
exit;
