<?php
define('FS_METHOD','direct');
require '/var/www/okoem/wp/wp-load.php';

$apply = in_array('--apply', $argv, true);
$dir   = '/root/legal';

$pages = array(
    'privacy' => array(
        'title' => 'Политика обработки персональных данных',
        'file'  => $dir . '/page-privacy.html',
        'from'  => 'policy',
    ),
    'consent' => array(
        'title' => 'Согласие на обработку персональных данных',
        'file'  => $dir . '/page-consent.html',
    ),
    'consent-newsletter' => array(
        'title' => 'Согласие на получение рассылки',
        'file'  => $dir . '/page-newsletter.html',
    ),
);

foreach ($pages as $slug => $cfg) {
    $content = (string) file_get_contents($cfg['file']);
    if ('' === trim($content)) {
        echo "  НЕТ ТЕКСТА: {$cfg['file']}\n";
        continue;
    }

    $existing = get_page_by_path($slug, OBJECT, 'page');
    $source   = $existing;

    if (!$source && !empty($cfg['from'])) {
        $source = get_page_by_path($cfg['from'], OBJECT, 'page');
    }

    if ($source) {
        printf("  %-20s обновляю #%d (%s)\n", $slug, $source->ID, $source->post_name);
        if ($apply) {
            $was = $source->post_name;

            wp_update_post(array(
                'ID'           => $source->ID,
                'post_name'    => $slug,
                'post_title'   => $cfg['title'],
                'post_content' => $content,
                'post_status'  => 'publish',
            ));

            if ($was !== $slug && !in_array($was, (array) get_post_meta($source->ID, '_wp_old_slug', false), true)) {
                add_post_meta($source->ID, '_wp_old_slug', $was);
                echo "    запомнил старый адрес /$was/\n";
            }
        }
        continue;
    }

    printf("  %-20s создаю новую\n", $slug);
    if ($apply) {
        wp_insert_post(array(
            'post_type'    => 'page',
            'post_name'    => $slug,
            'post_title'   => $cfg['title'],
            'post_content' => $content,
            'post_status'  => 'publish',
        ));
    }
}

if ($apply) {
    flush_rewrite_rules(false);
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '%sitemap%' AND option_name LIKE '\_transient\_%'");
    echo "страницы записаны, правила ссылок и кеш карты сайта сброшены\n";
} else {
    echo "предпросмотр, изменений не внесено\n";
}
