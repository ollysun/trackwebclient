<?php

return [
    'adminEmail' => 'admin@example.com',
    'apiUrl' => getenv('API_URL') !== false ? getenv('API_URL') : "http://local.courierplus.tntservice.com/",
    'cacheAppPrefix' => 'track_plus::'
];
