<?php
define('FS_METHOD','direct');
require '/var/www/okoem/wp/wp-load.php';

$apply = in_array('--apply', $argv, true);

$titles = (array) get_option('rank-math-options-titles', array());

$changes = array(
    'title_separator'                => '|',
    'tax_oko_subject_title'          => '%term% — фрески на стену, интерьерные муралы %sep% %sitename%',
    'tax_oko_subject_description'    => '%term% — авторские фрески на стену ОКОЁМ: интерьерные муралы с фото в интерьерах. Адаптация под размер, цветокоррекция, бесплатная визуализация.',
    'tax_oko_series_title'           => 'Серия «%term%» — интерьерные муралы, фрески на стену %sep% %sitename%',
    'tax_oko_series_description'     => 'Серия «%term%» — интерьерные муралы ОКОЁМ, фрески на стену с фото в интерьерах. Адаптация под размер, цветокоррекция, бесплатная визуализация.',
    'tax_oko_color_custom_robots'    => 'on',
);

foreach ($changes as $key => $value) {
    $now = $titles[$key] ?? null;
    $same = is_string($now) && $now === $value;
    printf("  %-32s %s\n", $key, $same ? 'уже стоит' : (is_string($now) ? mb_substr($now, 0, 40) . '  ->  ' . mb_substr($value, 0, 60) : 'не задано  ->  ' . mb_substr($value, 0, 60)));
    if ($apply) {
        $titles[$key] = $value;
    }
}

$robots_now = $titles['tax_oko_color_robots'] ?? null;
$robots_new = array('noindex');
printf("  %-32s %s\n", 'tax_oko_color_robots', json_encode($robots_now, 320) . '  ->  ' . json_encode($robots_new, 320));

if ($apply) {
    $titles['tax_oko_color_robots'] = $robots_new;
    update_option('rank-math-options-titles', $titles);

    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%sitemap%' AND option_name LIKE '\_transient\_%'");
    echo "настройки Rank Math обновлены, кеш карты сайта сброшен\n";
} else {
    echo "предпросмотр, изменений не внесено\n";
}
