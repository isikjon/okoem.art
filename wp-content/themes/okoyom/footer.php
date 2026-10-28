<?php
defined( 'ABSPATH' ) || exit;
?>
<footer class="sectionMain">
    <div class="container">
        <div class="flexFooter">
            <div class="col-1__footer">
                <a class="logo" href="/">
                    <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/logoFooter.svg" alt="Окоём" width="122" height="24" loading="lazy" decoding="async">
                </a>
                <p>
                    <?php echo okoyom_t( 'c4b4f38ff5a8', 'Студия интерьерных муралов' ); ?>
                </p>
            </div>
            <?php okoyom_nav( 'footer', 'col-2__footer' ); ?>
            <div class="col-3__footer">
                <a href="<?php echo esc_attr( okoyom_phone_href() ); ?>">
                    <?php echo esc_html( okoyom_option( 'phone' ) ); ?>
                </a>
                <a href="mailto:<?php echo esc_attr( okoyom_option( 'email' ) ); ?>">
                    <?php echo esc_html( okoyom_option( 'email' ) ); ?>
                </a>
                <form action="" method="post" data-subscribe>
                    <span>
                        <?php echo okoyom_t( '93418d156c5f', 'Новые коллекции и вдохновение' ); ?>
                    </span>
                    <div class="flex-col-3__footer">
                        <input type="email" name="email" required placeholder="Ваш e-mail">
                        <button type="submit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <path d="M3.75 9H14.25" stroke="#63615F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M9 3.75L14.25 9L9 14.25" stroke="#63615F" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-4__footer">
                <p>
                    <?php echo okoyom_t( 'df27ea2e7e22', 'Соцсети' ); ?>
                </p>
                <div class="flex-col-4__footer">
                    <a href="https://www.instagram.com/okoem.art" target="_blank" rel="noopener" class="socialTextIcon">
                        <?php echo okoyom_t( '46cf74d33e97', 'IG' ); ?>
                    </a>
                    <a href="https://ru.pinterest.com/okoemart" target="_blank" rel="noopener">
                        <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flex-col-4__footer-2.svg" alt="Pinterest" width="40" height="40" loading="lazy" decoding="async">
                    </a>
                    <a href="https://vk.com/okoem_art" target="_blank" rel="noopener">
                        <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flex-col-4__footer-3.svg" alt="ВКонтакте" width="40" height="40" loading="lazy" decoding="async">
                    </a>
                    <a href="https://yandex.ru/rythm/businesses/@okoem.art" target="_blank" rel="noopener">
                        <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flex-col-4__footer-4.svg" alt="Яндекс Ритм" width="40" height="40" loading="lazy" decoding="async">
                    </a>
                </div>
            </div>
        </div>
        <div class="line-footer"></div>
        <p class="lastTextFooter">
            <?php echo okoyom_t( '38c83ced84bd', '© 2026 ОКОЁМ' ); ?>
            <a class="lastTextFooter__link" href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>">
                <?php echo okoyom_t( '7f2b1c4ea93d', 'Политика обработки персональных данных' ); ?>
            </a>
        </p>
    </div>
</footer>
<div class="subsModal" data-subs-modal hidden>
    <div class="subsModal__overlay" data-subs-close></div>
    <div class="subsModal__window" role="dialog" aria-modal="true" aria-labelledby="subsModalTitle">
        <button type="button" class="subsModal__close" data-subs-close aria-label="Закрыть">
            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/close.svg" alt="" width="40" height="40" loading="lazy" decoding="async" aria-hidden="true">
        </button>
        <p class="titleSectionTitle" id="subsModalTitle">
            <?php echo okoyom_t( '2c7f4b9ad103', 'Подписка на рассылку' ); ?>
        </p>
        <p class="subsModal__email" data-subs-email></p>
        <label class="formConsent">
            <input type="checkbox" class="formConsent__box" data-subs-consent="data">
            <span class="formConsent__text"><?php echo okoyom_form_consent_data_text(); ?></span>
        </label>
        <label class="formConsent">
            <input type="checkbox" class="formConsent__box" data-subs-consent="news">
            <span class="formConsent__text"><?php echo okoyom_form_consent_news_text(); ?></span>
        </label>
        <p class="subsModal__error" data-subs-error hidden></p>
        <button type="button" class="btnWhiteTextBtn btnWhiteTextBtnV3 subsModal__submit" data-subs-submit disabled>
            <?php echo okoyom_t( 'b6d1e07c5a42', 'Подписаться' ); ?>
        </button>
    </div>
</div>
<div class="cookieBar" data-cookie-bar hidden>
    <div class="container">
        <div class="cookieBar__inner">
            <p class="cookieBar__text">
                <?php echo okoyom_t( 'd41f8cd98f10', 'Мы используем файлы cookie, чтобы сайт работал и был удобнее. Можно принять все или оставить только необходимые.' ); ?>
                <a href="<?php echo esc_url( home_url( '/privacy/' ) ); ?>"><?php echo okoyom_t( 'c0ffee1a2b3c', 'Политика конфиденциальности' ); ?></a>
            </p>
            <div class="cookieBar__actions">
                <button type="button" class="cookieBar__btn cookieBar__btn--ghost" data-cookie-choice="necessary">
                    <?php echo okoyom_t( 'a1b2c3d4e5f0', 'Только необходимые' ); ?>
                </button>
                <button type="button" class="cookieBar__btn" data-cookie-choice="all">
                    <?php echo okoyom_t( 'f0e1d2c3b4a5', 'Принять все' ); ?>
                </button>
            </div>
        </div>
    </div>
</div>
<?php wp_footer(); ?>
</body>
</html>
