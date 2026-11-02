const header = document.querySelector('.header');
const aboutSection = document.querySelector('#about');

function toggleHeader() {
    if (!header || !aboutSection) {
        return;
    }

    const aboutPosition =
        aboutSection.getBoundingClientRect().top + window.scrollY;

    if (window.scrollY >= aboutPosition - 100) {
        header.classList.add('header-visible');
    } else {
        header.classList.remove('header-visible');
    }
}

window.addEventListener('scroll', toggleHeader);
window.addEventListener('resize', toggleHeader);

toggleHeader();