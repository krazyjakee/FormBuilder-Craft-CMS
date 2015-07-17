<?php
namespace Craft;

class FormBuilder_MultiSelectFieldType extends BaseOptionsFieldType
{
  protected $multi = true;

  //======================================================================
  // Get FieldType Name
  //======================================================================
  public function getName()
  {
    return Craft::t('| FormBuilder | Multi-Select');
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

    $id = craft()->templates->namespaceInputId($fieldId, 'field'); 

    if ($this->isFresh()) {
      $values = $this->getDefaultValue();
    }

    craft()->path->setTemplatesPath(craft()->path->getPluginsPath().'formbuilder/templates');
    $html = craft()->templates->render('_includes/forms/multiselect', array(
      'id'            => $id,
      'name'          => $name,
      'instructions'  => $instructions,
      'required'      => $required,
      'values'        => $values,
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
    return Craft::t('Multi-select Options');
  }
}
