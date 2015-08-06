<?php
namespace Craft;

class FormBuilder_FormsService extends BaseApplicationComponent
{
    public $types;
    private $_allFormIds;
    private $_formsById;
    private $_fetchedAllForms = false;

    //======================================================================
    // Get All Form ID's
    //======================================================================
    public function getAllFormIds()
    {
        if (!isset($this->_allFormIds)) {
            if ($this->_fetchedAllForms) {
                $this->_allFormIds = array_keys($this->_formsById);
            } else {
                $this->_allFormIds = craft()->db->createCommand()
                    ->select('id')
                    ->from('formbuilder_forms')
                    ->queryColumn();
            }
        }
        return $this->_allFormIds;
    }

    //======================================================================
    // Get All Forms
    //======================================================================
    public function getAllForms($indexBy = null)
    {
        if (!$this->_fetchedAllForms) {
            $formRecords = FormBuilder_FormRecord::model()->ordered()->findAll();
            $this->_formsById = FormBuilder_FormModel::populateModels($formRecords, 'id');
            $this->_fetchedAllForms = true;
        }

        if ($indexBy == 'id') {
            return $this->_formsById;
        } else if (!$indexBy) {
            return array_values($this->_formsById);
        } else {
            $forms = array();
            foreach ($this->_formsById as $form) {
                $forms[$form->$indexBy] = $form;
            }
            return $forms;
        }
    }

    //======================================================================
    // Get Total Forms Count
    //======================================================================
    public function getTotalForms()
    {
        return count($this->getAllFormIds());
    }

    //======================================================================
    // Get Form By ID
    //======================================================================
    public function getFormById($formId)
    {
        if (!isset($this->_formsById) || !array_key_exists($formId, $this->_formsById)) {
            $formRecord = FormBuilder_FormRecord::model()->findById($formId);

            if ($formRecord) {
                $this->_formsById[$formId] = FormBuilder_FormModel::populateModel($formRecord);
            } else {
                $this->_formsById[$formId] = null;
            }
        }
        return $this->_formsById[$formId];
    }

    //======================================================================
    // Get Form By Handle
    //======================================================================
    public function getFormByHandle($formHandle)
    {
        $formRecord = FormBuilder_FormRecord::model()->findByAttributes(array(
            'handle' => $formHandle
        ));

        if ($formRecord) {
            return FormBuilder_FormModel::populateModel($formRecord);
        }
    }

    //======================================================================
    // Get form validation settings
    //======================================================================
    public function getFormValidationSettings($layoutId)
    {
        $formRecord = FormBuilder_FormRecord::model()->findByAttributes(array(
            'fieldLayoutId' => $layoutId
        ));

        if ($formRecord) {
            $form = FormBuilder_FormModel::populateModel($formRecord);
            if($form->validationSettings){
                return json_decode($form->validationSettings);
            }
        }

    }

    //======================================================================
    // Save Form
    //======================================================================
    public function saveForm(FormBuilder_FormModel $form)
    {
        if ($form->id) {
            $formRecord = FormBuilder_FormRecord::model()->findById($form->id);

            if (!$formRecord) {
                throw new Exception(Craft::t('No form exists with the ID “{id}”', array('id' => $form->id)));
            }

            $oldForm = FormBuilder_FormModel::populateModel($formRecord);
            $isNewForm = false;
        } else {
            $formRecord = new FormBuilder_FormRecord();
            $isNewForm = true;
        }

        $formRecord->name = $form->name;
        $formRecord->handle = $form->handle;
        $formRecord->subject = $form->subject;
        $formRecord->ajaxSubmit = $form->ajaxSubmit;
        $formRecord->successPageRedirect = $form->successPageRedirect;
        $formRecord->redirectUrl = $form->redirectUrl;
        $formRecord->useReCaptcha = $form->useReCaptcha;
        $formRecord->hasFileUploads = $form->hasFileUploads;
        $formRecord->uploadSource = $form->uploadSource;
        $formRecord->successMessage = $form->successMessage;
        $formRecord->errorMessage = $form->errorMessage;
        $formRecord->notifyFormAdmin = $form->notifyFormAdmin;
        $formRecord->toEmail = $form->toEmail;
        $formRecord->notificationTemplatePath = $form->notificationTemplatePath;
        $formRecord->notifyRegistrant = $form->notifyRegistrant;
        $formRecord->notificationTemplatePathRegistrant = $form->notificationTemplatePathRegistrant;
        $formRecord->notificationFieldHandleName = $form->notificationFieldHandleName;
        $formRecord->validationSettings = $form->validationSettings;


        $formRecord->validate();
        $form->addErrors($formRecord->getErrors());

        $emailField = craft()->fields->getFieldByHandle(craft()->request->getPost('notificationFieldHandleName'));

        if ($form->notifyRegistrant) {
            if ($emailField) {
                if ($emailField->type != 'FormBuilder_Email') {
                    $form->addError('notificationFieldHandleName', 'Must be an email fieldtype.');
                    return false;
                }
            } else {
                $form->addError('notificationFieldHandleName', 'Please select an email field.');
                return false;
            }
        }

        if (!$form->hasErrors()) {
            $transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
            try {
                if (!$isNewForm && $oldForm->fieldLayoutId) {
                    craft()->fields->deleteLayoutById($oldForm->fieldLayoutId);
                }

                $fieldLayout = $form->getFieldLayout();
                craft()->fields->saveLayout($fieldLayout);

                $form->fieldLayoutId = $fieldLayout->id;
                $formRecord->fieldLayoutId = $fieldLayout->id;

                $formRecord->save();

                if (!$form->id) {
                    $form->id = $formRecord->id;
                }

                $this->_formsById[$form->id] = $form;

                if ($transaction !== null) {
                    $transaction->commit();
                }
            } catch (\Exception $e) {
                if ($transaction !== null) {
                    $transaction->rollback();
                }
                throw $e;
            }
            return true;
        } else {
            return false;
        }
    }

    //======================================================================
    // Delete Form By ID
    //======================================================================
    public function deleteFormById($formId)
    {
        if (!$formId) {
            return false;
        }

        $transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
        try {
            $fieldLayoutId = craft()->db->createCommand()
                ->select('fieldLayoutId')
                ->from('formbuilder_forms')
                ->where(array('id' => $formId))
                ->queryScalar();

            if ($fieldLayoutId) {
                craft()->fields->deleteLayoutById($fieldLayoutId);
            }

            $entryIds = craft()->db->createCommand()
                ->select('id')
                ->from('formbuilder_entries')
                ->where(array('formId' => $formId))
                ->queryColumn();

            craft()->elements->deleteElementById($entryIds);
            $affectedRows = craft()->db->createCommand()->delete('formbuilder_forms', array('id' => $formId));

            if ($transaction !== null) {
                $transaction->commit();
            }
            return (bool)$affectedRows;
        } catch (\Exception $e) {
            if ($transaction !== null) {
                $transaction->rollback();
            }
            throw $e;
        }
    }
}