<?php
namespace Craft;

class FormBuilderVariable
{

  protected $inputJsClass;

  //======================================================================
  // Get Entries Criteria
  //======================================================================
	function entries()
	{
		return craft()->elements->getCriteria('FormBuilder_Entry');
	}

  //======================================================================
  // Get Form By Handle
  //======================================================================
  function getFormByHandle($formHandle)
  {
    return craft()->formBuilder_forms->getFormByHandle($formHandle);
  }

  //======================================================================
  // Get Form By ID
  //======================================================================
  function getFormById($formId)
  {
    return craft()->formBuilder_forms->getFormById($formId);
  }

  //======================================================================
  // Get All Asset Sources
  //======================================================================
  function getAllAssetSources()
  {
    return craft()->assetSources->allSources;
  }

  //======================================================================
  // Load Plugin Scripts
  //======================================================================
  function pluginScripts($form)
  {
    if ($form->ajaxSubmit) {
      $scripts = craft()->templates->includeJsFile(UrlHelper::getResourceUrl('formbuilder/js/parsley.min.js'));
      $scripts .= craft()->templates->includeJsFile(UrlHelper::getResourceUrl('formbuilder/js/formbuilder-form.js'));
      return $scripts;
    } else {
      return false;
    }
  }

  //======================================================================
  // Return Field's Input HTML
  //======================================================================
  function getInputHtml($field) 
  {
    $field = craft()->fields->getFieldById($field->fieldId);
    $fieldType = $field->getFieldType();

    craft()->path->setTemplatesPath(craft()->path->getPluginsPath().'formBuilder/templates');

    switch($field->getFieldType()->name) {
      case "Plain Text":
        return craft()->templates->render('fields/plainText', array("field" => $field));
        break;
      // todo, more types
    }
  }
}