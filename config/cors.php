<?php

return [
    'paths' => ['api/*', 'auth/google/callback'], // CORSを適用するパス
    'allowed_methods' => ['*'], // 許可するHTTPメソッド
    'allowed_origins' => ['http://localhost', 'https://todo-pdca-924718d95db9.herokuapp.com'], // 許可するオリジン
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'], // 許可するヘッダー
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
