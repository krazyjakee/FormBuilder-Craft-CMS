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
  // Load Plugin Styles
  //======================================================================
  function pluginStyles()
  {
    return craft()->templates->includeJsFile(UrlHelper::getResourceUrl('formbuilder/css/formbuilder-form.css'));
  }

  //======================================================================
  // Return Field's Input HTML
  //======================================================================
  function getInputHtml($field) 
  {
    $theField = craft()->fields->getFieldById($field->fieldId);
    $fieldType = $theField->getFieldType();
    $requiredField = $field->required; 
    $theField->required = $requiredField; 

    // UPDATE TEMPLATE PATHS
    craft()->path->setTemplatesPath(craft()->path->getPluginsPath().'formbuilder/templates'); 
    $getPluginInputHtml = $fieldType->getInputHtml($theField, null); 
    craft()->path->setTemplatesPath(craft()->path->getSiteTemplatesPath()); 

    return $getPluginInputHtml;
  }
}