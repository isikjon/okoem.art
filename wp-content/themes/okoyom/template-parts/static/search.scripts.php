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
    const modal = document.querySelector('.mfilter');
    document.querySelector('.mfilter-open').onclick=()=>{
        modal.classList.add('active');
        document.body.style.overflow='hidden';
    };
    function closeFilter(){
        modal.classList.remove('active');
        document.body.style.overflow='';
    }
    document.querySelector('.mfilter__close').onclick=closeFilter;
    document.querySelector('.mfilter__overlay').onclick=closeFilter;
</script>
