<?php
return [
  'DB' => [
    'HOST'    => getenv('DB_HOST') ?: 'db',
    'PORT'    => (int)(getenv('DB_PORT') ?: 3306),
    'NAME'    => getenv('DB_NAME') ?: 'projeto_rafaelnasser',
    'USER'    => getenv('DB_USER') ?: 'tccuser',
    'PASS'    => getenv('DB_PASS') ?: 'senhaFort3!',
    'CHARSET' => getenv('DB_CHARSET') ?: 'utf8mb4',
  ],
];
if (!defined('OPENAI_API_KEY')) {
  define('OPENAI_API_KEY', getenv('OPENAI_API_KEY') ?: '');
}