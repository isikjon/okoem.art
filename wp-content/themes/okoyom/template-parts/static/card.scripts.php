<?php
defined( 'ABSPATH' ) || exit;
?>
<script>
    const heroSection = document.getElementById('muralHero');
    const sliderEl = heroSection.querySelector('.mural-hero__slider');
    const parallaxLayers = heroSection.querySelectorAll('.js-hero-parallax');

    const muralHeroSlider = new Swiper('.mural-hero__slider', {
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
        loop: true,
        speed: 1400,
        allowTouchMove: true,
        autoplay: {
            delay: 4500,
            disableOnInteraction: false,
            pauseOnMouseEnter: true
        },
        pagination: {
            el: '.mural-hero__pagination',
            clickable: true
        }
    });

    /* Клик по половинам экрана */
    sliderEl.addEventListener('click', function (event) {
        const interactive = event.target.closest('a, button, .swiper-pagination-bullet');
        if (interactive) return;

        const rect = sliderEl.getBoundingClientRect();
        const clickX = event.clientX - rect.left;
        const isLeftHalf = clickX < rect.width / 2;

        if (isLeftHalf) {
            muralHeroSlider.slidePrev();
        } else {
            muralHeroSlider.slideNext();
        }
    });

    /* Убираем intro-класс после первой анимации */
    window.addEventListener('load', () => {
        setTimeout(() => {
            heroSection.classList.remove('mural-hero--intro');
        }, 1700);
    });

    /* Параллакс фона при скролле */
    let ticking = false;

    function updateHeroParallax() {
        const rect = heroSection.getBoundingClientRect();
        const viewportHeight = window.innerHeight || document.documentElement.clientHeight;

        if (rect.bottom < 0 || rect.top > viewportHeight) {
            ticking = false;
            return;
        }

        const progress = Math.max(-1, Math.min(1, rect.top / viewportHeight));
        const translateY = progress * -60;

        parallaxLayers.forEach((layer) => {
            layer.style.transform = `translate3d(0, ${translateY}px, 0) scale(1.04)`;
        });

        ticking = false;
    }

    function requestParallaxUpdate() {
        if (!ticking) {
            requestAnimationFrame(updateHeroParallax);
            ticking = true;
        }
    }

    window.addEventListener('scroll', requestParallaxUpdate, { passive: true });
    window.addEventListener('resize', requestParallaxUpdate);
    window.addEventListener('load', requestParallaxUpdate);

    requestParallaxUpdate();
</script>
<div id="modal__project1" class="modal modal-1">
    <div class="modal-content modal-content-1">
        <span class="close close1">
            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/close.svg" alt="" width="40" height="40" loading="lazy" decoding="async" aria-hidden="true">
        </span>
        <div class="modalContent">
            <p class="titleSectionTitle">
                Заполните форму
            </p>
            <p class="textTitleSection">
                Оставьте заявку, и мы свяжемся с вами в ближайшее время
            </p>
            <form action="mail-2.php" method="post" class="formAllProject">
                <label>
                    <input name="name" type="text" placeholder="Имя" required>
                </label>
                <label>
                    <input name="tel" type="text" placeholder="Телефон" required>
                </label>
                <label>
                    <textarea name="text" placeholder="Сообщение"></textarea>
                </label>
                <button type="submit" class="btnWhiteTextBtn btnWhiteTextBtnV3" style="width: 100%">
                    Отправить
                </button>
            </form>
        </div>
    </div>
    <script>
        let modal1 = document.getElementById("modal__project1");
        // Get the button that opens the modal
        let btn1 = document.getElementsByClassName("openModal");
        console.log()
        // Get the <span> element that closes the modal
        let span1 = document.getElementsByClassName("close1")[0];
        // When the user clicks the button, open the modal
        for(let i = 0;i < btn1.length; i++)
        {
            let v = btn1[i]
            v.onclick = function() {
                modal1.style.display = "block";
            }
        }
        // When the user clicks on <span> (x), close the modal
        span1.onclick = function() {
            modal1.style.display = "none";
        }
        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal1) {
                modal1.style.display = "none";
            }
        }
    </script>
</div>
<div id="modal__project2" class="modal modal-2">
    <div class="modal-content modal-content-1">
        <span class="close close2">
            <img src="<?php echo esc_url( OKOYOM_ASSETS_URI ); ?>/img/close.svg" alt="" width="40" height="40" loading="lazy" decoding="async" aria-hidden="true">
        </span>
        <div class="flexModalContent-2">
            <p class="titleSectionTitle">
                Фильтры
            </p>
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
            <div class="ui-filter ui-filter-4">
                <button class="ui-filter__trigger" type="button">
                                    <span class="ui-filter__label">
                                        Помещение:
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
            <div class="ui-filter ui-filter-5">
                <button class="ui-filter__trigger" type="button">
                                    <span class="ui-filter__label">
                                        Сортировка:
                                    </span>
                    <span class="ui-filter__value">
                                        Популярные
                                    </span>
                    <span class="ui-filter__arrow"></span>
                </button>
                <div class="ui-filter__dropdown">
                    <div class="ui-filter__list">
                        <button class="ui-filter__item" data-value="Все">
                            <span>Популярные</span>
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
        </div>
    </div>
    <script>
        let modal2 = document.getElementById("modal__project2");
        // Get the button that opens the modal
        let btn2 = document.getElementsByClassName("openModal2");
        console.log()
        // Get the <span> element that closes the modal
        let span2 = document.getElementsByClassName("close2")[0];
        // When the user clicks the button, open the modal
        for(let i = 0;i < btn2.length; i++)
        {
            let v = btn2[i]
            v.onclick = function() {
                modal2.style.display = "block";
            }
        }
        // When the user clicks on <span> (x), close the modal
        span2.onclick = function() {
            modal2.style.display = "none";
        }
        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal2) {
                modal2.style.display = "none";
            }
        }
    </script>
</div>
<script>

    document.querySelectorAll('.ui-filter').forEach(filter => {

        const trigger = filter.querySelector('.ui-filter__trigger');
        const value = filter.querySelector('.ui-filter__value');
        const items = filter.querySelectorAll('.ui-filter__item');

        // OPEN / CLOSE

        trigger.addEventListener('click', (e) => {

            e.stopPropagation();

            document.querySelectorAll('.ui-filter').forEach(other => {

                if(other !== filter){
                    other.classList.remove('is-open');
                }

            });

            filter.classList.toggle('is-open');

        });

        // MULTI SELECT

        items.forEach(item => {

            item.addEventListener('click', (e) => {

                e.stopPropagation();

                item.classList.toggle('is-active');

                updateValues();

            });

        });

        function updateValues(){

            const active = filter.querySelectorAll('.ui-filter__item.is-active');

            if(active.length === 0){

                value.textContent = 'Все';

                return;
            }

            const values = [...active].map(item =>
                item.dataset.value
            );

            value.textContent = values.join(', ');
        }

    });

    /* CLICK OUTSIDE */

    document.addEventListener('click', (e) => {

        document.querySelectorAll('.ui-filter').forEach(filter => {

            if(!filter.contains(e.target)){
                filter.classList.remove('is-open');
            }

        });

    });

</script>
<script>

    document.querySelectorAll('.material-select').forEach(select => {

        const head = select.querySelector('.material-select__head');
        const value = select.querySelector('.material-select__value');
        const items = select.querySelectorAll('.material-select__item');

        // OPEN / CLOSE

        head.addEventListener('click', () => {
            select.classList.toggle('is-open');
        });

        // SELECT

        items.forEach(item => {

            item.addEventListener('click', (e) => {

                e.stopPropagation();

                items.forEach(i => {
                    i.classList.remove('is-active');
                });

                item.classList.add('is-active');

                const text = item.querySelector('.material-select__name').textContent.trim();

                value.textContent = text;

                select.classList.remove('is-open');

            });

        });

        // CLICK OUTSIDE

        document.addEventListener('click', (e) => {

            if(!select.contains(e.target)){
                select.classList.remove('is-open');
            }

        });

    });

</script>
<script>

    /* =========================================
       MAIN TABS
    ========================================= */

    const mainButtons = document.querySelectorAll('.dtabs__btn');
    const mainContents = document.querySelectorAll('.dtabs__content');

    mainButtons.forEach(button => {

        button.addEventListener('click', () => {

            const tab = button.dataset.tab;

            mainButtons.forEach(btn =>
                btn.classList.remove('active')
            );

            mainContents.forEach(content =>
                content.classList.remove('active')
            );

            button.classList.add('active');

            document.getElementById(tab).classList.add('active');

        });

    });

    /* =========================================
       MATERIAL TABS
    ========================================= */

    const materialButtons = document.querySelectorAll('.material-tab');
    const materialPanes = document.querySelectorAll('.material-pane');

    materialButtons.forEach(button => {

        button.addEventListener('click', () => {

            const tab = button.dataset.material;

            materialButtons.forEach(btn =>
                btn.classList.remove('active')
            );

            materialPanes.forEach(pane =>
                pane.classList.remove('active')
            );

            button.classList.add('active');

            document.getElementById(tab).classList.add('active');

        });

    });

    /* =========================================
    MOBILE MATERIAL SELECT
    ========================================= */

    const mobileSelect = document.querySelector('.material-mobile-select');
    const mobileTrigger = document.querySelector('.material-mobile-select__trigger');
    const mobileValue = document.querySelector('.material-mobile-select__value');
    const mobileOptions = document.querySelectorAll('.material-mobile-option');

    if(mobileSelect){

        mobileTrigger.addEventListener('click', () => {

            mobileSelect.classList.toggle('active');

        });

        mobileOptions.forEach(option => {

            option.addEventListener('click', () => {

                const materialId = option.dataset.material;

                mobileOptions.forEach(item =>
                    item.classList.remove('active')
                );

                option.classList.add('active');

                mobileValue.textContent =
                    option.childNodes[0].textContent.trim();

                mobileSelect.classList.remove('active');

                /* переключаем контент */

                materialPanes.forEach(pane =>
                    pane.classList.remove('active')
                );

                document
                    .getElementById(materialId)
                    .classList.add('active');

                /* синхронизируем десктопные табы */

                materialButtons.forEach(btn => {

                    btn.classList.remove('active');

                    if(btn.dataset.material === materialId){
                        btn.classList.add('active');
                    }

                });

            });

        });

        document.addEventListener('click', e => {

            if(!mobileSelect.contains(e.target)){
                mobileSelect.classList.remove('active');
            }

        });

    }

</script>
<script>
    const muralGalleryThumbs = new Swiper('.muralGalleryThumbs', {
        direction: 'vertical',
        slidesPerView: 'auto',
        spaceBetween: 15,
        freeMode: true,
        watchSlidesProgress: true,

        breakpoints: {
            0: {
                direction: 'horizontal',
            },

            769: {
                direction: 'vertical',
            }
        }
    });

    const muralGalleryMain = new Swiper('.muralGalleryMain', {
        slidesPerView: 1,
        speed: 900,
        effect: 'fade',

        fadeEffect: {
            crossFade: true
        },

        thumbs: {
            swiper: muralGalleryThumbs
        }
    });
</script>
