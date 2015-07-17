<?php
namespace Craft;

/**
 * The class name is the UTC timestamp in the format of mYYMMDD_HHMMSS_pluginHandle_migrationName
 */
class m150619_155820_formbuilder_UpdateEntriesDataColumnType extends BaseMigration
{
	/**
	 * Any migration code in here is wrapped inside of a transaction.
	 *
	 * @return bool
	 */
	public function safeUp()
	{
    $this->alterColumn(
      'formbuilder_entries',
      'data',
      array('column' => ColumnType::Text)
    );

		return true;
	}
}
