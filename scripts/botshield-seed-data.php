<?php

/**
 * @file
 * Genera datos de prueba (demo) para el módulo BotShield.
 *
 * Rellena las tablas botshield_events, botshield_blocks y botshield_alerts
 * con datos realistas (bots, geolocalización, lat/lon) para poder
 * visualizar el Dashboard (/admin/reports/botshield) y el Map
 * (/admin/reports/botshield/map).
 *
 * Uso:
 *   drush scr scripts/botshield-seed-data.php
 *
 * Opciones (variables de entorno):
 *   BOTSHIELD_SEED_EVENTS=5000   Número de eventos a generar (default 3000).
 *   BOTSHIELD_SEED_DAYS=14       Rango de días hacia atrás (default 14).
 *   BOTSHIELD_SEED_TRUNCATE=1    Si se define, vacía las tablas antes de insertar.
 */

use Drupal\Core\Database\Database;

$database = Database::getConnection();

$num_events = (int) (getenv('BOTSHIELD_SEED_EVENTS') ?: 3000);
$days_back = (int) (getenv('BOTSHIELD_SEED_DAYS') ?: 14);
$truncate = (bool) getenv('BOTSHIELD_SEED_TRUNCATE');

// -----------------------------------------------------------------------
// Catálogo de ciudades/regiones con coordenadas reales para el mapa.
// -----------------------------------------------------------------------
$locations = [
  ['country' => 'US', 'region' => 'California', 'city' => 'San Francisco', 'lat' => 37.7749, 'lon' => -122.4194],
  ['country' => 'US', 'region' => 'Virginia', 'city' => 'Ashburn', 'lat' => 39.0438, 'lon' => -77.4874],
  ['country' => 'US', 'region' => 'New York', 'city' => 'New York', 'lat' => 40.7128, 'lon' => -74.0060],
  ['country' => 'US', 'region' => 'Washington', 'city' => 'Seattle', 'lat' => 47.6062, 'lon' => -122.3321],
  ['country' => 'US', 'region' => 'Texas', 'city' => 'Dallas', 'lat' => 32.7767, 'lon' => -96.7970],
  ['country' => 'CA', 'region' => 'Ontario', 'city' => 'Toronto', 'lat' => 43.6532, 'lon' => -79.3832],
  ['country' => 'GB', 'region' => 'England', 'city' => 'London', 'lat' => 51.5074, 'lon' => -0.1278],
  ['country' => 'DE', 'region' => 'Hesse', 'city' => 'Frankfurt', 'lat' => 50.1109, 'lon' => 8.6821],
  ['country' => 'DE', 'region' => 'Berlin', 'city' => 'Berlin', 'lat' => 52.5200, 'lon' => 13.4050],
  ['country' => 'FR', 'region' => 'Ile-de-France', 'city' => 'Paris', 'lat' => 48.8566, 'lon' => 2.3522],
  ['country' => 'NL', 'region' => 'North Holland', 'city' => 'Amsterdam', 'lat' => 52.3676, 'lon' => 4.9041],
  ['country' => 'IE', 'region' => 'Leinster', 'city' => 'Dublin', 'lat' => 53.3498, 'lon' => -6.2603],
  ['country' => 'ES', 'region' => 'Madrid', 'city' => 'Madrid', 'lat' => 40.4168, 'lon' => -3.7038],
  ['country' => 'ES', 'region' => 'Catalonia', 'city' => 'Barcelona', 'lat' => 41.3851, 'lon' => 2.1734],
  ['country' => 'PT', 'region' => 'Lisbon', 'city' => 'Lisbon', 'lat' => 38.7223, 'lon' => -9.1393],
  ['country' => 'IT', 'region' => 'Lazio', 'city' => 'Rome', 'lat' => 41.9028, 'lon' => 12.4964],
  ['country' => 'SE', 'region' => 'Stockholm', 'city' => 'Stockholm', 'lat' => 59.3293, 'lon' => 18.0686],
  ['country' => 'FI', 'region' => 'Uusimaa', 'city' => 'Helsinki', 'lat' => 60.1699, 'lon' => 24.9384],
  ['country' => 'RU', 'region' => 'Moscow', 'city' => 'Moscow', 'lat' => 55.7558, 'lon' => 37.6173],
  ['country' => 'CN', 'region' => 'Beijing', 'city' => 'Beijing', 'lat' => 39.9042, 'lon' => 116.4074],
  ['country' => 'CN', 'region' => 'Shanghai', 'city' => 'Shanghai', 'lat' => 31.2304, 'lon' => 121.4737],
  ['country' => 'JP', 'region' => 'Tokyo', 'city' => 'Tokyo', 'lat' => 35.6762, 'lon' => 139.6503],
  ['country' => 'KR', 'region' => 'Seoul', 'city' => 'Seoul', 'lat' => 37.5665, 'lon' => 126.9780],
  ['country' => 'IN', 'region' => 'Maharashtra', 'city' => 'Mumbai', 'lat' => 19.0760, 'lon' => 72.8777],
  ['country' => 'IN', 'region' => 'Karnataka', 'city' => 'Bangalore', 'lat' => 12.9716, 'lon' => 77.5946],
  ['country' => 'SG', 'region' => 'Singapore', 'city' => 'Singapore', 'lat' => 1.3521, 'lon' => 103.8198],
  ['country' => 'AU', 'region' => 'New South Wales', 'city' => 'Sydney', 'lat' => -33.8688, 'lon' => 151.2093],
  ['country' => 'BR', 'region' => 'Sao Paulo', 'city' => 'Sao Paulo', 'lat' => -23.5505, 'lon' => -46.6333],
  ['country' => 'MX', 'region' => 'CDMX', 'city' => 'Mexico City', 'lat' => 19.4326, 'lon' => -99.1332],
  ['country' => 'AR', 'region' => 'Buenos Aires', 'city' => 'Buenos Aires', 'lat' => -34.6037, 'lon' => -58.3816],
  ['country' => 'ZA', 'region' => 'Gauteng', 'city' => 'Johannesburg', 'lat' => -26.2041, 'lon' => 28.0473],
  ['country' => 'NG', 'region' => 'Lagos', 'city' => 'Lagos', 'lat' => 6.5244, 'lon' => 3.3792],
  ['country' => 'AE', 'region' => 'Dubai', 'city' => 'Dubai', 'lat' => 25.2048, 'lon' => 55.2708],
  ['country' => 'PL', 'region' => 'Masovian', 'city' => 'Warsaw', 'lat' => 52.2297, 'lon' => 21.0122],
  ['country' => 'UA', 'region' => 'Kyiv', 'city' => 'Kyiv', 'lat' => 50.4501, 'lon' => 30.5234],
  ['country' => 'TR', 'region' => 'Istanbul', 'city' => 'Istanbul', 'lat' => 41.0082, 'lon' => 28.9784],
  ['country' => 'ID', 'region' => 'Jakarta', 'city' => 'Jakarta', 'lat' => -6.2088, 'lon' => 106.8456],
  ['country' => 'VN', 'region' => 'Hanoi', 'city' => 'Hanoi', 'lat' => 21.0285, 'lon' => 105.8542],
];

// -----------------------------------------------------------------------
// Catálogo de bots (alineado con BotCatalog::knownBots()).
// -----------------------------------------------------------------------
$bots = [
  ['label' => 'openai_gptbot', 'ua' => 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko); compatible; GPTBot/1.2; +https://openai.com/gptbot'],
  ['label' => 'openai_chatgpt_user', 'ua' => 'Mozilla/5.0 (compatible; ChatGPT-User/1.0; +https://openai.com/bot)'],
  ['label' => 'anthropic_claudebot', 'ua' => 'Mozilla/5.0 (compatible; ClaudeBot/1.0; +claudebot@anthropic.com)'],
  ['label' => 'perplexitybot', 'ua' => 'Mozilla/5.0 (compatible; PerplexityBot/1.0; +https://perplexity.ai/bot)'],
  ['label' => 'ccbot', 'ua' => 'CCBot/2.0 (https://commoncrawl.org/faq/)'],
  ['label' => 'amazonbot', 'ua' => 'Mozilla/5.0 (compatible; Amazonbot/0.1; +https://developer.amazon.com/support/amazonbot)'],
  ['label' => 'google_extended', 'ua' => 'Mozilla/5.0 (compatible; Google-Extended)'],
  ['label' => 'googlebot', 'ua' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)'],
  ['label' => 'bingbot', 'ua' => 'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)'],
  ['label' => 'yandexbot', 'ua' => 'Mozilla/5.0 (compatible; YandexBot/3.0; +http://yandex.com/bots)'],
  ['label' => 'bytespider', 'ua' => 'Mozilla/5.0 (Linux; Android 5.0) AppleWebKit/537.36 (KHTML, like Gecko) Mobile Safari/537.36 (compatible; Bytespider; spider-feedback@bytedance.com)'],
  ['label' => 'baiduspider', 'ua' => 'Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.baidu.com/search/spider.html)'],
  ['label' => 'ahrefs', 'ua' => 'Mozilla/5.0 (compatible; AhrefsBot/7.0; +http://ahrefs.com/robot/)'],
  ['label' => 'semrush', 'ua' => 'Mozilla/5.0 (compatible; SemrushBot/7~bl; +http://www.semrush.com/bot.html)'],
  ['label' => 'mj12bot', 'ua' => 'Mozilla/5.0 (compatible; MJ12bot/v1.4.8; http://mj12bot.com/)'],
  ['label' => 'facebookexternalhit', 'ua' => 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)'],
  ['label' => 'twitterbot', 'ua' => 'Twitterbot/1.0'],
  ['label' => 'discordbot', 'ua' => 'Mozilla/5.0 (compatible; Discordbot/2.0; +https://discordapp.com)'],
  ['label' => 'linkedinbot', 'ua' => 'LinkedInBot/1.0 (compatible; Mozilla/5.0; +http://www.linkedin.com)'],
  ['label' => NULL, 'ua' => 'python-requests/2.31.0'],
  ['label' => NULL, 'ua' => 'curl/8.4.0'],
  ['label' => NULL, 'ua' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'],
];

$paths = [
  ['path' => '/', 'group' => 'page'],
  ['path' => '/node/1', 'group' => 'page'],
  ['path' => '/node/2', 'group' => 'page'],
  ['path' => '/articulos/noticia-destacada', 'group' => 'page'],
  ['path' => '/search?q=drupal', 'group' => 'search'],
  ['path' => '/api/v1/items', 'group' => 'api'],
  ['path' => '/api/v1/users', 'group' => 'api'],
  ['path' => '/sites/default/files/image.jpg', 'group' => 'asset'],
  ['path' => '/user/login', 'group' => 'auth'],
  ['path' => '/robots.txt', 'group' => 'misc'],
  ['path' => '/sitemap.xml', 'group' => 'misc'],
  ['path' => '/rss.xml', 'group' => 'feed'],
];

$event_types = ['allowed', 'allowed', 'allowed', 'allowed', 'rate_limited', 'blocked'];
$block_reasons = ['rate_limit', 'policy:block', 'bad_ua', 'honeypot'];

function botshield_seed_random_ip(string $country): string {
  // Genera IPv4 pseudoaleatorias (no necesitan ser reales, solo verosímiles).
  return mt_rand(1, 223) . '.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(1, 254);
}

$now = \Drupal::time()->getRequestTime();
$range_start = $now - ($days_back * 86400);

if ($truncate) {
  $database->truncate('botshield_events')->execute();
  $database->truncate('botshield_blocks')->execute();
  $database->truncate('botshield_alerts')->execute();
  echo "Tablas botshield_events, botshield_blocks, botshield_alerts vaciadas.\n";
}

// -----------------------------------------------------------------------
// 1) Generar eventos (botshield_events).
// -----------------------------------------------------------------------
$events_insert = $database->insert('botshield_events')->fields([
  'event_type', 'reason', 'ip', 'ip_hash', 'bot_label', 'user_agent',
  'path', 'path_group', 'region', 'country', 'city', 'lat', 'lon',
  'request_count', 'limit', 'window', 'created',
]);

$batch_count = 0;
$blocks_cache = [];

for ($i = 0; $i < $num_events; $i++) {
  $loc = $locations[array_rand($locations)];
  $bot = $bots[array_rand($bots)];
  $path = $paths[array_rand($paths)];
  $event_type = $event_types[array_rand($event_types)];
  $created = mt_rand($range_start, $now);
  $ip = botshield_seed_random_ip($loc['country']);
  $ip_hash = hash('sha256', $ip);
  $request_count = mt_rand(1, 250);
  $limit = 120;
  $window = 60;

  $reason = NULL;
  if ($event_type === 'rate_limited') {
    $reason = 'flood_control:' . ($bot['label'] ?? 'unknown');
  }
  elseif ($event_type === 'blocked') {
    $reason = $block_reasons[array_rand($block_reasons)];
  }

  $events_insert->values([
    'event_type' => $event_type,
    'reason' => $reason,
    'ip' => $ip,
    'ip_hash' => $ip_hash,
    'bot_label' => $bot['label'],
    'user_agent' => $bot['ua'],
    'path' => $path['path'],
    'path_group' => $path['group'],
    'region' => $loc['region'],
    'country' => $loc['country'],
    'city' => $loc['city'],
    'lat' => $loc['lat'] + (mt_rand(-50, 50) / 1000),
    'lon' => $loc['lon'] + (mt_rand(-50, 50) / 1000),
    'request_count' => $request_count,
    'limit' => $limit,
    'window' => $window,
    'created' => $created,
  ]);

  if ($event_type !== 'allowed') {
    $blocks_cache[] = [
      'ip' => $ip,
      'ip_hash' => $ip_hash,
      'bot_label' => $bot['label'],
      'region' => $loc['region'],
      'country' => $loc['country'],
      'city' => $loc['city'],
      'reason' => $reason ?: 'rate_limit',
      'path_group' => $path['group'],
      'created' => $created,
    ];
  }

  $batch_count++;
  // Insertar por lotes de 500 para no acumular toda la query en memoria.
  if ($batch_count % 500 === 0) {
    $events_insert->execute();
    $events_insert = $database->insert('botshield_events')->fields([
      'event_type', 'reason', 'ip', 'ip_hash', 'bot_label', 'user_agent',
      'path', 'path_group', 'region', 'country', 'city', 'lat', 'lon',
      'request_count', 'limit', 'window', 'created',
    ]);
    echo "Insertados $batch_count eventos...\n";
  }
}
// Insertar el resto.
if ($batch_count % 500 !== 0) {
  $events_insert->execute();
}
echo "Total eventos insertados: $num_events\n";

// -----------------------------------------------------------------------
// 2) Generar bloqueos (botshield_blocks) a partir de una muestra de eventos
//    no "allowed", evitando duplicados por (ip_hash, path_group, reason).
// -----------------------------------------------------------------------
$seen = [];
$blocks_insert = $database->insert('botshield_blocks')->fields([
  'ip', 'ip_hash', 'bot_label', 'region', 'country', 'city', 'reason',
  'path_group', 'blocked_until', 'created', 'changed',
]);
$blocks_count = 0;

shuffle($blocks_cache);
foreach ($blocks_cache as $b) {
  $key = $b['ip_hash'] . '|' . $b['path_group'] . '|' . $b['reason'];
  if (isset($seen[$key])) {
    continue;
  }
  $seen[$key] = TRUE;

  $blocked_until = $b['reason'] === 'rate_limit' ? $b['created'] + mt_rand(60, 3600) : 0;

  $blocks_insert->values([
    'ip' => $b['ip'],
    'ip_hash' => $b['ip_hash'],
    'bot_label' => $b['bot_label'],
    'region' => $b['region'],
    'country' => $b['country'],
    'city' => $b['city'],
    'reason' => $b['reason'],
    'path_group' => $b['path_group'],
    'blocked_until' => $blocked_until,
    'created' => $b['created'],
    'changed' => $b['created'],
  ]);
  $blocks_count++;

  if ($blocks_count % 300 === 0) {
    $blocks_insert->execute();
    $blocks_insert = $database->insert('botshield_blocks')->fields([
      'ip', 'ip_hash', 'bot_label', 'region', 'country', 'city', 'reason',
      'path_group', 'blocked_until', 'created', 'changed',
    ]);
  }
}
if ($blocks_count % 300 !== 0 || $blocks_count === 0) {
  $blocks_insert->execute();
}
echo "Total bloqueos insertados: $blocks_count\n";

// -----------------------------------------------------------------------
// 3) Generar alertas (botshield_alerts).
// -----------------------------------------------------------------------
$alert_types = ['flood_spike', 'new_bot_detected', 'suspicious_ua', 'repeated_block'];
$alerts_insert = $database->insert('botshield_alerts')->fields([
  'created', 'incident_type', 'title', 'body', 'ip', 'host', 'bot_label',
  'path_group', 'path', 'user_agent', 'country', 'region', 'city',
  'threshold', 'window', 'block_seconds', 'context_json',
]);

$num_alerts = max(10, (int) ($num_events / 50));
for ($i = 0; $i < $num_alerts; $i++) {
  $loc = $locations[array_rand($locations)];
  $bot = $bots[array_rand($bots)];
  $path = $paths[array_rand($paths)];
  $incident_type = $alert_types[array_rand($alert_types)];
  $created = mt_rand($range_start, $now);
  $ip = botshield_seed_random_ip($loc['country']);
  $threshold = 120;
  $window = 60;

  $titles = [
    'flood_spike' => 'Pico de tráfico detectado',
    'new_bot_detected' => 'Nuevo bot no catalogado detectado',
    'suspicious_ua' => 'User-Agent sospechoso bloqueado',
    'repeated_block' => 'IP bloqueada repetidamente',
  ];

  $alerts_insert->values([
    'created' => $created,
    'incident_type' => $incident_type,
    'title' => $titles[$incident_type],
    'body' => sprintf(
      'Se detectó actividad de "%s" desde %s, %s (%s) hacia %s con %d solicitudes en %d segundos.',
      $bot['label'] ?? 'desconocido',
      $loc['city'],
      $loc['region'],
      $loc['country'],
      $path['path'],
      mt_rand(130, 900),
      $window
    ),
    'ip' => $ip,
    'host' => 'example-site.com',
    'bot_label' => $bot['label'],
    'path_group' => $path['group'],
    'path' => $path['path'],
    'user_agent' => $bot['ua'],
    'country' => $loc['country'],
    'region' => $loc['region'],
    'city' => $loc['city'],
    'threshold' => $threshold,
    'window' => $window,
    'block_seconds' => mt_rand(60, 3600),
    'context_json' => json_encode(['seed' => TRUE, 'generated_at' => $now]),
  ]);
}
$alerts_insert->execute();
echo "Total alertas insertadas: $num_alerts\n";

echo "\nListo. Visita /admin/reports/botshield y /admin/reports/botshield/map\n";
