<?php
namespace Craft;

class FormBuilder_FormRecord extends BaseRecord
{
	/**
	 * @return string
	 */
	public function getTableName()
	{
		return 'formbuilder_forms';
	}

	/**
	 * @access protected
	 * @return array
	 */
	protected function defineAttributes()
	{
		return array(
			'name'          											=> array(AttributeType::Name, 'required' => true),
			'handle'        											=> array(AttributeType::Handle, 'required' => true),
			'subject'       											=> AttributeType::Name,
			'ajaxSubmit'       										=> AttributeType::Bool,
			'successMessage'       								=> AttributeType::String,
			'errorMessage'       									=> AttributeType::String,
			'toEmail'       											=> AttributeType::Name,
			'notifyFormAdmin'       							=> AttributeType::Bool,
			'notificationTemplatePath'						=> array(AttributeType::String, 'required' => true),
			'notifyRegistrant'       							=> AttributeType::Bool,
			'notificationFieldHandleName'					=> AttributeType::String,
			'notificationTemplatePathRegistrant'	=> array(AttributeType::String, 'required' => true),
			'fieldLayoutId' 											=> AttributeType::Number
		);
	}

	/**
	 * @return array
	 */
	public function defineRelations()
	{
		return array(
			'fieldLayout' 	=> array(static::BELONGS_TO, 'FieldLayoutRecord', 'onDelete' => static::SET_NULL),
			'entries'      	=> array(static::HAS_MANY, 'FormBuilder_EntryRecord', 'entrieId'),
		);
	}

	/**
	 * @return array
	 */
	public function defineIndexes()
	{
		return array(
			array('columns' => array('name'), 'unique' => true),
			array('columns' => array('handle'), 'unique' => true),
		);
	}

	/**
	 * @return array
	 */
	public function scopes()
	{
		return array(
			'ordered' => array('order' => 'name'),
		);
	}
}
