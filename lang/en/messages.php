<?php

return [
    'errors' => [
        'invalid_data' => 'Invalid data.',
        'resource_not_found' => 'Resource not found.',
        'not_authenticated' => 'Unauthenticated.',
        'unauthorized_action' => 'Unauthorized action.',
        'too_many_requests' => 'Too many requests. Please try again later.',
        'internal_server_error' => 'Internal server error.',
        'unauthorized' => 'Unauthorized.',
    ],
    'auth' => [
        'logout_success' => 'Logout successful.',
        'token_required' => 'Token is required.',
        'invalid_credentials' => 'Invalid credentials.',
        'user_not_found' => 'User not found.',
    ],
    'player' => [
        'deleted_success' => 'Player deleted successfully.',
        'delete_forbidden' => 'Only administrators can delete players.',
    ],
    'team' => [
        'deleted_success' => 'Team deleted successfully.',
        'delete_forbidden' => 'Only administrators can delete teams.',
    ],
    'game' => [
        'deleted_success' => 'Game deleted successfully.',
        'delete_forbidden' => 'Only administrators can delete games.',
    ],
    'import' => [
        'forbidden' => 'Only administrators can import data.',
    ],
];
