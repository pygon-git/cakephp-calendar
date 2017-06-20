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
                ],
            ],
            [
                'name' => 'HR',
                'value' => 'hr',
                'types' => [
                    'annual_leaves' => [
                        'name' => 'Annual Leaves',
                        'value' => 'annual_leaves',
                        'minDuration' => '1 day',
                    ],
                    'birthdays' => [
                        'name' => 'Birthdays',
                        'value' => 'birthdays',
                        'minDuration' => '1 day',
                    ],
                ],
            ],
        ], // Types
    ], // Calendar
];
