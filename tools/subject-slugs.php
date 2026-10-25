<?php
define('FS_METHOD','direct');
require '/var/www/okoem/wp/wp-load.php';

$apply = in_array('--apply', $argv, true);

$map = array(
    'landscape'   => 'peyzazhi',
    'forest'      => 'les',
    'mountains'   => 'gory',
    'flowers'     => 'tsvety',
    'leaves'      => 'listva',
    'abstraction' => 'abstraktsiya',
    'geometry'    => 'geometriya',
    'botany'      => 'botanika',
    'gradients'   => 'gradienty',
    'figurative'  => 'figurativnye',
);

$done = 0;
$skip = 0;

foreach ($map as $from => $to) {
    $term = get_term_by('slug', $from, 'oko_subject');

    if (!$term) {
        $exists = get_term_by('slug', $to, 'oko_subject');
        if ($exists) {
            echo "  уже переименован: $from -> $to\n";
            $skip++;
            continue;
        }
        echo "  НЕ НАЙДЕН: $from\n";
        $skip++;
        continue;
    }

    echo sprintf("  %-12s -> %-14s %s\n", $from, $to, $term->name);

    if ($apply) {
        $res = wp_update_term($term->term_id, 'oko_subject', array('slug' => $to));
        if (is_wp_error($res)) {
            echo "    ОШИБКА: " . $res->get_error_message() . "\n";
            continue;
        }
        okoyom_term_remember_slug((int) $term->term_id, $from);
        $done++;
    }
}

if ($apply) {
    flush_rewrite_rules(false);
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%sitemap%' AND option_name LIKE '\_transient\_%'");
    echo "переименовано: $done, пропущено: $skip, правила ссылок и кеш карты сайта сброшены\n";
} else {
    echo "предпросмотр, изменений не внесено (пропущено: $skip)\n";
}
