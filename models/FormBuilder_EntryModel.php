<?php
namespace Craft;

class FormBuilder_EntryModel extends BaseElementModel
{

	protected $elementType = 'FormBuilder';

	//======================================================================
  // Function 
  //======================================================================
	function __toString()
	{
		return $this->id;
	}

	//======================================================================
  // Define Attributes
  //======================================================================
	protected function defineAttributes()
	{
		return array_merge(parent::defineAttributes(), array(
			'id'     			=> AttributeType::Number,
			'formId' 			=> AttributeType::Number,
			'title'  			=> AttributeType::String,
			'data'   			=> AttributeType::String,
			'attachment'		=> AttributeType::Mixed
		));
	}

	//======================================================================
  // Define if editable
  //======================================================================
	public function isEditable()
	{
		return true;
	}

	//======================================================================
	// Get Control Panel Edit Url
	//======================================================================
	public function getCpEditUrl()
	{
		return UrlHelper::getCpUrl('formbuilder/entries/'.$this->id);
	}

	//======================================================================
	// Normalize Data For Elements Table
	//======================================================================
	public function _normalizeDataForElementsTable()
	{
		$data = json_decode($this->data, true);

		// Pop off the first (4) items from the data array
		$data = array_slice($data, 0, 4);

		$newData = '<ul>';

		foreach ($data as $key => $value) {	

			$fieldHandle = craft()->fields->getFieldByHandle($key);

			$capitalize = ucfirst($key);
			$removeUnderscore = str_replace('_', ' ', $key);
			$valueArray = is_array($value);

			if ($valueArray == '1') {
				$newData .= '<li class="left icon" style="margin-right:10px;"><strong>' . $fieldHandle . '</strong>: ';
				foreach ($value as $item) {
					if ($item != '') {
						$newData .= $item;
						if (next($value)==true) $newData .= ', ';
					}
				}
			} else {
				if ($value != ''){
					$newData .= '<li class="left icon" style="margin-right:10px;"><strong>' . $fieldHandle . '</strong>: ' . $value . '</li>';
				}
			}
		}

		$newData .= '</ul>';
		$this->__set('data', $newData);
		return $this;
	}

}