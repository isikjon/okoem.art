<?php
defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header>
    <div class="container">
        <div class="flexHeader">
            <a class="logo" href="/">
                <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/logoHeader.svg" alt="Окоём" width="122" height="24" fetchpriority="high">
            </a>
            <?php okoyom_nav( 'primary', 'listHeader' ); ?>
            <div class="rightHeader">
                <div class="telHeaderFlex">
                    <a href="<?php echo esc_attr( okoyom_phone_href() ); ?>">
                        <?php echo esc_html( okoyom_option( 'phone' ) ); ?>
                    </a>
                    <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/messageIcon-2.svg" alt="WhatsApp" width="26" height="26" fetchpriority="high">
                    <div class="line-telHeaderFlex"></div>
                </div>
                <div class="rightIconHeader">
                    <a href="<?php echo esc_attr( okoyom_phone_href() ); ?>" class="telIconHeader">
                        <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/telIconHeader.svg" alt="Позвонить" width="32" height="32" loading="lazy" decoding="async">
                    </a>
                    <a href="<?php echo esc_url( okoyom_favorites_url() ); ?>" class="likeHeader">
                        <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/likeHeader.svg" alt="Избранное" width="32" height="32" loading="lazy" decoding="async">
                    </a>
                    <a href="<?php echo esc_url( okoyom_cart_url() ); ?>" class="bagHeader">
                        <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/bagHeader.svg" alt="Корзина" width="32" height="32" loading="lazy" decoding="async">
                    </a>
                    <div class="hamburger-menu">
                        <input id="menu__toggle" type="checkbox" />
                        <label class="menu__btn">
                            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/burger.svg" alt="Открыть меню" class="burgerSvg" width="32" height="32" loading="lazy" decoding="async">
                            <img class="closeBurger" src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/closeBurger.svg" alt="Закрыть меню" width="32" height="32" loading="lazy" decoding="async">
                        </label>
                        <ul class="menu__box">
                            <li class="content-burger">
                                <ul class="listBurgerUl">
                                    <li class="listBurgerUlContentLi">
                                        <a class="logo" href="/">
                                            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/logoHeader.svg" alt="Окоём" width="122" height="24" loading="lazy" decoding="async">
                                        </a>
                                        <?php okoyom_nav( 'primary', 'linksBurger' ); ?>
                                        <div class="lineBurger"></div>
                                        <div class="flexContactBurger">
                                            <div class="flexContactBurger__a">
                                                <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/telBurger.svg" alt="Позвонить" width="26" height="26" loading="lazy" decoding="async">
                                                <a href="<?php echo esc_attr( okoyom_phone_href() ); ?>">
                                                    <?php echo esc_html( okoyom_option( 'phone' ) ); ?>
                                                </a>
                                            </div>
                                            <div class="flexContactBurger__a">
                                                <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/messageIcon-2.svg" alt="WhatsApp" width="26" height="26" loading="lazy" decoding="async">
                                                <a href="<?php echo esc_attr( okoyom_phone_href() ); ?>">
                                                    <?php echo okoyom_t( '8b777ebcc503', 'WhatsApp' ); ?>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="flexBtnBurger">
                                            <a href="/favorites/" class="btnWhiteTextBtn btnWhiteTextBtnV3">
                                                <?php echo okoyom_t( 'c968bdd54318', 'Избранное (2)' ); ?>
                                            </a>
                                            <a href="/cart/" class="btnWhiteTextBtn btnWhiteTextBtnV2">
                                                <?php echo okoyom_t( 'b7697b9693d2', 'Корзина' ); ?>
                                            </a>
                                        </div>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
