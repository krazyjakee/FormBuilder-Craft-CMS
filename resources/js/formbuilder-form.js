var ajaxForm;

ajaxForm = function() {
  var notificationContainer, theForm;
  notificationContainer = $('.formbuilder-notification');
  theForm = $('.formbuilder-form');
  theForm.parsley();
  return theForm.submit(function(e) {
    var data, redirect, redirectUrl, url;
    notificationContainer.html('');
    e.preventDefault();
    url = '/admin/actions/' + $(this).children('[name=action]').attr('value');
    redirect = $(this).children('[name=formredirect]').attr('data-redirect');
    redirectUrl = $(this).children('[name=formredirect]').attr('value');
    if ($(this).parsley().isValid()) {
      data = $(this).serialize();
      return $.post(url, data, function(response) {
        if (response.success) {
          if (redirect === '1') {
            return window.location.href = redirectUrl;
          } else {
            notificationContainer.html('<p class="success-message">' + response.message + '</p>');
            return theForm[0].reset();
          }
        } else {
          return notificationContainer.html('<p class="error-message">' + response.message + '</p>');
        }
      });
    }
  });
};

$(ajaxForm);
