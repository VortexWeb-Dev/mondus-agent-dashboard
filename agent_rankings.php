<?php
include_once "./crest/crest.php";
include_once "./crest/settings.php";
include('includes/header.php');
include('includes/sidebar.php');
include_once "./data/fetch_deals.php";
include_once "./data/fetch_users.php";
include_once "./controllers/calculate_agent_rank.php";

$global_ranking = calculateAgentRank();

// Get selected year from URL, default to current year if not provided
$selected_year = isset($_GET['year']) ? explode('/', $_GET['year'])[2] : date('Y');
$current_user = getCurrentUser();
$current_user_id = $current_user['ID'];
$agent_name = trim(implode(' ', array_filter([$current_user['NAME'], $current_user['SECOND_NAME'], $current_user['LAST_NAME']])));

/**
 * Filter rankings to get data for a specific user
 * 
 * @param array $rank_data The ranking data
 * @param int $user_id The user ID to filter
 * @return array Filtered rankings
 */
function filterRankings($rank_data, $user_id)
{
    $ranking = [];
    if (empty($rank_data) || !is_array($rank_data)) {
        return $ranking;
    }

    foreach ($rank_data as $period => $agents) {
        // Use array_filter to find the agent by ID
        $filtered_agents = array_filter($agents, function ($agent) use ($user_id) {
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

// Format numbers for display with currency
function formatCurrency($amount)
{
    return number_format($amount, 2) . ' AED';
}

// Filter rankings for the current user
$weekwise_ranked_agents = filterRankings($global_ranking[$selected_year]['weekwise_rank'] ?? [], $current_user_id);
$monthwise_ranked_agents = filterRankings($global_ranking[$selected_year]['monthwise_rank'] ?? [], $current_user_id);
$quarterly_ranked_agents = filterRankings($global_ranking[$selected_year]['quarterly_rank'] ?? [], $current_user_id);
$yearly_ranked_agents = [];

// For yearly ranking, we need to process it differently since it's not period-based
if (isset($global_ranking[$selected_year]['yearly_rank'])) {
    $yearly_data = $global_ranking[$selected_year]['yearly_rank'];
    if (isset($yearly_data[$current_user_id])) {
        $yearly_ranked_agents[$current_user_id]['name'] = $yearly_data[$current_user_id]['name'];
        $yearly_ranked_agents[$current_user_id]['rankings'][$selected_year] = [
            'gross_comms' => $yearly_data[$current_user_id]['gross_comms'] ?? 0,
            'rank' => $yearly_data[$current_user_id]['rank'] ?? 0
        ];
    }
}

/**
 * Render a ranking table
 * 
 * @param array $ranked_agents Array of ranking data
 * @param string $label Label for the period column
 */
function renderTable($ranked_agents, $label)
{
    global $current_user_id;
    if (empty($ranked_agents) || !isset($ranked_agents[$current_user_id]['rankings'])) {
        echo "<div class='flex items-center justify-center h-full'>
                <p class='text-gray-600 dark:text-gray-400'>No data available for the selected period.</p>
              </div>";
        return;
    }

    echo "<div class='overflow-auto h-full'>
        <table class='min-w-full divide-y divide-gray-200 dark:divide-gray-700 dark:bg-gray-800'>
            <thead class='bg-gray-50 dark:bg-gray-900'>
                <tr>
                    <th class='px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider'>{$label}</th>
                    <th class='px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider'>Rank</th>
                    <th class='px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider'>Opportunity Value</th>
                </tr>
            </thead>
            <tbody class='bg-white divide-y divide-gray-200 dark:divide-gray-700'>";

    // Sort periods chronologically
    $periods = array_keys($ranked_agents[$current_user_id]['rankings']);
    if ($label == 'Month') {
        $month_order = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        usort($periods, function ($a, $b) use ($month_order) {
            return array_search($a, $month_order) - array_search($b, $month_order);
        });
    } elseif ($label == 'Quarter') {
        $quarter_order = ['Q1', 'Q2', 'Q3', 'Q4'];
        usort($periods, function ($a, $b) use ($quarter_order) {
            return array_search($a, $quarter_order) - array_search($b, $quarter_order);
        });
    }

    foreach ($periods as $period) {
        $data = $ranked_agents[$current_user_id]['rankings'][$period];
        $gross_comms = formatCurrency($data['gross_comms']);
        $rank_class = '';

        // Highlight top rankings
        if ($data['rank'] == 1) {
            $rank_class = 'bg-yellow-100 dark:bg-yellow-900';
        } elseif ($data['rank'] == 2) {
            $rank_class = 'bg-gray-100 dark:bg-gray-700';
        } elseif ($data['rank'] == 3) {
            $rank_class = 'bg-amber-100 dark:bg-amber-900';
        }

        echo "<tr class='whitespace-nowrap text-sm font-medium hover:bg-gray-50 dark:bg-gray-800 dark:hover:bg-gray-700 {$rank_class}'>
                <td class='px-6 py-4 text-gray-900 dark:text-gray-200'>{$period}</td>
                <td class='px-6 py-4 text-gray-900 dark:text-gray-200'>{$data['rank']}</td>
                <td class='px-6 py-4 text-gray-900 dark:text-gray-200'>{$gross_comms}</td>
              </tr>";
    }
    echo "</tbody></table></div>";
}
?>

<div class="w-[85%] bg-gray-100 dark:bg-gray-900">
    <?php include('includes/navbar.php'); ?>
    <div class="px-8 py-6">
        <?php include('./includes/datepicker.php'); ?>
        <h1 class="text-2xl text-center font-bold mb-6 dark:text-gray-200"><?= $agent_name ?>'s Rankings for <?= $selected_year ?></h1>

        <div class="max-w-8xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-2">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md h-[400px] flex flex-col gap-4">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold dark:text-white">Weekly Rankings</h2>
                        <a href="download_rankings.php?year=<?= $selected_year ?>&type=weekly"
                            class="text-xs bg-blue-500 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded transition duration-200">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                    <?php renderTable($weekwise_ranked_agents, 'Week'); ?>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md h-[400px] flex flex-col gap-4">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold dark:text-white">Monthly Rankings</h2>
                        <a href="download_rankings.php?year=<?= $selected_year ?>&type=monthly"
                            class="text-xs bg-blue-500 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded transition duration-200">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                    <?php renderTable($monthwise_ranked_agents, 'Month'); ?>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md h-[400px] flex flex-col gap-4">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold dark:text-white">Quarterly Rankings</h2>
                        <a href="download_rankings.php?year=<?= $selected_year ?>&type=quarterly"
                            class="text-xs bg-blue-500 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded transition duration-200">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                    <?php renderTable($quarterly_ranked_agents, 'Quarter'); ?>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md h-[400px] flex flex-col gap-4">
                    <div class="flex justify-between items-center">
                        <h2 class="text-xl font-semibold dark:text-white">Yearly Ranking</h2>
                        <a href="download_rankings.php?year=<?= $selected_year ?>&type=yearly"
                            class="text-xs bg-blue-500 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded transition duration-200">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                    <?php renderTable($yearly_ranked_agents, 'Year'); ?>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="mt-6 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-semibold mb-4 dark:text-white">Performance Summary</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <?php
                    // Calculate summary statistics if data is available
                    $total_value = 0;
                    $best_period = null;
                    $best_rank = PHP_INT_MAX;

                    // Process monthly data for summary
                    if (!empty($monthwise_ranked_agents[$current_user_id]['rankings'])) {
                        foreach ($monthwise_ranked_agents[$current_user_id]['rankings'] as $period => $data) {
                            $total_value += $data['gross_comms'];
                            if ($data['rank'] < $best_rank) {
                                $best_rank = $data['rank'];
                                $best_period = $period;
                            }
                        }
                    }
                    ?>

                    <div class="bg-blue-50 dark:bg-blue-900/30 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-blue-800 dark:text-blue-300 mb-2">Total Opportunity Value</h3>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-200"><?= formatCurrency($total_value) ?></p>
                    </div>

                    <div class="bg-green-50 dark:bg-green-900/30 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-green-800 dark:text-green-300 mb-2">Best Ranking</h3>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-200">
                            <?= $best_rank !== PHP_INT_MAX ? "Rank $best_rank" : "N/A" ?>
                        </p>
                    </div>

                    <div class="bg-purple-50 dark:bg-purple-900/30 p-4 rounded-lg">
                        <h3 class="text-lg font-medium text-purple-800 dark:text-purple-300 mb-2">Best Period</h3>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-200">
                            <?= $best_period ? $best_period : "N/A" ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('includes/footer.php'); ?>