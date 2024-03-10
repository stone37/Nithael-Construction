$(document).ready(function () {

    lightBox();
});

const lightBox = function () {
    $('.skin-light .advert-view-image .mdb-lightbox .lightbox-plus').click(function () {
        $('#advert-img-plus').trigger('click');
    });

    $("#mdb-lightbox-ui").load("/assets/mdb-addons/mdb-lightbox-ui.html");
};