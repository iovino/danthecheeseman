var $contactForm = $('#contact-form');
if ($contactForm.length) {
    $contactForm.submit(function (e) {
        e.preventDefault();
        $.ajax({
            type      : 'post',
            url       : '/send-message',
            dataType  : 'html',
            data      : {
                name                : $('input[id=name]').val(),
                email               : $('input[id=email]').val(),
                phone               : $('input[id=phone]').val(),
                message             : $('textarea[id=message]').val(),
                adcopy_challenge    : $('#adcopy_challenge').val(),
                adcopy_response     : $('#adcopy_response').val()
            },
            beforeSend: function () {
                $('.contact-results').html();
            },
            success: function (data) {
                var results = jQuery.parseJSON(data);

                if (results['success']) {
                    $('.contact-results').html('<div class="contact-success">'+ results['message'] +'</div>');
                    $('.contact button').prop('disabled', true);
                    $('.contact-form').hide();
                } else {
                    $('.contact-results').html('<div class="contact-errors">'+ results['message'] +'</div>');
                }
            }
        });
    });
}