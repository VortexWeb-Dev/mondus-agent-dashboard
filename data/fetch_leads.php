<?php
require_once __DIR__ . '/../crest/crest.php';

/**
 * Fetch leads with optional pagination.
 *
 * @param int|null $start Pagination start value.
 * @return array Response from the CRM containing lead data.
 */
function getLeads($start = null)
{
    $params = ['select' => ['*']];

    if ($start !== null) {
        $params['start'] = $start;
    }

    return CRest::call('crm.lead.list', $params);
}

/**
 * Fetch details of a specific lead by ID.
 *
 * @param int $leadId The ID of the lead to retrieve.
 * @return array|null Lead details or null if not found.
 */
function getLead($leadId)
{
    $result = CRest::call('crm.lead.get', ['ID' => $leadId]);
    return $result['result'] ?? null;
}

/**
 * Fetch all leads from the CRM using pagination.
 *
 * @return array List of all leads.
 */
function getAllLeads()
{
    $allLeads = [];
    $start = 0;

    do {
        $leadsData = getLeads($start);

        if (isset($leadsData['result'])) {
            $allLeads = array_merge($allLeads, $leadsData['result']);
        }

        $start = $leadsData['next'] ?? null;
    } while ($start !== null);

    return $allLeads;
}
