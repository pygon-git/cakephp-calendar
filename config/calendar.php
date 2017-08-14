<?php

return [
    'Calendar' => [
        'Configs' => [
            'color' => '#337ab7',
        ],
        'Types' => [
            [
                'name' => 'Default',
                'value' => 'default',
                'types' => [
                    'default' => [
                        'name' => 'Event',
                        'value' => 'default_event',
                    ],
                ],
            ],
            [
                'name' => 'Shifts',
                'value' => 'shifts',
                'types' => [
                    'morning_shift' => [
                        'name' => 'Morning Shift',
                        'value' => 'morning_shift',
                        'start_time' => '09:00',
                        'end_time' => '17:00',
                        'exclude_fields' => ['title', 'content'],
                    ],
                    'evening_shift' => [
                        'name' => 'Evening Shift',
                        'value' => 'evening_shift',
                        'start_time' => '17:00',
                        'end_time' => '01:00',
                        'exclude_fields' => ['title', 'content'],
                    ],
                    'night_shift' => [
                        'name' => 'Night Shift',
                        'value' => 'night_shift',
                        'start_time' => '01:00',
                        'end_time' => '09:00',
                        'exclude_fields' => ['title', 'content'],
                    ],
                ],
            ],
        ], // Types
    ]
];
