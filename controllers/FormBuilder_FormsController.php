<?php
namespace Craft;

class FormBuilder_FormsController extends BaseController
{

	//======================================================================
	// Show All Forms
	//======================================================================
	public function actionFormIndex()
	{	
		$variables['forms'] = craft()->formBuilder_forms->getAllForms();
		$variables['tabs'] = $this->_getTabs();
		$variables['settings'] = craft()->plugins->getPlugin('formbuilder');

		return $this->renderTemplate('formbuilder/forms', $variables);
	}

	//======================================================================
	// Edit Form
	//======================================================================
	public function actionEditForm(array $variables = array())
	{
		$variables['brandNewForm'] = false;

		if (!empty($variables['formId'])) {
			if (empty($variables['form'])) {
				$variables['form'] = craft()->formBuilder_forms->getFormById($variables['formId']);
				if (!$variables['form']) { throw new HttpException(404); }
			}
			$variables['title'] = $variables['form']->name;
		} else {
			if (empty($variables['form'])) {
				$variables['form'] = new FormBuilder_FormModel();
				$variables['brandNewForm'] = true;
			}
			$variables['title'] = Craft::t('Create a new form');
		}

		$variables['tabs'] = $this->_getTabs();

		$this->renderTemplate('formbuilder/forms/_edit', $variables);
	}

	//======================================================================
	// Save New Form
	//======================================================================
	public function actionSaveForm()
	{
		$this->requirePostRequest();

		$form = new FormBuilder_FormModel();

		$form->id         														= craft()->request->getPost('formId');
		$form->name       														= craft()->request->getPost('name');
		$form->handle     														= craft()->request->getPost('handle');
		$form->subject     														= craft()->request->getPost('subject');
		$form->ajaxSubmit     												= craft()->request->getPost('ajaxSubmit');
		$form->successPageRedirect     								= craft()->request->getPost('successPageRedirect');
		$form->redirectUrl     												= craft()->request->getPost('redirectUrl');
		$form->useReCaptcha     											= craft()->request->getPost('useReCaptcha');
		$form->hasFileUploads     										= craft()->request->getPost('hasFileUploads');
		$form->uploadSource     											= craft()->request->getPost('uploadSource');
		$form->successMessage     										= craft()->request->getPost('successMessage');
		$form->errorMessage     											= craft()->request->getPost('errorMessage');
		$form->notifyFormAdmin     										= craft()->request->getPost('notifyFormAdmin');
		$form->toEmail     														= craft()->request->getPost('toEmail');
		$form->notificationTemplatePath     					= craft()->request->getPost('notificationTemplatePath');
		$form->notifyRegistrant     									= craft()->request->getPost('notifyRegistrant');
		$form->notificationTemplatePathRegistrant     = craft()->request->getPost('notificationTemplatePathRegistrant');
		$form->notificationFieldHandleName     				= craft()->request->getPost('notificationFieldHandleName');

		$fieldLayout = craft()->fields->assembleLayoutFromPost();
		$fieldLayout->type = ElementType::Asset;
		$form->setFieldLayout($fieldLayout);

		if (craft()->formBuilder_forms->saveForm($form)) {
			craft()->userSession->setNotice(Craft::t('Form saved.'));
			$this->redirectToPostedUrl($form);
		}	else {
			craft()->userSession->setError(Craft::t('Couldn’t save form.'));
		}

		craft()->urlManager->setRouteVariables(array(
			'form' => $form
		));
	}

	//======================================================================
	// Delete Form
	//======================================================================
	public function actionDeleteForm()
	{
		$this->requirePostRequest();
		$this->requireAjaxRequest();

		$formId = craft()->request->getRequiredPost('id');

		craft()->formBuilder_forms->deleteFormById($formId);
		$this->returnJson(array('success' => true));
	}

	//======================================================================
	// List All Tabs
	//======================================================================
	protected function _getTabs()
	{
		return array(
			'forms' => array(
				'label' => "Forms", 
				'url'   => UrlHelper::getUrl('formbuilder/'),
			),
			'entries' => array(
				'label' => "Entries", 
				'url'   => UrlHelper::getUrl('formbuilder/entries'),
			),
		);
	}
}
