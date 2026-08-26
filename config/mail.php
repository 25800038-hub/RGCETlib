<?php
// Load environment variables from .env file if it exists
$envFile = __DIR__ . '/../.env';
$envVars = [];
if (file_exists($envFile)) {
    $envVars = parse_ini_file($envFile);
}

// Helper to get env var
if (!function_exists('getEnvVal')) {
    function getEnvVal($key, $default, $envVars) {
        if (isset($envVars[$key])) return $envVars[$key];
        $val = getenv($key);
        return $val !== false ? $val : $default;
    }
}

return [
    'host' => getEnvVal('SMTP_HOST', 'sandbox.smtp.mailtrap.io', $envVars),
    'port' => (int)getEnvVal('SMTP_PORT', 2525, $envVars),
    'username' => getEnvVal('SMTP_USERNAME', '801a88a0348164', $envVars),
    'password' => getEnvVal('SMTP_PASSWORD', 'aecd8cf5cc7aab', $envVars),
    'encryption' => getEnvVal('SMTP_ENCRYPTION', 'tls', $envVars),
    'from_email' => getEnvVal('SMTP_FROM_EMAIL', 'library@rgcet.edu.in', $envVars),
    'from_name' => getEnvVal('SMTP_FROM_NAME', 'RGCET Library', $envVars)
];
