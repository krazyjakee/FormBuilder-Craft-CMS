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
    // Return Field's Input HTML
    //======================================================================
    function field($fieldLayoutField, $opts = array())
    {
        // We need more information from the field itself, not just it's reference (fieldLayoutField).
        $field = craft()->fields->getFieldById($fieldLayoutField->fieldId);

        // Get validation settings.
        $validationType = false;
        $validationSettings = craft()->formBuilder_forms->getFormValidationSettings($fieldLayoutField->layoutId);
        if($validationSettings){
            foreach($validationSettings as $vSetting){
                if($vSetting->fieldId == $field->id){
                    $validationType = $vSetting->value;
                }
            }
        }

        // Get errors
        $error = false;
        if(craft()->userSession->hasFlash("error_" . $field->handle)){
            $error = craft()->userSession->getFlash("error_" . $field->handle);
        }

        // Define a default options object
        $optsDefault = array(
            "class" => "formbuilder__field",
            "id" => "formbuilder_" . $field->handle,
            "attributes" => false,
            "label" => false,
            "labelClass" => "formbuilder__label",
            "value" => false,
            "checked" => false,
            "selected" => false,
            "required" => $fieldLayoutField->required,
            "placeholder" => false,
            "validation" => $validationType,
            "error" => $error
        );

        // Add defaults into user options.
        $opts = array_merge($optsDefault, $opts);

        // Set the template path to formbuilders own directory
        craft()->path->setTemplatesPath(craft()->path->getPluginsPath() . 'formBuilder/templates');

        // Detect the field type and render the appropriate template.
        switch ($field->getFieldType()->name) {
            case "Plain Text":
                return craft()->templates->render('fields/plainText', array("field" => $field, "settings" => $opts));
                break;
            case "Assets":
                return craft()->templates->render('fields/assets', array("field" => $field, "settings" => $opts));
                break;
        }
    }
}