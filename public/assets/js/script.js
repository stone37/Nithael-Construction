$(document).ready(function() {
    // Animation init
    new WOW().init();

    // Tooltip Initialization
    $('[data-toggle="tooltip"]').tooltip({
        template: '<div class="tooltip md-tooltip"><div class="tooltip-arrow md-arrow"></div><div class="tooltip-inner md-inner"></div></div>'
    });

    // Material select
    $('.mdb-select').materialSelect();
    $('.select-wrapper.md-form.md-outline input.select-dropdown').bind('focus blur', function () {
        $(this).closest('.select-outline').find('label').toggleClass('active');
        $(this).closest('.select-outline').find('.caret').toggleClass('active');
    });

    cookieConsent();
    navbarToggle();
    carouselSilver();
    smoothScroll();
    newsletter($('#newsletter-form'));
});

const cookieConsent = function () {
    $('#cookieConsent').cookieConsent({
        testing: true,
        consentStyle: 'font-weight:bold;'
    });
};

const navbarToggle = function () {
    const $icon_bulk = $('.skin-light .navbar .navbar-toggler .button-icon');

    $('.skin-light .navbar .navbar-toggler').click(function () {

        if ($icon_bulk.hasClass('open')) {
            $icon_bulk.find('i').removeClass('fa-times').addClass('fa-bars');
            $('html, body').removeClass('stop-scroll');
        } else {
            $icon_bulk.find('i').removeClass('fa-bars').addClass('fa-times');
            $('html, body').addClass('stop-scroll');
        }

        $('.skin-light .navbar .navbar-toggler .button-icon').toggleClass('open');
    });

}

const carouselSilver = function () {
    $('.carousel .carousel-inner.vv-3 .carousel-item').each(function () {
        let next = $(this).next();

        if (!next.length) {
            next = $(this).siblings(':first');
        }

        next.children(':first-child').clone().appendTo($(this));

        for (let i = 0; i < 4; i++) {

            next = next.next();

            if (!next.length) {
                next = $(this).siblings(':first');
            }

            next.children(':first-child').clone().appendTo($(this));
        }

        $('.carousel').carousel('cycle');
    });
};


