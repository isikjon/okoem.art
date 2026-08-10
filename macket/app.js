AOS.init();


document.querySelectorAll('.hover-slider').forEach(slider => {

    const slides = slider.querySelectorAll('.hover-slider__slide');
    const bullets = slider.querySelectorAll('.hover-slider__pagination span');
    const zones = slider.querySelectorAll('.hover-slider__zones div');

    let current = 0;

    /* =========================
       SET SLIDE
    ========================= */

    function setSlide(index) {

        if (index < 0) index = slides.length - 1;
        if (index >= slides.length) index = 0;

        slides.forEach(slide => {
            slide.classList.remove('active');
        });

        bullets.forEach(bullet => {
            bullet.classList.remove('active');
        });

        slides[index].classList.add('active');
        bullets[index].classList.add('active');

        current = index;
    }

    /* =========================
       DESKTOP HOVER
    ========================= */

    zones.forEach(zone => {

        zone.addEventListener('mouseenter', () => {

            if (window.innerWidth <= 768) return;

            const index = +zone.dataset.index;

            setSlide(index);

        });

    });

    /* =========================
       MOBILE SWIPE
    ========================= */

    let startX = 0;
    let currentX = 0;
    let isDragging = false;

    slider.addEventListener('touchstart', e => {

        startX = e.touches[0].clientX;
        isDragging = true;

    }, { passive: true });

    slider.addEventListener('touchmove', e => {

        if (!isDragging) return;

        currentX = e.touches[0].clientX;

    }, { passive: true });

    slider.addEventListener('touchend', () => {

        if (!isDragging) return;

        const diff = startX - currentX;

        /* свайп влево */
        if (diff > 50) {
            setSlide(current + 1);
        }

        /* свайп вправо */
        if (diff < -50) {
            setSlide(current - 1);
        }

        isDragging = false;

    });

});






var jsTriggers = document.querySelectorAll('.js-tab-trigger'),
    jsContents = document.querySelectorAll('.js-tab-content');

jsTriggers.forEach(function(trigger) {
    trigger.addEventListener('click', function() {
        var id = this.getAttribute('data-tab'),
            content = document.querySelector('.js-tab-content[data-tab="'+id+'"]'),
            activeTrigger = document.querySelector('.js-tab-trigger.active'),
            activeContent = document.querySelector('.js-tab-content.active');

        activeTrigger.classList.remove('active');
        trigger.classList.add('active');

        activeContent.classList.remove('active');
        content.classList.add('active');
    });
});