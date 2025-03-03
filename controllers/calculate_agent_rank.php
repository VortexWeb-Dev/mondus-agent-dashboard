<?php
require_once __DIR__ . '/../crest/crest.php';
require_once __DIR__ . '/../crest/settings.php';
include_once __DIR__ . '/../data/fetch_deals.php';
include_once __DIR__ . '/../data/fetch_users.php';
include_once  __DIR__ . '/../utils/index.php';

function calculateAgentRank()
{
    $current_year = date('Y');
    $start_year = $current_year - 4;
    $cache_file = __DIR__ . '/../cache/global_ranking_cache.json';
    $global_ranking = [];

    if (file_exists($cache_file)) {
        $global_ranking = json_decode(file_get_contents($cache_file), true);
    } else {
        for ($year = $start_year; $year <= $current_year; $year++) {
            $global_ranking[$year] = [
                'weekwise_rank' => [
                    'Week 1' => [],
                    'Week 2' => [],
                    'Week 3' => [],
                    'Week 4' => [],
                    'Week 5' => [],
                ],
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


        $deal_filters = [
            '>BEGINDATE' => date('Y-m-d', strtotime("$start_year-01-01")),
            '<=BEGINDATE' => date('Y-m-d', strtotime("$current_year-12-31")),
            // 'STAGE_ID' => 'WON'
        ];
        $deal_selects = ['BEGINDATE', 'ASSIGNED_BY_ID', 'OPPORTUNITY', 'STAGE_ID'];
        $deal_orders = ['BEGINDATE' => 'DESC'];


        $sorted_deals = getFilteredDeals($deal_filters, $deal_selects, $deal_orders);


        function store_agents($sorted_deals, &$global_ranking)
        {
            foreach ($sorted_deals as $deal) {
                $responsible_agent_id = $deal['ASSIGNED_BY_ID'];
                $agent = getUser($responsible_agent_id);

                if ($agent) {
                    $year = date('Y', strtotime($deal['BEGINDATE']));
                    $month = date('M', strtotime($deal['BEGINDATE']));
                    $week = date('W', strtotime($deal['BEGINDATE']));
                    $quarter = get_quarter($month);


                    $gross_comms = isset($deal['OPPORTUNITY']) ? (float)$deal['OPPORTUNITY'] : 0;

                    $agent_full_name = ($agent['NAME'] ?? '') . ' ' . ($agent['LAST_NAME'] ?? '');

                    if(!isset($global_ranking[$year]['weekwise_rank'][$week][$responsible_agent_id])) {
                        $global_ranking[$year]['weekwise_rank'][$week][$responsible_agent_id] = [
                            'id' => $responsible_agent_id,
                            'name' => $agent_full_name,
                            'gross_comms' => 0
                        ];
                    }
                    $global_ranking[$year]['weekwise_rank'][$week][$responsible_agent_id]['gross_comms'] += $gross_comms;


                    if (!isset($global_ranking[$year]['monthwise_rank'][$month][$responsible_agent_id])) {
                        $global_ranking[$year]['monthwise_rank'][$month][$responsible_agent_id] = [
                            'id' => $responsible_agent_id,
                            'name' => $agent_full_name,
                            'gross_comms' => 0
                        ];
                    }
                    $global_ranking[$year]['monthwise_rank'][$month][$responsible_agent_id]['gross_comms'] += $gross_comms;


                    if (!isset($global_ranking[$year]['quarterly_rank'][$quarter][$responsible_agent_id])) {
                        $global_ranking[$year]['quarterly_rank'][$quarter][$responsible_agent_id] = [
                            'id' => $responsible_agent_id,
                            'name' => $agent_full_name,
                            'gross_comms' => 0
                        ];
                    }
                    $global_ranking[$year]['quarterly_rank'][$quarter][$responsible_agent_id]['gross_comms'] += $gross_comms;


                    if (!isset($global_ranking[$year]['yearly_rank'][$responsible_agent_id])) {
                        $global_ranking[$year]['yearly_rank'][$responsible_agent_id] = [
                            'id' => $responsible_agent_id,
                            'name' => $agent_full_name,
                            'gross_comms' => 0
                        ];
                    }
                    $global_ranking[$year]['yearly_rank'][$responsible_agent_id]['gross_comms'] += $gross_comms;
                }
            }
        }

        store_agents($sorted_deals, $global_ranking);


        $agents = getUsers();
        function store_remaining_agents($agents, &$global_ranking, $start_year, $current_year)
        {
            for ($year = $start_year; $year <= $current_year; $year++) {

                foreach ($global_ranking[$year]['weekwise_rank'] as $week => &$agents_data) {
                    foreach ($agents as $agent) {
                        $agent_id = $agent['ID'] ?? '';
                        $agent_full_name = ($agent['NAME'] ?? '') . ' ' . ($agent['LAST_NAME'] ?? '');

                        if (!empty($agent_id) && !isset($agents_data[$agent_id])) {
                            $agents_data[$agent_id] = [
                                'id' => $agent_id,
                                'name' => $agent_full_name,
                                'gross_comms' => 0
                            ];
                        }
                    }
                }

                foreach ($global_ranking[$year]['monthwise_rank'] as $month => &$agents_data) {
                    foreach ($agents as $agent) {
                        $agent_id = $agent['ID'] ?? '';
                        $agent_full_name = ($agent['NAME'] ?? '') . ' ' . ($agent['LAST_NAME'] ?? '');

                        if (!empty($agent_id) && !isset($agents_data[$agent_id])) {
                            $agents_data[$agent_id] = [
                                'id' => $agent_id,
                                'name' => $agent_full_name,
                                'gross_comms' => 0
                            ];
                        }
                    }
                }


                foreach ($global_ranking[$year]['quarterly_rank'] as $quarter => &$agents_data) {
                    foreach ($agents as $agent) {
                        $agent_id = $agent['ID'] ?? '';
                        $agent_full_name = ($agent['NAME'] ?? '') . ' ' . ($agent['LAST_NAME'] ?? '');

                        if (!empty($agent_id) && !isset($agents_data[$agent_id])) {
                            $agents_data[$agent_id] = [
                                'id' => $agent_id,
                                'name' => $agent_full_name,
                                'gross_comms' => 0
                            ];
                        }
                    }
                }


                foreach ($agents as $agent) {
                    $agent_id = $agent['ID'] ?? '';
                    $agent_full_name = ($agent['NAME'] ?? '') . ' ' . ($agent['LAST_NAME'] ?? '');

                    if (!empty($agent_id) && !isset($global_ranking[$year]['yearly_rank'][$agent_id])) {
                        $global_ranking[$year]['yearly_rank'][$agent_id] = [
                            'id' => $agent_id,
                            'name' => $agent_full_name,
                            'gross_comms' => 0
                        ];
                    }
                }
            }
        }

        store_remaining_agents($agents, $global_ranking, $start_year, $current_year);


        function assign_rank(&$global_ranking)
        {
            foreach ($global_ranking as $year => &$data) {

                foreach ($data['weekwise_rank'] as $week => &$agents) {

                    uasort($agents, function ($a, $b) {
                        return $b['gross_comms'] <=> $a['gross_comms'];
                    }); 


                    $rank = 1;
                    $previous_gross_comms = null;
                    $previous_rank = 0;

                    foreach ($agents as &$agent) {
                        if ($previous_gross_comms !== null && $agent['gross_comms'] == $previous_gross_comms) {
                            $agent['rank'] = $previous_rank;
                        } else {
                            $agent['rank'] = $rank;
                            $previous_gross_comms = $agent['gross_comms'];
                            $previous_rank = $rank;
                        }
                        $rank++;
                    }
                }

                foreach ($data['monthwise_rank'] as $month => &$agents) {

                    uasort($agents, function ($a, $b) {
                        return $b['gross_comms'] <=> $a['gross_comms'];
                    });


                    $rank = 1;
                    $previous_gross_comms = null;
                    $previous_rank = 0;

                    foreach ($agents as &$agent) {
                        if ($previous_gross_comms !== null && $agent['gross_comms'] == $previous_gross_comms) {
                            $agent['rank'] = $previous_rank;
                        } else {
                            $agent['rank'] = $rank;
                            $previous_gross_comms = $agent['gross_comms'];
                            $previous_rank = $rank;
                        }
                        $rank++;
                    }
                }


                foreach ($data['quarterly_rank'] as $quarter => &$agents) {

                    uasort($agents, function ($a, $b) {
                        return $b['gross_comms'] <=> $a['gross_comms'];
                    });


                    $rank = 1;
                    $previous_gross_comms = null;
                    $previous_rank = 0;

                    foreach ($agents as &$agent) {
                        if ($previous_gross_comms !== null && $agent['gross_comms'] == $previous_gross_comms) {
                            $agent['rank'] = $previous_rank;
                        } else {
                            $agent['rank'] = $rank;
                            $previous_gross_comms = $agent['gross_comms'];
                            $previous_rank = $rank;
                        }
                        $rank++;
                    }
                }


                $yearly_agents = &$data['yearly_rank'];


                uasort($yearly_agents, function ($a, $b) {
                    return $b['gross_comms'] <=> $a['gross_comms'];
                });


                $rank = 1;
                $previous_gross_comms = null;
                $previous_rank = 0;

                foreach ($yearly_agents as &$agent) {
                    if ($previous_gross_comms !== null && $agent['gross_comms'] == $previous_gross_comms) {
                        $agent['rank'] = $previous_rank;
                    } else {
                        $agent['rank'] = $rank;
                        $previous_gross_comms = $agent['gross_comms'];
                        $previous_rank = $rank;
                    }
                    $rank++;
                }
            }
        }

        assign_rank($global_ranking);


        $cacheDir = dirname($cache_file);
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }
        file_put_contents($cache_file, json_encode($global_ranking));
    }

    return $global_ranking;
}