<?php
require_once __DIR__ . '/../crest/crest.php';
require_once __DIR__ . '/../crest/crestcurrent.php';

/**
 * Fetches all users from the system.
 * 
 * @return array An array of user data.
 */
function getUsers()
{
    $result = CRest::call('user.get', [
        'select' => ['*', 'UF_*'],
    ]);
    return $result['result'];
}

/**
 * Fetches filtered users based on provided criteria.
 * 
 * @param array $filter Filter criteria for users.
 * @param array $select Fields to select for each user.
 * @param array $order Sorting order for the results.
 * @return array An array of filtered user data.
 */
function getFilteredUsers($filter = [], $select = ['*', 'UF_*'], $order = [])
{
    $result = CRest::call('user.get', [
        'filter' => $filter,
        'select' => $select,
        'order' => $order,
    ]);
    return $result['result'];
}

/**
 * Retrieves the metadata for all user fields.
 * 
 * @return array An array of user field metadata.
 */
function getUserFields()
{
    $result = CRest::call('user.fields');
    return $result['result'];
}

/**
 * Retrieves custom user fields defined in the system.
 * 
 * @return array An array of custom user fields.
 */
function getCustomUserFields()
{
    $result = CRest::call('user.userfield.list');
    return $result['result'];
}

/**
 * Fetches data for a specific user by ID.
 * 
 * @param int $userId The ID of the user to retrieve.
 * @return array|null An array of user data or null if not found.
 */
function getUser($userId)
{
    $result = CRest::call('user.get', ['ID' => $userId]);
    return $result['result'][0] ?? null;
}

/**
 * Retrieves the current logged-in user's data.
 * 
 * @return array The current user's data.
 */
function getCurrentUser()
{
    $result = CRestCurrent::call('user.current');
    return $result['result'];
}
