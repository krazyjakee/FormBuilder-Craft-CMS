$validationSettingsElem = $('#validationSettings')
validationSettings = JSON.parse($validationSettingsElem.val()) if $validationSettingsElem.val()

validationFormBody = """
<div class='formBuilderUI'>
  <div class='field'>
    <div class='heading'>
      <label>Validation Type</label>
    </div>
    <div class='input'>
      <div class='select'>
        <select>
          <option value=''>None</option>
          <option value='email'>Email</option>
          <option value='alphanum'>AlphaNumeric</option>
          <option value='alpha'>Alpha</option>
          <option value='number'>Numeric</option>
          <option value='url'>Url</option>
          <option value='date'>Date</option>
          <option value='color'>Color</option>
        </select>
      </div>
    </div>
  </div>
</div>
  """

validationFormModal = '<div class="modal elementselectormodal formbuilderModal"><div class="body" /><div class="footer"><div class="buttons rightalign first"><div class="btn close submit">Done</div></div></div></div>'

onFieldSettingsMenuItemClick = (e) ->
  e.preventDefault()
  e.stopPropagation()

  $trigger = $(e.target)
  $modal = $trigger.data('_formBuilderModal')

  if !$modal
    $modal = $(validationFormModal)
    modal = new Garnish.Modal $modal,
      resizable: true
      autoShow: false
      onShow: ->
        $field = $trigger.data('_formBuilderField')
        fieldId = $field.data('id')
        select = $modal.find('select')
        select.data('field', $field)
        for setting in validationSettings when setting.fieldId is fieldId
          select.val(setting.value)
      onHide: ->
        select = $modal.find('select')
        fieldId = select.data('field').data('id')
        value = select.val()

        exists = false
        for setting, index in validationSettings
          if setting.fieldId is fieldId
            exists = true
            validationSettings[index].value = value

        validationSettings.push({ fieldId: fieldId, value: value }) unless exists
        $validationSettingsElem.val(JSON.stringify(validationSettings))

    $modal.find('.body').append(validationFormBody)

    $modal.on 'click', '.close', (e) ->
      modal.hide()

    $trigger.data('_formBuilderModal', modal)

  $trigger.data('_formBuilderModal').show()

init = ->
  $container = $('#fieldlayoutform')
  $fields = $container.find('.fld-field')

  $fields.find('.settings').click ->
    elem = $(@)
    unless elem.hasClass("has-validation")
      $field = elem.closest('.fld-field')
      $menu = $('.menu:visible')

      $menu
      .find('ul')
      .children(':first')
      .clone(true)
      .prependTo($menu.find('ul:first'))
      .find('a:first')
      .data('_formBuilderField', $field)
      .attr('data-action', 'toggle-validation')
      .text(Craft.t('Manage Validation'))
      .on('click', onFieldSettingsMenuItemClick)

      elem.addClass("has-validation")

init()