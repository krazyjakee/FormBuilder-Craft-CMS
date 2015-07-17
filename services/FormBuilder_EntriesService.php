<?php
namespace Craft;

class FormBuilder_EntriesService extends BaseApplicationComponent
{

	//======================================================================
	// Get All Entries
	//======================================================================
	public function getAllEntries()
	{
		$entries = FormBuilder_EntryRecord::model()->findAll();
		return $entries;
	}

	//======================================================================
	// Get All Forms
	//======================================================================
	public function getAllForms()
	{
		$forms = FormBuilder_FormRecord::model()->findAll();
		return $forms;
	}

	//======================================================================
	// Get Form By Handle Name
	//======================================================================
	public function getFormByHandle($handle)
	{
		$formRecord = FormBuilder_FormRecord::model()->findByAttributes(array(
			'handle' => $handle,
		));

		if (!$formRecord) {	return false; }
		return FormBuilder_FormModel::populateModel($formRecord);
	}

	//======================================================================
	// Get Form Entry By ID
	//======================================================================
	public function getFormEntryById($id)
	{
		return craft()->elements->getElementById($id, 'FormBuilder');
	}

	//======================================================================
	// Save Form Entry
	//======================================================================
	public function saveFormEntry(FormBuilder_EntryModel $entry)
	{
		$entryRecord = new FormBuilder_EntryRecord();

		$entryRecord->formId 	= $entry->formId;
		$entryRecord->title 	= $entry->title;
		$entryRecord->data   	= $entry->data;

		$entryRecord->validate();
		$entry->addErrors($entryRecord->getErrors());

		if (!$entry->hasErrors()) {
			$transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
			try {
				if (craft()->elements->saveElement($entry))	{
					$entryRecord->id = $entry->id;
					$entryRecord->save(false);

					if ($transaction !== null) { $transaction->commit(); }
					return $entryRecord->id;
				} else { return false; }
			} catch (\Exception $e) {
				if ($transaction !== null) { $transaction->rollback(); }
				throw $e;
			}
			return true;
		}	else { return false; }
	}

	//======================================================================
  // Send Email Notification to Admin
  //======================================================================
	public function sendEmailNotification($form, $message, $html = true, $email = null)
	{	
		$errors = false;
		$email = new EmailModel();
		$emailTo = explode(',', $form->toEmail);

		$email->toEmail		= $form->toEmail;
		$email->replyTo   = $emailTo[0];
		$email->fromName  = craft()->getSiteName() . ' | Submission Notification';
		$email->subject   = $form->subject;
		$email->htmlBody  = $message;

		foreach ($emailTo as $emailAddress) {
			$email->toEmail = $emailAddress;
			if (!craft()->email->sendEmail($email)) {
				$errors = true;
			}
		}
		return $errors ? false : true;
	}

	//======================================================================
  // Send Email Notification to Submitter
  //======================================================================
	public function sendRegistrantEmailNotification($form, $message, $submitterEmail, $html = true, $email = null)
	{
		$errors = false;
		$email = new EmailModel();
		$emailTo = explode(',', $form->toEmail);

		$email->toEmail   = $submitterEmail;
		$email->fromEmail = $emailTo[0];
		$email->replyTo   = $emailTo[0];
		$email->fromName  = craft()->getSiteName() . ' | Submission Notification';
		$email->subject   = $form->subject;
		$email->htmlBody  = $message;

		if (!craft()->email->sendEmail($email)) {
			$errors = true;
		}
		return $errors ? false : true;
	}

}