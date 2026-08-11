<?php
defined( 'ABSPATH' ) || exit;
?>
<section class="inspirationTop">
    <div class="container">
        <div class="title-inspirationTop">
            <h1 class="title-inspirationTop__title">
                <?php echo okoyom_t( 'b7697b9693d2', 'Корзина' ); ?>
            </h1>
            <p class="title-inspirationTop__text">
                <?php echo okoyom_t( 'c97e1b84d2f2', 'Ваша корзина пуста' ); ?>
            </p>
        </div>
    </div>
    <div class="container">
        <div class="likeContentWrapperFlex likeContentWrapperFlex-1">
            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/bagPage.svg" alt="" width="80" height="80" loading="lazy" decoding="async" aria-hidden="true">
            <h3>
                <?php echo okoyom_t( 'bf65d9bdeb83', 'Корзина пуста' ); ?>
            </h3>
            <p>
                <?php echo okoyom_t( '7ff4b8164fbf', 'Добавьте товары из каталога' ); ?>
            </p>
            <a style="width: fit-content" href="/catalog/" class="material-link openModal">
                <?php echo okoyom_t( 'ef15998d4730', 'Перейти в каталог →' ); ?>
            </a>
        </div>
    </div>


</section>
