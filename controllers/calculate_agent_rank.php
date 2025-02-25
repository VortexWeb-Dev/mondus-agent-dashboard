<?php
require_once __DIR__ . '/../crest/crest.php';
require_once __DIR__ . '/../crest/settings.php';

// include the fetch deals page
include_once __DIR__ . '/../data/fetch_deals.php';
include_once __DIR__ . '/../data/fetch_users.php';

// import utility functions
include_once  __DIR__ . '/../utils/index.php';

function calculateAgentRank()
{
    $current_year = date('Y');
    $start_year = $current_year - 4; // Start from 5 years ago
    $cache_file = 'cache/global_ranking_cache.json';
    $global_ranking = [];

    if (file_exists($cache_file)) {
        $global_ranking = json_decode(file_get_contents($cache_file), true);
    } else {
        // Initialize global ranking structure for the last 5 years
        for ($year = $start_year; $year <= $current_year; $year++) {
            $global_ranking[$year] = [
                'monthwise_rank' => [
                    'Jan' => [],
                    'Feb' => [],
                    'Mar' => [],
                    'Apr' => [],
                    'May' => [],
                    'Jun' => [],
                    'Jul' => [],
                    'Aug' => [],
                    'Sep' => [],
                    'Oct' => [],
                    'Nov' => [],
                    'Dec' => [],
                ],
                'quarterly_rank' => [
                    'Q1' => [],
                    'Q2' => [],
                    'Q3' => [],
                    'Q4' => []
                ],
                'yearly_rank' => []
            ];
        }

        // Fetch deals for the last 5 years
        $deal_filters = [
            '>BEGINDATE' => date('Y-m-d', strtotime("$start_year-01-01")),
            '<=BEGINDATE' => date('Y-m-d', strtotime("$current_year-12-31")),
        ];
        $deal_selects = ['BEGINDATE', 'ASSIGNED_BY_ID', 'UF_CRM_1727871887978'];
        $deal_orders = ['UF_CRM_1727871887978' => 'DESC', 'BEGINDATE' => 'DESC'];

        // Sorted deals
        $sorted_deals = getFilteredDeals($deal_filters, $deal_selects, $deal_orders);

        // Store the sorted agent details from the deals to the ranking array
        function store_agents($sorted_deals, &$global_ranking)
        {
            foreach ($sorted_deals as $deal) {
                $responsible_agent_id = $deal['ASSIGNED_BY_ID'];
                $responsible_agent = getUser($responsible_agent_id); // Get responsible agent

                if (true) { // Adjust condition if necessary
                    $year = date('Y', strtotime($deal['BEGINDATE']));
                    $month = date('M', strtotime($deal['BEGINDATE']));
                    $quarter = get_quarter($month); // Get the quarter based on the month, Q1, Q2, Q3, Q4

                    $gross_comms = isset($deal['UF_CRM_1727871887978']) ? (int)explode('|', $deal['UF_CRM_1727871887978'])[0] : 0;

                    $agent = getUser($responsible_agent_id);
                    $agent_full_name = ($agent['NAME'] ?? '') . ' ' . ($agent['LAST_NAME'] ?? '');

                    $global_ranking[$year]['monthwise_rank'][$month][$responsible_agent_id]['name'] = $agent_full_name;
                    $global_ranking[$year]['monthwise_rank'][$month][$responsible_agent_id]['gross_comms'] =
                        ($global_ranking[$year]['monthwise_rank'][$month][$responsible_agent_id]['gross_comms'] ?? 0) + $gross_comms;

                    $global_ranking[$year]['quarterly_rank'][$quarter][$responsible_agent_id]['name'] = $agent_full_name;
                    $global_ranking[$year]['quarterly_rank'][$quarter][$responsible_agent_id]['gross_comms'] =
                        ($global_ranking[$year]['quarterly_rank'][$quarter][$responsible_agent_id]['gross_comms'] ?? 0) + $gross_comms;

                    $global_ranking[$year]['yearly_rank'][$responsible_agent_id]['name'] = $agent_full_name;
                    $global_ranking[$year]['yearly_rank'][$responsible_agent_id]['gross_comms'] =
                        ($global_ranking[$year]['yearly_rank'][$responsible_agent_id]['gross_comms'] ?? 0) + $gross_comms;
                }
            }
        }

        store_agents($sorted_deals, $global_ranking);

        // Fetch all users and ensure every agent is included
        $agents = getUsers();
        function store_remaining_agents($agents, &$global_ranking, $start_year, $current_year)
        {
            for ($year = $start_year; $year <= $current_year; $year++) {
                foreach ($global_ranking[$year] as $rank_type => &$rank_data) {
                    foreach ($rank_data as $period => &$agents_data) {
                        foreach ($agents as $id => $agent) {
                            $agent_full_name = $agent['NAME'] ?? '';
                            $agent_id = $agent['ID'] ?? '';

                            // Check if the agent exists in the specific rank section (monthly, quarterly, or yearly)
                            if (!isset($agents_data[$id])) {
                                // Initialize the agent data if not already set
                                $agents_data[$id] = [
                                    'id' => $agent_id,
                                    'name' => $agent_full_name,
                                    'gross_comms' => 0
                                ];
                            }
                        }
                    }
                }

                // Ensure agents are included in the yearly rank as well (if not already present)
                if (!isset($global_ranking[$year]['yearly_rank'])) {
                    $global_ranking[$year]['yearly_rank'] = [];
                }

                foreach ($agents as $id => $agent) {
                    $agent_full_name = $agent['NAME'] ?? '';
                    $agent_id = $agent['ID'] ?? '';

                    // Ensure that each agent is initialized in the yearly rank section
                    if (!isset($global_ranking[$year]['yearly_rank'][$id])) {
                        $global_ranking[$year]['yearly_rank'][$id] = [
                            'id' => $agent_id,
                            'name' => $agent_full_name,
                            'gross_comms' => 0
                        ];
                    }
                }
            }
        }

        store_remaining_agents($agents, $global_ranking, $start_year, $current_year);

        // Assign ranks
        function assign_rank(&$global_ranking, $rank_type)
        {
            foreach ($global_ranking as $year => &$data) {
                // Ensure $data[$rank_type] is an array
                if (!isset($data[$rank_type]) || !is_array($data[$rank_type])) {
                    error_log("Invalid rank_type structure for year $year: " . print_r($data[$rank_type] ?? null, true));
                    continue;
                }

                foreach ($data[$rank_type] as $period => &$agents) {
                    // Debug output
                    error_log("Processing $year - $period");
                    error_log("Agents data: " . print_r($agents, true));

                    // Ensure $agents is an array and contains valid data
                    if (!is_array($agents)) {
                        error_log("Invalid agents data structure for $year - $period: " . gettype($agents));
                        continue;
                    }

                    // Validate each agent entry before sorting
                    foreach ($agents as $agent_id => $agent_data) {
                        if (!is_array($agent_data)) {
                            error_log("Invalid agent data for ID $agent_id: " . print_r($agent_data, true));
                            // Initialize proper structure if needed
                            $agents[$agent_id] = [
                                'name' => is_string($agent_data) ? $agent_data : 'Unknown',
                                'gross_comms' => 0
                            ];
                        }

                        // Ensure gross_comms exists and is numeric
                        if (!isset($agents[$agent_id]['gross_comms'])) {
                            $agents[$agent_id]['gross_comms'] = 0;
                        }
                    }

                    // Sort agents by gross_comms
                    uasort($agents, function ($a, $b) {
                        if (!isset($a['gross_comms']) || !isset($b['gross_comms'])) {
                            error_log("Missing gross_comms in comparison: " . print_r([$a, $b], true));
                            return 0;
                        }
                        return $b['gross_comms'] <=> $a['gross_comms'];
                    });

                    $rank = 1;
                    $previous_gross_comms = null;
                    foreach ($agents as &$agent) {
                        if ($previous_gross_comms !== null && $agent['gross_comms'] == $previous_gross_comms) {
                            $agent['rank'] = $rank;
                        } else {
                            $agent['rank'] = $rank;
                            $previous_gross_comms = $agent['gross_comms'];
                            $rank++;
                        }
                    }
                }
            }
        }


        assign_rank($global_ranking, 'monthwise_rank');
        assign_rank($global_ranking, 'quarterly_rank');
        assign_rank($global_ranking, 'yearly_rank');

        // Save the data to cache
        $cacheDir = __DIR__ . '/../cache/';
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }
        file_put_contents($cacheDir . 'global_ranking_cache.json', json_encode($global_ranking));
    }

    return $global_ranking;
}
