<?php
namespace Craft;

class FormBuilder_RadioButtonsFieldType extends BaseOptionsFieldType
{
  //======================================================================
  // Get FieldType Name
  //======================================================================
  public function getName()
  {
    return Craft::t('| FormBuilder | Radio Buttons');
  }

 //======================================================================
  // Get Input HTML
  //======================================================================
  public function getInputHtml($name, $value)
  {
    $fieldId      = $name->id;
    $required     = $name->required;
    $options      = $this->getTranslatedOptions();
    $instructions = $name->instructions;
    
    $id = craft()->templates->namespaceInputId($fieldId, 'field');

    if ($this->isFresh()) {
      $value = $this->getDefaultValue();
    }

    craft()->path->setTemplatesPath(craft()->path->getPluginsPath().'formbuilder/templates');
    $html = craft()->templates->render('_includes/forms/radioGroup', array(
      'id'            => $id,
      'name'          => $name,
      'instructions'  => $instructions,
      'value'         => $value,
      'required'      => $required,
      'options'       => $options
    ));
    craft()->path->setTemplatesPath(craft()->path->getTemplatesPath());

    return $html;
  }

  //======================================================================
  // Get Settings Label
  //======================================================================
  protected function getOptionsSettingsLabel()
  {
    return Craft::t('Radio Button Options');
  }
}
