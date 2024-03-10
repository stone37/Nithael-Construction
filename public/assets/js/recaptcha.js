$(document).ready(function() {
    grecaptcha.ready(function() {grecaptcha.execute('6LfSnr0UAAAAAGgVbAAZQdtn8UdJ6CAMTf79myG_', {action: route})
        .then(function(token) {
            $('input.app-recaptchaToken').val(token);
        });
    });
});