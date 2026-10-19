<?php
define('FS_METHOD','direct');
require '/var/www/okoem/wp/wp-load.php';

$apply = in_array('--apply', $argv, true);
$file  = '/root/seo63.json';
$rows  = json_decode((string) file_get_contents($file), true);
if (!is_array($rows)) { fwrite(STDERR, "не прочитал $file\n"); exit(1); }

function oko_sku_key(string $s): string {
    return mb_strtolower(str_replace(array(' ', "\xc2\xa0"), '', trim($s)), 'UTF-8');
}

$bySku = array();
foreach (get_posts(array('post_type'=>'product','post_status'=>'publish','numberposts'=>-1,'fields'=>'ids')) as $id) {
    $sku = oko_sku_key((string) get_post_meta($id, '_sku', true));
    if ('' !== $sku) { $bySku[$sku] = (int) $id; }
}

$stat = array('h1'=>0,'title'=>0,'desc'=>0,'slug'=>0,'palette'=>0,'skip'=>0);
$log  = array();

foreach ($rows as $r) {
    $id = $bySku[oko_sku_key((string) $r['Артикул'])] ?? 0;
    if (!$id) { $stat['skip']++; $log[] = "  НЕ НАЙДЕН артикул {$r['Артикул']}"; continue; }

    $post   = get_post($id);
    $h1     = trim((string) $r['H1']);
    $title  = trim((string) $r['Title']);
    $desc   = trim((string) $r['Meta description']);
    $slug   = trim((string) $r['Ярлык (slug)']);
    $pal    = trim((string) $r['Палитра JAPANDI (характеристика)']);
    $color  = trim((string) $r['Цвет (характеристика)']);
    $palTxt = trim((string) $r['Абзац о палитре на карточке']);

    $changes = array();

    if ('' !== $h1 && $h1 !== (string) get_post_meta($id, '_okoyom_h1', true)) {
        $changes[] = 'H1';
        if ($apply) { update_post_meta($id, '_okoyom_h1', $h1); }
        $stat['h1']++;
    }
    if ('' !== $title && $title !== (string) get_post_meta($id, 'rank_math_title', true)) {
        $changes[] = 'title';
        if ($apply) { update_post_meta($id, 'rank_math_title', $title); }
        $stat['title']++;
    }
    if ('' !== $desc && $desc !== (string) get_post_meta($id, 'rank_math_description', true)) {
        $changes[] = 'description';
        if ($apply) { update_post_meta($id, 'rank_math_description', $desc); }
        $stat['desc']++;
    }
    if ('' !== $pal && $pal !== (string) get_post_meta($id, '_okoyom_palette', true)) {
        $changes[] = 'палитра';
        if ($apply) {
            update_post_meta($id, '_okoyom_palette', $pal);
            update_post_meta($id, '_okoyom_palette_text', $palTxt);
            if ('' !== $color) { update_post_meta($id, '_okoyom_color_name', $color); }
        }
        $stat['palette']++;
    }
    if ('' !== $slug && $slug !== $post->post_name) {
        $changes[] = "адрес {$post->post_name} -> $slug";
        if ($apply) {
            add_post_meta($id, '_wp_old_slug', $post->post_name);
            wp_update_post(array('ID'=>$id, 'post_name'=>$slug));
        }
        $stat['slug']++;
    }

    if ($changes) { $log[] = sprintf("  %-7s %s", $r['Артикул'], implode('; ', $changes)); }
}

echo implode("\n", array_slice($log, 0, 80)), "\n";
printf("\nстрок: %d | H1: %d, title: %d, description: %d, палитра: %d, адрес: %d, не найдено: %d\n",
    count($rows), $stat['h1'], $stat['title'], $stat['desc'], $stat['palette'], $stat['slug'], $stat['skip']);
echo $apply ? "ПРИМЕНЕНО\n" : "предпросмотр, для записи добавьте --apply\n";

if ($apply) {
    flush_rewrite_rules(false);
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%transient%sitemap%'");
}
