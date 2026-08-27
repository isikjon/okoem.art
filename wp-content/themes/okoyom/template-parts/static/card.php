<?php
defined( 'ABSPATH' ) || exit;
?>
<section class="cardSection" style="background: url(<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/cardBG.webp) center center no-repeat; background-size: cover;">
    <div class="titleCardSection titleCardSectionContent titleCardSectionContent-22">
        <p class="text-titleCardSection">
            <?php echo okoyom_t( '86fde826ee06', 'Эхо Рериха' ); ?>
        </p>
        <h1 class="title-titleCardSection">
            <?php echo okoyom_t( 'a666836860da', 'Дальние хребты' ); ?>
        </h1>
    </div>
    <div class="container">
        <div class="downArrowCard">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                <path d="M3.5 5.25L7 8.75L10.5 5.25" stroke="white" stroke-opacity="0.4" stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
</section>
<section class="cardSectionContent">
    <div class="container">
        <div class="breadLinks">
            <a href="/">
                <?php echo okoyom_t( '047f5653b183', 'Главная' ); ?>
            </a>
            <span>
                /
            </span>
            <a href="/catalog/">
                <?php echo okoyom_t( 'ad51225e2ef0', 'Каталог' ); ?>
            </a>
            <span>
                /
            </span>
            <p>
                <?php echo okoyom_t( 'a666836860da', 'Дальние хребты' ); ?>
            </p>
        </div>
        <div class="flex-cardSectionContent">
            <div class="left-flex-cardSectionContent">
                <div class="muralGallery">
                    <!-- миниатюры -->
                    <div class="swiper muralGalleryThumbs">
                        <div class="swiper-wrapper">

                            <div class="swiper-slide">
                                <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/left-flex-cardSectionContent-1.webp" alt="" width="3030" height="3787" loading="lazy" decoding="async">
                            </div>

                            <div class="swiper-slide">
                                <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/left-flex-cardSectionContent-3.webp" alt="" width="3030" height="3787" loading="lazy" decoding="async">
                            </div>

                            <div class="swiper-slide">
                                <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/left-flex-cardSectionContent-1.webp" alt="" width="3030" height="3787" loading="lazy" decoding="async">
                            </div>

                        </div>
                    </div>
                    <!-- большая картинка -->
                    <div class="swiper muralGalleryMain">
                        <div class="swiper-wrapper">

                            <div class="swiper-slide">
                                <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/left-flex-cardSectionContent-1.webp" alt="" width="3030" height="3787" loading="lazy" decoding="async">
                            </div>

                            <div class="swiper-slide">
                                <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/left-flex-cardSectionContent-3.webp" alt="" width="3030" height="3787" loading="lazy" decoding="async">
                            </div>

                            <div class="swiper-slide">
                                <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/left-flex-cardSectionContent-1.webp" alt="" width="3030" height="3787" loading="lazy" decoding="async">
                            </div>


                        </div>
                    </div>
                </div>
            </div>
            <div class="right-flex-cardSectionContent">
                <div class="title-right-flex-cardSectionContent">
                    <span>
                        <?php echo okoyom_t( '86fde826ee06', 'Эхо Рериха' ); ?>
                    </span>
                    <h1>
                        <?php echo okoyom_t( 'a666836860da', 'Дальние хребты' ); ?>
                    </h1>
                    <p>
                        <?php echo okoyom_t( '9631fcec3788', 'Панорама горных хребтов в духе пейзажной живописи начала XX века. Многослойные планы создают ощущение глубины и тихого величия.' ); ?>
                    </p>
                </div>
                <div class="flexBtnsCards">
                    <a href="/favorites/" class="flexBtnsCards__links">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <path d="M11.0834 8.16667C11.9526 7.315 12.8334 6.29417 12.8334 4.95833C12.8334 4.10743 12.4954 3.29138 11.8937 2.6897C11.292 2.08802 10.476 1.75 9.62508 1.75C8.59841 1.75 7.87508 2.04167 7.00008 2.91667C6.12508 2.04167 5.40175 1.75 4.37508 1.75C3.52418 1.75 2.70813 2.08802 2.10645 2.6897C1.50477 3.29138 1.16675 4.10743 1.16675 4.95833C1.16675 6.3 2.04175 7.32083 2.91675 8.16667L7.00008 12.25L11.0834 8.16667Z" stroke="#161412" stroke-opacity="0.5" stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <?php echo okoyom_t( '2fc413929104', 'Избранное' ); ?>
                    </a>
                    <a href="#!" class="flexBtnsCards__links openModal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <path d="M4.60841 11.6666C5.72175 12.2377 7.00246 12.3924 8.21977 12.1028C9.43707 11.8132 10.5109 11.0983 11.2478 10.0871C11.9846 9.07575 12.3361 7.83453 12.2388 6.58704C12.1414 5.33955 11.6018 4.16784 10.717 3.28306C9.83218 2.39827 8.66047 1.85859 7.41299 1.76127C6.1655 1.66395 4.92427 2.01539 3.91298 2.75226C2.90168 3.48912 2.18682 4.56296 1.89721 5.78026C1.6076 6.99756 1.7623 8.27828 2.33341 9.39161L1.16675 12.8333L4.60841 11.6666Z" stroke="#161412" stroke-opacity="0.5" stroke-width="1.16667" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <?php echo okoyom_t( '10ac7a75673b', 'Задать вопрос' ); ?>
                    </a>
                </div>
                <div class="flexColorsCards">
                    <span>
                        <?php echo okoyom_t( '2f4adfdde280', 'Цветовое решение' ); ?>
                    </span>
                    <div class="flex-flexColorsCards">
                        <div class="block-flexColorsCards block-flexColorsCards__active" style="background: #8C8470;"></div>
                        <div class="block-flexColorsCards" style="background: #B6A471;"></div>
                        <div class="block-flexColorsCards" style="background: #5C6670;"></div>
                    </div>
                    <p>
                        <?php echo okoyom_t( 'f63c491cc018', 'Тёплый' ); ?>
                    </p>
                </div>
                <div class="line-right-flex-cardSectionContent"></div>
                <p class="text-title2-right-flex-cardSectionContent">
                    <?php echo okoyom_t( '4abc4ee0c082', 'Индивидуальный расчёт' ); ?>
                </p>
<!--                <p class="text-title3-right-flex-cardSectionContent">-->
<!--                    Каждый мурал создаём под ваше пространство. <br>-->
<!--                    Укажите параметры стены для предварительной оценки.-->
<!--                </p>-->
                <div class="flexForm-right-flex-cardSectionContent">
                    <label>
                        <span>
                            <?php echo okoyom_t( '0b3365ed791d', 'Ширина, см' ); ?>
                        </span>
                        <input type="text" placeholder="300" value="300">
                    </label>
                    <label>
                        <span>
                            <?php echo okoyom_t( '12a131ae3b8d', 'Высота, см' ); ?>
                        </span>
                        <input type="text" placeholder="300" value="300">
                    </label>
                </div>
                <div class="material-select">
                    <div class="material-select__head">
                        <div class="material-select__label">
                            <?php echo okoyom_t( '82f235bf1c13', 'Материал' ); ?>
                        </div>
                        <div class="material-select__current">
                            <span class="material-select__value">
                                <?php echo okoyom_t( '15a8fe50396e', 'Основа' ); ?>
                            </span>
                            <span class="material-select__arrow"></span>
                        </div>
                    </div>
                    <div class="material-select__dropdown">
                        <div class="material-select__list">
                            <button class="material-select__item is-active">
                                <div class="material-select__left">
                                    <span class="material-select__dot"></span>
                                    <span class="material-select__name">
                                        <?php echo okoyom_t( '15a8fe50396e', 'Основа' ); ?>
                                    </span>
                                </div>
                            </button>
                            <button class="material-select__item">
                                <div class="material-select__left">
                                    <span class="material-select__dot"></span>
                                    <span class="material-select__name">
                                        <?php echo okoyom_t( 'dad90bde7323', 'Текстиль' ); ?>
                                    </span>
                                </div>
                            </button>
                            <button class="material-select__item">
                                <div class="material-select__left">
                                    <span class="material-select__dot"></span>
                                    <span class="material-select__name">
                                        <?php echo okoyom_t( '14093e910091', 'Полотно' ); ?>
                                    </span>
                                </div>
                            </button>
                            <button class="material-select__item">
                                <div class="material-select__left">
                                    <span class="material-select__dot"></span>
                                    <span class="material-select__name">
                                        <?php echo okoyom_t( '70e35568ed46', 'HoReCa' ); ?>
                                    </span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="flexPriceInfo-right-flex-cardSectionContent">
                    <div class="line-flexPriceInfo-right-flex-cardSectionContent">
                        <span>
                            <?php echo okoyom_t( '746ebf524c94', 'Площадь' ); ?>
                        </span>
                        <p>
                            <?php echo okoyom_t( '7bce9f0e600e', '7.80 м²' ); ?>
                        </p>
                    </div>
                    <div class="line-flexPriceInfo-right-flex-cardSectionContent">
                        <span>
                            <?php echo okoyom_t( '3e1b860b3d53', 'Ориентировочно' ); ?>
                        </span>
                        <h2>
                            35 100 ₽
                        </h2>
                    </div>
                </div>
                <a href="#!" class="btnWhiteTextBtn btnWhiteTextBtnV3">
                    <?php echo okoyom_t( '43ddcaeab510', 'Запросить расчёт' ); ?>
                </a>
                <p class="textright-flex-cardSectionContent-123">
                    <?php echo okoyom_t( '63242d589455', 'Мы подготовим визуализацию в вашем интерьере и точный расчёт стоимости' ); ?>
                </p>
            </div>
        </div>
    </div>
</section>
<section class="sectionMain">
    <div class="container">
        <div class="titleSection">
            <h2 class="titleSectionTitle">
                <?php echo okoyom_t( 'f815f174101d', 'Материалы и печать' ); ?>
            </h2>
        </div>
        <div class="dtabs">
            <!--            <div class="dtabs__nav">-->
            <!--                <button class="dtabs__btn active" data-tab="material">-->
            <!--                    О материале-->
            <!--                </button>-->
            <!--                <button class="dtabs__btn" data-tab="print">-->
            <!--                    О принте-->
            <!--                </button>-->
            <!--            </div>-->
            <div class="dtabs__content active" id="material">
                <!-- MOBILE SELECT -->
                <div class="material-mobile-select">
                    <button class="material-mobile-select__trigger">
                        <span class="material-mobile-select__value">
                            <?php echo okoyom_t( '15a8fe50396e', 'Основа' ); ?>
                        </span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path d="M6 9L12 15L18 9"
                                  stroke="currentColor"
                                  stroke-width="2"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>
                    </button>
                    <div class="material-mobile-select__dropdown">
                        <div class="material-mobile-option active" data-material="m1">
                            <?php echo okoyom_t( '15a8fe50396e', 'Основа' ); ?>
                        </div>
                        <div class="material-mobile-option" data-material="m2">
                            <?php echo okoyom_t( 'dad90bde7323', 'Текстиль' ); ?>
                        </div>
                        <div class="material-mobile-option" data-material="m3">
                            <?php echo okoyom_t( '14093e910091', 'Полотно' ); ?>
                        </div>
                        <div class="material-mobile-option" data-material="m4">
                            <?php echo okoyom_t( '70e35568ed46', 'HoReCa' ); ?>
                        </div>
                    </div>
                </div>
                <div class="material-tabs">
                    <button class="material-tab active" data-material="m1">
                        <?php echo okoyom_t( '15a8fe50396e', 'Основа' ); ?>
                    </button>
                    <button class="material-tab" data-material="m2">
                        <?php echo okoyom_t( 'dad90bde7323', 'Текстиль' ); ?>
                    </button>
                    <button class="material-tab" data-material="m3">
                        <?php echo okoyom_t( '14093e910091', 'Полотно' ); ?>
                    </button>
                    <button class="material-tab" data-material="m4">
                        <?php echo okoyom_t( '70e35568ed46', 'HoReCa' ); ?>
                    </button>
                </div>

                <div class="material-pane active" id="m1">
                    <div class="material-layout">
                        <div class="material-layout__contentFlex">
                            <div class="material-desc">
                                <?php echo okoyom_t( '656b92213b9e', 'Матовое покрытие с благородной текстурой. Идеально для жилых интерьеров.' ); ?>
                            </div>
                            <div class="material-specs">
                                <div class="material-row">
                                    <span><?php echo okoyom_t( 'fa9392a7ba2a', 'Состав' ); ?></span>
                                    <span><?php echo okoyom_t( '53173db30307', '20% флизелин, 80% полиэстер, акриловый грунт' ); ?></span>
                                </div>
                                <div class="material-row">
                                    <span><?php echo okoyom_t( 'b7726c78f3e3', 'Плотность' ); ?></span>
                                    <span><?php echo okoyom_t( '43c6e5d4366f', '240 г/м²' ); ?></span>
                                </div>
                                <div class="material-row">
                                    <span><?php echo okoyom_t( '0749b9b3bc79', 'Бесшовная печать' ); ?></span>
                                    <span><?php echo okoyom_t( '3a9c6286c810', 'до 317 см' ); ?></span>
                                </div>
                                <div class="material-row">
                                    <span><?php echo okoyom_t( '682fa8dbadd5', 'Цена' ); ?></span>
                                    <span><?php echo okoyom_t( 'c52957f7a8f9', '7 000 ₽/м²' ); ?></span>
                                </div>
                            </div>
                            <div class="material-tags">
                                <div class="material-tag">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M7.00008 12.8332C10.2217 12.8332 12.8334 10.2215 12.8334 6.99984C12.8334 3.77818 10.2217 1.1665 7.00008 1.1665C3.77842 1.1665 1.16675 3.77818 1.16675 6.99984C1.16675 10.2215 3.77842 12.8332 7.00008 12.8332Z" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5.25 7.00016L6.41667 8.16683L8.75 5.8335" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php echo okoyom_t( 'd06f4c8818fa', 'Матовая поверхность' ); ?>
                                </div>
                                <div class="material-tag">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M7.00008 12.8332C10.2217 12.8332 12.8334 10.2215 12.8334 6.99984C12.8334 3.77818 10.2217 1.1665 7.00008 1.1665C3.77842 1.1665 1.16675 3.77818 1.16675 6.99984C1.16675 10.2215 3.77842 12.8332 7.00008 12.8332Z" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5.25 7.00016L6.41667 8.16683L8.75 5.8335" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php echo okoyom_t( 'a4d0f73bc4cc', 'Паропроницаемый материал' ); ?>
                                </div>
                                <div class="material-tag">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M7.00008 12.8332C10.2217 12.8332 12.8334 10.2215 12.8334 6.99984C12.8334 3.77818 10.2217 1.1665 7.00008 1.1665C3.77842 1.1665 1.16675 3.77818 1.16675 6.99984C1.16675 10.2215 3.77842 12.8332 7.00008 12.8332Z" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5.25 7.00016L6.41667 8.16683L8.75 5.8335" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php echo okoyom_t( 'ce0af72b1645', 'Мягкие границы изображения' ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="material-preview">
                            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/photoTabsCard.webp" alt="" width="2212" height="1659" loading="lazy" decoding="async">
                            <div class="material-preview-title">
                                <?php echo okoyom_t( '83d1904f48c3', 'Флизелин премиум' ); ?>
                            </div>
                            <div class="material-preview-text">
                                <?php echo okoyom_t( '51adf144ba92', 'Матовая поверхность с мягкой тактильной текстурой' ); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="material-pane" id="m2">
                    <div class="material-layout">
                        <div class="material-layout__contentFlex">
                            <div class="material-desc">
                                <?php echo okoyom_t( '137b9ec3c3f0', 'Усиленная структура для общественных пространств с повышенной проходимостью.' ); ?>
                            </div>
                            <div class="material-specs">
                                <div class="material-row">
                                    <span><?php echo okoyom_t( 'fa9392a7ba2a', 'Состав' ); ?></span>
                                    <span><?php echo okoyom_t( '8803efdb44d1', '100% полиэстер, акриловый грунт' ); ?></span>
                                </div>
                                <div class="material-row">
                                    <span><?php echo okoyom_t( 'b7726c78f3e3', 'Плотность' ); ?></span>
                                    <span><?php echo okoyom_t( 'fc761b63c974', '220 г/м²' ); ?></span>
                                </div>
                                <div class="material-row">
                                    <span><?php echo okoyom_t( '0749b9b3bc79', 'Бесшовная печать' ); ?></span>
                                    <span><?php echo okoyom_t( '3a9c6286c810', 'до 317 см' ); ?></span>
                                </div>
                                <div class="material-row">
                                    <span><?php echo okoyom_t( '682fa8dbadd5', 'Цена' ); ?></span>
                                    <span><?php echo okoyom_t( '3a38cb2af0e2', '8 700 ₽/м²' ); ?></span>
                                </div>
                            </div>
                            <div class="material-tags">
                                <div class="material-tag">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M7.00008 12.8332C10.2217 12.8332 12.8334 10.2215 12.8334 6.99984C12.8334 3.77818 10.2217 1.1665 7.00008 1.1665C3.77842 1.1665 1.16675 3.77818 1.16675 6.99984C1.16675 10.2215 3.77842 12.8332 7.00008 12.8332Z" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5.25 7.00016L6.41667 8.16683L8.75 5.8335" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php echo okoyom_t( 'd3fe0057d7c1', 'Лёгкая текстильная фактура' ); ?>
                                </div>
                                <div class="material-tag">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M7.00008 12.8332C10.2217 12.8332 12.8334 10.2215 12.8334 6.99984C12.8334 3.77818 10.2217 1.1665 7.00008 1.1665C3.77842 1.1665 1.16675 3.77818 1.16675 6.99984C1.16675 10.2215 3.77842 12.8332 7.00008 12.8332Z" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5.25 7.00016L6.41667 8.16683L8.75 5.8335" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php echo okoyom_t( 'f95163432e4a', 'Цветоблокирующая подложка' ); ?>
                                </div>
                                <div class="material-tag">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M7.00008 12.8332C10.2217 12.8332 12.8334 10.2215 12.8334 6.99984C12.8334 3.77818 10.2217 1.1665 7.00008 1.1665C3.77842 1.1665 1.16675 3.77818 1.16675 6.99984C1.16675 10.2215 3.77842 12.8332 7.00008 12.8332Z" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5.25 7.00016L6.41667 8.16683L8.75 5.8335" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php echo okoyom_t( '273f55141c22', 'Премиальное покрытие' ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="material-preview">
                            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/photoTabsCard.webp" alt="" width="2212" height="1659" loading="lazy" decoding="async">
                            <div class="material-preview-title">
                                <?php echo okoyom_t( '83d1904f48c3', 'Флизелин премиум' ); ?>
                            </div>
                            <div class="material-preview-text">
                                <?php echo okoyom_t( '4e5a0068f9cf', 'Премиальная поверхность с мелкой фактурой ткани' ); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="material-pane" id="m3">
                    <div class="material-layout">
                        <div class="material-layout__contentFlex">
                            <div class="material-desc">
                                <?php echo okoyom_t( '656b92213b9e', 'Матовое покрытие с благородной текстурой. Идеально для жилых интерьеров.' ); ?>
                            </div>
                            <div class="material-specs">
                                <div class="material-row">
                                    <span><?php echo okoyom_t( 'fa9392a7ba2a', 'Состав' ); ?></span>
                                    <span><?php echo okoyom_t( '8803efdb44d1', '100% полиэстер, акриловый грунт' ); ?></span>
                                </div>
                                <div class="material-row">
                                    <span><?php echo okoyom_t( 'b7726c78f3e3', 'Плотность' ); ?></span>
                                    <span><?php echo okoyom_t( '4a455a4fbcf3', '255 г/м²' ); ?></span>
                                </div>
                                <div class="material-row">
                                    <span><?php echo okoyom_t( '0749b9b3bc79', 'Бесшовная печать' ); ?></span>
                                    <span><?php echo okoyom_t( '3a9c6286c810', 'до 317 см' ); ?></span>
                                </div>
                                <div class="material-row">
                                    <span><?php echo okoyom_t( '682fa8dbadd5', 'Цена' ); ?></span>
                                    <span><?php echo okoyom_t( 'c52957f7a8f9', '7 000 ₽/м²' ); ?></span>
                                </div>
                            </div>
                            <div class="material-tags">
                                <div class="material-tag">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M7.00008 12.8332C10.2217 12.8332 12.8334 10.2215 12.8334 6.99984C12.8334 3.77818 10.2217 1.1665 7.00008 1.1665C3.77842 1.1665 1.16675 3.77818 1.16675 6.99984C1.16675 10.2215 3.77842 12.8332 7.00008 12.8332Z" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5.25 7.00016L6.41667 8.16683L8.75 5.8335" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php echo okoyom_t( '7d9412566b26', 'Крупная фактура ткани' ); ?>
                                </div>
                                <div class="material-tag">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M7.00008 12.8332C10.2217 12.8332 12.8334 10.2215 12.8334 6.99984C12.8334 3.77818 10.2217 1.1665 7.00008 1.1665C3.77842 1.1665 1.16675 3.77818 1.16675 6.99984C1.16675 10.2215 3.77842 12.8332 7.00008 12.8332Z" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5.25 7.00016L6.41667 8.16683L8.75 5.8335" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php echo okoyom_t( 'c3bc80a83291', 'Рельеф холста' ); ?>
                                </div>
                                <div class="material-tag">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M7.00008 12.8332C10.2217 12.8332 12.8334 10.2215 12.8334 6.99984C12.8334 3.77818 10.2217 1.1665 7.00008 1.1665C3.77842 1.1665 1.16675 3.77818 1.16675 6.99984C1.16675 10.2215 3.77842 12.8332 7.00008 12.8332Z" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5.25 7.00016L6.41667 8.16683L8.75 5.8335" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php echo okoyom_t( '438f6c499bb5', 'Текстильная основа' ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="material-preview">
                            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/photoTabsCard.webp" alt="" width="2212" height="1659" loading="lazy" decoding="async">
                            <div class="material-preview-title">
                                <?php echo okoyom_t( '83d1904f48c3', 'Флизелин премиум' ); ?>
                            </div>
                            <div class="material-preview-text">
                                <?php echo okoyom_t( '31bb38d7e5a9', 'Крупная фактура ткани с рельефом холста' ); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="material-pane" id="m4">
                    <div class="material-layout">
                        <div class="material-layout__contentFlex">
                            <div class="material-desc">
                                <?php echo okoyom_t( '656b92213b9e', 'Матовое покрытие с благородной текстурой. Идеально для жилых интерьеров.' ); ?>
                            </div>
                            <div class="material-specs">
                                <div class="material-row">
                                    <span><?php echo okoyom_t( 'fa9392a7ba2a', 'Состав' ); ?></span>
                                    <span><?php echo okoyom_t( '6f4c9fc022b1', 'Флизелиновая основа, ПВХ-ламинация' ); ?></span>
                                </div>
                                <div class="material-row">
                                    <span><?php echo okoyom_t( 'b7726c78f3e3', 'Плотность' ); ?></span>
                                    <span><?php echo okoyom_t( 'ef851cca4b7a', '210 г/м²' ); ?></span>
                                </div>
                                <div class="material-row">
                                    <span><?php echo okoyom_t( 'b24389e0f0ca', 'Ширина рулона' ); ?></span>
                                    <span><?php echo okoyom_t( 'e9f13dd10bb3', '100 см' ); ?></span>
                                </div>
                                <div class="material-row">
                                    <span><?php echo okoyom_t( '682fa8dbadd5', 'Цена' ); ?></span>
                                    <span><?php echo okoyom_t( '6915c12c87e9', '6 500 ₽/м²' ); ?></span>
                                </div>
                            </div>
                            <div class="material-tags">
                                <div class="material-tag">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M7.00008 12.8332C10.2217 12.8332 12.8334 10.2215 12.8334 6.99984C12.8334 3.77818 10.2217 1.1665 7.00008 1.1665C3.77842 1.1665 1.16675 3.77818 1.16675 6.99984C1.16675 10.2215 3.77842 12.8332 7.00008 12.8332Z" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5.25 7.00016L6.41667 8.16683L8.75 5.8335" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php echo okoyom_t( '1797cdeeffd3', 'Антивандальное покрытие' ); ?>
                                </div>
                                <div class="material-tag">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M7.00008 12.8332C10.2217 12.8332 12.8334 10.2215 12.8334 6.99984C12.8334 3.77818 10.2217 1.1665 7.00008 1.1665C3.77842 1.1665 1.16675 3.77818 1.16675 6.99984C1.16675 10.2215 3.77842 12.8332 7.00008 12.8332Z" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5.25 7.00016L6.41667 8.16683L8.75 5.8335" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php echo okoyom_t( 'ee86dff1a799', 'Стойкость к мытью' ); ?>
                                </div>
                                <div class="material-tag">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                        <path d="M7.00008 12.8332C10.2217 12.8332 12.8334 10.2215 12.8334 6.99984C12.8334 3.77818 10.2217 1.1665 7.00008 1.1665C3.77842 1.1665 1.16675 3.77818 1.16675 6.99984C1.16675 10.2215 3.77842 12.8332 7.00008 12.8332Z" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M5.25 7.00016L6.41667 8.16683L8.75 5.8335" stroke="#161412" stroke-opacity="0.3" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php echo okoyom_t( '86433cd30ee8', 'Для коммерческих пространств' ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="material-preview">
                            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/photoTabsCard.webp" alt="" width="2212" height="1659" loading="lazy" decoding="async">
                            <div class="material-preview-title">
                                <?php echo okoyom_t( '83d1904f48c3', 'Флизелин премиум' ); ?>
                            </div>
                            <div class="material-preview-text">
                                <?php echo okoyom_t( 'e7bb936dd98e', 'Матовая ламинация, устойчивая к механическим воздействиям' ); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dtabs__content" id="print">
                <div class="print-layout">
                    <div class="print-layout__1First">
                        <div class="print-text">
                            <?php echo okoyom_t( 'fbf361e9ca1c', 'Наши принты сохраняют безупречную чёткость даже на высоте 6 метров. Каждая прожилка камня или мазок кисти выглядят так, будто они нанесены вручную.' ); ?>
                        </div>
                        <div class="print-stats">
                            <div>
                                <div class="print-stat-number">
                                    <?php echo esc_html( okoyom_option( 'subs_pinterest' ) ); ?>
                                </div>
                                <div class="print-stat-label">
                                    <?php echo okoyom_t( 'b39edbac6991', 'Пикселей' ); ?>
                                </div>
                            </div>
                            <div>
                                <div class="print-stat-number">
                                    2400
                                </div>
                                <div class="print-stat-label">
                                    <?php echo okoyom_t( 'a76534bae88b', 'DPI' ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="material-specs">
                            <div class="material-row">
                                <span><?php echo okoyom_t( '4e4a0b47cf2a', 'Шов' ); ?></span>
                                <span><?php echo okoyom_t( 'e8c837c44bfc', 'Бесшовный до 600 см' ); ?></span>
                            </div>
                            <div class="material-row">
                                <span><?php echo okoyom_t( '15a8fe50396e', 'Основа' ); ?></span>
                                <span><?php echo okoyom_t( '71fb3d9f0c6e', 'Флизелин' ); ?></span>
                            </div>
                            <div class="material-row">
                                <span><?php echo okoyom_t( 'edf02a258cd0', 'Макс. ширина' ); ?></span>
                                <span><?php echo okoyom_t( '963818109ce4', '600 см' ); ?></span>
                            </div>
                            <div class="material-row">
                                <span><?php echo okoyom_t( '83138732fdbb', 'Уход' ); ?></span>
                                <span><?php echo okoyom_t( 'd64aec2d6ee5', 'Сухая чистка мягкой щёткой' ); ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="print-layout__2Second">
                        <div class="print-right-title">
                            <?php echo okoyom_t( '4df7b7dbdb94', 'Рекомендуемые помещения' ); ?>
                        </div>
                        <div class="flexBorderTextInfoSection">
                            <div class="block-flexBorderTextInfoSection">
                                <p>
                                    <?php echo okoyom_t( 'de945f3658cf', 'Гостиная' ); ?>
                                </p>
                            </div>
                            <div class="block-flexBorderTextInfoSection">
                                <p>
                                    <?php echo okoyom_t( 'd72034b60eea', 'Прихожая' ); ?>
                                </p>
                            </div>
                            <div class="block-flexBorderTextInfoSection">
                                <p>
                                    <?php echo okoyom_t( '70e35568ed46', 'HoReCa' ); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="sectionMain">
    <div class="container">
        <div class="titleSection">
            <span class="titleSectionSpan">
               <?php echo okoyom_t( 'a5e900e9a6a7', 'Другие дизайны' ); ?>
            </span>
            <div class="flex-titleSection">
                <h2 class="titleSectionTitle">
                    <?php echo okoyom_t( '86fde826ee06', 'Эхо Рериха' ); ?>
                </h2>
            </div>
        </div>
        <div class="flexTwoTypeInfoMain flexTwoTypeInfoMain-2">
            <a href="/catalog/" class="blockCardCatalog__card">
                <div class="hover-slider">
                    <div class="likeCardCatalog">
                        <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/like.svg" alt="" width="24" height="24" loading="lazy" decoding="async" aria-hidden="true">
                    </div>
                    <div class="hover-slider__slides">
                        <div class="hover-slider__slide active">
                            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flexGreyInfoBlockRow__big-1.webp" alt="" width="552" height="690" loading="lazy" decoding="async">
                        </div>
                        <div class="hover-slider__slide">
                            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flexGreyInfoBlockRow__big-1.webp" alt="" width="552" height="690" loading="lazy" decoding="async">
                        </div>
                        <div class="hover-slider__slide">
                            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flexGreyInfoBlockRow__big-1.webp" alt="" width="552" height="690" loading="lazy" decoding="async">
                        </div>
                    </div>
                    <div class="hover-slider__zones">
                        <div data-index="0"></div>
                        <div data-index="1"></div>
                        <div data-index="2"></div>
                    </div>
                    <div class="hover-slider__pagination">
                        <span class="active"></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
                <div class="text-block-flexTwoTypeInfoMain">
                    <p>
                        <?php echo okoyom_t( '64a36b22322f', 'Муралы' ); ?>
                    </p>
                    <div class="flex-text-block-flexTwoTypeInfoMain">
                        <span>
                            <?php echo okoyom_t( '6a986857cf56', 'пейзажи' ); ?>
                        </span>
                        <span>
                            <?php echo okoyom_t( '3f41c521bf4f', 'от 4 500 ₽/м²' ); ?>
                        </span>
                    </div>
                </div>
            </a>
            <a href="/catalog/" class="blockCardCatalog__card">
                <div class="hover-slider">
                    <div class="likeCardCatalog">
                        <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/like.svg" alt="" width="24" height="24" loading="lazy" decoding="async" aria-hidden="true">
                    </div>
                    <div class="hover-slider__slides">
                        <div class="hover-slider__slide active">
                            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flexGreyInfoBlockRow__big-1.webp" alt="" width="552" height="690" loading="lazy" decoding="async">
                        </div>
                        <div class="hover-slider__slide">
                            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flexGreyInfoBlockRow__big-1.webp" alt="" width="552" height="690" loading="lazy" decoding="async">
                        </div>
                        <div class="hover-slider__slide">
                            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flexGreyInfoBlockRow__big-1.webp" alt="" width="552" height="690" loading="lazy" decoding="async">
                        </div>
                    </div>
                    <div class="hover-slider__zones">
                        <div data-index="0"></div>
                        <div data-index="1"></div>
                        <div data-index="2"></div>
                    </div>
                    <div class="hover-slider__pagination">
                        <span class="active"></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
                <div class="text-block-flexTwoTypeInfoMain">
                    <p>
                        <?php echo okoyom_t( '64a36b22322f', 'Муралы' ); ?>
                    </p>
                    <div class="flex-text-block-flexTwoTypeInfoMain">
                        <span>
                            <?php echo okoyom_t( '6a986857cf56', 'пейзажи' ); ?>
                        </span>
                        <span>
                            <?php echo okoyom_t( '3f41c521bf4f', 'от 4 500 ₽/м²' ); ?>
                        </span>
                    </div>
                </div>
            </a>
            <a href="/catalog/" class="blockCardCatalog__card">
                <div class="hover-slider">
                    <div class="likeCardCatalog">
                        <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/like.svg" alt="" width="24" height="24" loading="lazy" decoding="async" aria-hidden="true">
                    </div>
                    <div class="hover-slider__slides">
                        <div class="hover-slider__slide active">
                            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flexGreyInfoBlockRow__big-1.webp" alt="" width="552" height="690" loading="lazy" decoding="async">
                        </div>
                        <div class="hover-slider__slide">
                            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flexGreyInfoBlockRow__big-1.webp" alt="" width="552" height="690" loading="lazy" decoding="async">
                        </div>
                        <div class="hover-slider__slide">
                            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flexGreyInfoBlockRow__big-1.webp" alt="" width="552" height="690" loading="lazy" decoding="async">
                        </div>
                    </div>
                    <div class="hover-slider__zones">
                        <div data-index="0"></div>
                        <div data-index="1"></div>
                        <div data-index="2"></div>
                    </div>
                    <div class="hover-slider__pagination">
                        <span class="active"></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
                <div class="text-block-flexTwoTypeInfoMain">
                    <p>
                        <?php echo okoyom_t( '64a36b22322f', 'Муралы' ); ?>
                    </p>
                    <div class="flex-text-block-flexTwoTypeInfoMain">
                        <span>
                            <?php echo okoyom_t( '6a986857cf56', 'пейзажи' ); ?>
                        </span>
                        <span>
                            <?php echo okoyom_t( '3f41c521bf4f', 'от 4 500 ₽/м²' ); ?>
                        </span>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>
<section class="sectionMain sectionMainV2">
    <div class="container">
        <div class="titleSection">
            <span class="titleSectionSpan">
               <?php echo okoyom_t( '2c7efaa0f0d4', 'Дополните пространство' ); ?>
            </span>
            <h2 class="titleSectionTitle">
                <?php echo okoyom_t( '970a7a275077', 'Фоновое покрытие в тон муралу' ); ?>
            </h2>
        </div>
        <div class="flexCardLastInfoBlock">
            <div class="photo-left-flexCardLastInfoBlock">
                <div class="photo-left-flexCardLastInfoBlock__photoBlock">
                    <img class="photo-left-flexCardLastInfoBlock__photo" src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flexCard3BlocksPhoto.webp" alt="" width="247" height="317" loading="lazy" decoding="async">
                    <p>
                        <?php echo okoyom_t( 'b069b3d8ff01', 'в тон муралу' ); ?>
                    </p>
                </div>
                <div class="contentText-photo-left-flexCardLastInfoBlock">
                    <p class="text-contentText-photo-left-flexCardLastInfoBlock">
                        <?php echo okoyom_t( 'e1aa752f1530', 'Изготовим однотонные фоновые обои для соседних стен — в любом цвете из палитры «Дальние хребты» и на том же материале, что и сам мурал.' ); ?>
                    </p>
                    <div class="flex-contentText-photo-left-flexCardLastInfoBlock">
                        <div class="block-flex-contentText-photo-left-flexCardLastInfoBlock">
                            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/block-flex-contentText-photo-left-flexCardLastInfoBlock-1.svg" alt="" width="16" height="16" loading="lazy" decoding="async" aria-hidden="true">
                            <div class="text-block-flex-contentText-photo-left-flexCardLastInfoBlock">
                                <p>
                                    <?php echo okoyom_t( 'a1275a1c9ca2', 'Любой цвет из мурала' ); ?>
                                </p>
                                <span>
                                    <?php echo okoyom_t( '3ec5db39395d', 'Подберём оттенок точно по образцу художественного полотна.' ); ?>
                                </span>
                            </div>
                        </div>
                        <div class="block-flex-contentText-photo-left-flexCardLastInfoBlock">
                            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/block-flex-contentText-photo-left-flexCardLastInfoBlock-2.svg" alt="" width="16" height="16" loading="lazy" decoding="async" aria-hidden="true">
                            <div class="text-block-flex-contentText-photo-left-flexCardLastInfoBlock">
                                <p>
                                    <?php echo okoyom_t( '296a740ac38a', 'Тот же материал' ); ?>
                                </p>
                                <span>
                                    <?php echo okoyom_t( 'cd64df5b90f6', 'Единая фактура и качество печати по всей композиции.' ); ?>
                                </span>
                            </div>
                        </div>
                        <div class="block-flex-contentText-photo-left-flexCardLastInfoBlock">
                            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/block-flex-contentText-photo-left-flexCardLastInfoBlock-3.svg" alt="" width="16" height="16" loading="lazy" decoding="async" aria-hidden="true">
                            <div class="text-block-flex-contentText-photo-left-flexCardLastInfoBlock">
                                <p>
                                    <?php echo okoyom_t( '2b2d04c22334', 'Печать по размеру стены' ); ?>
                                </p>
                                <span>
                                    <?php echo okoyom_t( '0ca93cdabfe9', 'Бесшовное полотно под точные параметры помещения.' ); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="line-flexCardLastInfoBlock"></div>
            <div class="right-flexCardLastInfoBlock">
                <div class="flexTop-right-flexCardLastInfoBlock">
                    <p class="text-title2-right-flex-cardSectionContent">
                        <?php echo okoyom_t( '5a427f3dada6', 'Расчёт стоимости' ); ?>
                    </p>
<!--                    <div class="material-select">-->
<!--                        <div class="material-select__head">-->
<!--                            <div class="material-select__label">-->
<!--                                Материал-->
<!--                            </div>-->
<!--                            <div class="material-select__current">-->
<!--                            <span class="material-select__value">-->
<!--                                Винил на флизелине-->
<!--                            </span>-->
<!--                                <span class="material-select__arrow"></span>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="material-select__dropdown">-->
<!--                            <div class="material-select__list">-->
<!--                                <button class="material-select__item">-->
<!--                                    <div class="material-select__left">-->
<!--                                        <span class="material-select__dot"></span>-->
<!--                                        <span class="material-select__name">-->
<!--                                        Флизелин премиум-->
<!--                                    </span>-->
<!--                                    </div>-->
<!--                                </button>-->
<!--                                <button class="material-select__item">-->
<!--                                    <div class="material-select__left">-->
<!--                                        <span class="material-select__dot"></span>-->
<!--                                        <span class="material-select__name">-->
<!--                                        Флизелин коммерческий-->
<!--                                    </span>-->
<!--                                    </div>-->
<!--                                    <span class="material-select__percent">-->
<!--                                    +15%-->
<!--                                </span>-->
<!--                                </button>-->
<!--                                <button class="material-select__item is-active">-->
<!--                                    <div class="material-select__left">-->
<!--                                        <span class="material-select__dot"></span>-->
<!--                                        <span class="material-select__name">-->
<!--                                        Винил на флизелине-->
<!--                                    </span>-->
<!--                                    </div>-->
<!--                                    <span class="material-select__percent">-->
<!--                                    +35%-->
<!--                                </span>-->
<!--                                </button>-->
<!--                                <button class="material-select__item">-->
<!--                                    <div class="material-select__left">-->
<!--                                        <span class="material-select__dot"></span>-->
<!--                                        <span class="material-select__name">-->
<!--                                        Текстильное покрытие-->
<!--                                    </span>-->
<!--                                    </div>-->
<!--                                    <span class="material-select__percent">-->
<!--                                    +50%-->
<!--                                </span>-->
<!--                                </button>-->
<!--                                <button class="material-select__item">-->
<!--                                    <div class="material-select__left">-->
<!--                                        <span class="material-select__dot"></span>-->
<!--                                        <span class="material-select__name">-->
<!--                                        Самоклеящаяся плёнка-->
<!--                                    </span>-->
<!--                                    </div>-->
<!--                                    <span class="material-select__percent">-->
<!--                                    +20%-->
<!--                                </span>-->
<!--                                </button>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
                    <div class="flexForm-right-flex-cardSectionContent">
                        <label>
                            <span>
                                <?php echo okoyom_t( '82f235bf1c13', 'Материал' ); ?>
                            </span>
                            <input disabled type="text" placeholder="300" value="Основа">
                        </label>
                    </div>
                    <div class="flexForm-right-flex-cardSectionContent">
                        <label>
                        <span>
                            <?php echo okoyom_t( '0b3365ed791d', 'Ширина, см' ); ?>
                        </span>
                            <input type="text" placeholder="300" value="300">
                        </label>
                        <label>
                        <span>
                            <?php echo okoyom_t( '12a131ae3b8d', 'Высота, см' ); ?>
                        </span>
                            <input type="text" placeholder="300" value="300">
                        </label>
                    </div>
                </div>
                <div class="flexTop-right-flexCardLastInfoBlock">
                    <div class="flexPriceInfo-right-flex-cardSectionContent">
                        <div class="line-flexPriceInfo-right-flex-cardSectionContent">
                            <span>
                                <?php echo okoyom_t( '746ebf524c94', 'Площадь' ); ?>
                            </span>
                            <span style="color: rgba(22, 20, 18, 0.65);">
                                 <?php echo okoyom_t( '7bce9f0e600e', '7.80 м²' ); ?>
                            </span>
                        </div>
                        <div class="line-flexPriceInfo-right-flex-cardSectionContent">
                            <span>
                                <?php echo okoyom_t( '82f235bf1c13', 'Материал' ); ?>
                            </span>
                            <span style="color: rgba(22, 20, 18, 0.65);">
                                <?php echo okoyom_t( '15a8fe50396e', 'Основа' ); ?>
                            </span>
                        </div>
                        <div class="line-flexPriceInfo-right-flex-cardSectionContent">
                        <span>
                            <?php echo okoyom_t( '3e1b860b3d53', 'Ориентировочно' ); ?>
                        </span>
                            <h2>
                                35 100 ₽
                            </h2>
                        </div>
                    </div>
                    <div class="flexBottom-right-flexCardLastInfoBlock">
                        <a href="#!" class="btnWhiteTextBtn btnWhiteTextBtnV3">
                            <?php echo okoyom_t( '4206957bb2cf', 'Отправить запрос' ); ?>
                        </a>
                        <p style="text-align: center" class="textright-flex-cardSectionContent-123">
                            <?php echo okoyom_t( 'b475696f02c1', 'Подберём оттенок по образцу и пришлём визуализацию' ); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>









<!--<section class="sectionMain">-->
<!--    <div class="container">-->
<!--        <div class="flexSectionCardFreeSections">-->
<!--            <div class="left-flexSectionCardFreeSections">-->
<!--                <div class="title-right-flexGreyInfoBlockRow__block">-->
<!--                    <h2 class="titleSectionTitle">-->
<!--                        Фоновые <br>-->
<!--                        покрытия-->
<!--                    </h2>-->
<!--                    <p>-->
<!--                        Подобрали обои в тон для соседних стен.-->
<!--                    </p>-->
<!--                    <a href="#" class="material-link">-->
<!--                        Подробнее о материалах →-->
<!--                    </a>-->
<!--                </div>-->
<!--            </div>-->
<!--            <div class="right-flexSectionCardFreeSections">-->
<!--                <div class="block-right-flexSectionCardFreeSections">-->
<!--                    <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flexCard3BlocksPhoto.webp" alt="" width="247" height="317" loading="lazy" decoding="async">-->
<!--                    <h3><?php echo okoyom_t( 'f89eeab23a79', 'Silentia Fog' ); ?></h3>-->
<!--                </div>-->
<!--                <div class="block-right-flexSectionCardFreeSections">-->
<!--                    <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flexCard3BlocksPhoto.webp" alt="" width="247" height="317" loading="lazy" decoding="async">-->
<!--                    <h3><?php echo okoyom_t( 'f89eeab23a79', 'Silentia Fog' ); ?></h3>-->
<!--                </div>-->
<!--                <div class="block-right-flexSectionCardFreeSections">-->
<!--                    <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flexCard3BlocksPhoto.webp" alt="" width="247" height="317" loading="lazy" decoding="async">-->
<!--                    <h3><?php echo okoyom_t( 'f89eeab23a79', 'Silentia Fog' ); ?></h3>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</section>-->
<section class="sectionMain">
    <div class="container">
        <div class="titleSection">
            <span class="titleSectionSpan">
               <?php echo okoyom_t( '04fbee09e388', 'Процесс' ); ?>
            </span>
            <h2 class="titleSectionTitle">
                <?php echo okoyom_t( 'e5e142a7ef3a', 'Как устроена работа' ); ?>
            </h2>
        </div>
        <div class="flexPlusesMainCards">
            <div data-aos="fade-left" data-aos-offset="200" class="block-flexPlusesMainCards">
                <span>
                    01
                </span>
                <h4>
                    <?php echo okoyom_t( '6a3f1224a13d', 'Выбор' ); ?>
                </h4>
                <p>
                    <?php echo okoyom_t( '781426bf2cdc', 'Выбираете изображение из коллекции или описываете задачу — поможем определиться с серией и форматом.' ); ?>
                </p>
            </div>
            <div data-aos="fade-left" data-aos-offset="200" class="block-flexPlusesMainCards">
                <span>
                    02
                </span>
                <h4>
                    <?php echo okoyom_t( 'c3eb9f9bff66', 'Визуализация' ); ?>
                </h4>
                <p>
                    <?php echo okoyom_t( '000fea3c08dd', 'Присылаете размеры и фото интерьера — готовим бесплатную визуализацию, прежде чем принимать решение.' ); ?>
                </p>
            </div>
            <div data-aos="fade-left" data-aos-offset="200" class="block-flexPlusesMainCards">
                <span>
                    03
                </span>
                <h4>
                    <?php echo okoyom_t( '99df38712d18', 'Адаптация' ); ?>
                </h4>
                <p>
                    <?php echo okoyom_t( '49d2ad4e033f', 'Корректируем кадрирование, цвет и пропорции. Макет утверждается до запуска в печать.' ); ?>
                </p>
            </div>
            <div data-aos="fade-left" data-aos-offset="200" class="block-flexPlusesMainCards">
                <span>
                    04
                </span>
                <h4>
                    <?php echo okoyom_t( '90376f704b35', 'Производство и доставка' ); ?>
                </h4>
                <p>
                    <?php echo okoyom_t( '1637d6fca98e', 'Производство 5–7 рабочих дней. Доставка по всей России. В комплекте — инструкция по монтажу.' ); ?>
                </p>
            </div>
        </div>

    </div>
</section>
