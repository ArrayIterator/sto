<?php
namespace ArrayIterator\Helper\Area;

/**
 * Class TimeZone
 * @package ArrayIterator\Helper\Area
 */
final class TimeZone
{
    /**
     * @var array[]
     */
    protected $timezone = [
        "Australia/Adelaide" => [
            "country_name" => "Australia",
            "country_code" => "AU",
            "latitude" => -34.91667,
            "longitude" => 138.58333,
            "zone_name" => "Australia/Adelaide",
            "abbreviation" => "CAST",
            "offset" => 34200,
            "diff" => [
                "hours" => 9,
                "minutes" => 30,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "South Australia"
        ],
        "Australia/Broken_Hill" => [
            "country_name" => "Australia",
            "country_code" => "AU",
            "latitude" => -31.950000000000003,
            "longitude" => 141.45,
            "zone_name" => "Australia/Broken_Hill",
            "abbreviation" => "ACST",
            "offset" => 34200,
            "diff" => [
                "hours" => 9,
                "minutes" => 30,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "New South Wales (Yancowinna)"
        ],
        "Australia/Darwin" => [
            "country_name" => "Australia",
            "country_code" => "AU",
            "latitude" => -12.466669999999993,
            "longitude" => 130.83333,
            "zone_name" => "Australia/Darwin",
            "abbreviation" => "ACST",
            "offset" => 34200,
            "diff" => [
                "hours" => 9,
                "minutes" => 30,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Northern Territory"
        ],
        "America/Goose_Bay" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 53.33332999999999,
            "longitude" => -60.416669999999996,
            "zone_name" => "America/Goose_Bay",
            "abbreviation" => "NWT",
            "offset" => -9000,
            "diff" => [
                "hours" => -3,
                "minutes" => -30,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Atlantic - Labrador (most areas)"
        ],
        "America/Pangnirtung" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 66.13333,
            "longitude" => -65.73334,
            "zone_name" => "America/Pangnirtung",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Eastern - NU (Pangnirtung)"
        ],
        "America/Halifax" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 44.650000000000006,
            "longitude" => -63.599999999999994,
            "zone_name" => "America/Halifax",
            "abbreviation" => "AWT",
            "offset" => -10800,
            "diff" => [
                "hours" => -3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Atlantic - NS (most areas); PE"
        ],
        "America/Barbados" => [
            "country_name" => "Barbados",
            "country_code" => "BB",
            "latitude" => 13.099999999999994,
            "longitude" => -59.61667,
            "zone_name" => "America/Barbados",
            "abbreviation" => "BMT",
            "offset" => -14309,
            "diff" => [
                "hours" => -4,
                "minutes" => -58,
                "seconds" => -29
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Blanc-Sablon" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 51.41666000000001,
            "longitude" => -57.11667,
            "zone_name" => "America/Blanc-Sablon",
            "abbreviation" => "AWT",
            "offset" => -10800,
            "diff" => [
                "hours" => -3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "AST - QC (Lower North Shore)"
        ],
        "America/Glace_Bay" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 46.199990000000014,
            "longitude" => -59.95,
            "zone_name" => "America/Glace_Bay",
            "abbreviation" => "AWT",
            "offset" => -10800,
            "diff" => [
                "hours" => -3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Atlantic - NS (Cape Breton)"
        ],
        "America/Martinique" => [
            "country_name" => "Martinique",
            "country_code" => "MQ",
            "latitude" => 14.599999999999994,
            "longitude" => -61.08334000000001,
            "zone_name" => "America/Martinique",
            "abbreviation" => "FFMT",
            "offset" => -14660,
            "diff" => [
                "hours" => -5,
                "minutes" => -4,
                "seconds" => -20
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Moncton" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 46.099999999999994,
            "longitude" => -64.78334,
            "zone_name" => "America/Moncton",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Atlantic - New Brunswick"
        ],
        "America/Thule" => [
            "country_name" => "Greenland",
            "country_code" => "GL",
            "latitude" => 76.56666000000001,
            "longitude" => -68.78334,
            "zone_name" => "America/Thule",
            "abbreviation" => "AST",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Thule/Pituffik"
        ],
        "Atlantic/Bermuda" => [
            "country_name" => "Bermuda",
            "country_code" => "BM",
            "latitude" => 32.28333000000001,
            "longitude" => -64.76667,
            "zone_name" => "Atlantic/Bermuda",
            "abbreviation" => "AST",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Australia/Melbourne" => [
            "country_name" => "Australia",
            "country_code" => "AU",
            "latitude" => -37.81667,
            "longitude" => 144.96666,
            "zone_name" => "Australia/Melbourne",
            "abbreviation" => "AEST",
            "offset" => 36000,
            "diff" => [
                "hours" => 10,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Victoria"
        ],
        "Antarctica/Macquarie" => [
            "country_name" => "Australia",
            "country_code" => "AU",
            "latitude" => -54.5,
            "longitude" => 158.95,
            "zone_name" => "Antarctica/Macquarie",
            "abbreviation" => "AEST",
            "offset" => 36000,
            "diff" => [
                "hours" => 10,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Macquarie Island"
        ],
        "Australia/Brisbane" => [
            "country_name" => "Australia",
            "country_code" => "AU",
            "latitude" => -27.46667,
            "longitude" => 153.03332999999998,
            "zone_name" => "Australia/Brisbane",
            "abbreviation" => "AEST",
            "offset" => 36000,
            "diff" => [
                "hours" => 10,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Queensland (most areas)"
        ],
        "Australia/Currie" => [
            "country_name" => "Australia",
            "country_code" => "AU",
            "latitude" => -39.93334,
            "longitude" => 143.86666000000002,
            "zone_name" => "Australia/Currie",
            "abbreviation" => "AEST",
            "offset" => 36000,
            "diff" => [
                "hours" => 10,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Tasmania (King Island)"
        ],
        "Australia/Hobart" => [
            "country_name" => "Australia",
            "country_code" => "AU",
            "latitude" => -42.88334,
            "longitude" => 147.31666,
            "zone_name" => "Australia/Hobart",
            "abbreviation" => "AEST",
            "offset" => 36000,
            "diff" => [
                "hours" => 10,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Tasmania (most areas)"
        ],
        "Australia/Lindeman" => [
            "country_name" => "Australia",
            "country_code" => "AU",
            "latitude" => -20.266670000000005,
            "longitude" => 149,
            "zone_name" => "Australia/Lindeman",
            "abbreviation" => "AEST",
            "offset" => 36000,
            "diff" => [
                "hours" => 10,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Queensland (Whitsunday Islands)"
        ],
        "Australia/Sydney" => [
            "country_name" => "Australia",
            "country_code" => "AU",
            "latitude" => -33.86667,
            "longitude" => 151.21666,
            "zone_name" => "Australia/Sydney",
            "abbreviation" => "AEST",
            "offset" => 36000,
            "diff" => [
                "hours" => 10,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "New South Wales (most areas)"
        ],
        "Australia/Lord_Howe" => [
            "country_name" => "Australia",
            "country_code" => "AU",
            "latitude" => -31.549999999999997,
            "longitude" => 159.08333,
            "zone_name" => "Australia/Lord_Howe",
            "abbreviation" => "AEST",
            "offset" => 36000,
            "diff" => [
                "hours" => 10,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Lord Howe Island"
        ],
        "America/Anchorage" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 61.218050000000005,
            "longitude" => -149.90028,
            "zone_name" => "America/Anchorage",
            "abbreviation" => "YST",
            "offset" => -32400,
            "diff" => [
                "hours" => -9,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Alaska (most areas)"
        ],
        "America/Adak" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 51.879999999999995,
            "longitude" => -176.65806,
            "zone_name" => "America/Adak",
            "abbreviation" => "NWT",
            "offset" => -36000,
            "diff" => [
                "hours" => -10,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Aleutian Islands"
        ],
        "America/Juneau" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 58.30194,
            "longitude" => -134.41973000000002,
            "zone_name" => "America/Juneau",
            "abbreviation" => "YST",
            "offset" => -32400,
            "diff" => [
                "hours" => -9,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Alaska - Juneau area"
        ],
        "America/Metlakatla" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 55.12693999999999,
            "longitude" => -131.57639,
            "zone_name" => "America/Metlakatla",
            "abbreviation" => "PWT",
            "offset" => -25200,
            "diff" => [
                "hours" => -7,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Alaska - Annette Island"
        ],
        "America/Nome" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 64.50111000000001,
            "longitude" => -165.40639,
            "zone_name" => "America/Nome",
            "abbreviation" => "YST",
            "offset" => -32400,
            "diff" => [
                "hours" => -9,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Alaska (west)"
        ],
        "America/Sitka" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 57.176379999999995,
            "longitude" => -135.30195,
            "zone_name" => "America/Sitka",
            "abbreviation" => "YST",
            "offset" => -32400,
            "diff" => [
                "hours" => -9,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Alaska - Sitka area"
        ],
        "America/Yakutat" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 59.546940000000006,
            "longitude" => -139.72723,
            "zone_name" => "America/Yakutat",
            "abbreviation" => "YWT",
            "offset" => -28800,
            "diff" => [
                "hours" => -8,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Alaska - Yakutat"
        ],
        "America/Asuncion" => [
            "country_name" => "Paraguay",
            "country_code" => "PY",
            "latitude" => -25.266670000000005,
            "longitude" => -57.666669999999996,
            "zone_name" => "America/Asuncion",
            "abbreviation" => "AMT",
            "offset" => -13840,
            "diff" => [
                "hours" => -4,
                "minutes" => -50,
                "seconds" => -40
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Amsterdam" => [
            "country_name" => "Netherlands",
            "country_code" => "NL",
            "latitude" => 52.366659999999996,
            "longitude" => 4.900000000000006,
            "zone_name" => "Europe/Amsterdam",
            "abbreviation" => "NST",
            "offset" => 4772,
            "diff" => [
                "hours" => 1,
                "minutes" => 19,
                "seconds" => 32
            ],
            "dst" => true,
            "comments" => ""
        ],
        "Europe/Athens" => [
            "country_name" => "Greece",
            "country_code" => "GR",
            "latitude" => 37.966660000000005,
            "longitude" => 23.71665999999999,
            "zone_name" => "Europe/Athens",
            "abbreviation" => "EET",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Puerto_Rico" => [
            "country_name" => "Puerto Rico",
            "country_code" => "PR",
            "latitude" => 18.468329999999995,
            "longitude" => -66.10612,
            "zone_name" => "America/Puerto_Rico",
            "abbreviation" => "AWT",
            "offset" => -10800,
            "diff" => [
                "hours" => -3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => ""
        ],
        "America/Anguilla" => [
            "country_name" => "Anguilla",
            "country_code" => "AI",
            "latitude" => 18.200000000000003,
            "longitude" => -63.06667,
            "zone_name" => "America/Anguilla",
            "abbreviation" => "AST",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Antigua" => [
            "country_name" => "Antigua and Barbuda",
            "country_code" => "AG",
            "latitude" => 17.049999999999997,
            "longitude" => -61.8,
            "zone_name" => "America/Antigua",
            "abbreviation" => "AST",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Aruba" => [
            "country_name" => "Aruba",
            "country_code" => "AW",
            "latitude" => 12.5,
            "longitude" => -69.96667,
            "zone_name" => "America/Aruba",
            "abbreviation" => "AST",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Curacao" => [
            "country_name" => "Curacao",
            "country_code" => "CW",
            "latitude" => 12.183329999999998,
            "longitude" => -69,
            "zone_name" => "America/Curacao",
            "abbreviation" => "AST",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Dominica" => [
            "country_name" => "Dominica",
            "country_code" => "DM",
            "latitude" => 15.299999999999997,
            "longitude" => -61.400000000000006,
            "zone_name" => "America/Dominica",
            "abbreviation" => "AST",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Grand_Turk" => [
            "country_name" => "Turks and Caicos Islands",
            "country_code" => "TC",
            "latitude" => 21.466660000000005,
            "longitude" => -71.13334,
            "zone_name" => "America/Grand_Turk",
            "abbreviation" => "KMT",
            "offset" => -18430,
            "diff" => [
                "hours" => -6,
                "minutes" => -7,
                "seconds" => -10
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Grenada" => [
            "country_name" => "Grenada",
            "country_code" => "GD",
            "latitude" => 12.049999999999997,
            "longitude" => -61.75,
            "zone_name" => "America/Grenada",
            "abbreviation" => "AST",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Guadeloupe" => [
            "country_name" => "Guadeloupe",
            "country_code" => "GP",
            "latitude" => 16.233329999999995,
            "longitude" => -61.533339999999995,
            "zone_name" => "America/Guadeloupe",
            "abbreviation" => "AST",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Kralendijk" => [
            "country_name" => "Bonaire, Saint Eustatius and Saba ",
            "country_code" => "BQ",
            "latitude" => 12.15083,
            "longitude" => -68.27667,
            "zone_name" => "America/Kralendijk",
            "abbreviation" => "AST",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Lower_Princes" => [
            "country_name" => "Sint Maarten",
            "country_code" => "SX",
            "latitude" => 18.051379999999995,
            "longitude" => -63.04723,
            "zone_name" => "America/Lower_Princes",
            "abbreviation" => "AST",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Marigot" => [
            "country_name" => "Saint Martin",
            "country_code" => "MF",
            "latitude" => 18.06666,
            "longitude" => -63.08334000000001,
            "zone_name" => "America/Marigot",
            "abbreviation" => "AST",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Miquelon" => [
            "country_name" => "Saint Pierre and Miquelon",
            "country_code" => "PM",
            "latitude" => 47.05000000000001,
            "longitude" => -56.33334000000001,
            "zone_name" => "America/Miquelon",
            "abbreviation" => "AST",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Montserrat" => [
            "country_name" => "Montserrat",
            "country_code" => "MS",
            "latitude" => 16.716660000000005,
            "longitude" => -62.21666999999999,
            "zone_name" => "America/Montserrat",
            "abbreviation" => "AST",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Port_of_Spain" => [
            "country_name" => "Trinidad and Tobago",
            "country_code" => "TT",
            "latitude" => 10.650000000000006,
            "longitude" => -61.516670000000005,
            "zone_name" => "America/Port_of_Spain",
            "abbreviation" => "AST",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Santo_Domingo" => [
            "country_name" => "Dominican Republic",
            "country_code" => "DO",
            "latitude" => 18.466660000000005,
            "longitude" => -69.9,
            "zone_name" => "America/Santo_Domingo",
            "abbreviation" => "SDMT",
            "offset" => -16800,
            "diff" => [
                "hours" => -5,
                "minutes" => -40,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/St_Barthelemy" => [
            "country_name" => "Saint Barthelemy",
            "country_code" => "BL",
            "latitude" => 17.88333,
            "longitude" => -62.849999999999994,
            "zone_name" => "America/St_Barthelemy",
            "abbreviation" => "AST",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/St_Kitts" => [
            "country_name" => "Saint Kitts and Nevis",
            "country_code" => "KN",
            "latitude" => 17.299999999999997,
            "longitude" => -62.71666999999999,
            "zone_name" => "America/St_Kitts",
            "abbreviation" => "AST",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/St_Lucia" => [
            "country_name" => "Saint Lucia",
            "country_code" => "LC",
            "latitude" => 14.016660000000002,
            "longitude" => -61,
            "zone_name" => "America/St_Lucia",
            "abbreviation" => "AST",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/St_Thomas" => [
            "country_name" => "U.S. Virgin Islands",
            "country_code" => "VI",
            "latitude" => 18.349999999999994,
            "longitude" => -64.93334,
            "zone_name" => "America/St_Thomas",
            "abbreviation" => "AST",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/St_Vincent" => [
            "country_name" => "Saint Vincent and the Grenadines",
            "country_code" => "VC",
            "latitude" => 13.150000000000006,
            "longitude" => -61.23334,
            "zone_name" => "America/St_Vincent",
            "abbreviation" => "AST",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Tortola" => [
            "country_name" => "British Virgin Islands",
            "country_code" => "VG",
            "latitude" => 18.450000000000003,
            "longitude" => -64.61667,
            "zone_name" => "America/Tortola",
            "abbreviation" => "AST",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Australia/Perth" => [
            "country_name" => "Australia",
            "country_code" => "AU",
            "latitude" => -31.950000000000003,
            "longitude" => 115.85000000000002,
            "zone_name" => "Australia/Perth",
            "abbreviation" => "AWST",
            "offset" => 28800,
            "diff" => [
                "hours" => 8,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Western Australia (most areas)"
        ],
        "Europe/London" => [
            "country_name" => "United Kingdom",
            "country_code" => "GB",
            "latitude" => 51.50833,
            "longitude" => -0.1252800000000036,
            "zone_name" => "Europe/London",
            "abbreviation" => "GMT",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Gibraltar" => [
            "country_name" => "Gibraltar",
            "country_code" => "GI",
            "latitude" => 36.13333,
            "longitude" => -5.349999999999994,
            "zone_name" => "Europe/Gibraltar",
            "abbreviation" => "GMT",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Guernsey" => [
            "country_name" => "Guernsey",
            "country_code" => "GG",
            "latitude" => 49.45472000000001,
            "longitude" => -2.536120000000011,
            "zone_name" => "Europe/Guernsey",
            "abbreviation" => "GMT",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Isle_of_Man" => [
            "country_name" => "Isle of Man",
            "country_code" => "IM",
            "latitude" => 54.150000000000006,
            "longitude" => -4.4666699999999935,
            "zone_name" => "Europe/Isle_of_Man",
            "abbreviation" => "GMT",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Jersey" => [
            "country_name" => "Jersey",
            "country_code" => "JE",
            "latitude" => 49.18360999999999,
            "longitude" => -2.1066700000000083,
            "zone_name" => "Europe/Jersey",
            "abbreviation" => "GMT",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Bogota" => [
            "country_name" => "Colombia",
            "country_code" => "CO",
            "latitude" => 4.599999999999994,
            "longitude" => -74.08334,
            "zone_name" => "America/Bogota",
            "abbreviation" => "BMT",
            "offset" => -17776,
            "diff" => [
                "hours" => -5,
                "minutes" => -56,
                "seconds" => -16
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Baghdad" => [
            "country_name" => "Iraq",
            "country_code" => "IQ",
            "latitude" => 33.349999999999994,
            "longitude" => 44.41666000000001,
            "zone_name" => "Asia/Baghdad",
            "abbreviation" => "BMT",
            "offset" => 10656,
            "diff" => [
                "hours" => 2,
                "minutes" => 57,
                "seconds" => 36
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Bangkok" => [
            "country_name" => "Thailand",
            "country_code" => "TH",
            "latitude" => 13.75,
            "longitude" => 100.51666,
            "zone_name" => "Asia/Bangkok",
            "abbreviation" => "BMT",
            "offset" => 24124,
            "diff" => [
                "hours" => 6,
                "minutes" => 42,
                "seconds" => 4
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Phnom_Penh" => [
            "country_name" => "Cambodia",
            "country_code" => "KH",
            "latitude" => 11.549999999999997,
            "longitude" => 104.91665999999998,
            "zone_name" => "Asia/Phnom_Penh",
            "abbreviation" => "BMT",
            "offset" => 24124,
            "diff" => [
                "hours" => 6,
                "minutes" => 42,
                "seconds" => 4
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Vientiane" => [
            "country_name" => "Laos",
            "country_code" => "LA",
            "latitude" => 17.966660000000005,
            "longitude" => 102.60000000000002,
            "zone_name" => "Asia/Vientiane",
            "abbreviation" => "BMT",
            "offset" => 24124,
            "diff" => [
                "hours" => 6,
                "minutes" => 42,
                "seconds" => 4
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Jakarta" => [
            "country_name" => "Indonesia",
            "country_code" => "ID",
            "latitude" => -6.166669999999996,
            "longitude" => 106.80000000000001,
            "zone_name" => "Asia/Jakarta",
            "abbreviation" => "WIB",
            "offset" => 25200,
            "diff" => [
                "hours" => 7,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Java, Sumatra"
        ],
        "Europe/Bucharest" => [
            "country_name" => "Romania",
            "country_code" => "RO",
            "latitude" => 44.43333000000001,
            "longitude" => 26.099999999999994,
            "zone_name" => "Europe/Bucharest",
            "abbreviation" => "EET",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Chisinau" => [
            "country_name" => "Moldova",
            "country_code" => "MD",
            "latitude" => 47,
            "longitude" => 28.83332999999999,
            "zone_name" => "Europe/Chisinau",
            "abbreviation" => "MSK",
            "offset" => 10800,
            "diff" => [
                "hours" => 3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/La_Paz" => [
            "country_name" => "Bolivia",
            "country_code" => "BO",
            "latitude" => -16.5,
            "longitude" => -68.15,
            "zone_name" => "America/La_Paz",
            "abbreviation" => "CMT",
            "offset" => -16356,
            "diff" => [
                "hours" => -5,
                "minutes" => -32,
                "seconds" => -36
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Dublin" => [
            "country_name" => "Ireland",
            "country_code" => "IE",
            "latitude" => 53.33332999999999,
            "longitude" => -6.25,
            "zone_name" => "Europe/Dublin",
            "abbreviation" => "IST",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => ""
        ],
        "Africa/Juba" => [
            "country_name" => "South Sudan",
            "country_code" => "SS",
            "latitude" => 4.849999999999994,
            "longitude" => 31.616659999999996,
            "zone_name" => "Africa/Juba",
            "abbreviation" => "EAT",
            "offset" => 10800,
            "diff" => [
                "hours" => 3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Khartoum" => [
            "country_name" => "Sudan",
            "country_code" => "SD",
            "latitude" => 15.599999999999994,
            "longitude" => 32.53333000000001,
            "zone_name" => "Africa/Khartoum",
            "abbreviation" => "EAT",
            "offset" => 10800,
            "diff" => [
                "hours" => 3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Blantyre" => [
            "country_name" => "Malawi",
            "country_code" => "MW",
            "latitude" => -15.783339999999995,
            "longitude" => 35,
            "zone_name" => "Africa/Blantyre",
            "abbreviation" => "CAT",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Bujumbura" => [
            "country_name" => "Burundi",
            "country_code" => "BI",
            "latitude" => -3.383340000000004,
            "longitude" => 29.366659999999996,
            "zone_name" => "Africa/Bujumbura",
            "abbreviation" => "CAT",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Gaborone" => [
            "country_name" => "Botswana",
            "country_code" => "BW",
            "latitude" => -24.650009999999995,
            "longitude" => 25.916660000000007,
            "zone_name" => "Africa/Gaborone",
            "abbreviation" => "CAT",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Harare" => [
            "country_name" => "Zimbabwe",
            "country_code" => "ZW",
            "latitude" => -17.833340000000007,
            "longitude" => 31.05000000000001,
            "zone_name" => "Africa/Harare",
            "abbreviation" => "CAT",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Kigali" => [
            "country_name" => "Rwanda",
            "country_code" => "RW",
            "latitude" => -1.9500000000000028,
            "longitude" => 30.066660000000013,
            "zone_name" => "Africa/Kigali",
            "abbreviation" => "CAT",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Lubumbashi" => [
            "country_name" => "Democratic Republic of the Congo",
            "country_code" => "CD",
            "latitude" => -11.666669999999996,
            "longitude" => 27.46665999999999,
            "zone_name" => "Africa/Lubumbashi",
            "abbreviation" => "CAT",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Dem. Rep. of Congo (east)"
        ],
        "Africa/Lusaka" => [
            "country_name" => "Zambia",
            "country_code" => "ZM",
            "latitude" => -15.416669999999996,
            "longitude" => 28.283330000000007,
            "zone_name" => "Africa/Lusaka",
            "abbreviation" => "CAT",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Maputo" => [
            "country_name" => "Mozambique",
            "country_code" => "MZ",
            "latitude" => -25.966669999999993,
            "longitude" => 32.58332999999999,
            "zone_name" => "Africa/Maputo",
            "abbreviation" => "CAT",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Windhoek" => [
            "country_name" => "Namibia",
            "country_code" => "NA",
            "latitude" => -22.566670000000002,
            "longitude" => 17.099999999999994,
            "zone_name" => "Africa/Windhoek",
            "abbreviation" => "WAT",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => ""
        ],
        "America/Rankin_Inlet" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 62.81666000000001,
            "longitude" => -92.08306,
            "zone_name" => "America/Rankin_Inlet",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Central - NU (central)"
        ],
        "America/Resolute" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 74.69555,
            "longitude" => -94.82917,
            "zone_name" => "America/Resolute",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Central - NU (Resolute)"
        ],
        "America/Chicago" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 41.849999999999994,
            "longitude" => -87.65,
            "zone_name" => "America/Chicago",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Central (most areas)"
        ],
        "Asia/Shanghai" => [
            "country_name" => "China",
            "country_code" => "CN",
            "latitude" => 31.233329999999995,
            "longitude" => 121.46665999999999,
            "zone_name" => "Asia/Shanghai",
            "abbreviation" => "CST",
            "offset" => 28800,
            "diff" => [
                "hours" => 8,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Beijing Time"
        ],
        "America/Havana" => [
            "country_name" => "Cuba",
            "country_code" => "CU",
            "latitude" => 23.13333,
            "longitude" => -82.36667,
            "zone_name" => "America/Havana",
            "abbreviation" => "HMT",
            "offset" => -19776,
            "diff" => [
                "hours" => -6,
                "minutes" => -29,
                "seconds" => -36
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Atikokan" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 48.758610000000004,
            "longitude" => -91.62167,
            "zone_name" => "America/Atikokan",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "EST - ON (Atikokan); NU (Coral H)"
        ],
        "America/Bahia_Banderas" => [
            "country_name" => "Mexico",
            "country_code" => "MX",
            "latitude" => 20.799999999999997,
            "longitude" => -105.25,
            "zone_name" => "America/Bahia_Banderas",
            "abbreviation" => "PST",
            "offset" => -28800,
            "diff" => [
                "hours" => -8,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Central Time - Bahia de Banderas"
        ],
        "America/Belize" => [
            "country_name" => "Belize",
            "country_code" => "BZ",
            "latitude" => 17.5,
            "longitude" => -88.2,
            "zone_name" => "America/Belize",
            "abbreviation" => "CST",
            "offset" => -21600,
            "diff" => [
                "hours" => -6,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Cambridge_Bay" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 69.11388,
            "longitude" => -105.05278,
            "zone_name" => "America/Cambridge_Bay",
            "abbreviation" => "MWT",
            "offset" => -21600,
            "diff" => [
                "hours" => -6,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Mountain - NU (west)"
        ],
        "America/Cancun" => [
            "country_name" => "Mexico",
            "country_code" => "MX",
            "latitude" => 21.083330000000004,
            "longitude" => -86.76667,
            "zone_name" => "America/Cancun",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Eastern Standard Time - Quintana Roo"
        ],
        "America/Chihuahua" => [
            "country_name" => "Mexico",
            "country_code" => "MX",
            "latitude" => 28.63333,
            "longitude" => -106.08334,
            "zone_name" => "America/Chihuahua",
            "abbreviation" => "MST",
            "offset" => -25200,
            "diff" => [
                "hours" => -7,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Mountain Time - Chihuahua (most areas)"
        ],
        "America/Costa_Rica" => [
            "country_name" => "Costa Rica",
            "country_code" => "CR",
            "latitude" => 9.933329999999998,
            "longitude" => -84.08334,
            "zone_name" => "America/Costa_Rica",
            "abbreviation" => "SJMT",
            "offset" => -20173,
            "diff" => [
                "hours" => -6,
                "minutes" => -36,
                "seconds" => -13
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/El_Salvador" => [
            "country_name" => "El Salvador",
            "country_code" => "SV",
            "latitude" => 13.700000000000003,
            "longitude" => -89.2,
            "zone_name" => "America/El_Salvador",
            "abbreviation" => "CST",
            "offset" => -21600,
            "diff" => [
                "hours" => -6,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Guatemala" => [
            "country_name" => "Guatemala",
            "country_code" => "GT",
            "latitude" => 14.63333,
            "longitude" => -90.51667,
            "zone_name" => "America/Guatemala",
            "abbreviation" => "CST",
            "offset" => -21600,
            "diff" => [
                "hours" => -6,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Indiana/Indianapolis" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 39.76832999999999,
            "longitude" => -86.15806,
            "zone_name" => "America/Indiana/Indianapolis",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Eastern - IN (most areas)"
        ],
        "America/Indiana/Knox" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 41.295829999999995,
            "longitude" => -86.625,
            "zone_name" => "America/Indiana/Knox",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Central - IN (Starke)"
        ],
        "America/Indiana/Marengo" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 38.375550000000004,
            "longitude" => -86.34473,
            "zone_name" => "America/Indiana/Marengo",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Eastern - IN (Crawford)"
        ],
        "America/Indiana/Petersburg" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 38.49194,
            "longitude" => -87.27862,
            "zone_name" => "America/Indiana/Petersburg",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Eastern - IN (Pike)"
        ],
        "America/Indiana/Tell_City" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 37.953050000000005,
            "longitude" => -86.76139,
            "zone_name" => "America/Indiana/Tell_City",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Central - IN (Perry)"
        ],
        "America/Indiana/Vevay" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 38.74777,
            "longitude" => -85.06723,
            "zone_name" => "America/Indiana/Vevay",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Eastern - IN (Switzerland)"
        ],
        "America/Indiana/Vincennes" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 38.677220000000005,
            "longitude" => -87.52862,
            "zone_name" => "America/Indiana/Vincennes",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Eastern - IN (Da, Du, K, Mn)"
        ],
        "America/Indiana/Winamac" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 41.051379999999995,
            "longitude" => -86.60306,
            "zone_name" => "America/Indiana/Winamac",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Eastern - IN (Pulaski)"
        ],
        "America/Iqaluit" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 63.733329999999995,
            "longitude" => -68.46667,
            "zone_name" => "America/Iqaluit",
            "abbreviation" => "EWT",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Eastern - NU (most east areas)"
        ],
        "America/Kentucky/Louisville" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 38.25416000000001,
            "longitude" => -85.75945,
            "zone_name" => "America/Kentucky/Louisville",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Eastern - KY (Louisville area)"
        ],
        "America/Kentucky/Monticello" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 36.829719999999995,
            "longitude" => -84.84917,
            "zone_name" => "America/Kentucky/Monticello",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Eastern - KY (Wayne)"
        ],
        "America/Managua" => [
            "country_name" => "Nicaragua",
            "country_code" => "NI",
            "latitude" => 12.150000000000006,
            "longitude" => -86.28334,
            "zone_name" => "America/Managua",
            "abbreviation" => "MMT",
            "offset" => -20712,
            "diff" => [
                "hours" => -6,
                "minutes" => -45,
                "seconds" => -12
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Matamoros" => [
            "country_name" => "Mexico",
            "country_code" => "MX",
            "latitude" => 25.833330000000004,
            "longitude" => -97.5,
            "zone_name" => "America/Matamoros",
            "abbreviation" => "CST",
            "offset" => -21600,
            "diff" => [
                "hours" => -6,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Central Time US - Coahuila, Nuevo Leon, Tamaulipas (US border)"
        ],
        "America/Menominee" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 45.10776999999999,
            "longitude" => -87.61417,
            "zone_name" => "America/Menominee",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Central - MI (Wisconsin border)"
        ],
        "America/Merida" => [
            "country_name" => "Mexico",
            "country_code" => "MX",
            "latitude" => 20.966660000000005,
            "longitude" => -89.61667,
            "zone_name" => "America/Merida",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Central Time - Campeche, Yucatan"
        ],
        "America/Mexico_City" => [
            "country_name" => "Mexico",
            "country_code" => "MX",
            "latitude" => 19.400000000000006,
            "longitude" => -99.15001,
            "zone_name" => "America/Mexico_City",
            "abbreviation" => "MST",
            "offset" => -25200,
            "diff" => [
                "hours" => -7,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Central Time"
        ],
        "America/Monterrey" => [
            "country_name" => "Mexico",
            "country_code" => "MX",
            "latitude" => 25.666659999999993,
            "longitude" => -100.31667,
            "zone_name" => "America/Monterrey",
            "abbreviation" => "CST",
            "offset" => -21600,
            "diff" => [
                "hours" => -6,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Central Time - Durango; Coahuila, Nuevo Leon, Tamaulipas (most areas)"
        ],
        "America/North_Dakota/Beulah" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 47.264160000000004,
            "longitude" => -101.77778,
            "zone_name" => "America/North_Dakota/Beulah",
            "abbreviation" => "MWT",
            "offset" => -21600,
            "diff" => [
                "hours" => -6,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Central - ND (Mercer)"
        ],
        "America/North_Dakota/Center" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 47.11637999999999,
            "longitude" => -101.29917,
            "zone_name" => "America/North_Dakota/Center",
            "abbreviation" => "MWT",
            "offset" => -21600,
            "diff" => [
                "hours" => -6,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Central - ND (Oliver)"
        ],
        "America/North_Dakota/New_Salem" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 46.845,
            "longitude" => -101.41084,
            "zone_name" => "America/North_Dakota/New_Salem",
            "abbreviation" => "MWT",
            "offset" => -21600,
            "diff" => [
                "hours" => -6,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Central - ND (Morton rural)"
        ],
        "America/Ojinaga" => [
            "country_name" => "Mexico",
            "country_code" => "MX",
            "latitude" => 29.56666,
            "longitude" => -104.41667,
            "zone_name" => "America/Ojinaga",
            "abbreviation" => "MST",
            "offset" => -25200,
            "diff" => [
                "hours" => -7,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Mountain Time US - Chihuahua (US border)"
        ],
        "America/Rainy_River" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 48.71665999999999,
            "longitude" => -94.56667,
            "zone_name" => "America/Rainy_River",
            "abbreviation" => "CWT",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Central - ON (Rainy R, Ft Frances)"
        ],
        "America/Tegucigalpa" => [
            "country_name" => "Honduras",
            "country_code" => "HN",
            "latitude" => 14.099999999999994,
            "longitude" => -87.21667,
            "zone_name" => "America/Tegucigalpa",
            "abbreviation" => "CST",
            "offset" => -21600,
            "diff" => [
                "hours" => -6,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Winnipeg" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 49.88333,
            "longitude" => -97.15001,
            "zone_name" => "America/Winnipeg",
            "abbreviation" => "CWT",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Central - ON (west); Manitoba"
        ],
        "Asia/Macau" => [
            "country_name" => "Macao",
            "country_code" => "MO",
            "latitude" => 22.19722,
            "longitude" => 113.54165999999998,
            "zone_name" => "Asia/Macau",
            "abbreviation" => "CST",
            "offset" => 28800,
            "diff" => [
                "hours" => 8,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Taipei" => [
            "country_name" => "Taiwan",
            "country_code" => "TW",
            "latitude" => 25.049999999999997,
            "longitude" => 121.5,
            "zone_name" => "Asia/Taipei",
            "abbreviation" => "JST",
            "offset" => 32400,
            "diff" => [
                "hours" => 9,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Berlin" => [
            "country_name" => "Germany",
            "country_code" => "DE",
            "latitude" => 52.5,
            "longitude" => 13.366659999999996,
            "zone_name" => "Europe/Berlin",
            "abbreviation" => "CET",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Germany (most areas)"
        ],
        "Europe/Kaliningrad" => [
            "country_name" => "Russia",
            "country_code" => "RU",
            "latitude" => 54.71665999999999,
            "longitude" => 20.5,
            "zone_name" => "Europe/Kaliningrad",
            "abbreviation" => "MSK",
            "offset" => 10800,
            "diff" => [
                "hours" => 3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "MSK-01 - Kaliningrad"
        ],
        "Africa/Algiers" => [
            "country_name" => "Algeria",
            "country_code" => "DZ",
            "latitude" => 36.78333000000001,
            "longitude" => 3.0500000000000114,
            "zone_name" => "Africa/Algiers",
            "abbreviation" => "WET",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Ceuta" => [
            "country_name" => "Spain",
            "country_code" => "ES",
            "latitude" => 35.88333,
            "longitude" => -5.316669999999988,
            "zone_name" => "Africa/Ceuta",
            "abbreviation" => "WET",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Ceuta, Melilla"
        ],
        "Africa/Tripoli" => [
            "country_name" => "Libya",
            "country_code" => "LY",
            "latitude" => 32.900000000000006,
            "longitude" => 13.183330000000012,
            "zone_name" => "Africa/Tripoli",
            "abbreviation" => "EET",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Tunis" => [
            "country_name" => "Tunisia",
            "country_code" => "TN",
            "latitude" => 36.8,
            "longitude" => 10.183330000000012,
            "zone_name" => "Africa/Tunis",
            "abbreviation" => "PMT",
            "offset" => 561,
            "diff" => [
                "hours" => 0,
                "minutes" => 9,
                "seconds" => 21
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Arctic/Longyearbyen" => [
            "country_name" => "Svalbard and Jan Mayen",
            "country_code" => "SJ",
            "latitude" => 78,
            "longitude" => 16,
            "zone_name" => "Arctic/Longyearbyen",
            "abbreviation" => "CET",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Andorra" => [
            "country_name" => "Andorra",
            "country_code" => "AD",
            "latitude" => 42.5,
            "longitude" => 1.5166600000000017,
            "zone_name" => "Europe/Andorra",
            "abbreviation" => "WET",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Belgrade" => [
            "country_name" => "Serbia",
            "country_code" => "RS",
            "latitude" => 44.83332999999999,
            "longitude" => 20.5,
            "zone_name" => "Europe/Belgrade",
            "abbreviation" => "CET",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Bratislava" => [
            "country_name" => "Slovakia",
            "country_code" => "SK",
            "latitude" => 48.150000000000006,
            "longitude" => 17.116659999999996,
            "zone_name" => "Europe/Bratislava",
            "abbreviation" => "GMT",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => ""
        ],
        "Europe/Brussels" => [
            "country_name" => "Belgium",
            "country_code" => "BE",
            "latitude" => 50.83332999999999,
            "longitude" => 4.3333299999999895,
            "zone_name" => "Europe/Brussels",
            "abbreviation" => "WET",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Budapest" => [
            "country_name" => "Hungary",
            "country_code" => "HU",
            "latitude" => 47.5,
            "longitude" => 19.08332999999999,
            "zone_name" => "Europe/Budapest",
            "abbreviation" => "CET",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Busingen" => [
            "country_name" => "Germany",
            "country_code" => "DE",
            "latitude" => 47.699990000000014,
            "longitude" => 8.683330000000012,
            "zone_name" => "Europe/Busingen",
            "abbreviation" => "CET",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Busingen"
        ],
        "Europe/Copenhagen" => [
            "country_name" => "Denmark",
            "country_code" => "DK",
            "latitude" => 55.66666000000001,
            "longitude" => 12.58332999999999,
            "zone_name" => "Europe/Copenhagen",
            "abbreviation" => "CET",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Kiev" => [
            "country_name" => "Ukraine",
            "country_code" => "UA",
            "latitude" => 50.43333000000001,
            "longitude" => 30.51666,
            "zone_name" => "Europe/Kiev",
            "abbreviation" => "MSK",
            "offset" => 10800,
            "diff" => [
                "hours" => 3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Ukraine (most areas)"
        ],
        "Europe/Lisbon" => [
            "country_name" => "Portugal",
            "country_code" => "PT",
            "latitude" => 38.71665999999999,
            "longitude" => -9.133340000000004,
            "zone_name" => "Europe/Lisbon",
            "abbreviation" => "WET",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Portugal (mainland)"
        ],
        "Europe/Ljubljana" => [
            "country_name" => "Slovenia",
            "country_code" => "SI",
            "latitude" => 46.05000000000001,
            "longitude" => 14.516660000000002,
            "zone_name" => "Europe/Ljubljana",
            "abbreviation" => "CET",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Luxembourg" => [
            "country_name" => "Luxembourg",
            "country_code" => "LU",
            "latitude" => 49.599999999999994,
            "longitude" => 6.150000000000006,
            "zone_name" => "Europe/Luxembourg",
            "abbreviation" => "WET",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Madrid" => [
            "country_name" => "Spain",
            "country_code" => "ES",
            "latitude" => 40.400000000000006,
            "longitude" => -3.683339999999987,
            "zone_name" => "Europe/Madrid",
            "abbreviation" => "WET",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Spain (mainland)"
        ],
        "Europe/Malta" => [
            "country_name" => "Malta",
            "country_code" => "MT",
            "latitude" => 35.900000000000006,
            "longitude" => 14.516660000000002,
            "zone_name" => "Europe/Malta",
            "abbreviation" => "CET",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Minsk" => [
            "country_name" => "Belarus",
            "country_code" => "BY",
            "latitude" => 53.900000000000006,
            "longitude" => 27.566660000000013,
            "zone_name" => "Europe/Minsk",
            "abbreviation" => "MSK",
            "offset" => 10800,
            "diff" => [
                "hours" => 3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Monaco" => [
            "country_name" => "Monaco",
            "country_code" => "MC",
            "latitude" => 43.699990000000014,
            "longitude" => 7.383330000000001,
            "zone_name" => "Europe/Monaco",
            "abbreviation" => "WET",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Oslo" => [
            "country_name" => "Norway",
            "country_code" => "NO",
            "latitude" => 59.91666000000001,
            "longitude" => 10.75,
            "zone_name" => "Europe/Oslo",
            "abbreviation" => "CET",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Paris" => [
            "country_name" => "France",
            "country_code" => "FR",
            "latitude" => 48.866659999999996,
            "longitude" => 2.3333299999999895,
            "zone_name" => "Europe/Paris",
            "abbreviation" => "WET",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Podgorica" => [
            "country_name" => "Montenegro",
            "country_code" => "ME",
            "latitude" => 42.43333000000001,
            "longitude" => 19.26666,
            "zone_name" => "Europe/Podgorica",
            "abbreviation" => "CET",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Prague" => [
            "country_name" => "Czech Republic",
            "country_code" => "CZ",
            "latitude" => 50.08332999999999,
            "longitude" => 14.433330000000012,
            "zone_name" => "Europe/Prague",
            "abbreviation" => "GMT",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => ""
        ],
        "Europe/Riga" => [
            "country_name" => "Latvia",
            "country_code" => "LV",
            "latitude" => 56.949990000000014,
            "longitude" => 24.099999999999994,
            "zone_name" => "Europe/Riga",
            "abbreviation" => "RMT",
            "offset" => 5794,
            "diff" => [
                "hours" => 1,
                "minutes" => 36,
                "seconds" => 34
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Rome" => [
            "country_name" => "Italy",
            "country_code" => "IT",
            "latitude" => 41.900000000000006,
            "longitude" => 12.483329999999995,
            "zone_name" => "Europe/Rome",
            "abbreviation" => "CET",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/San_Marino" => [
            "country_name" => "San Marino",
            "country_code" => "SM",
            "latitude" => 43.91666000000001,
            "longitude" => 12.46665999999999,
            "zone_name" => "Europe/San_Marino",
            "abbreviation" => "CET",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Sarajevo" => [
            "country_name" => "Bosnia and Herzegovina",
            "country_code" => "BA",
            "latitude" => 43.866659999999996,
            "longitude" => 18.416660000000007,
            "zone_name" => "Europe/Sarajevo",
            "abbreviation" => "CET",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Simferopol" => [
            "country_name" => "Ukraine",
            "country_code" => "UA",
            "latitude" => 44.949990000000014,
            "longitude" => 34.099999999999994,
            "zone_name" => "Europe/Simferopol",
            "abbreviation" => "SMT",
            "offset" => 8160,
            "diff" => [
                "hours" => 2,
                "minutes" => 16,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "MSK+00 - Crimea"
        ],
        "Europe/Skopje" => [
            "country_name" => "Macedonia",
            "country_code" => "MK",
            "latitude" => 41.983329999999995,
            "longitude" => 21.433330000000012,
            "zone_name" => "Europe/Skopje",
            "abbreviation" => "CET",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Sofia" => [
            "country_name" => "Bulgaria",
            "country_code" => "BG",
            "latitude" => 42.68333000000001,
            "longitude" => 23.316660000000013,
            "zone_name" => "Europe/Sofia",
            "abbreviation" => "EET",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Stockholm" => [
            "country_name" => "Sweden",
            "country_code" => "SE",
            "latitude" => 59.33332999999999,
            "longitude" => 18.05000000000001,
            "zone_name" => "Europe/Stockholm",
            "abbreviation" => "CET",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Tallinn" => [
            "country_name" => "Estonia",
            "country_code" => "EE",
            "latitude" => 59.41666000000001,
            "longitude" => 24.75,
            "zone_name" => "Europe/Tallinn",
            "abbreviation" => "TMT",
            "offset" => 5940,
            "diff" => [
                "hours" => 1,
                "minutes" => 39,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Tirane" => [
            "country_name" => "Albania",
            "country_code" => "AL",
            "latitude" => 41.33332999999999,
            "longitude" => 19.83332999999999,
            "zone_name" => "Europe/Tirane",
            "abbreviation" => "CET",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Uzhgorod" => [
            "country_name" => "Ukraine",
            "country_code" => "UA",
            "latitude" => 48.616659999999996,
            "longitude" => 22.30000000000001,
            "zone_name" => "Europe/Uzhgorod",
            "abbreviation" => "MSK",
            "offset" => 10800,
            "diff" => [
                "hours" => 3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Ruthenia"
        ],
        "Europe/Vaduz" => [
            "country_name" => "Liechtenstein",
            "country_code" => "LI",
            "latitude" => 47.150000000000006,
            "longitude" => 9.516660000000002,
            "zone_name" => "Europe/Vaduz",
            "abbreviation" => "CET",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Vatican" => [
            "country_name" => "Vatican",
            "country_code" => "VA",
            "latitude" => 41.90222,
            "longitude" => 12.45304999999999,
            "zone_name" => "Europe/Vatican",
            "abbreviation" => "CET",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Vienna" => [
            "country_name" => "Austria",
            "country_code" => "AT",
            "latitude" => 48.21665999999999,
            "longitude" => 16.33332999999999,
            "zone_name" => "Europe/Vienna",
            "abbreviation" => "CET",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Vilnius" => [
            "country_name" => "Lithuania",
            "country_code" => "LT",
            "latitude" => 54.68333000000001,
            "longitude" => 25.316660000000013,
            "zone_name" => "Europe/Vilnius",
            "abbreviation" => "WMT",
            "offset" => 5040,
            "diff" => [
                "hours" => 1,
                "minutes" => 24,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Warsaw" => [
            "country_name" => "Poland",
            "country_code" => "PL",
            "latitude" => 52.25,
            "longitude" => 21,
            "zone_name" => "Europe/Warsaw",
            "abbreviation" => "WMT",
            "offset" => 5040,
            "diff" => [
                "hours" => 1,
                "minutes" => 24,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Zagreb" => [
            "country_name" => "Croatia",
            "country_code" => "HR",
            "latitude" => 45.80000000000001,
            "longitude" => 15.96665999999999,
            "zone_name" => "Europe/Zagreb",
            "abbreviation" => "CET",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Zaporozhye" => [
            "country_name" => "Ukraine",
            "country_code" => "UA",
            "latitude" => 47.83332999999999,
            "longitude" => 35.16666000000001,
            "zone_name" => "Europe/Zaporozhye",
            "abbreviation" => "MSK",
            "offset" => 10800,
            "diff" => [
                "hours" => 3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Zaporozh'ye/Zaporizhia; Lugansk/Luhansk (east)"
        ],
        "Europe/Zurich" => [
            "country_name" => "Switzerland",
            "country_code" => "CH",
            "latitude" => 47.38333,
            "longitude" => 8.533330000000007,
            "zone_name" => "Europe/Zurich",
            "abbreviation" => "CET",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Argentina/Buenos_Aires" => [
            "country_name" => "Argentina",
            "country_code" => "AR",
            "latitude" => -34.6,
            "longitude" => -58.45,
            "zone_name" => "America/Argentina/Buenos_Aires",
            "abbreviation" => "CMT",
            "offset" => -15408,
            "diff" => [
                "hours" => -5,
                "minutes" => -16,
                "seconds" => -48
            ],
            "dst" => false,
            "comments" => "Buenos Aires (BA, CF)"
        ],
        "America/Argentina/Catamarca" => [
            "country_name" => "Argentina",
            "country_code" => "AR",
            "latitude" => -28.46667,
            "longitude" => -65.78334,
            "zone_name" => "America/Argentina/Catamarca",
            "abbreviation" => "CMT",
            "offset" => -15408,
            "diff" => [
                "hours" => -5,
                "minutes" => -16,
                "seconds" => -48
            ],
            "dst" => false,
            "comments" => "Catamarca (CT); Chubut (CH)"
        ],
        "America/Argentina/Cordoba" => [
            "country_name" => "Argentina",
            "country_code" => "AR",
            "latitude" => -31.4,
            "longitude" => -64.18334,
            "zone_name" => "America/Argentina/Cordoba",
            "abbreviation" => "CMT",
            "offset" => -15408,
            "diff" => [
                "hours" => -5,
                "minutes" => -16,
                "seconds" => -48
            ],
            "dst" => false,
            "comments" => "Argentina (most areas => CB, CC, CN, ER, FM, MN, SE, SF)"
        ],
        "America/Argentina/Jujuy" => [
            "country_name" => "Argentina",
            "country_code" => "AR",
            "latitude" => -24.18334,
            "longitude" => -65.3,
            "zone_name" => "America/Argentina/Jujuy",
            "abbreviation" => "CMT",
            "offset" => -15408,
            "diff" => [
                "hours" => -5,
                "minutes" => -16,
                "seconds" => -48
            ],
            "dst" => false,
            "comments" => "Jujuy (JY)"
        ],
        "America/Argentina/La_Rioja" => [
            "country_name" => "Argentina",
            "country_code" => "AR",
            "latitude" => -29.43334,
            "longitude" => -66.85,
            "zone_name" => "America/Argentina/La_Rioja",
            "abbreviation" => "CMT",
            "offset" => -15408,
            "diff" => [
                "hours" => -5,
                "minutes" => -16,
                "seconds" => -48
            ],
            "dst" => false,
            "comments" => "La Rioja (LR)"
        ],
        "America/Argentina/Mendoza" => [
            "country_name" => "Argentina",
            "country_code" => "AR",
            "latitude" => -32.88334,
            "longitude" => -68.81667,
            "zone_name" => "America/Argentina/Mendoza",
            "abbreviation" => "CMT",
            "offset" => -15408,
            "diff" => [
                "hours" => -5,
                "minutes" => -16,
                "seconds" => -48
            ],
            "dst" => false,
            "comments" => "Mendoza (MZ)"
        ],
        "America/Argentina/Rio_Gallegos" => [
            "country_name" => "Argentina",
            "country_code" => "AR",
            "latitude" => -51.63334,
            "longitude" => -69.21667,
            "zone_name" => "America/Argentina/Rio_Gallegos",
            "abbreviation" => "CMT",
            "offset" => -15408,
            "diff" => [
                "hours" => -5,
                "minutes" => -16,
                "seconds" => -48
            ],
            "dst" => false,
            "comments" => "Santa Cruz (SC)"
        ],
        "America/Argentina/Salta" => [
            "country_name" => "Argentina",
            "country_code" => "AR",
            "latitude" => -24.783339999999995,
            "longitude" => -65.41667,
            "zone_name" => "America/Argentina/Salta",
            "abbreviation" => "CMT",
            "offset" => -15408,
            "diff" => [
                "hours" => -5,
                "minutes" => -16,
                "seconds" => -48
            ],
            "dst" => false,
            "comments" => "Salta (SA, LP, NQ, RN)"
        ],
        "America/Argentina/San_Juan" => [
            "country_name" => "Argentina",
            "country_code" => "AR",
            "latitude" => -31.533340000000003,
            "longitude" => -68.51667,
            "zone_name" => "America/Argentina/San_Juan",
            "abbreviation" => "CMT",
            "offset" => -15408,
            "diff" => [
                "hours" => -5,
                "minutes" => -16,
                "seconds" => -48
            ],
            "dst" => false,
            "comments" => "San Juan (SJ)"
        ],
        "America/Argentina/San_Luis" => [
            "country_name" => "Argentina",
            "country_code" => "AR",
            "latitude" => -33.31667,
            "longitude" => -66.35,
            "zone_name" => "America/Argentina/San_Luis",
            "abbreviation" => "CMT",
            "offset" => -15408,
            "diff" => [
                "hours" => -5,
                "minutes" => -16,
                "seconds" => -48
            ],
            "dst" => false,
            "comments" => "San Luis (SL)"
        ],
        "America/Argentina/Tucuman" => [
            "country_name" => "Argentina",
            "country_code" => "AR",
            "latitude" => -26.816670000000002,
            "longitude" => -65.21667,
            "zone_name" => "America/Argentina/Tucuman",
            "abbreviation" => "CMT",
            "offset" => -15408,
            "diff" => [
                "hours" => -5,
                "minutes" => -16,
                "seconds" => -48
            ],
            "dst" => false,
            "comments" => "Tucuman (TM)"
        ],
        "America/Argentina/Ushuaia" => [
            "country_name" => "Argentina",
            "country_code" => "AR",
            "latitude" => -54.8,
            "longitude" => -68.3,
            "zone_name" => "America/Argentina/Ushuaia",
            "abbreviation" => "CMT",
            "offset" => -15408,
            "diff" => [
                "hours" => -5,
                "minutes" => -16,
                "seconds" => -48
            ],
            "dst" => false,
            "comments" => "Tierra del Fuego (TF)"
        ],
        "America/Caracas" => [
            "country_name" => "Venezuela",
            "country_code" => "VE",
            "latitude" => 10.5,
            "longitude" => -66.93334,
            "zone_name" => "America/Caracas",
            "abbreviation" => "CMT",
            "offset" => -16060,
            "diff" => [
                "hours" => -5,
                "minutes" => -27,
                "seconds" => -40
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Cayman" => [
            "country_name" => "Cayman Islands",
            "country_code" => "KY",
            "latitude" => 19.299999999999997,
            "longitude" => -81.38334,
            "zone_name" => "America/Cayman",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Panama" => [
            "country_name" => "Panama",
            "country_code" => "PA",
            "latitude" => 8.966660000000005,
            "longitude" => -79.53334,
            "zone_name" => "America/Panama",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Detroit" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 42.331379999999996,
            "longitude" => -83.04584,
            "zone_name" => "America/Detroit",
            "abbreviation" => "EWT",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Eastern - MI (most areas)"
        ],
        "America/Hermosillo" => [
            "country_name" => "Mexico",
            "country_code" => "MX",
            "latitude" => 29.06666,
            "longitude" => -110.96667,
            "zone_name" => "America/Hermosillo",
            "abbreviation" => "PST",
            "offset" => -28800,
            "diff" => [
                "hours" => -8,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Mountain Standard Time - Sonora"
        ],
        "America/Mazatlan" => [
            "country_name" => "Mexico",
            "country_code" => "MX",
            "latitude" => 23.216660000000005,
            "longitude" => -106.41667,
            "zone_name" => "America/Mazatlan",
            "abbreviation" => "PST",
            "offset" => -28800,
            "diff" => [
                "hours" => -8,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Mountain Time - Baja California Sur, Nayarit, Sinaloa"
        ],
        "America/Regina" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 50.400000000000006,
            "longitude" => -104.65001,
            "zone_name" => "America/Regina",
            "abbreviation" => "MWT",
            "offset" => -21600,
            "diff" => [
                "hours" => -6,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "CST - SK (most areas)"
        ],
        "America/Swift_Current" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 50.28333000000001,
            "longitude" => -107.83334,
            "zone_name" => "America/Swift_Current",
            "abbreviation" => "MWT",
            "offset" => -21600,
            "diff" => [
                "hours" => -6,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "CST - SK (midwest)"
        ],
        "America/Thunder_Bay" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 48.38333,
            "longitude" => -89.25,
            "zone_name" => "America/Thunder_Bay",
            "abbreviation" => "EWT",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Eastern - ON (Thunder Bay)"
        ],
        "Pacific/Guam" => [
            "country_name" => "Guam",
            "country_code" => "GU",
            "latitude" => 13.466660000000005,
            "longitude" => 144.75,
            "zone_name" => "Pacific/Guam",
            "abbreviation" => "GST",
            "offset" => 36000,
            "diff" => [
                "hours" => 10,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Pacific/Saipan" => [
            "country_name" => "Northern Mariana Islands",
            "country_code" => "MP",
            "latitude" => 15.200000000000003,
            "longitude" => 145.75,
            "zone_name" => "Pacific/Saipan",
            "abbreviation" => "GST",
            "offset" => 36000,
            "diff" => [
                "hours" => 10,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Addis_Ababa" => [
            "country_name" => "Ethiopia",
            "country_code" => "ET",
            "latitude" => 9.033330000000007,
            "longitude" => 38.69999999999999,
            "zone_name" => "Africa/Addis_Ababa",
            "abbreviation" => "EAT",
            "offset" => 10800,
            "diff" => [
                "hours" => 3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Asmara" => [
            "country_name" => "Eritrea",
            "country_code" => "ER",
            "latitude" => 15.333330000000004,
            "longitude" => 38.88333,
            "zone_name" => "Africa/Asmara",
            "abbreviation" => "EAT",
            "offset" => 10800,
            "diff" => [
                "hours" => 3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Dar_es_Salaam" => [
            "country_name" => "Tanzania",
            "country_code" => "TZ",
            "latitude" => -6.799999999999997,
            "longitude" => 39.28333000000001,
            "zone_name" => "Africa/Dar_es_Salaam",
            "abbreviation" => "EAT",
            "offset" => 10800,
            "diff" => [
                "hours" => 3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Djibouti" => [
            "country_name" => "Djibouti",
            "country_code" => "DJ",
            "latitude" => 11.599999999999994,
            "longitude" => 43.150000000000006,
            "zone_name" => "Africa/Djibouti",
            "abbreviation" => "EAT",
            "offset" => 10800,
            "diff" => [
                "hours" => 3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Kampala" => [
            "country_name" => "Uganda",
            "country_code" => "UG",
            "latitude" => 0.31665999999999883,
            "longitude" => 32.41666000000001,
            "zone_name" => "Africa/Kampala",
            "abbreviation" => "EAT",
            "offset" => 10800,
            "diff" => [
                "hours" => 3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Mogadishu" => [
            "country_name" => "Somalia",
            "country_code" => "SO",
            "latitude" => 2.066659999999999,
            "longitude" => 45.366659999999996,
            "zone_name" => "Africa/Mogadishu",
            "abbreviation" => "EAT",
            "offset" => 10800,
            "diff" => [
                "hours" => 3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Nairobi" => [
            "country_name" => "Kenya",
            "country_code" => "KE",
            "latitude" => -1.2833399999999955,
            "longitude" => 36.81666000000001,
            "zone_name" => "Africa/Nairobi",
            "abbreviation" => "EAT",
            "offset" => 10800,
            "diff" => [
                "hours" => 3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Indian/Antananarivo" => [
            "country_name" => "Madagascar",
            "country_code" => "MG",
            "latitude" => -18.916669999999996,
            "longitude" => 47.51666,
            "zone_name" => "Indian/Antananarivo",
            "abbreviation" => "EAT",
            "offset" => 10800,
            "diff" => [
                "hours" => 3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Indian/Comoro" => [
            "country_name" => "Comoros",
            "country_code" => "KM",
            "latitude" => -11.683340000000001,
            "longitude" => 43.26666,
            "zone_name" => "Indian/Comoro",
            "abbreviation" => "EAT",
            "offset" => 10800,
            "diff" => [
                "hours" => 3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Indian/Mayotte" => [
            "country_name" => "Mayotte",
            "country_code" => "YT",
            "latitude" => -12.783339999999995,
            "longitude" => 45.233329999999995,
            "zone_name" => "Indian/Mayotte",
            "abbreviation" => "EAT",
            "offset" => 10800,
            "diff" => [
                "hours" => 3,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/New_York" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 40.71415999999999,
            "longitude" => -74.00639,
            "zone_name" => "America/New_York",
            "abbreviation" => "EWT",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Eastern (most areas)"
        ],
        "America/Jamaica" => [
            "country_name" => "Jamaica",
            "country_code" => "JM",
            "latitude" => 17.968050000000005,
            "longitude" => -76.79334,
            "zone_name" => "America/Jamaica",
            "abbreviation" => "KMT",
            "offset" => -18430,
            "diff" => [
                "hours" => -6,
                "minutes" => -7,
                "seconds" => -10
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Nassau" => [
            "country_name" => "Bahamas",
            "country_code" => "BS",
            "latitude" => 25.083330000000004,
            "longitude" => -77.35,
            "zone_name" => "America/Nassau",
            "abbreviation" => "EST",
            "offset" => -18000,
            "diff" => [
                "hours" => -5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Nipigon" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 49.01666,
            "longitude" => -88.26667,
            "zone_name" => "America/Nipigon",
            "abbreviation" => "EWT",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Eastern - ON, QC (no DST 1967-73)"
        ],
        "America/Port-au-Prince" => [
            "country_name" => "Haiti",
            "country_code" => "HT",
            "latitude" => 18.533330000000007,
            "longitude" => -72.33334,
            "zone_name" => "America/Port-au-Prince",
            "abbreviation" => "PPMT",
            "offset" => -17340,
            "diff" => [
                "hours" => -5,
                "minutes" => -49,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Toronto" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 43.650000000000006,
            "longitude" => -79.38334,
            "zone_name" => "America/Toronto",
            "abbreviation" => "EWT",
            "offset" => -14400,
            "diff" => [
                "hours" => -4,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Eastern - ON, QC (most areas)"
        ],
        "Europe/Helsinki" => [
            "country_name" => "Finland",
            "country_code" => "FI",
            "latitude" => 60.16666000000001,
            "longitude" => 24.96665999999999,
            "zone_name" => "Europe/Helsinki",
            "abbreviation" => "HMT",
            "offset" => 5989,
            "diff" => [
                "hours" => 1,
                "minutes" => 39,
                "seconds" => 49
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Cairo" => [
            "country_name" => "Egypt",
            "country_code" => "EG",
            "latitude" => 30.049999999999997,
            "longitude" => 31.25,
            "zone_name" => "Africa/Cairo",
            "abbreviation" => "EET",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Amman" => [
            "country_name" => "Jordan",
            "country_code" => "JO",
            "latitude" => 31.950000000000003,
            "longitude" => 35.93333000000001,
            "zone_name" => "Asia/Amman",
            "abbreviation" => "EET",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Beirut" => [
            "country_name" => "Lebanon",
            "country_code" => "LB",
            "latitude" => 33.88333,
            "longitude" => 35.5,
            "zone_name" => "Asia/Beirut",
            "abbreviation" => "EET",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Damascus" => [
            "country_name" => "Syria",
            "country_code" => "SY",
            "latitude" => 33.5,
            "longitude" => 36.30000000000001,
            "zone_name" => "Asia/Damascus",
            "abbreviation" => "EET",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Famagusta" => [
            "country_name" => "Cyprus",
            "country_code" => "CY",
            "latitude" => 35.116659999999996,
            "longitude" => 33.94999999999999,
            "zone_name" => "Asia/Famagusta",
            "abbreviation" => "EET",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Northern Cyprus"
        ],
        "Asia/Gaza" => [
            "country_name" => "Palestinian Territory",
            "country_code" => "PS",
            "latitude" => 31.5,
            "longitude" => 34.46665999999999,
            "zone_name" => "Asia/Gaza",
            "abbreviation" => "IST",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Gaza Strip"
        ],
        "Asia/Hebron" => [
            "country_name" => "Palestinian Territory",
            "country_code" => "PS",
            "latitude" => 31.533330000000007,
            "longitude" => 35.095,
            "zone_name" => "Asia/Hebron",
            "abbreviation" => "IST",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "West Bank"
        ],
        "Asia/Nicosia" => [
            "country_name" => "Cyprus",
            "country_code" => "CY",
            "latitude" => 35.16665999999999,
            "longitude" => 33.366659999999996,
            "zone_name" => "Asia/Nicosia",
            "abbreviation" => "EET",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Cyprus (most areas)"
        ],
        "Europe/Istanbul" => [
            "country_name" => "Turkey",
            "country_code" => "TR",
            "latitude" => 41.01666,
            "longitude" => 28.96665999999999,
            "zone_name" => "Europe/Istanbul",
            "abbreviation" => "IMT",
            "offset" => 7016,
            "diff" => [
                "hours" => 1,
                "minutes" => 56,
                "seconds" => 56
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Mariehamn" => [
            "country_name" => "Aland Islands",
            "country_code" => "AX",
            "latitude" => 60.099999999999994,
            "longitude" => 19.94999999999999,
            "zone_name" => "Europe/Mariehamn",
            "abbreviation" => "HMT",
            "offset" => 5989,
            "diff" => [
                "hours" => 1,
                "minutes" => 39,
                "seconds" => 49
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Europe/Moscow" => [
            "country_name" => "Russia",
            "country_code" => "RU",
            "latitude" => 55.75583,
            "longitude" => 37.61777000000001,
            "zone_name" => "Europe/Moscow",
            "abbreviation" => "MST",
            "offset" => 12679,
            "diff" => [
                "hours" => 3,
                "minutes" => 31,
                "seconds" => 19
            ],
            "dst" => true,
            "comments" => "MSK+00 - Moscow area"
        ],
        "Pacific/Easter" => [
            "country_name" => "Chile",
            "country_code" => "CL",
            "latitude" => -27.15,
            "longitude" => -109.43334,
            "zone_name" => "Pacific/Easter",
            "abbreviation" => "EMT",
            "offset" => -26248,
            "diff" => [
                "hours" => -8,
                "minutes" => -17,
                "seconds" => -28
            ],
            "dst" => false,
            "comments" => "Easter Island"
        ],
        "Atlantic/Madeira" => [
            "country_name" => "Portugal",
            "country_code" => "PT",
            "latitude" => 32.63333,
            "longitude" => -16.900000000000006,
            "zone_name" => "Atlantic/Madeira",
            "abbreviation" => "WET",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Madeira Islands"
        ],
        "Africa/Abidjan" => [
            "country_name" => "Ivory Coast",
            "country_code" => "CI",
            "latitude" => 5.316659999999999,
            "longitude" => -4.03334000000001,
            "zone_name" => "Africa/Abidjan",
            "abbreviation" => "GMT",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Accra" => [
            "country_name" => "Ghana",
            "country_code" => "GH",
            "latitude" => 5.549999999999997,
            "longitude" => -0.21666999999999348,
            "zone_name" => "Africa/Accra",
            "abbreviation" => "GMT",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Bamako" => [
            "country_name" => "Mali",
            "country_code" => "ML",
            "latitude" => 12.650000000000006,
            "longitude" => -8,
            "zone_name" => "Africa/Bamako",
            "abbreviation" => "GMT",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Banjul" => [
            "country_name" => "Gambia",
            "country_code" => "GM",
            "latitude" => 13.466660000000005,
            "longitude" => -16.650000000000006,
            "zone_name" => "Africa/Banjul",
            "abbreviation" => "GMT",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Bissau" => [
            "country_name" => "Guinea-Bissau",
            "country_code" => "GW",
            "latitude" => 11.849999999999994,
            "longitude" => -15.583339999999993,
            "zone_name" => "Africa/Bissau",
            "abbreviation" => "GMT",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Conakry" => [
            "country_name" => "Guinea",
            "country_code" => "GN",
            "latitude" => 9.516660000000002,
            "longitude" => -13.716669999999993,
            "zone_name" => "Africa/Conakry",
            "abbreviation" => "GMT",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Dakar" => [
            "country_name" => "Senegal",
            "country_code" => "SN",
            "latitude" => 14.666659999999993,
            "longitude" => -17.433339999999987,
            "zone_name" => "Africa/Dakar",
            "abbreviation" => "GMT",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Freetown" => [
            "country_name" => "Sierra Leone",
            "country_code" => "SL",
            "latitude" => 8.5,
            "longitude" => -13.25,
            "zone_name" => "Africa/Freetown",
            "abbreviation" => "GMT",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Lome" => [
            "country_name" => "Togo",
            "country_code" => "TG",
            "latitude" => 6.133330000000001,
            "longitude" => 1.2166599999999903,
            "zone_name" => "Africa/Lome",
            "abbreviation" => "GMT",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Monrovia" => [
            "country_name" => "Liberia",
            "country_code" => "LR",
            "latitude" => 6.299999999999997,
            "longitude" => -10.78334000000001,
            "zone_name" => "Africa/Monrovia",
            "abbreviation" => "MMT",
            "offset" => -2670,
            "diff" => [
                "hours" => -1,
                "minutes" => -44,
                "seconds" => -30
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Nouakchott" => [
            "country_name" => "Mauritania",
            "country_code" => "MR",
            "latitude" => 18.099999999999994,
            "longitude" => -15.949999999999989,
            "zone_name" => "Africa/Nouakchott",
            "abbreviation" => "GMT",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Ouagadougou" => [
            "country_name" => "Burkina Faso",
            "country_code" => "BF",
            "latitude" => 12.366659999999996,
            "longitude" => -1.5166700000000048,
            "zone_name" => "Africa/Ouagadougou",
            "abbreviation" => "GMT",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Sao_Tome" => [
            "country_name" => "Sao Tome and Principe",
            "country_code" => "ST",
            "latitude" => 0.3333300000000037,
            "longitude" => 6.733329999999995,
            "zone_name" => "Africa/Sao_Tome",
            "abbreviation" => "WAT",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Danmarkshavn" => [
            "country_name" => "Greenland",
            "country_code" => "GL",
            "latitude" => 76.76666,
            "longitude" => -18.66667000000001,
            "zone_name" => "America/Danmarkshavn",
            "abbreviation" => "GMT",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "National Park (east coast)"
        ],
        "Atlantic/Reykjavik" => [
            "country_name" => "Iceland",
            "country_code" => "IS",
            "latitude" => 64.15,
            "longitude" => -21.849999999999994,
            "zone_name" => "Atlantic/Reykjavik",
            "abbreviation" => "GMT",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Atlantic/St_Helena" => [
            "country_name" => "Saint Helena",
            "country_code" => "SH",
            "latitude" => -15.916669999999996,
            "longitude" => -5.699999999999989,
            "zone_name" => "Atlantic/St_Helena",
            "abbreviation" => "GMT",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Pacific/Honolulu" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 21.306939999999997,
            "longitude" => -157.85834,
            "zone_name" => "Pacific/Honolulu",
            "abbreviation" => "HWT",
            "offset" => -34200,
            "diff" => [
                "hours" => -10,
                "minutes" => -30,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Hawaii"
        ],
        "Asia/Hong_Kong" => [
            "country_name" => "Hong Kong",
            "country_code" => "HK",
            "latitude" => 22.283330000000007,
            "longitude" => 114.14999,
            "zone_name" => "Asia/Hong_Kong",
            "abbreviation" => "JST",
            "offset" => 32400,
            "diff" => [
                "hours" => 9,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Atlantic/Azores" => [
            "country_name" => "Portugal",
            "country_code" => "PT",
            "latitude" => 37.733329999999995,
            "longitude" => -25.66667000000001,
            "zone_name" => "Atlantic/Azores",
            "abbreviation" => "WET",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Azores"
        ],
        "Asia/Dhaka" => [
            "country_name" => "Bangladesh",
            "country_code" => "BD",
            "latitude" => 23.716660000000005,
            "longitude" => 90.41665999999998,
            "zone_name" => "Asia/Dhaka",
            "abbreviation" => "HMT",
            "offset" => 21200,
            "diff" => [
                "hours" => 5,
                "minutes" => 53,
                "seconds" => 20
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Jerusalem" => [
            "country_name" => "Israel",
            "country_code" => "IL",
            "latitude" => 31.780550000000005,
            "longitude" => 35.22388000000001,
            "zone_name" => "Asia/Jerusalem",
            "abbreviation" => "JMT",
            "offset" => 8440,
            "diff" => [
                "hours" => 2,
                "minutes" => 20,
                "seconds" => 40
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Irkutsk" => [
            "country_name" => "Russia",
            "country_code" => "RU",
            "latitude" => 52.26666,
            "longitude" => 104.33332999999999,
            "zone_name" => "Asia/Irkutsk",
            "abbreviation" => "IMT",
            "offset" => 25025,
            "diff" => [
                "hours" => 6,
                "minutes" => 57,
                "seconds" => 5
            ],
            "dst" => false,
            "comments" => "MSK+05 - Irkutsk, Buryatia"
        ],
        "Asia/Kolkata" => [
            "country_name" => "India",
            "country_code" => "IN",
            "latitude" => 22.533330000000007,
            "longitude" => 88.36666000000002,
            "zone_name" => "Asia/Kolkata",
            "abbreviation" => "MMT",
            "offset" => 19270,
            "diff" => [
                "hours" => 5,
                "minutes" => 21,
                "seconds" => 10
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Tokyo" => [
            "country_name" => "Japan",
            "country_code" => "JP",
            "latitude" => 35.654439999999994,
            "longitude" => 139.74471999999997,
            "zone_name" => "Asia/Tokyo",
            "abbreviation" => "JST",
            "offset" => 32400,
            "diff" => [
                "hours" => 9,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Manila" => [
            "country_name" => "Philippines",
            "country_code" => "PH",
            "latitude" => 14.583330000000004,
            "longitude" => 121,
            "zone_name" => "Asia/Manila",
            "abbreviation" => "PST",
            "offset" => 28800,
            "diff" => [
                "hours" => 8,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Pyongyang" => [
            "country_name" => "North Korea",
            "country_code" => "KP",
            "latitude" => 39.01666,
            "longitude" => 125.75,
            "zone_name" => "Asia/Pyongyang",
            "abbreviation" => "KST",
            "offset" => 30600,
            "diff" => [
                "hours" => 8,
                "minutes" => 30,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Seoul" => [
            "country_name" => "South Korea",
            "country_code" => "KR",
            "latitude" => 37.55,
            "longitude" => 126.96665999999999,
            "zone_name" => "Asia/Seoul",
            "abbreviation" => "KST",
            "offset" => 32400,
            "diff" => [
                "hours" => 9,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Yellowknife" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 62.449990000000014,
            "longitude" => -114.35,
            "zone_name" => "America/Yellowknife",
            "abbreviation" => "MWT",
            "offset" => -21600,
            "diff" => [
                "hours" => -6,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Mountain - NT (central)"
        ],
        "America/Denver" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 39.73916,
            "longitude" => -104.98417,
            "zone_name" => "America/Denver",
            "abbreviation" => "MWT",
            "offset" => -21600,
            "diff" => [
                "hours" => -6,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Mountain (most areas)"
        ],
        "America/Boise" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 43.613609999999994,
            "longitude" => -116.2025,
            "zone_name" => "America/Boise",
            "abbreviation" => "PST",
            "offset" => -28800,
            "diff" => [
                "hours" => -8,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Mountain - ID (south); OR (east)"
        ],
        "America/Edmonton" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 53.55000000000001,
            "longitude" => -113.46667,
            "zone_name" => "America/Edmonton",
            "abbreviation" => "MWT",
            "offset" => -21600,
            "diff" => [
                "hours" => -6,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Mountain - AB; BC (E); SK (W)"
        ],
        "America/Inuvik" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 68.34971999999999,
            "longitude" => -133.71667,
            "zone_name" => "America/Inuvik",
            "abbreviation" => "PST",
            "offset" => -28800,
            "diff" => [
                "hours" => -8,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Mountain - NT (west)"
        ],
        "America/Phoenix" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 33.44833,
            "longitude" => -112.07334,
            "zone_name" => "America/Phoenix",
            "abbreviation" => "MWT",
            "offset" => -21600,
            "diff" => [
                "hours" => -6,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "MST - Arizona (except Navajo)"
        ],
        "America/Montevideo" => [
            "country_name" => "Uruguay",
            "country_code" => "UY",
            "latitude" => -34.90917,
            "longitude" => -56.212500000000006,
            "zone_name" => "America/Montevideo",
            "abbreviation" => "MMT",
            "offset" => -13491,
            "diff" => [
                "hours" => -4,
                "minutes" => -44,
                "seconds" => -51
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Indian/Maldives" => [
            "country_name" => "Maldives",
            "country_code" => "MV",
            "latitude" => 4.166659999999993,
            "longitude" => 73.5,
            "zone_name" => "Indian/Maldives",
            "abbreviation" => "MMT",
            "offset" => 17640,
            "diff" => [
                "hours" => 4,
                "minutes" => 54,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Colombo" => [
            "country_name" => "Sri Lanka",
            "country_code" => "LK",
            "latitude" => 6.933329999999998,
            "longitude" => 79.85000000000002,
            "zone_name" => "Asia/Colombo",
            "abbreviation" => "MMT",
            "offset" => 19172,
            "diff" => [
                "hours" => 5,
                "minutes" => 19,
                "seconds" => 32
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Makassar" => [
            "country_name" => "Indonesia",
            "country_code" => "ID",
            "latitude" => -5.116669999999999,
            "longitude" => 119.39999,
            "zone_name" => "Asia/Makassar",
            "abbreviation" => "WITA",
            "offset" => 28800,
            "diff" => [
                "hours" => 8,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Borneo (east, south); Sulawesi/Celebes, Bali, Nusa Tengarra; Timor (west)"
        ],
        "America/Creston" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 49.099999999999994,
            "longitude" => -116.51667,
            "zone_name" => "America/Creston",
            "abbreviation" => "PST",
            "offset" => -28800,
            "diff" => [
                "hours" => -8,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "MST - BC (Creston)"
        ],
        "America/Dawson_Creek" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 59.76666,
            "longitude" => -120.23334,
            "zone_name" => "America/Dawson_Creek",
            "abbreviation" => "PWT",
            "offset" => -25200,
            "diff" => [
                "hours" => -7,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "MST - BC (Dawson Cr, Ft St John)"
        ],
        "America/Fort_Nelson" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 58.80000000000001,
            "longitude" => -122.7,
            "zone_name" => "America/Fort_Nelson",
            "abbreviation" => "PWT",
            "offset" => -25200,
            "diff" => [
                "hours" => -7,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "MST - BC (Ft Nelson)"
        ],
        "America/Tijuana" => [
            "country_name" => "Mexico",
            "country_code" => "MX",
            "latitude" => 32.53333000000001,
            "longitude" => -117.01667,
            "zone_name" => "America/Tijuana",
            "abbreviation" => "PWT",
            "offset" => -25200,
            "diff" => [
                "hours" => -7,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Pacific Time US - Baja California"
        ],
        "America/St_Johns" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 47.56666000000001,
            "longitude" => -52.71666999999999,
            "zone_name" => "America/St_Johns",
            "abbreviation" => "NWT",
            "offset" => -9000,
            "diff" => [
                "hours" => -3,
                "minutes" => -30,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Newfoundland; Labrador (southeast)"
        ],
        "Pacific/Auckland" => [
            "country_name" => "New Zealand",
            "country_code" => "NZ",
            "latitude" => -36.86667,
            "longitude" => 174.76666,
            "zone_name" => "Pacific/Auckland",
            "abbreviation" => "NZST",
            "offset" => 45000,
            "diff" => [
                "hours" => 12,
                "minutes" => 30,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "New Zealand (most areas)"
        ],
        "Antarctica/McMurdo" => [
            "country_name" => "Antarctica",
            "country_code" => "AQ",
            "latitude" => -77.83333999999999,
            "longitude" => 166.60000000000002,
            "zone_name" => "Antarctica/McMurdo",
            "abbreviation" => "NZST",
            "offset" => 45000,
            "diff" => [
                "hours" => 12,
                "minutes" => 30,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "New Zealand time - McMurdo, South Pole"
        ],
        "America/Los_Angeles" => [
            "country_name" => "United States",
            "country_code" => "US",
            "latitude" => 34.052220000000005,
            "longitude" => -118.24278000000001,
            "zone_name" => "America/Los_Angeles",
            "abbreviation" => "PWT",
            "offset" => -25200,
            "diff" => [
                "hours" => -7,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Pacific"
        ],
        "America/Dawson" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 64.06666000000001,
            "longitude" => -139.41667,
            "zone_name" => "America/Dawson",
            "abbreviation" => "YWT",
            "offset" => -28800,
            "diff" => [
                "hours" => -8,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Pacific - Yukon (north)"
        ],
        "America/Vancouver" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 49.26666,
            "longitude" => -123.11667,
            "zone_name" => "America/Vancouver",
            "abbreviation" => "PWT",
            "offset" => -25200,
            "diff" => [
                "hours" => -7,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Pacific - BC (most areas)"
        ],
        "America/Whitehorse" => [
            "country_name" => "Canada",
            "country_code" => "CA",
            "latitude" => 60.71665999999999,
            "longitude" => -135.05001,
            "zone_name" => "America/Whitehorse",
            "abbreviation" => "YWT",
            "offset" => -28800,
            "diff" => [
                "hours" => -8,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => true,
            "comments" => "Pacific - Yukon (south)"
        ],
        "Asia/Karachi" => [
            "country_name" => "Pakistan",
            "country_code" => "PK",
            "latitude" => 24.866659999999996,
            "longitude" => 67.05000000000001,
            "zone_name" => "Asia/Karachi",
            "abbreviation" => "PKT",
            "offset" => 18000,
            "diff" => [
                "hours" => 5,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Ho_Chi_Minh" => [
            "country_name" => "Vietnam",
            "country_code" => "VN",
            "latitude" => 10.75,
            "longitude" => 106.66665999999998,
            "zone_name" => "Asia/Ho_Chi_Minh",
            "abbreviation" => "PLMT",
            "offset" => 25590,
            "diff" => [
                "hours" => 7,
                "minutes" => 6,
                "seconds" => 30
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Paramaribo" => [
            "country_name" => "Suriname",
            "country_code" => "SR",
            "latitude" => 5.833330000000004,
            "longitude" => -55.166669999999996,
            "zone_name" => "America/Paramaribo",
            "abbreviation" => "PMT",
            "offset" => -13252,
            "diff" => [
                "hours" => -4,
                "minutes" => -40,
                "seconds" => -52
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Yekaterinburg" => [
            "country_name" => "Russia",
            "country_code" => "RU",
            "latitude" => 56.849999999999994,
            "longitude" => 60.599999999999994,
            "zone_name" => "Asia/Yekaterinburg",
            "abbreviation" => "PMT",
            "offset" => 13505,
            "diff" => [
                "hours" => 3,
                "minutes" => 45,
                "seconds" => 5
            ],
            "dst" => false,
            "comments" => "MSK+02 - Urals"
        ],
        "Asia/Pontianak" => [
            "country_name" => "Indonesia",
            "country_code" => "ID",
            "latitude" => -0.033339999999995484,
            "longitude" => 109.33332999999999,
            "zone_name" => "Asia/Pontianak",
            "abbreviation" => "WITA",
            "offset" => 28800,
            "diff" => [
                "hours" => 8,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Borneo (west, central)"
        ],
        "America/Guayaquil" => [
            "country_name" => "Ecuador",
            "country_code" => "EC",
            "latitude" => -2.1666699999999963,
            "longitude" => -79.83334,
            "zone_name" => "America/Guayaquil",
            "abbreviation" => "QMT",
            "offset" => -18840,
            "diff" => [
                "hours" => -6,
                "minutes" => -14,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Ecuador (mainland)"
        ],
        "Asia/Yangon" => [
            "country_name" => "Myanmar",
            "country_code" => "MM",
            "latitude" => 16.783330000000007,
            "longitude" => 96.16665999999998,
            "zone_name" => "Asia/Yangon",
            "abbreviation" => "RMT",
            "offset" => 23087,
            "diff" => [
                "hours" => 6,
                "minutes" => 24,
                "seconds" => 47
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Johannesburg" => [
            "country_name" => "South Africa",
            "country_code" => "ZA",
            "latitude" => -26.25,
            "longitude" => 28,
            "zone_name" => "Africa/Johannesburg",
            "abbreviation" => "SAST",
            "offset" => 5400,
            "diff" => [
                "hours" => 1,
                "minutes" => 30,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Maseru" => [
            "country_name" => "Lesotho",
            "country_code" => "LS",
            "latitude" => -29.46667,
            "longitude" => 27.5,
            "zone_name" => "Africa/Maseru",
            "abbreviation" => "SAST",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Mbabane" => [
            "country_name" => "Swaziland",
            "country_code" => "SZ",
            "latitude" => -26.299999999999997,
            "longitude" => 31.099999999999994,
            "zone_name" => "Africa/Mbabane",
            "abbreviation" => "SAST",
            "offset" => 7200,
            "diff" => [
                "hours" => 2,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Atlantic/Stanley" => [
            "country_name" => "Falkland Islands",
            "country_code" => "FK",
            "latitude" => -51.70001,
            "longitude" => -57.849999999999994,
            "zone_name" => "Atlantic/Stanley",
            "abbreviation" => "SMT",
            "offset" => -13884,
            "diff" => [
                "hours" => -4,
                "minutes" => -51,
                "seconds" => -24
            ],
            "dst" => false,
            "comments" => ""
        ],
        "America/Punta_Arenas" => [
            "country_name" => "Chile",
            "country_code" => "CL",
            "latitude" => -53.15,
            "longitude" => -70.91667,
            "zone_name" => "America/Punta_Arenas",
            "abbreviation" => "SMT",
            "offset" => -16966,
            "diff" => [
                "hours" => -5,
                "minutes" => -42,
                "seconds" => -46
            ],
            "dst" => false,
            "comments" => "Region of Magallanes"
        ],
        "America/Santiago" => [
            "country_name" => "Chile",
            "country_code" => "CL",
            "latitude" => -33.45,
            "longitude" => -70.66667,
            "zone_name" => "America/Santiago",
            "abbreviation" => "SMT",
            "offset" => -16966,
            "diff" => [
                "hours" => -5,
                "minutes" => -42,
                "seconds" => -46
            ],
            "dst" => false,
            "comments" => "Chile (most areas)"
        ],
        "Asia/Kuala_Lumpur" => [
            "country_name" => "Malaysia",
            "country_code" => "MY",
            "latitude" => 3.166659999999993,
            "longitude" => 101.69999999999999,
            "zone_name" => "Asia/Kuala_Lumpur",
            "abbreviation" => "SMT",
            "offset" => 24925,
            "diff" => [
                "hours" => 6,
                "minutes" => 55,
                "seconds" => 25
            ],
            "dst" => false,
            "comments" => "Malaysia (peninsula)"
        ],
        "Asia/Singapore" => [
            "country_name" => "Singapore",
            "country_code" => "SG",
            "latitude" => 1.2833300000000065,
            "longitude" => 103.85000000000002,
            "zone_name" => "Asia/Singapore",
            "abbreviation" => "SMT",
            "offset" => 24925,
            "diff" => [
                "hours" => 6,
                "minutes" => 55,
                "seconds" => 25
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Pacific/Midway" => [
            "country_name" => "United States Minor Outlying Islands",
            "country_code" => "UM",
            "latitude" => 28.216660000000005,
            "longitude" => -177.36667,
            "zone_name" => "Pacific/Midway",
            "abbreviation" => "SST",
            "offset" => -39600,
            "diff" => [
                "hours" => -11,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Midway Islands"
        ],
        "Pacific/Pago_Pago" => [
            "country_name" => "American Samoa",
            "country_code" => "AS",
            "latitude" => -14.266670000000005,
            "longitude" => -170.7,
            "zone_name" => "Pacific/Pago_Pago",
            "abbreviation" => "SST",
            "offset" => -39600,
            "diff" => [
                "hours" => -11,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Tbilisi" => [
            "country_name" => "Georgia",
            "country_code" => "GE",
            "latitude" => 41.71665999999999,
            "longitude" => 44.81666000000001,
            "zone_name" => "Asia/Tbilisi",
            "abbreviation" => "TBMT",
            "offset" => 10751,
            "diff" => [
                "hours" => 2,
                "minutes" => 59,
                "seconds" => 11
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Tehran" => [
            "country_name" => "Iran",
            "country_code" => "IR",
            "latitude" => 35.66665999999999,
            "longitude" => 51.43333000000001,
            "zone_name" => "Asia/Tehran",
            "abbreviation" => "TMT",
            "offset" => 12344,
            "diff" => [
                "hours" => 3,
                "minutes" => 25,
                "seconds" => 44
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Ndjamena" => [
            "country_name" => "Chad",
            "country_code" => "TD",
            "latitude" => 12.116659999999996,
            "longitude" => 15.050000000000011,
            "zone_name" => "Africa/Ndjamena",
            "abbreviation" => "WAT",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Brazzaville" => [
            "country_name" => "Republic of the Congo",
            "country_code" => "CG",
            "latitude" => -4.266670000000005,
            "longitude" => 15.283330000000007,
            "zone_name" => "Africa/Brazzaville",
            "abbreviation" => "WAT",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Bangui" => [
            "country_name" => "Central African Republic",
            "country_code" => "CF",
            "latitude" => 4.366659999999996,
            "longitude" => 18.58332999999999,
            "zone_name" => "Africa/Bangui",
            "abbreviation" => "WAT",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Douala" => [
            "country_name" => "Cameroon",
            "country_code" => "CM",
            "latitude" => 4.049999999999997,
            "longitude" => 9.699999999999989,
            "zone_name" => "Africa/Douala",
            "abbreviation" => "WAT",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Kinshasa" => [
            "country_name" => "Democratic Republic of the Congo",
            "country_code" => "CD",
            "latitude" => -4.299999999999997,
            "longitude" => 15.300000000000011,
            "zone_name" => "Africa/Kinshasa",
            "abbreviation" => "WAT",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Dem. Rep. of Congo (west)"
        ],
        "Africa/Lagos" => [
            "country_name" => "Nigeria",
            "country_code" => "NG",
            "latitude" => 6.450000000000003,
            "longitude" => 3.4000000000000057,
            "zone_name" => "Africa/Lagos",
            "abbreviation" => "WAT",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Libreville" => [
            "country_name" => "Gabon",
            "country_code" => "GA",
            "latitude" => 0.38333000000000084,
            "longitude" => 9.449999999999989,
            "zone_name" => "Africa/Libreville",
            "abbreviation" => "WAT",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Luanda" => [
            "country_name" => "Angola",
            "country_code" => "AO",
            "latitude" => -8.799999999999997,
            "longitude" => 13.233329999999995,
            "zone_name" => "Africa/Luanda",
            "abbreviation" => "WAT",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Malabo" => [
            "country_name" => "Equatorial Guinea",
            "country_code" => "GQ",
            "latitude" => 3.75,
            "longitude" => 8.783330000000007,
            "zone_name" => "Africa/Malabo",
            "abbreviation" => "WAT",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Niamey" => [
            "country_name" => "Niger",
            "country_code" => "NE",
            "latitude" => 13.516660000000002,
            "longitude" => 2.116659999999996,
            "zone_name" => "Africa/Niamey",
            "abbreviation" => "WAT",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Africa/Porto-Novo" => [
            "country_name" => "Benin",
            "country_code" => "BJ",
            "latitude" => 6.483329999999995,
            "longitude" => 2.616659999999996,
            "zone_name" => "Africa/Porto-Novo",
            "abbreviation" => "WAT",
            "offset" => 3600,
            "diff" => [
                "hours" => 1,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Atlantic/Canary" => [
            "country_name" => "Spain",
            "country_code" => "ES",
            "latitude" => 28.099999999999994,
            "longitude" => -15.400000000000006,
            "zone_name" => "Atlantic/Canary",
            "abbreviation" => "WET",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "Canary Islands"
        ],
        "Atlantic/Faroe" => [
            "country_name" => "Faroe Islands",
            "country_code" => "FO",
            "latitude" => 62.01666,
            "longitude" => -6.766670000000005,
            "zone_name" => "Atlantic/Faroe",
            "abbreviation" => "WET",
            "offset" => 0,
            "diff" => [
                "hours" => 0,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => ""
        ],
        "Asia/Jayapura" => [
            "country_name" => "Indonesia",
            "country_code" => "ID",
            "latitude" => -2.5333399999999955,
            "longitude" => 140.7,
            "zone_name" => "Asia/Jayapura",
            "abbreviation" => "WIT",
            "offset" => 32400,
            "diff" => [
                "hours" => 9,
                "minutes" => 0,
                "seconds" => 0
            ],
            "dst" => false,
            "comments" => "New Guinea (West Papua / Irian Jaya); Malukus/Moluccas"
        ]
    ];

    /**
     * @return array[]
     */
    public function getTimezone(): array
    {
        return $this->timezone;
    }

    /**
     * @param string $code
     * @return array
     */
    public function getTimeZonesByCountryCode(string $code) : array
    {
        $result = [];
        $code = strtoupper($code);
        $length = strlen($code);
        if ($length > 3 || $length < 2) {
            return $result;
        }

        if ($length === 3) {
            $code = (new Country())->getCountryByCode($code);
            if (!$code) {
                return $result;
            }
            $code = $code['code']['alpha2'];
        }
        foreach ($this->getTimezone() as $key => $value) {
            if ($value['country_code'] === $code) {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
