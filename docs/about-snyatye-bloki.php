<?php
/**
 * Два блока, снятые со страницы «О студии» 20.10.2026 по просьбе заказчика.
 * Вёрстка и тексты сохранены дословно, чтобы вернуть их одной вставкой
 * в template-parts/static/about.php на прежнее место — между секцией
 * «Производство» и секцией с кнопкой внизу страницы.
 *
 * Блок 1 — «Что отличает Окоём» (Масштаб, Детализация, Погружение).
 * Блок 2 — «Лучше один раз увидеть вживую».
 */
?>
<section class="sectionMain">
    <div class="container">
        <div class="titleSection">
            <span class="titleSectionSpan">
               <?php echo okoyom_t( 'ca33447258c2', 'Подход' ); ?>
            </span>
            <h2 class="titleSectionTitle">
                <?php echo okoyom_t( 'aef9da18a436', 'Что отличает Окоём' ); ?>
            </h2>
        </div>
        <div class="flexGreyInfoBlockRow">
            <div data-aos="fade-up" data-aos-offset="200" class="flexGreyInfoBlockRow__block flexGreyInfoBlockRow__block-1 aos-init aos-animate">
                <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flexGreyInfoBlockRow__big-111.webp" alt="" class="flexGreyInfoBlockRow__big" width="552" height="690" loading="lazy" decoding="async">
                <div class="right-flexGreyInfoBlockRow__block">
                    <div class="title-right-flexGreyInfoBlockRow__block">
                        <h3>
                            <?php echo okoyom_t( '0883abc602e9', 'Масштаб' ); ?>
                        </h3>
                        <span style="letter-spacing: 1.32px; text-transform: uppercase;">
                           <?php echo okoyom_t( 'ff7635913dd3', 'Нарисовано в размере стены' ); ?>
                        </span>
                    </div>
                    <p class="text-right-flexGreyInfoBlockRow__block">
                        <?php echo okoyom_t( 'a62ab1a081b6', 'Мурал не растягивают — его пишут сразу в масштабе вашей комнаты. Поэтому он меняет восприятие пространства: угол, на который наклеен мурал, перестаёт ощущаться углом — стена будто скругляется, и комната дышит свободнее.' ); ?>
                    </p>
                </div>
            </div>
            <div data-aos="fade-up" data-aos-offset="200" class="flexGreyInfoBlockRow__block flexGreyInfoBlockRow__block-2 aos-init">
                <div class="right-flexGreyInfoBlockRow__block">
                    <div class="title-right-flexGreyInfoBlockRow__block">
                        <h3>
                            <?php echo okoyom_t( 'c72e598c1ce5', 'Детализация' ); ?>
                        </h3>
                        <span style="letter-spacing: 1.32px; text-transform: uppercase;">
                            <?php echo okoyom_t( '0e28ee4404be', 'Хочется подойти и рассмотреть' ); ?>
                        </span>
                    </div>
                    <p class="text-right-flexGreyInfoBlockRow__block">
                        <?php echo okoyom_t( '0b4ed20de51a', 'Издалека мурал держит образ, а вблизи раскрывается мазками и тонкими переходами цвета — как настоящая картина. К нему хочется подойти ближе.' ); ?>
                    </p>
                </div>
                <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flexGreyInfoBlockRow__big-222.webp" alt="" class="flexGreyInfoBlockRow__big" width="552" height="690" loading="lazy" decoding="async">
            </div>
            <div data-aos="fade-up" data-aos-offset="200" class="flexGreyInfoBlockRow__block flexGreyInfoBlockRow__block-1 aos-init aos-animate">
                <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flexGreyInfoBlockRow__big-333.webp" alt="" class="flexGreyInfoBlockRow__big" width="552" height="690" loading="lazy" decoding="async">
                <div class="right-flexGreyInfoBlockRow__block">
                    <div class="title-right-flexGreyInfoBlockRow__block">
                        <h3>
                            <?php echo okoyom_t( '626eadad2853', 'Погружение' ); ?>
                        </h3>
                        <span style="letter-spacing: 1.32px; text-transform: uppercase;">
                            <?php echo okoyom_t( '6d685d9e0201', 'Стена перестаёт быть стеной' ); ?>
                        </span>
                    </div>
                    <p class="text-right-flexGreyInfoBlockRow__block">
                        <?php echo okoyom_t( '3091d38d4d1f', 'Мурал создаёт атмосферу, в которой хочется задержаться взглядом. Комната ощущается глубже и спокойнее — особенно в пейзажах и атмосферных сериях.' ); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="sectionMain sectionMainV2">
    <div class="container">
        <div class="flexAlignV2Address flexAlignV2Address__23">
            <div class="left-flexAlignV2Address">
                <div class="titleSection">
                    <span class="titleSectionSpan">
                       <?php echo okoyom_t( '0561b1ff7d04', 'Шоурум' ); ?>
                    </span>
                    <h2 class="titleSectionTitle">
                        <?php echo okoyom_t( '4d03663b44f1', 'Лучше один раз увидеть вживую' ); ?>
                    </h2>
                    <p class="textTitleSection " style="margin-top: 40px !important;">
                        <?php echo okoyom_t( 'f8df4221d39f', 'Экран не передаёт главного — глубины цвета, фактуры материала и того, как мурал начинает работать в реальном масштабе. Скоро в Москве откроется шоурум Окоём, где это можно увидеть лично.' ); ?>
                    </p>
                </div>
                <div class="flex-contentText-photo-left-flexCardLastInfoBlock">
                    <div class="block-flex-contentText-photo-left-flexCardLastInfoBlock">
                        <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flexAlignV2Address-1.svg" alt="" width="16" height="16" loading="lazy" decoding="async" aria-hidden="true">
                        <div class="text-block-flex-contentText-photo-left-flexCardLastInfoBlock">
                            <span>
                                <?php echo okoyom_t( 'defed2ea3a6f', 'Шоурум в Москве' ); ?>
                            </span>
                            <p>
                                <?php echo okoyom_t( '1ffe2ca832f1', 'Открытие в ближайшее время · адрес уточняется' ); ?>
                            </p>
                        </div>
                    </div>
                    <div class="block-flex-contentText-photo-left-flexCardLastInfoBlock">
                        <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flexAlignV2Address-2.svg" alt="" width="16" height="16" loading="lazy" decoding="async" aria-hidden="true">
                        <div class="text-block-flex-contentText-photo-left-flexCardLastInfoBlock">
                            <span>
                                <?php echo okoyom_t( '52fc67f6f4fd', 'Пока — образцы и визуализация' ); ?>
                            </span>
                            <p>
                                <?php echo okoyom_t( 'c5a9ea8356b2', 'Пришлём материальный сет и покажем мурал в вашем интерьере' ); ?>
                            </p>
                        </div>
                    </div>
                    <div class="block-flex-contentText-photo-left-flexCardLastInfoBlock">
                        <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/flexAlignV2Address-3.svg" alt="" width="16" height="16" loading="lazy" decoding="async" aria-hidden="true">
                        <div class="text-block-flex-contentText-photo-left-flexCardLastInfoBlock">
                            <span>
                                <?php echo okoyom_t( 'ea9dfd52c607', 'Узнать первыми' ); ?>

                            </span>
                            <p>
                                <?php echo esc_html( okoyom_option( 'phone' ) ); ?> <?php echo okoyom_t( '7504dd87874e', '· оставьте контакт, сообщим об открытии' ); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="left-flexAlignV2Address__btn">
                    <div style="justify-content: left" class="btnsFlexCenter">
                        <a href="#!" class="btnWhiteTextBtn btnWhiteTextBtnV3">
                            <?php echo okoyom_t( 'dff3d9c79601', 'Узнать об открытии' ); ?>
                        </a>
                        <a href="/contacts/" class="btnWhiteTextBtn btnWhiteTextBtnV2">
                            <?php echo okoyom_t( '75768c49c246', 'Контакты' ); ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                <path d="M2.9165 7H11.0832" stroke="#161412" stroke-opacity="0.9" stroke-width="0.729167" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7 2.91699L11.0833 7.00033L7 11.0837" stroke="#161412" stroke-opacity="0.9" stroke-width="0.729167" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                    <p class="textright-flex-cardSectionContent-123">
                        <?php echo okoyom_t( 'c69c42cb6d28', 'Представительство в Санкт-Петербурге — ТЦ «Интерио». В других городах организуем выезд с образцами или присылаем материальный сет курьером.' ); ?>
                    </p>
                </div>
            </div>
            <div class="right-flexAlignV2Address">
                <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/left-flexSectionAboutFreeSections.webp" alt="" width="543" height="677" loading="lazy" decoding="async">
                <p class="textright-flex-cardSectionContent-123">
                    <?php echo okoyom_t( 'e6eb3a7361bf', 'Москва' ); ?>
                </p>
            </div>
        </div>
    </div>
</section>
