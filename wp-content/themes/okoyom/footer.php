<?php
defined( 'ABSPATH' ) || exit;
?>
<footer class="sectionMain">
    <div class="container">
        <div class="flexFooter">
            <div class="col-1__footer">
                <a class="logo" href="index.html">
                    <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/logoFooter.svg" alt="Окоём" width="122" height="24" loading="lazy" decoding="async">
                </a>
                <p>
                    Студия авторских настенных муралов
                </p>
            </div>
            <?php okoyom_nav( 'footer', 'col-2__footer' ); ?>
            <div class="col-3__footer">
                <a href="<?php echo esc_attr( okoyom_phone_href() ); ?>">
                    <?php echo esc_html( okoyom_option( 'phone' ) ); ?>
                </a>
                <a href="mailto:hello@okoyom.studio">
                    hello@okoyom.studio
                </a>
                <form action="" method="post">
                    <span>
                        Новые коллекции и вдохновение
                    </span>
                    <div class="flex-col-3__footer">
                        <input type="email" required placeholder="Ваш e-mail">
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
                    Соцсети
                </p>
                <div class="flex-col-4__footer">
                    <a href="https://www.instagram.com/okoem.art" target="_blank" rel="noopener">
                        <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flex-col-4__footer-1.svg" alt="Instagram" width="40" height="40" loading="lazy" decoding="async">
                    </a>
                    <a href="https://ru.pinterest.com/okoemart" target="_blank" rel="noopener">
                        <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flex-col-4__footer-2.svg" alt="Pinterest" width="40" height="40" loading="lazy" decoding="async">
                    </a>
                    <a href="https://vk.com/okoem_art" target="_blank" rel="noopener">
                        <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flex-col-4__footer-3.svg" alt="ВКонтакте" width="40" height="40" loading="lazy" decoding="async">
                    </a>
                    <a href="https://yandex.ru/rythm/businesses/@okoem.art" target="_blank" rel="noopener">
                        <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flex-col-4__footer-4.svg" alt="Яндекс Дзен" width="40" height="40" loading="lazy" decoding="async">
                    </a>
                </div>
            </div>
        </div>
        <div class="line-footer"></div>
        <p class="lastTextFooter">
            © 2026 ОКОЁМ
        </p>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
