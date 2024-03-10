$(document).ready(function() {
    // SideNav Button Initialization
    $('.button-collapse').sideNav({
        edge: 'left',
        closeOnClick: false,
        breakpoint: 1440,
        menuWidth: 270,
        timeDurationOpen: 300,
        timeDurationClose: 200,
        timeDurationOverlayOpen: 50,
        timeDurationOverlayClose: 200,
        easingOpen: 'easeOutQuad',
        easingClose: 'easeOutCubic',
        showOverlay: true,
        showCloseButton: false
    });

    // SideNav Scrollbar Initialization
    const sideNavScrollbar = document.querySelector('.custom-scrollbar');
    new PerfectScrollbar(sideNavScrollbar);

    // Material select
    $('.mdb-select').materialSelect();
    $('.select-wrapper.md-form.md-outline input.select-dropdown').bind('focus blur', function () {
        $(this).closest('.select-outline').find('label').toggleClass('active');
        $(this).closest('.select-outline').find('.caret').toggleClass('active');
    });

    tableCheckbox();


    radioCheck($('.card .form-check.data'));
    modalSingleConfirmed($('.entity-delete-btn'));
    modalMultipleConfirmed($('#entity-bulk-delete-btn'));
    passwordGenerate($('#password-generate-btn'));
    readImage($('.entity-image'));
    viewPassword($('.input-prefix.fa-eye'));
});


const passwordGenerate = function (element) {
    element.click(function (e) {
        e.preventDefault();

        generatePassword($('#password-bulk').find('input'));
    });
}

const readImage = function (element) {
    element.change(function () {readURL(this)});
}

const viewPassword = function (element) {
    element.click(function () {
        passwordView($(this));
    });
}

