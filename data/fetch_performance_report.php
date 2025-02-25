<?php
include('fetch_leads.php');
include('fetch_deals.php');
include('fetch_users.php');
include('../controllers/calculate_agent_rank.php');
require_once __DIR__ . '/../crest/crest.php';

// Get the current user details.
$user = getCurrentUser();
$userId = $user['ID'];

// Get the current month and year.
$currentMonth = date('M');
$currentYear = date('Y');

// Fetch overall ranking from cache.
$cacheFile = '../cache/global_ranking_cache.json';
$agentRankings = json_decode(file_get_contents($cacheFile), true);

$rankForMonth = $agentRankings[$currentYear]['monthwise_rank'][$currentMonth][$userId]['rank'] ?? '-';
$ytdRanking = $agentRankings[$currentYear]['yearly_rank'][$userId]['rank'] ?? '-';

// Fetch all leads and relevant statistics.
$allLeads = getAllLeads();
$marketingLeads = 200;
$activeLeads = CRest::call('crm.lead.list', ['filter' => ['ASSIGNED_BY_ID' => $userId]])['total'];
$unQualifiedLeads = CRest::call('crm.lead.list', ['filter' => ['ASSIGNED_BY_ID' => $userId, 'STATUS_ID' => 'JUNK']])['total'];

$leadsWithoutUpdate = 2;
$newHubspotDeals = 0;
$newHubspotMeetings = 0;
$propertyFinderAds = 3;
$propertyFinderLeads = 5;

// Fetch deals and calculate total earnings.
$allDeals = getFilteredDeals(['ASSIGNED_BY_ID' => $userId]);
$totalEarnings = array_reduce($allDeals, function ($sum, $deal) {
    return $sum + $deal['OPPORTUNITY'];
}, 0);

// Fetch deals in the current year.
$dealsInYear = getFilteredDeals(
    [
        '>=DATE_CREATE' => "$currentYear-01-01",
        '<=DATE_CREATE' => "$currentYear-12-31",
        'ASSIGNED_BY_ID' => $userId
    ],
    ["*", "UF_*"]
);

$totalWorthOfProperties = 6420000;
$avgMonthlyEarnings = round($totalEarnings / 12, 2);

// Prepare the JSON response.
echo json_encode([
    'totalLeads' => count($allLeads),
    'marketingLeads' => $marketingLeads,
    'activeLeads' => $activeLeads,
    'unQualifiedLeads' => $unQualifiedLeads,
    'leadsWithoutUpdate' => $leadsWithoutUpdate,
    'newHubspotDeals' => $newHubspotDeals,
    'newHubspotMeetings' => $newHubspotMeetings,
    'propertyFinderAds' => $propertyFinderAds,
    'propertyFinderLeads' => $propertyFinderLeads,
    'totalEarnings' => $totalEarnings,
    'totalWorthOfProperties' => $totalWorthOfProperties,
    'avgMonthlyEarnings' => $avgMonthlyEarnings,
    'rankForMonth' => $rankForMonth,
    'ytdRanking' => $ytdRanking,
    'dealsInYear' => $dealsInYear,
    'currentMonth' => $currentMonth,
    'currentYear' => $currentYear,
    'user' => trim($user['NAME'] . ' ' . $user['LAST_NAME'])
]);
