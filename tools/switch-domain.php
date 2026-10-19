<?php
define('FS_METHOD','direct');
require '/var/www/okoem/wp/wp-load.php';

global $wpdb;

$apply = in_array('--apply', $argv, true);
$from  = '186.246.12.230';
$to    = 'okoem.art';

foreach ($argv as $a) {
    if (0 === strpos($a, '--from=')) { $from = substr($a, 7); }
    if (0 === strpos($a, '--to='))   { $to   = substr($a, 5); }
}

$pairs = array(
    'http://'  . $from => 'https://' . $to,
    'https://' . $from => 'https://' . $to,
    '//'       . $from => '//'       . $to,
);

function oko_swap($value, array $pairs, int &$hits) {
    if (is_string($value)) {
        $before = $value;
        foreach ($pairs as $a => $b) { $value = str_replace($a, $b, $value); }
        if ($value !== $before) { $hits++; }
        return $value;
    }
    if (is_array($value)) {
        foreach ($value as $k => $v) { $value[$k] = oko_swap($v, $pairs, $hits); }
        return $value;
    }
    if (is_object($value)) {
        foreach (get_object_vars($value) as $k => $v) { $value->$k = oko_swap($v, $pairs, $hits); }
        return $value;
    }
    return $value;
}

$targets = array(
    array($wpdb->posts,    'ID',       'post_content'),
    array($wpdb->posts,    'ID',       'post_excerpt'),
    array($wpdb->postmeta, 'meta_id',  'meta_value'),
    array($wpdb->termmeta, 'meta_id',  'meta_value'),
    array($wpdb->options,  'option_id','option_value'),
);

$total = 0;
$log   = array();

foreach ($targets as list($table, $id, $col)) {
    $rows = $wpdb->get_results(
        $wpdb->prepare("SELECT `$id` AS id, `$col` AS val FROM `$table` WHERE `$col` LIKE %s", '%' . $wpdb->esc_like($from) . '%')
    );

    foreach ($rows as $row) {
        if ($table === $wpdb->options) {
            $name = $wpdb->get_var($wpdb->prepare("SELECT option_name FROM `$table` WHERE option_id = %d", $row->id));
            if (0 === strpos((string) $name, '_transient_') || 0 === strpos((string) $name, '_site_transient_')) {
                $log[] = "  удаляю транзиент $name";
                if ($apply) { $wpdb->delete($table, array('option_id' => $row->id)); }
                $total++;
                continue;
            }
        }

        $hits  = 0;
        $value = oko_swap(maybe_unserialize($row->val), $pairs, $hits);
        if (!$hits) { continue; }

        $new = maybe_serialize($value);
        if ($new === $row->val) { continue; }

        $log[] = sprintf('  %s #%d (%s): %d замен', $table, $row->id, $col, $hits);
        $total++;

        if ($apply) {
            $wpdb->query($wpdb->prepare("UPDATE `$table` SET `$col` = %s WHERE `$id` = %d", $new, $row->id));
        }
    }
}

echo implode("\n", $log), "\n";
echo ($apply ? 'заменено' : 'нашёл'), ": $total строк, $from -> $to\n";

if ($apply) {
    wp_cache_flush();
    flush_rewrite_rules(false);
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%sitemap%' AND option_name LIKE '\_transient\_%'");
    echo "правила ссылок и кеш карты сайта сброшены\n";
}
