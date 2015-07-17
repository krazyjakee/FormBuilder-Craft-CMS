ajaxForm = ->
  # AJAX + Parsleyjs Validation 
  notificationContainer = $('.formbuilder-notification')
  theForm = $('.formbuilder-form')

  # Parsleyjs
  theForm.parsley()

  # AJAX Form Submit
  theForm.submit (e) ->
    notificationContainer.html ''
    e.preventDefault()
    url = '/admin/actions/' + $(this).children('[name=action]').attr('value')
    redirect = $(this).children('[name=formredirect]').attr('data-redirect')
    redirectUrl = $(this).children('[name=formredirect]').attr('value')

    # Validate Parsley
    if $(this).parsley().isValid()
      # Get the post data
      data = $(this).serialize()
      # Send it to the server
      $.post url, data, (response) ->
        if response.success
          if redirect == '1' 
            window.location.href = redirectUrl
          else
            notificationContainer.html '<p class="success-message">' + response.message + '</p>'
            theForm[0].reset()
        else
          notificationContainer.html '<p class="error-message">' + response.message + '</p>'

$(ajaxForm)