<?php
include_once "./crest/crest.php";
include_once "./crest/settings.php";
include('includes/header.php');
include('includes/sidebar.php');
include_once "./data/fetch_deals.php";
include_once "./data/fetch_users.php";
include_once "./controllers/calculate_agent_rank.php";

$global_ranking = calculateAgentRank();
// echo '<pre>';
// print_r($global_ranking);
// echo '</pre>';

$selected_year = $_GET['year'] ? explode('/', $_GET['year'])[2] : date('Y');
$current_user = getCurrentUser();
$current_user_id = $current_user['ID'];
$agent_name = trim(implode(' ', array_filter([$current_user['NAME'], $current_user['SECOND_NAME'], $current_user['LAST_NAME']])));

function filterRankings($rank_data, $user_id)
{
    $ranking = [];
    foreach ($rank_data as $period => $agents) {
        // Use array_filter to find the agent by name
        $filtered_agents = array_filter($agents, function($agent) use ($user_id) {
            return isset($agent['id']) && $agent['id'] == $user_id;
        });
        
        // If a matching agent is found, add the relevant data
        if (!empty($filtered_agents)) {
            $agent = reset($filtered_agents); // Get the first (and presumably only) matching agent
            $ranking[$user_id]['name'] = $agent['name'];
            $ranking[$user_id]['rankings'][$period] = [
                'gross_comms' => $agent['gross_comms'] ?? 0,
                'rank' => $agent['rank'] ?? 0
            ];
        }
    }
    return $ranking;
}

$monthwise_ranked_agents = filterRankings($global_ranking[$selected_year]['monthwise_rank'] ?? [], $current_user_id);
$quarterly_ranked_agents = filterRankings($global_ranking[$selected_year]['quarterly_rank'] ?? [], $current_user_id);
$yearly_ranked_agents = filterRankings($global_ranking[$selected_year]['yearly_rank'] ?? [], $current_user_id);

function renderTable($ranked_agents, $label)
{
    global $current_user_id;
    if (empty($ranked_agents)) {
        echo "<p class='text-gray-600 dark:text-gray-400'>No data available.</p>";
        return;
    }
    echo "<div class='overflow-auto'>
        <table class='min-w-full divide-y divide-gray-200 dark:divide-gray-700 dark:bg-gray-800'>
            <thead class='bg-gray-50 dark:bg-gray-900'>
                <tr>
                    <th class='px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider'>{$label}</th>
                    <th class='px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider'>Rank</th>
                    <th class='px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider'>Gross Comm</th>
                </tr>
            </thead>
            <tbody class='bg-white divide-y divide-gray-200 dark:divide-gray-700'>";
    foreach ($ranked_agents[$current_user_id]['rankings'] as $period => $data) {
        echo "<tr class='whitespace-nowrap text-sm font-medium hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700'>
                <td class='px-6 py-4 text-gray-900 dark:text-gray-200'>{$period}</td>
                <td class='px-6 py-4 text-gray-900 dark:text-gray-200'>{$data['rank']}</td>
                <td class='px-6 py-4 text-gray-900 dark:text-gray-200'>{$data['gross_comms']} AED</td>
              </tr>";
    }
    echo "</tbody></table></div>";
}
?>

<div class="w-[85%] bg-gray-100 dark:bg-gray-900">
    <?php include('includes/navbar.php'); ?>
    <div class="px-8 py-6">
        <?php include('./includes/datepicker.php'); ?>
        <h1 class="text-xl text-center font-bold mb-4 dark:text-gray-200"><?= $agent_name ?>'s Rankings</h1>
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm h-[400px] flex flex-col gap-1">
                    <h2 class="text-xl font-semibold mb-6 dark:text-white">Monthly Ranking</h2>
                    <?php renderTable($monthwise_ranked_agents, 'Month'); ?>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm h-[400px] flex flex-col gap-1">
                    <h2 class="text-xl font-semibold mb-6 dark:text-white">Quarterly Ranking</h2>
                    <?php renderTable($quarterly_ranked_agents, 'Quarter'); ?>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm h-[400px] flex flex-col gap-1">
                    <h2 class="text-xl font-semibold mb-6 dark:text-white">Yearly Ranking</h2>
                    <?php renderTable($yearly_ranked_agents, 'Year'); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('includes/footer.php'); ?>