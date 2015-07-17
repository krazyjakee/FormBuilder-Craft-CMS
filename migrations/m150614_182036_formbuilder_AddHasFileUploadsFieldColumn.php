<?php
namespace Craft;

/**
 * The class name is the UTC timestamp in the format of mYYMMDD_HHMMSS_pluginHandle_migrationName
 */
class m150614_182036_formbuilder_AddHasFileUploadsFieldColumn extends BaseMigration
{
	/**
	 * Any migration code in here is wrapped inside of a transaction.
	 *
	 * @return bool
	 */
	public function safeUp()
	{
    $this->addColumnAfter('formbuilder_forms', 'hasFileUploads', array(ColumnType::TinyInt, 'required' => false, 'length' => 1), 'useReCaptcha');
    return true;
	}
}
