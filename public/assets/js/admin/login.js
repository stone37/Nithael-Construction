$(document).ready(function() {

    viewPassword($('.input-prefix.fa-eye'));
});

const viewPassword = function (element) {
    element.click(function () {
        passwordView($(this));
    });
}

