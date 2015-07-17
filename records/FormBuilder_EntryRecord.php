<?php
namespace Craft;

class FormBuilder_EntryRecord extends BaseRecord
{
  //======================================================================
  // Get Tablet Name
  //======================================================================
  public function getTableName()
  {
    return 'formbuilder_entries';
  }

  //======================================================================
  // Define Attributes
  //======================================================================
  public function defineAttributes()
  {
    return array(
      'formId' => AttributeType::Number,
      'title'  => AttributeType::String,
      'data'   => AttributeType::Mixed,
    );
  }

  //======================================================================
  // Define Relationships
  //======================================================================
  public function defineRelations()
  {
    return array(
      'element' => array(static::BELONGS_TO, 'ElementRecord', 'id', 'required' => true, 'onDelete' => static::CASCADE),
      'form'    => array(static::BELONGS_TO, 'FormBuilder_FormRecord', 'required' => true, 'onDelete' => static::CASCADE),
    );
  }
}