const albumSwiper = new Swiper(".album-swiper", {
    // Эффект карусели с объёмным расположением
    effect: "coverflow",

    // Активная обложка находится по центру
    centeredSlides: true,

    // Бесконечное перелистывание
    loop: true,

    // Плавность перехода в миллисекундах
    speed: 700,

    // Перетаскивание мышью
    grabCursor: true,

    // Навигация с клавиатуры
    keyboard: {
        enabled: true,
        onlyInViewport: true,
    },

    // Кнопки управления
    navigation: {
        nextEl: ".album-swiper-next",
        prevEl: ".album-swiper-prev",
    },

    // Параметры 3D-эффекта
    coverflowEffect: {
        rotate: 20,
        stretch: 0,
        depth: 180,
        modifier: 1,
        scale: 0.88,
        slideShadows: false,
    },

    // Количество видимых карточек на разных экранах
    slidesPerView: 3,
    spaceBetween: 12,

    breakpoints: {
        0: {
            slidesPerView: 1.35,
            spaceBetween: 6,
        },

        600: {
            slidesPerView: 2.2,
            spaceBetween: 8,
        },

        900: {
            slidesPerView: 3,
            spaceBetween: 12,
        },
    },
});


const concertSwiper = new Swiper(".concert-swiper", {
    // Обычный горизонтальный слайдер
    slidesPerView: 3.15,
    spaceBetween: 22,

    // Бесконечное перелистывание
    loop: true,

    // Плавность переключения
    speed: 650,

    // Перетаскивание мышью
    grabCursor: true,

    // Управление с клавиатуры
    keyboard: {
        enabled: true,
        onlyInViewport: true,
    },

    // Стрелки
    navigation: {
        nextEl: ".concerts__next",
        prevEl: ".concerts__prev",
    },

    // Индикаторы внизу
    pagination: {
        el: ".concerts__pagination",
        clickable: true,
    },

    // Количество карточек под размер экрана
    breakpoints: {
        0: {
            slidesPerView: 1.08,
            spaceBetween: 14,
        },

        520: {
            slidesPerView: 1.65,
            spaceBetween: 16,
        },

        768: {
            slidesPerView: 2.2,
            spaceBetween: 20,
        },

        1100: {
            slidesPerView: 3.15,
            spaceBetween: 22,
        },
    },
});