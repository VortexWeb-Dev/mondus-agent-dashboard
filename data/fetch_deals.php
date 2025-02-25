<?php
require_once __DIR__ . '/../crest/crest.php';

/**
 * Fetch all deals from the CRM with CATEGORY_ID = 0.
 *
 * @return array List of all deals.
 */
function getAllDeals()
{
    return CRest::call('crm.deal.list', [
        'select' => ['*', 'UF_*'],
        'filter' => ['CATEGORY_ID' => 0],
    ])['result'] ?? [];
}

/**
 * Fetch all deal fields from the CRM.
 *
 * @return array List of all deal fields.
 */
function getDealFields()
{
    return CRest::call('crm.deal.fields', [])['result'] ?? [];
}

/**
 * Fetch deals from the CRM based on the given filters, selected fields, and order.
 *
 * @param array $filter Filter criteria for deals.
 * @param array $select Fields to be selected in the response.
 * @param array $order  Sorting order for the deals.
 * @return array Filtered list of deals.
 */
function getFilteredDeals(array $filter = [], array $select = ['*', 'UF_*'], array $order = [])
{
    return CRest::call('crm.deal.list', [
        'select' => $select,
        'filter' => $filter,
        'order' => $order,
    ])['result'] ?? [];
}
