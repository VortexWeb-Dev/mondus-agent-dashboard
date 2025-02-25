<?php
include_once './crest/crest.php';
include_once './crest/settings.php';

CRest::call(
    'event.bind',
    [
        'event' => 'onCrmDealAdd',
        'handler' => 'https://mondus.group/apps/agent-dashboard/handlers/update_agent_ranking.php',
    ]
);
CRest::call(
    'event.bind',
    [
        'event' => 'onCrmDealUpdate',
        'handler' => 'https://mondus.group/apps/agent-dashboard/handlers/update_agent_ranking.php',
    ]
);
CRest::call(
    'event.bind',
    [
        'event' => 'onCrmDealDelete',
        'handler' => 'https://mondus.group/apps/agent-dashboard/handlers/update_agent_ranking.php',
    ]
);
CRest::call(
    'event.bind',
    [
        'event' => 'onCrmDealUserFieldUpdate',
        'handler' => 'https://mondus.group/apps/agent-dashboard/handlers/update_agent_ranking.php',
    ]
);
