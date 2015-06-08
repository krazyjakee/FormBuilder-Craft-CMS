<?php
namespace Craft;

class FormBuilder_CheckboxesFieldType extends BaseOptionsFieldType
{

  protected $multi = true;

  //======================================================================
  // Get FieldType Name
  //======================================================================
  public function getName()
  {
    return Craft::t('| FormBuilder | Checkboxes');
  }

  //======================================================================
  // Get Input HTML
  //======================================================================
  public function getInputHtml($name, $values)
  {
    $fieldId      = $name->id;
    $required     = $name->required;
    $options      = $this->getTranslatedOptions();
    $instructions = $name->instructions;
    $handle       = $name->handle;
    
    $id = craft()->templates->namespaceInputId($fieldId, 'field'); 

    if ($this->isFresh()) {
      $values = $this->getDefaultValue();
    }

    craft()->path->setTemplatesPath(craft()->path->getPluginsPath().'formbuilder/templates');
    $html = craft()->templates->render('_includes/forms/checkboxGroup', array(
      'id'            => $id,
      'name'          => $name,
      'handle'        => $handle,
      'instructions'  => $instructions,
      'required'      => $required,
      'options'       => $options,
      'values'        => $values
    ));
    craft()->path->setTemplatesPath(craft()->path->getTemplatesPath());

    return $html;
  }

  //======================================================================
  // Get Label
  //======================================================================
  protected function getOptionsSettingsLabel()
  {
    return Craft::t('Checkbox Options');
  }
}
