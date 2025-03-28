$(function () {
    let currentUri = location.origin + location.pathname.replace(/\/$/, '');
    $('.navbar-menu a').each(function () {
        let href = $(this).attr('href').replace(/\/$/, '');
        if (href === currentUri) {
            $(this).addClass('active');
        }
    });

    $('.ajax-form').on('submit', function (e) {
        e.preventDefault();

        let form = $(this);
        let btn = form.find('button');
        let btnText = btn.text();
        let method = form.attr('method');
        if (method) {
            method = method.toLowerCase();
        }
        let action = form.attr('action') ? form.attr('action') : location.href;

        $.ajax({
            url: action,
            type: method === 'post' ? 'post' : 'get',
            data: form.serialize(),
            beforeSend: function () {
                btn.prop('disabled', true).text('Отправляю...');
            },
            success: function (res) {
                res = JSON.parse(res);
                if (res.status === 'success') {
                    toastr.success(res.data);
                    form.trigger('reset');
                    if (res.redirect) {
                        location = res.redirect;
                    }
                } else {
                    toastr.error(res.data);
                }
                btn.prop('disabled', false).text(btnText);
            },
            error: function () {
                toastr.error('Error!');
                //alert('Error!');
                btn.prop('disabled', false).text(btnText);
            },
        });
    });
});