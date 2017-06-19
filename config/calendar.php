<?php

return [
    'Calendar' => [
        'Types' => [
            [
                'name' => 'Actitivies',
                'value' => 'user_activities',
                'types' => [
                    'calls' => [
                        'name' => 'Calls',
                        'value' => 'calls'
                    ],
                    'meetings' => [
                        'name' => 'Meetings',
                        'value' => 'meetings',
                    ],
                    'tasks' => [
                        'name' => 'Tasks',
                        'value' => 'tasks',
                    ],
                ],
            ],
            [
                'name' => 'HR',
                'value' => 'hr',
                'types' => [
                    'annual_leaves' => [
                        'name' => 'Annual Leaves',
                        'value' => 'annual_leaves',
                    ],
                    'birthdays' => [
                        'name' => 'Birthdays',
                        'value' => 'birthdays',
                    ],
                ],
            ],
        ],
        'Templates' => [
            '_default' => [
                'name' => 'Default Template',
                'value' => '_default',
                'minDuration' => '30 minutes',
            ],
            'calls' => [
                'name' => 'Calls',
                'value' => 'calls',
                'minDuration' => '30 minutes',
            ],
            'meetings' => [
                'name' => 'Meetings',
                'value' => 'meetings',
                'minDuration' => '1 hour',
            ],
            'tasks' => [
                'name' => 'Tasks',
                'value' => 'tasks',
                'minDuration' => '1 hour',
            ],
            'annual_leaves' => [
                'name' => 'Annual Leaves',
                'value' => 'annual_leaves',
                'minDuration' => '1 day',
            ],
            'birthdays' => [
                'name' => 'Birthday',
                'value' => 'birthdays',
                'minDuration' => '1 day',
            ],
        ],
    ],
];
