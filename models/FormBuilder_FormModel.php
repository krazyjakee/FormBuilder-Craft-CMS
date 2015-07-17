<?php
namespace Craft;

class FormBuilder_FormModel extends BaseModel
{
	//======================================================================
  // Name to String
  //======================================================================
	function __toString()
	{
		return Craft::t($this->name);
	}

	//======================================================================
  // Define Attributes
  //======================================================================
	protected function defineAttributes()
	{
		return array(
			'id'            													=> AttributeType::Number,
			'name'          													=> AttributeType::String,
			'handle'        													=> AttributeType::String,
			'subject'       													=> AttributeType::String,
			'ajaxSubmit'       												=> AttributeType::Bool,
			'successPageRedirect'       							=> AttributeType::Bool,
			'redirectUrl'       											=> AttributeType::String,
			'useReCaptcha'       											=> AttributeType::Bool,
			'hasFileUploads'       										=> AttributeType::Bool,
			'uploadSource'       											=> AttributeType::String,
			'successMessage'       										=> AttributeType::String,
			'errorMessage'       											=> AttributeType::String,
			'toEmail'      														=> AttributeType::String,
			'notifyFormAdmin'      										=> AttributeType::Bool,
			'notifyRegistrant'      									=> AttributeType::Bool,
			'notificationTemplatePathRegistrant'      => AttributeType::String,
			'notificationFieldHandleName'      				=> AttributeType::String,
			'notificationTemplatePath'      					=> AttributeType::String,
			'fieldLayoutId' 													=> AttributeType::Number,
		);
	}

	//======================================================================
  // Define Behaviors
  //======================================================================
	public function behaviors()
	{
		return array(
			'fieldLayout' => new FieldLayoutBehavior('FormBuilder_Entry'),
		);
	}
}