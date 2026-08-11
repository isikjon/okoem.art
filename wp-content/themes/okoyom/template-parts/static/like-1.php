<?php
defined( 'ABSPATH' ) || exit;
?>
<section class="inspirationTop">
    <div class="container">
        <div class="title-inspirationTop">
            <h1 class="title-inspirationTop__title">
                <?php echo okoyom_t( '2fc413929104', 'Избранное' ); ?>
            </h1>
            <p class="title-inspirationTop__text">
                <?php echo okoyom_t( '4bbe9818b17c', 'Ваш список избранного пуст' ); ?>
            </p>
        </div>
    </div>
    <div class="container">
        <div class="likeContentWrapperFlex likeContentWrapperFlex-1">
            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/likePage.svg" alt="" width="80" height="80" loading="lazy" decoding="async" aria-hidden="true">
            <h3>
                <?php echo okoyom_t( 'f930cdb2b61b', 'Список пуст' ); ?>
            </h3>
            <p>
                <?php echo okoyom_t( 'd918c7389b94', 'Добавляйте понравившиеся муралы и панно в избранное, нажимая на иконку сердца' ); ?>
            </p>
            <a style="width: fit-content" href="/catalog/" class="material-link openModal">
                <?php echo okoyom_t( 'ef15998d4730', 'Перейти в каталог →' ); ?>
            </a>
        </div>
    </div>


</section>
