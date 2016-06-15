
var callback = {

    order: function(form) {
        $('.callback-loading', form)
            .attr('disabled', 'disabled')
            .addClass('in');
        var str = $(form).serialize();

        $.ajax({
            url: 'callback.php',
            method: 'post',
            data: str,
            dataType: 'json',
            success: function(data) {
                $('.form-group').removeClass('has-error');
                var $response = $('#callback-response')
                    .removeClass('text-danger');

                if (data.error_code) {
                    $response
                        .addClass('text-danger')
                        .text(data.message)
                        .show();

                } else if (data.status && data.status == 'success') {
                    $response
                        .addClass('text-success')
                        .text('Отлично! Скоро мы вам перезвоним.')
                        .show();
                }
            },
            error: function() {
                $('#callback-response')
                    .addClass('text-danger')
                    .text('Сервис временно недоступен, попробуйте пожалуйста позже.')
                    .show();
            },
            complete: function() {
                $('.callback-loading')
                    .removeAttr('disabled')
                    .removeClass('in');
            }
        });
    }
};
