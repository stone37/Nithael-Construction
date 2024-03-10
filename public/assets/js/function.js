const showLoading = function () {
    $(document).find('#loader').show();
}

const hideLoading = function () {
    $(document).find('#loader').hide();
}

const flashes = function (selector) {
    selector.each(function (index, element) {
        if ($(element).html() !== undefined) {
            toastr[$(element).attr('data-label')]($(element).html());
        }
    });
}

const radioCheck = function (element) {
    element.click(function () {
        const $this = $(this);

        $this.parents('.parent-data').find('.form-check.data').removeClass('active');
        $this.find('input[type="radio"]').prop('checked', true);
        $this.addClass('active');
    });
}

const tableCheckbox = function () {
    const $main_checkbox = $('#principal-checkbox');
    const $checkbox_list = $('.list-checkbook');
    let checkbox_list_length = $checkbox_list.length,
        checkbox_number = 0,
        $btn_bulk_delete = $('#entity-list-delete-bulk-btn'),
        $btn_class_bulk_delete = $('.entity-list-delete-bulk-btn');

    $main_checkbox.on('click', function () {
        const $this = $(this);

        if ($this.prop('checked')) {
            $checkbox_list.prop('checked', true);

            checkbox_number = checkbox_list_length;

            if (checkbox_list_length > 0) {
                $btn_bulk_delete.removeClass('d-none');
                $btn_class_bulk_delete.removeClass('d-none');
            }
        } else {
            $checkbox_list.prop('checked', false);
            $btn_bulk_delete.addClass('d-none');
            $btn_class_bulk_delete.addClass('d-none');

            checkbox_number = 0;
        }
    });

    $checkbox_list.on('click', function () {
        const $this = $(this);

        if ($this.prop('checked')) {
            checkbox_number++;
            $btn_bulk_delete.removeClass('d-none');
            $btn_class_bulk_delete.removeClass('d-none');

            if (checkbox_number === checkbox_list_length) {
                $main_checkbox.prop('checked', true)
            }
        } else {
            checkbox_number--;

            if (checkbox_number === 0) {
                $btn_bulk_delete.addClass('d-none');
                $btn_class_bulk_delete.addClass('d-none');
            }

            if (checkbox_number < checkbox_list_length) {
                $main_checkbox.prop('checked', false)
            }
        }
    });
}

const modalSingleConfirmed = function (element) {
    element.click(function (e) {
        e.preventDefault();

        showLoading();

        const $this = $(this);

        $.ajax({
            url: $this.attr('data-url'),
            type: 'GET',
            error: function () {
                hideLoading();
            },
            success: function(data) {
                hideLoading();

                $('#modal-container').html(data.html);
                $('#confirm' + $this.attr('data-id')).modal();
            }
        });
    });
}

const modalMultipleConfirmed = function (element) {
    element.click(function (e) {
        e.preventDefault();

        const $this = $(this);

        showLoading();

        const ids = [];

        $('#list-checkbook-container').find('.list-checkbook').each(function () {
            const $this = $(this);

            if ($this.prop('checked')) {
                ids.push($this.val());
            }
        });

        if (ids.length) {
            $.ajax({
                url: $this.attr('data-url'),
                data: {'data': JSON.stringify(ids)},
                type: 'GET',
                error: function () {
                    hideLoading();
                },
                success: function(data) {
                    hideLoading();

                    $('#modal-container').html(data.html);
                    $('#confirmMulti' + ids.length).modal();
                },
            });
        }
    });
}

const passwordView = function (element) {
    if (element.hasClass('fa-eye')) {
        element.removeClass('fa-eye').addClass('fa-eye-slash view');
        element.siblings('input').get(0).type = 'text';
    } else {
        element.removeClass('fa-eye-slash view').addClass('fa-eye');
        element.siblings('input').get(0).type = 'password';
    }
}

const generatePassword = function ($elements) {
    showLoading();

    let $request = new Request('https://api.motdepasse.xyz/create/?include_digits&include_lowercase&include_uppercase&password_length=8&quantity=1');

    fetch($request)
        .then((response) => response.json())
        .then(function(json_response) {
            json_response.passwords.forEach((password) => {
                $.each($elements, function(index, element){
                    $(element).val(password);
                })

                hideLoading();
            });
        });
}

const newsletter = function (element) {
    element.submit(function (e) {
        e.preventDefault();

        showLoading();

        $.ajax({
            url: $(element).attr('action'),
            type: $(element).attr('method'),
            data: element.serialize(),
            success: function(data) {
                hideLoading();

                if (data.success) {
                    notification('Newsletter', data.message, {}, 'success')
                } else {
                    let errors = $.parseJSON(data.errors);

                    $(errors).each(function (key, value) {
                        notification('Newsletter', value, {}, 'error')
                    });
                }
            }
        })
    });
}

const readURL = function (input) {
    const url = input.value;
    const ext = url.substring(url.lastIndexOf('.')+1).toLowerCase();

    if (input.files && input.files[0] && (ext === 'gif' || ext === 'png' || ext === 'jpeg' || ext === 'jpg')) {
        const reader = new FileReader();

        reader.onload = function (e) {
            const $img = $(input).parents('.image-bulk-container').find('img.image-view');
            $img.attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0])
    }
}

const silverCarousel = function () {
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
}

const smoothScroll = function () {
    $(window).scroll(function() {
        let scroll = $(window).scrollTop();
        let currScrollTop = $(this).scrollTop();

        if (scroll >= 200) {
            $('#btn-smooth-scroll').removeClass('d-none').addClass('animated fadeInRight');
        } else {
            $('#btn-smooth-scroll').addClass('d-none').removeClass('animated fadeInRight');
        }

        lastScrollTop = currScrollTop;
    });
}

const password = function (element) {
    element.click(function () {
        passwordView($(this));
    });
}

const notification = function (titre, message, options, type) {
    if(typeof options == 'undefined') options = {};
    if(typeof type == 'undefined') type = "info";

    toastr[type](message, titre, options);
}


