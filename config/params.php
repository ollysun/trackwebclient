<?php

return [
    'adminEmail' => 'admin@example.com',
    'apiUrl' => getenv('API_URL') !== false ? getenv('API_URL') : "http://localhost:82/trackplus/webservice/",
    'cacheAppPrefix' => 'track_plus::'
];
