<?php
defined( 'ABSPATH' ) || exit;
?>
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

</script>
<script>
    document.querySelectorAll('.muralFaqHead').forEach(button => {

        button.addEventListener('click', () => {

            const item = button.closest('.muralFaqItem');

            item.classList.toggle('active');

        });

    });
</script>
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
<script>
    const favWorksSlider = new Swiper('.favWorksSlider', {
        slidesPerView: 1,
        speed: 900,
        spaceBetween: 40,

        navigation: {
            nextEl: '.favWorks__btn--next',
            prevEl: '.favWorks__btn--prev',
        },

        pagination: {
            el: '.favWorks__pagination',
            clickable: true,
        },

        effect: 'fade',
        fadeEffect: {
            crossFade: true
        }
    });
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
