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
                        'start_time' => '09:00',
                        'end_time' => '10:00',
                    ],
                    'meetings' => [
                        'name' => 'Meetings',
                        'value' => 'meetings',
                        'start_time' => '14:00',
                        'end_time' => '21:00'
                    ],
                    'tasks' => [
                        'name' => 'Tasks',
                        'value' => 'tasks',
                        'start_time' => '08:30',
                        'end_time' => '12:30',
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
                        'start_time' => '09:00',
                        'end_time' => '18:00',
                    ],
                    'birthdays' => [
                        'name' => 'Birthdays',
                        'value' => 'birthdays',
                        'start_time' => '09:30',
                        'end_time' => '12:30',
                    ],
                ],
            ],
        ], // Types
    ], // Calendar
];
