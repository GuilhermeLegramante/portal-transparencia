// Controle do Loader
function esconderLoader() { $('#loader').fadeOut('slow'); }

$(window).on('pageshow', esconderLoader);
$(document).ready(function () {
    esconderLoader();
    $('a').on('click', function () {
        const href = $(this).attr('href');
        if (href && !href.startsWith('#') && !$(this).attr('target')) $('#loader').fadeIn('fast');
    });

    // Reset de Animações do Carrossel
    const carousel = document.getElementById('portalCarousel');
    if (carousel) {
        carousel.addEventListener('slide.bs.carousel', e => {
            $(e.target).find('.animate__animated').each(function () {
                const anim = Array.from(this.classList).find(c => c.startsWith('animate__'));
                this.classList.remove(anim);
                void this.offsetWidth;
                this.classList.add(anim);
            });
        });
    }
});
