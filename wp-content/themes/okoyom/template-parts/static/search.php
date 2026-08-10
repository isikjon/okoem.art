<?php
defined( 'ABSPATH' ) || exit;
?>
<section class="sectionCatalogMain" style="background: url(<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/catalogMainPhoto.webp) center center no-repeat; background-size: cover;">
    <div class="container">
        <div class="titleCatalog">
            <h1 class="titleCatalog__title">
                Результаты поиска
            </h1>
            <p class="titleCatalog__text">
                Поиск по названию и артикулу
            </p>
        </div>
    </div>
</section>
<section class="sectionMain sectionMainPaddingMobile">
    <div class="container">
        <div class="tabs">
<!--            <ul class="tab-header">-->
<!--                <li class="tab-header__item js-tab-trigger active" data-tab="1">Все</li>-->
<!--                <li class="tab-header__item js-tab-trigger" data-tab="2">Муралы</li>-->
<!--                <li class="tab-header__item js-tab-trigger" data-tab="3">Фоновые обои</li>-->
<!--            </ul>-->
            <ul class="tab-content">
                <li class="tab-content__item js-tab-content active" data-tab="1">
                    <div class="flexFiltersCatalog">
                        <div class="left-flexFiltersCatalog left-flexFiltersCatalog-1234234">
                            <a href="#!" class="filterModalOpen mfilter-open">
                                <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/filters.svg" alt="Фильтры" width="24" height="24" loading="lazy" decoding="async">
                                Фильтры
                            </a>
                            <div class="mfilter">
                                <div class="mfilter__overlay"></div>
                                <div class="mfilter__panel">
                                    <div class="mfilter__head">
                                        <div class="mfilter__title">
                                            ФИЛЬТРЫ
                                        </div>
                                        <button class="mfilter__close">
                                            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/close.svg" alt="" width="40" height="40" loading="lazy" decoding="async" aria-hidden="true">
                                        </button>
                                    </div>
                                    <div class="mfilter__content">
                                        <!-- поиск -->
                                        <div class="mfilter-group">
                                            <div class="mfilter-label">
                                                ПОИСК
                                            </div>
                                            <form action="" method="post">
                                                <div class="flex-col-3__footer">
                                                    <input type="search" required placeholder="Название, артикул...">
                                                    <button type="submit">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                                            <path d="M8.625 15.75C12.56 15.75 15.75 12.56 15.75 8.625C15.75 4.68997 12.56 1.5 8.625 1.5C4.68997 1.5 1.5 4.68997 1.5 8.625C1.5 12.56 4.68997 15.75 8.625 15.75Z" stroke="#B0AEAB" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                            <path d="M16.5 16.5L15 15" stroke="#B0AEAB" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        <!-- сортировка -->
                                        <div class="mfilter-group">
                                            <div class="mfilter-label">
                                                СОРТИРОВКА
                                            </div>
                                            <div class="mfilter-list">
                                                <button class="active">
                                                    По умолчанию
                                                </button>
                                                <button>Новинки</button>
                                            </div>
                                        </div>
                                        <!-- коллекция -->
                                        <div class="mfilter-group">
                                            <div class="mfilter-label">
                                                КОЛЛЕКЦИЯ
                                            </div>
                                            <div class="mfilter-scroll-1">
                                                <div class="mfilter-scroll">
                                                    <button class="active">
                                                        Все коллекции
                                                    </button>
                                                    <button>Silentia</button>
                                                    <button>Botanica</button>
                                                    <button>Forma</button>
                                                    <button>Materia</button>
                                                    <button>Linea</button>
                                                    <button>Classic</button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- серия -->
                                        <div class="mfilter-group">
                                            <div class="mfilter-label">
                                                СЕРИЯ
                                            </div>
                                            <div class="mfilter-scroll-1">
                                                <div class="mfilter-scroll">
                                                    <button class="active">
                                                        Все коллекции
                                                    </button>
                                                    <button>Silentia</button>
                                                    <button>Botanica</button>
                                                    <button>Forma</button>
                                                    <button>Materia</button>
                                                    <button>Linea</button>
                                                    <button>Classic</button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- сюжет -->
                                        <div class="mfilter-group">
                                            <div class="mfilter-label">
                                                СЮЖЕТ
                                            </div>
                                            <div class="mfilter-scroll-1">
                                                <div class="mfilter-scroll">
                                                    <button class="active">
                                                        Все коллекции
                                                    </button>
                                                    <button>Silentia</button>
                                                    <button>Botanica</button>
                                                    <button>Forma</button>
                                                    <button>Materia</button>
                                                    <button>Linea</button>
                                                    <button>Classic</button>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- цвета -->
                                        <div class="mfilter-group">
                                            <div class="mfilter-label">
                                                ЦВЕТ
                                            </div>
                                            <div class="mfilter-colors">
                                                <button class="ui-filter__item" data-value="Зелёный">
                                                    <span class="circleFilter circleFilter-1"></span>
                                                </button>
                                                <button class="ui-filter__item" data-value="Silentia">
                                                    <span class="circleFilter circleFilter-2"></span>
                                                </button>
                                                <button class="ui-filter__item" data-value="Silentia">
                                                    <span class="circleFilter circleFilter-3"></span>
                                                </button>
                                                <button class="ui-filter__item" data-value="Silentia">
                                                    <span class="circleFilter circleFilter-4"></span>
                                                </button>
                                                <button class="ui-filter__item" data-value="Silentia">
                                                    <span class="circleFilter circleFilter-5"></span>
                                                </button>
                                                <button class="ui-filter__item" data-value="Зелёный">
                                                    <span class="circleFilter circleFilter-6"></span>
                                                </button>
                                                <button class="ui-filter__item" data-value="Зелёный">
                                                    <span class="circleFilter circleFilter-7"></span>
                                                </button>
                                                <button class="ui-filter__item" data-value="Silentia">
                                                    <span class="circleFilter circleFilter-8"></span>
                                                </button>
                                                <button class="ui-filter__item" data-value="Silentia">
                                                    <span class="circleFilter circleFilter-9"></span>
                                                </button>
                                                <button class="ui-filter__item" data-value="Silentia">
                                                    <span class="circleFilter circleFilter-10"></span>
                                                </button>
                                                <button class="ui-filter__item" data-value="Silentia">
                                                    <span class="circleFilter circleFilter-11"></span>
                                                </button>
                                                <button class="ui-filter__item" data-value="Silentia">
                                                    <span class="circleFilter circleFilter-12"></span>
                                                </button>
                                                <button class="ui-filter__item" data-value="Silentia">
                                                    <span class="circleFilter circleFilter-13"></span>
                                                </button>
                                                <button class="ui-filter__item" data-value="Silentia">
                                                    <span class="circleFilter circleFilter-14"></span>
                                                </button>
                                                <button class="ui-filter__item" data-value="Silentia">
                                                    <span class="circleFilter circleFilter-15"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mfilter-bottom">
                                        <button class="mfilter-reset">
                                            СБРОСИТЬ
                                        </button>
                                        <button class="mfilter-show">
                                            ПОКАЗАТЬ (17)
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="ui-filter ui-filter-1">
                                <button class="ui-filter__trigger" type="button">
                                    <span class="ui-filter__label">
                                        Коллекция:
                                    </span>
                                    <span class="ui-filter__value">
                                        Все
                                    </span>
                                    <span class="ui-filter__arrow"></span>
                                </button>
                                <div class="ui-filter__dropdown">
                                    <div class="ui-filter__list">
                                        <button class="ui-filter__item" data-value="Все">
                                            <span>Все</span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Silentia">
                                            <span>Silentia</span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Botanica">
                                            <span>Botanica</span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Forma">
                                            <span>Forma</span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Materia">
                                            <span>Materia</span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                    </div>

                                </div>
                            </div>
                            <div class="ui-filter ui-filter-4">
                                <button class="ui-filter__trigger" type="button">
                                    <span class="ui-filter__label">
                                        Cерия:
                                    </span>
                                    <span class="ui-filter__value">
                                        Все
                                    </span>
                                    <span class="ui-filter__arrow"></span>
                                </button>
                                <div class="ui-filter__dropdown">
                                    <div class="ui-filter__list">
                                        <button class="ui-filter__item" data-value="Все">
                                            <span>Все</span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Silentia">
                                            <span>Silentia</span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Botanica">
                                            <span>Botanica</span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Forma">
                                            <span>Forma</span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Materia">
                                            <span>Materia</span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                    </div>

                                </div>
                            </div>
                            <div class="ui-filter ui-filter-3">
                                <button class="ui-filter__trigger" type="button">
                                    <span class="ui-filter__label">
                                        Сюжет:
                                    </span>
                                    <span class="ui-filter__value">
                                        Все
                                    </span>
                                    <span class="ui-filter__arrow"></span>
                                </button>
                                <div class="ui-filter__dropdown">
                                    <div class="ui-filter__list">
                                        <button class="ui-filter__item" data-value="Все">
                                            <span>Все</span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Silentia">
                                            <span>Silentia</span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Botanica">
                                            <span>Botanica</span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Forma">
                                            <span>Forma</span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Materia">
                                            <span>Materia</span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                    </div>

                                </div>
                            </div>
                            <div class="ui-filter ui-filter-2">
                                <button class="ui-filter__trigger" type="button">
                                    <span class="ui-filter__label">
                                        Цвет:
                                    </span>
                                    <span class="ui-filter__value">
                                        Все
                                    </span>
                                    <span class="ui-filter__arrow"></span>
                                </button>
                                <div class="ui-filter__dropdown">
                                    <div class="ui-filter__list">
                                        <button class="ui-filter__item" data-value="Зелёный">
                                            <span class="circleFilter circleFilter-1"></span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Silentia">
                                            <span class="circleFilter circleFilter-2"></span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Silentia">
                                            <span class="circleFilter circleFilter-3"></span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Silentia">
                                            <span class="circleFilter circleFilter-4"></span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Silentia">
                                            <span class="circleFilter circleFilter-5"></span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Зелёный">
                                            <span class="circleFilter circleFilter-6"></span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Зелёный">
                                            <span class="circleFilter circleFilter-7"></span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Silentia">
                                            <span class="circleFilter circleFilter-8"></span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Silentia">
                                            <span class="circleFilter circleFilter-9"></span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Silentia">
                                            <span class="circleFilter circleFilter-10"></span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Silentia">
                                            <span class="circleFilter circleFilter-11"></span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Silentia">
                                            <span class="circleFilter circleFilter-12"></span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Silentia">
                                            <span class="circleFilter circleFilter-13"></span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Silentia">
                                            <span class="circleFilter circleFilter-14"></span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                        <button class="ui-filter__item" data-value="Silentia">
                                            <span class="circleFilter circleFilter-15"></span>
                                            <span class="ui-filter__check"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="left-flexFiltersCatalog left-flexFiltersCatalog-1234">
                            <form action="" method="post">
                                <div class="flex-col-3__footer">
                                    <input type="search" required placeholder="Поиск">
                                    <button type="submit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                            <path d="M8.625 15.75C12.56 15.75 15.75 12.56 15.75 8.625C15.75 4.68997 12.56 1.5 8.625 1.5C4.68997 1.5 1.5 4.68997 1.5 8.625C1.5 12.56 4.68997 15.75 8.625 15.75Z" stroke="#B0AEAB" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M16.5 16.5L15 15" stroke="#B0AEAB" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </div>
                            </form>
<!--                            <div class="ui-filter ui-filter-5">-->
<!--                                <button class="ui-filter__trigger" type="button">-->
<!--                                    <span class="ui-filter__label">-->
<!--                                        Сортировка-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__value">-->
<!--                                        Популярные-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__arrow"></span>-->
<!--                                </button>-->
<!--                                <div class="ui-filter__dropdown">-->
<!--                                    <div class="ui-filter__list">-->
<!--                                        <button class="ui-filter__item" data-value="Все">-->
<!--                                            <span>Популярные</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span>Silentia</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Botanica">-->
<!--                                            <span>Botanica</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Forma">-->
<!--                                            <span>Forma</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Materia">-->
<!--                                            <span>Materia</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                    </div>-->

<!--                                </div>-->
<!--                            </div>-->
                            <span class="textSpanQuantityCatalog"><?php echo esc_html( okoyom_catalog_count() ); ?></span>
                        </div>
                    </div>
                    <div class="flexTwoTypeInfoMain flexTwoTypeInfoMain-2">
        <?php okoyom_catalog_grid( 'all' ); ?>
    </div>
                    <div class="btnsFlexCenter btnsFlexCenter-22">
                        <a href="#!" class="btnWhiteTextBtn btnWhiteTextBtnV2">
                            Смотреть ещё
                        </a>
                    </div>
                </li>
<!--                <li class="tab-content__item js-tab-content" data-tab="2">-->
<!--                    <div class="flexFiltersCatalog">-->
<!--                        <div class="left-flexFiltersCatalog">-->
<!--                            <div class="ui-filter ui-filter-1">-->
<!--                                <button class="ui-filter__trigger" type="button">-->
<!--                                    <span class="ui-filter__label">-->
<!--                                        Коллекция:-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__value">-->
<!--                                        Все-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__arrow"></span>-->
<!--                                </button>-->
<!--                                <div class="ui-filter__dropdown">-->
<!--                                    <div class="ui-filter__list">-->
<!--                                        <button class="ui-filter__item" data-value="Все">-->
<!--                                            <span>Все</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span>Silentia</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Botanica">-->
<!--                                            <span>Botanica</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Forma">-->
<!--                                            <span>Forma</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Materia">-->
<!--                                            <span>Materia</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                    </div>-->

<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="ui-filter ui-filter-2">-->
<!--                                <button class="ui-filter__trigger" type="button">-->
<!--                                    <span class="ui-filter__label">-->
<!--                                        Цвет:-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__value">-->
<!--                                        Все-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__arrow"></span>-->
<!--                                </button>-->
<!--                                <div class="ui-filter__dropdown">-->
<!--                                    <div class="ui-filter__list">-->
<!--                                        <button class="ui-filter__item" data-value="Зелёный">-->
<!--                                            <span class="circleFilter circleFilter-1"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-2"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-3"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-4"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-5"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Зелёный">-->
<!--                                            <span class="circleFilter circleFilter-6"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Зелёный">-->
<!--                                            <span class="circleFilter circleFilter-7"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-8"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-9"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-10"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-11"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-12"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-13"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-14"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-15"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="ui-filter ui-filter-3">-->
<!--                                <button class="ui-filter__trigger" type="button">-->
<!--                                    <span class="ui-filter__label">-->
<!--                                        Сюжет:-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__value">-->
<!--                                        Все-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__arrow"></span>-->
<!--                                </button>-->
<!--                                <div class="ui-filter__dropdown">-->
<!--                                    <div class="ui-filter__list">-->
<!--                                        <button class="ui-filter__item" data-value="Все">-->
<!--                                            <span>Все</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span>Silentia</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Botanica">-->
<!--                                            <span>Botanica</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Forma">-->
<!--                                            <span>Forma</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Materia">-->
<!--                                            <span>Materia</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                    </div>-->

<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="ui-filter ui-filter-4">-->
<!--                                <button class="ui-filter__trigger" type="button">-->
<!--                                    <span class="ui-filter__label">-->
<!--                                        Помещение:-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__value">-->
<!--                                        Все-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__arrow"></span>-->
<!--                                </button>-->
<!--                                <div class="ui-filter__dropdown">-->
<!--                                    <div class="ui-filter__list">-->
<!--                                        <button class="ui-filter__item" data-value="Все">-->
<!--                                            <span>Все</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span>Silentia</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Botanica">-->
<!--                                            <span>Botanica</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Forma">-->
<!--                                            <span>Forma</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Materia">-->
<!--                                            <span>Materia</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                    </div>-->

<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="left-flexFiltersCatalog">-->
<!--                            <div class="ui-filter ui-filter-5">-->
<!--                                <button class="ui-filter__trigger" type="button">-->
<!--                                    <span class="ui-filter__label">-->
<!--                                        Сортировка:-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__value">-->
<!--                                        Популярные-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__arrow"></span>-->
<!--                                </button>-->
<!--                                <div class="ui-filter__dropdown">-->
<!--                                    <div class="ui-filter__list">-->
<!--                                        <button class="ui-filter__item" data-value="Все">-->
<!--                                            <span>Популярные</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span>Silentia</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Botanica">-->
<!--                                            <span>Botanica</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Forma">-->
<!--                                            <span>Forma</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Materia">-->
<!--                                            <span>Materia</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                    </div>-->

<!--                                </div>-->
<!--                            </div>-->
<!--                            <span class="textSpanQuantityCatalog">-->
<!--                                23 работ-->
<!--                            </span>-->
<!--                        </div>-->

<!--                    </div>-->
<!--                    <div class="flexTwoTypeInfoMain flexTwoTypeInfoMain-2">
        <?php okoyom_catalog_grid( 'murals' ); ?>
    </div>-->
<!--                    <div class="btnsFlexCenter btnsFlexCenter-22">-->
<!--                        <a href="#!" class="btnWhiteTextBtn btnWhiteTextBtnV2">-->
<!--                            Смотреть ещё-->
<!--                        </a>-->
<!--                    </div>-->
<!--                </li>-->
<!--                <li class="tab-content__item js-tab-content" data-tab="3">-->
<!--                    <div class="flexFiltersCatalog">-->
<!--                        <div class="left-flexFiltersCatalog">-->
<!--                            <div class="ui-filter ui-filter-1">-->
<!--                                <button class="ui-filter__trigger" type="button">-->
<!--                                    <span class="ui-filter__label">-->
<!--                                        Коллекция:-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__value">-->
<!--                                        Все-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__arrow"></span>-->
<!--                                </button>-->
<!--                                <div class="ui-filter__dropdown">-->
<!--                                    <div class="ui-filter__list">-->
<!--                                        <button class="ui-filter__item" data-value="Все">-->
<!--                                            <span>Все</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span>Silentia</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Botanica">-->
<!--                                            <span>Botanica</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Forma">-->
<!--                                            <span>Forma</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Materia">-->
<!--                                            <span>Materia</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                    </div>-->

<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="ui-filter ui-filter-2">-->
<!--                                <button class="ui-filter__trigger" type="button">-->
<!--                                    <span class="ui-filter__label">-->
<!--                                        Цвет:-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__value">-->
<!--                                        Все-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__arrow"></span>-->
<!--                                </button>-->
<!--                                <div class="ui-filter__dropdown">-->
<!--                                    <div class="ui-filter__list">-->
<!--                                        <button class="ui-filter__item" data-value="Зелёный">-->
<!--                                            <span class="circleFilter circleFilter-1"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-2"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-3"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-4"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-5"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Зелёный">-->
<!--                                            <span class="circleFilter circleFilter-6"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Зелёный">-->
<!--                                            <span class="circleFilter circleFilter-7"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-8"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-9"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-10"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-11"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-12"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-13"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-14"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span class="circleFilter circleFilter-15"></span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="ui-filter ui-filter-3">-->
<!--                                <button class="ui-filter__trigger" type="button">-->
<!--                                    <span class="ui-filter__label">-->
<!--                                        Сюжет:-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__value">-->
<!--                                        Все-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__arrow"></span>-->
<!--                                </button>-->
<!--                                <div class="ui-filter__dropdown">-->
<!--                                    <div class="ui-filter__list">-->
<!--                                        <button class="ui-filter__item" data-value="Все">-->
<!--                                            <span>Все</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span>Silentia</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Botanica">-->
<!--                                            <span>Botanica</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Forma">-->
<!--                                            <span>Forma</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Materia">-->
<!--                                            <span>Materia</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                    </div>-->

<!--                                </div>-->
<!--                            </div>-->
<!--                            <div class="ui-filter ui-filter-4">-->
<!--                                <button class="ui-filter__trigger" type="button">-->
<!--                                    <span class="ui-filter__label">-->
<!--                                        Помещение:-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__value">-->
<!--                                        Все-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__arrow"></span>-->
<!--                                </button>-->
<!--                                <div class="ui-filter__dropdown">-->
<!--                                    <div class="ui-filter__list">-->
<!--                                        <button class="ui-filter__item" data-value="Все">-->
<!--                                            <span>Все</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span>Silentia</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Botanica">-->
<!--                                            <span>Botanica</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Forma">-->
<!--                                            <span>Forma</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Materia">-->
<!--                                            <span>Materia</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                    </div>-->

<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="left-flexFiltersCatalog">-->
<!--                            <div class="ui-filter ui-filter-5">-->
<!--                                <button class="ui-filter__trigger" type="button">-->
<!--                                    <span class="ui-filter__label">-->
<!--                                        Сортировка:-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__value">-->
<!--                                        Популярные-->
<!--                                    </span>-->
<!--                                    <span class="ui-filter__arrow"></span>-->
<!--                                </button>-->
<!--                                <div class="ui-filter__dropdown">-->
<!--                                    <div class="ui-filter__list">-->
<!--                                        <button class="ui-filter__item" data-value="Все">-->
<!--                                            <span>Популярные</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Silentia">-->
<!--                                            <span>Silentia</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Botanica">-->
<!--                                            <span>Botanica</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Forma">-->
<!--                                            <span>Forma</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                        <button class="ui-filter__item" data-value="Materia">-->
<!--                                            <span>Materia</span>-->
<!--                                            <span class="ui-filter__check"></span>-->
<!--                                        </button>-->
<!--                                    </div>-->

<!--                                </div>-->
<!--                            </div>-->
<!--                            <span class="textSpanQuantityCatalog">-->
<!--                                23 работ-->
<!--                            </span>-->
<!--                        </div>-->

<!--                    </div>-->
<!--                    <div class="flexTwoTypeInfoMain flexTwoTypeInfoMain-2">
        <?php okoyom_catalog_grid( 'companion' ); ?>
    </div>-->
<!--                    <div class="btnsFlexCenter btnsFlexCenter-22">-->
<!--                        <a href="#!" class="btnWhiteTextBtn btnWhiteTextBtnV2">-->
<!--                            Смотреть ещё-->
<!--                        </a>-->
<!--                    </div>-->
<!--                </li>-->
            </ul>
        </div>
    </div>
</section>
