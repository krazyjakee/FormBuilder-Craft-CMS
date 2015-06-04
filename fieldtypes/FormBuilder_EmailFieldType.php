<?php
namespace Craft;

/**
 * Class PlainTextFieldType
 *
 * @author    Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @copyright Copyright (c) 2014, Pixel & Tonic, Inc.
 * @license   http://buildwithcraft.com/license Craft License Agreement
 * @see       http://buildwithcraft.com
 * @package   craft.app.fieldtypes
 * @since     1.0
 */
class FormBuilder_EmailFieldType extends BaseFieldType
{
  // Public Methods
  // =========================================================================

  /**
   * @inheritDoc IComponentType::getName()
   *
   * @return string
   */
  public function getName()
  {
    return Craft::t('| FormBuilder | Email Address');
  }

  /**
   * @inheritDoc ISavableComponentType::getSettingsHtml()
   *
   * @return string|null
   */
  public function getSettingsHtml()
  {
    $settings = craft()->templates->render('_components/fieldtypes/PlainText/settings', array(
      'settings' => $this->getSettings()
    ));

    return $settings;
  }

  /**
   * @inheritDoc IFieldType::getInputHtml()
   *
   * @param string $name
   * @param mixed  $value
   *
   * @return string
   */
  public function getInputHtml($name, $value)
  {
    // Variables
    $fieldId      = $name->id;
    $required     = $name->required;
    $instructions = $name->instructions;

    // Namespace our field id
    $id = craft()->templates->namespaceInputId($fieldId, 'field'); 

    craft()->path->setTemplatesPath(craft()->path->getPluginsPath().'formBuilder/templates');
    $html = craft()->templates->render('_includes/forms/email', array(
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

  // Protected Methods
  // =========================================================================

  /**
   * @inheritDoc BaseSavableComponentType::defineSettings()
   *
   * @return array
   */
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
