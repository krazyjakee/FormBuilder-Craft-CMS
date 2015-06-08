<?php
namespace Craft;

class FormBuilder_EmailFieldType extends BaseFieldType
{
  //======================================================================
  // Get FieldType Name
  //======================================================================
  public function getName()
  {
    return Craft::t('| FormBuilder | Email Address');
  }

  //======================================================================
  // Get Settings HTML
  //======================================================================
  public function getSettingsHtml()
  {
    $settings = craft()->templates->render('_components/fieldtypes/PlainText/settings', array(
      'settings' => $this->getSettings()
    ));

    return $settings;
  }

  //======================================================================
  // Get Input HTML
  //======================================================================
  public function getInputHtml($name, $value)
  {
    $fieldId      = $name->id;
    $required     = $name->required;
    $instructions = $name->instructions;
    $placeholder  = $name->settings['placeholder'];

    $id = craft()->templates->namespaceInputId($fieldId, 'field'); 

    craft()->path->setTemplatesPath(craft()->path->getPluginsPath().'formbuilder/templates');
    $html = craft()->templates->render('_includes/forms/email', array(
      'id'            => $id,
      'name'          => $name,
      'instructions'  => $instructions,
      'placeholder'   => $placeholder,
      'value'         => $value,
      'required'      => $required,
      'settings'      => $this->getSettings()
    ));
    craft()->path->setTemplatesPath(craft()->path->getTemplatesPath());

    return $html;
  }

  //======================================================================
  // Define Settings
  //======================================================================
  protected function defineSettings()
  {
    return array(
      'placeholder'   => array(AttributeType::String),
      'multiline'     => array(AttributeType::Bool),
      'initialRows'   => array(AttributeType::Number, 'min' => 1, 'default' => 4),
      'maxLength'     => array(AttributeType::Number, 'min' => 0),
    );
  }
}
