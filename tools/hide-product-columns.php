<?php
define('FS_METHOD','direct');
require '/var/www/okoem/wp/wp-load.php';
$apply = in_array('--apply', $argv, true);
$add = array('global_unique_id','product_tag','taxonomy-oko_collection','taxonomy-oko_series','taxonomy-oko_subject','taxonomy-oko_color');
foreach (get_users() as $u) {
    $hidden = get_user_meta($u->ID, 'manageedit-productcolumnshidden', true);
    if (!is_array($hidden)) { continue; }
    $new = array_values(array_unique(array_merge($hidden, $add)));
    if ($new === $hidden) { printf("  %-14s без изменений\n", $u->user_login); continue; }
    printf("  %-14s было: %s\n                 станет: %s\n", $u->user_login, implode(', ', $hidden), implode(', ', $new));
    if ($apply) { update_user_meta($u->ID, 'manageedit-productcolumnshidden', $new); }
}
echo $apply ? "применено\n" : "предпросмотр, для записи добавьте --apply\n";
