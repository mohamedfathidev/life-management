<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Color Schemes
    |--------------------------------------------------------------------------
    |
    | Define color palettes for different schemes. Each scheme has separate
    | colors for light and dark modes. Colors are defined in RGB format
    | (without # or rgb()) for use with Tailwind's rgb() and opacity utilities.
    |
    */

    'default' => [
        'light' => [
            'primary' => '63 125 122',         // #3F7D7A
            'primary-dark' => '95 166 162',    // Used in dark mode
            'secondary' => '216 201 163',      // #D8C9A3
            'secondary-dark' => '185 169 124',
            'success' => '122 158 126',        // #7A9E7E
            'success-dark' => '143 182 147',
            'warning' => '217 162 90',         // #D9A25A
            'warning-dark' => '224 179 120',
            'danger' => '199 123 123',         // #C77B7B
            'danger-dark' => '208 143 143',
        ],
    ],

    'ocean' => [
        'light' => [
            'primary' => '46 92 138',          // Deep blue
            'primary-dark' => '93 156 236',
            'secondary' => '127 179 213',      // Sky blue
            'secondary-dark' => '155 196 224',
            'success' => '82 183 136',         // Teal
            'success-dark' => '118 200 160',
            'warning' => '255 193 7',          // Amber
            'warning-dark' => '255 213 79',
            'danger' => '244 67 54',           // Red
            'danger-dark' => '239 108 99',
        ],
    ],

    'forest' => [
        'light' => [
            'primary' => '45 106 79',          // Forest green
            'primary-dark' => '82 183 136',
            'secondary' => '149 213 178',      // Mint
            'secondary-dark' => '179 229 203',
            'success' => '40 167 69',          // Vibrant green
            'success-dark' => '76 175 80',
            'warning' => '255 152 0',          // Orange
            'warning-dark' => '255 183 77',
            'danger' => '211 47 47',           // Dark red
            'danger-dark' => '239 83 80',
        ],
    ],

    'sunset' => [
        'light' => [
            'primary' => '224 122 95',         // Coral
            'primary-dark' => '255 138 101',
            'secondary' => '244 162 97',       // Sandy
            'secondary-dark' => '255 183 131',
            'success' => '126 188 111',        // Soft green
            'success-dark' => '156 204 101',
            'warning' => '255 179 71',         // Golden
            'warning-dark' => '255 202 40',
            'danger' => '239 71 111',          // Pink
            'danger-dark' => '240 98 146',
        ],
    ],

    'lavender' => [
        'light' => [
            'primary' => '155 89 182',         // Purple
            'primary-dark' => '187 143 206',
            'secondary' => '215 189 226',      // Light lavender
            'secondary-dark' => '225 206 232',
            'success' => '102 187 106',        // Green
            'success-dark' => '129 199 132',
            'warning' => '255 167 38',         // Orange
            'warning-dark' => '255 193 7',
            'danger' => '229 115 115',         // Pink red
            'danger-dark' => '239 154 154',
        ],
    ],

    'monochrome' => [
        'light' => [
            'primary' => '74 74 74',           // Dark gray
            'primary-dark' => '158 158 158',
            'secondary' => '165 165 165',      // Medium gray
            'secondary-dark' => '189 189 189',
            'success' => '97 97 97',           // Darker gray
            'success-dark' => '117 117 117',
            'warning' => '130 130 130',        // Gray
            'warning-dark' => '158 158 158',
            'danger' => '66 66 66',            // Near black
            'danger-dark' => '97 97 97',
        ],
    ],
];
