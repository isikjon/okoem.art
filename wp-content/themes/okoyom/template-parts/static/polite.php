<?php
defined( 'ABSPATH' ) || exit;

$okoyom_legal_id = get_queried_object_id();
?>
<section class="inspirationTop">
    <div class="container">
        <div class="title-inspirationTop">
            <h1 class="title-inspirationTop__title">
                <?php echo esc_html( get_the_title( $okoyom_legal_id ) ); ?>
            </h1>
        </div>
    </div>
    <div class="container">
        <div class="containerPoliteText">
            <?php echo apply_filters( 'the_content', (string) get_post_field( 'post_content', $okoyom_legal_id ) ); ?>
        </div>
    </div>
</section>
