<?php
return [
    'adminEmail' => 'admin@example.com',
    'apiUrl' => getenv('API_URL') !== false ? getenv('API_URL') : "http://54.93.116.160/",
    //'apiUrl' => "http://54.93.116.160/",
    //'apiUrl' => "http://52.29.170.65/",
    //'apiUrl' => getenv('API_URL') !== false ? getenv('API_URL') : "http://trackplus-server.com:82/",
    'cacheAppPrefix' => 'track_plus::'
];