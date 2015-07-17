<?php
namespace Craft;

class FormBuilder_FileUploadFieldType extends BaseFieldType
{
  //======================================================================
  // Get FieldType Name
  //======================================================================
  public function getName()
  {
    return Craft::t('| FormBuilder | File Upload');
  }

 //======================================================================
  // Get Settings HTML
  //======================================================================
  // public function getSettingsHtml()
  // {
  //   $settings = craft()->templates->render('_components/fieldtypes/PlainText/settings', array(
  //     'settings' => $this->getSettings()
  //   ));

  //   return $settings;
  // }

 //======================================================================
  // Define Content Attribute
  //======================================================================
  // public function defineContentAttribute()
  // {
  //   $maxLength = $this->getSettings()->maxLength;

  //   if (!$maxLength) {
  //     $columnType = ColumnType::Text;
  //   } else {
  //     $columnType = DbHelper::getTextualColumnTypeByContentLength($maxLength);
  //   }

  //   return array(AttributeType::String, 'column' => $columnType, 'maxLength' => $maxLength);
  // }

  //======================================================================
  // Get Input HTML
  //======================================================================
  public function getInputHtml($name, $value)
  {
    $fieldId      = $name->id;
    $required     = $name->required;
    $instructions = $name->instructions;

    $id = craft()->templates->namespaceInputId($fieldId, 'field'); 

    craft()->path->setTemplatesPath(craft()->path->getPluginsPath().'formbuilder/templates');
    $html = craft()->templates->render('_includes/forms/file', array(
      'id'            => $id,
      'name'          => $name,
      'instructions'  => $instructions,
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
  // protected function defineSettings()
  // {
  //   return array(
  //     'placeholder'   => array(AttributeType::String),
  //     'multiline'     => array(AttributeType::Bool),
  //     'initialRows'   => array(AttributeType::Number, 'min' => 1, 'default' => 4),
  //     'maxLength'     => array(AttributeType::Number, 'min' => 0),
  //   );
  // }
}
