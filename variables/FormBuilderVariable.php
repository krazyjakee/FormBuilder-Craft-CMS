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
    function getForm($formHandle)
    {
        return craft()->formBuilder_forms->getFormByHandle($formHandle);
    }

    //======================================================================
    // Get Fields By Form
    //======================================================================
    function getFields($form)
    {
        return $form->fieldLayout->getFieldLayout()->getFields();
    }

    //======================================================================
    // Get Field Type
    //======================================================================
    function getFieldType($fieldLayoutField)
    {
        return craft()->fields->getFieldById($fieldLayoutField->fieldId)->getFieldType()->name;
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
            "label" => $field->name,
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
        $GLOBALS['oldTemplatePath'] = craft()->path->getTemplatesPath();
        craft()->path->setTemplatesPath(craft()->path->getPluginsPath() . 'formBuilder/templates');

        $_renderTemplate = function($templateTitle, $field, $opts){
            $output = craft()->templates->render('fields/' . $templateTitle, array("field" => $field, "settings" => $opts));
            craft()->path->setTemplatesPath($GLOBALS['oldTemplatePath']);
            return new \Twig_Markup($output, craft()->templates->getTwig()->getCharset());
        };

        $output = "";
        // Detect the field type and render the appropriate template.
        switch ($field->getFieldType()->name) {
            case "Plain Text":
                return $_renderTemplate('plainText', $field, $opts);
                break;
            case "Assets":
                return $_renderTemplate('assets', $field, $opts);
                break;
            case "Checkboxes":
                return $_renderTemplate('checkboxes', $field, $opts);
                break;
            case "Date/Time":
                return $_renderTemplate('datetime', $field, $opts);
                break;
            case "Multi-select":
                return $_renderTemplate('multiselect', $field, $opts);
                break;
            case "Radio Buttons":
                return $_renderTemplate('radiobuttons', $field, $opts);
                break;
            case "Color":
                return $_renderTemplate('color', $field, $opts);
                break;
            case "Dropdown":
                return $_renderTemplate('dropdown', $field, $opts);
                break;
        }

    }

    //======================================================================
    // Return Opening form tag
    //======================================================================
    function openForm($form, $opts = array()){

        // Define a default options object
        $optsDefault = array(
            "class" => "formbuilder__form",
            "id" => "formbuilder_" . $form->handle,
            "attributes" => false
        );

        // Add defaults into user options.
        $opts = array_merge($optsDefault, $opts);

        // Set the template path to formbuilders own directory
        $oldPath = craft()->path->getTemplatesPath();
        craft()->path->setTemplatesPath(craft()->path->getPluginsPath() . 'formBuilder/templates');

        $output = craft()->templates->render('forms/openForm', array("form" => $form, "settings" => $opts));
        craft()->path->setTemplatesPath($oldPath);
        return new \Twig_Markup($output, craft()->templates->getTwig()->getCharset());
    }

    //======================================================================
    // Return Closing form tag for template clarity
    //======================================================================
    function closeForm($form = false){
        return new \Twig_Markup("</form>", craft()->templates->getTwig()->getCharset());
    }

}
