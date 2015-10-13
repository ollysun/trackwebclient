<?php

return [
    'adminEmail' => 'admin@example.com',
//    'apiUrl' => getenv('API_URL') !== false ? getenv('API_URL') : "http://local.courierplus.tntservice.com/",
    'apiUrl' => getenv('API_URL') !== false ? getenv('API_URL') : "http://staging-tnt-service.cottacush.com/",
    'cacheAppPrefix' => 'track_plus::'
];
