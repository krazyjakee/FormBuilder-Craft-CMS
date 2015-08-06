<?php
namespace Craft;

class FormBuilder_EntriesController extends BaseController
{
  protected $allowAnonymous = true;
  protected $defaultEmailTemplate = 'formbuilder/email/default';
  protected $defaultRegistrantEmailTemplate = 'formbuilder/email/registrant';


  protected $valid_extensions = array('image', 'compressed', 'excel', 'html', 'illustrator', 'json', 'pdf', 'photoshop', 'powerpoint', 'text', 'video', 'word', 'xml');
  protected $assetFolderId = 1;

  //======================================================================
  // View All Entries
  //======================================================================
  public function actionEntriesIndex()
  {
    $variables['entries'] = craft()->formBuilder_entries->getAllEntries();
    $variables['tabs'] = $this->_getTabs();
    $this->renderTemplate('formbuilder/entries/index', $variables);
  }

  //======================================================================
  // View Single Entry
  //======================================================================
  public function actionViewEntry(array $variables = array())
  {
    $entry = craft()->formBuilder_entries->getFormEntryById($variables['entryId']);
    $variables['entry'] = $entry;

    if (empty($entry)) {
      throw new HttpException(404);
    }

    $variables['form'] = craft()->formBuilder_forms->getFormById($entry->formId);
    $variables['tabs'] = $this->_getTabs();
    $variables['selectedTab'] = 'entries';
    $variables['data'] = json_decode($entry->data, true);

    $this->renderTemplate('formbuilder/entries/_view', $variables);
  }

  //======================================================================
  // Save Form Entry
  //======================================================================
  public function actionSaveFormEntry()
  {
    $ajax = false;

    $formBuilderHandle = craft()->request->getPost('formHandle');
    if (!$formBuilderHandle) {
      throw new HttpException(404);
    }


    $form = craft()->formBuilder_entries->getFormByHandle($formBuilderHandle);

    if (!$form) {
      throw new HttpException(404);
    }

    $ajaxSubmit = $form->ajaxSubmit;
    $formRedirect = $form->successPageRedirect;
    $formRedirectUrl = $form->redirectUrl;

    if ($ajaxSubmit) {
      $ajax = true;
      $this->requirePostRequest();
      $this->requireAjaxRequest();
    } else {
      $this->requirePostRequest();
    }

    $data = craft()->request->getPost();
    $postData = $this->_filterPostKeys($data);

    // Validation
//    foreach ($postData as $key => $value) {
//        $field = craft()->fields->getFieldByHandle($key);
//    }


    $formBuilderEntry = new FormBuilder_EntryModel();

    $fileupload = true;
    $validExtension = false;
    if ($form->hasFileUploads) {
      if ((array_values($_FILES)[0]['name'] != '')) {
        $filename = array_values($_FILES)[0]['name'];
        $file = array_values($_FILES)[0]['tmp_name'];
        $extension = IOHelper::getFileKind(IOHelper::getExtension($filename));

        if (!in_array($extension, $this->valid_extensions)) {
          $fileupload = false;
          $validExtension = false;
        } else {
          $validExtension = true;
        }

        if ($validExtension) {
          // Create formbuilder directory inside craft/storage if one doesn't exist
          $storagePath = craft()->path->getStoragePath();
          $myStoragePath = $storagePath . 'formbuilder/';
          IOHelper::ensureFolderExists($myStoragePath);
          $uploadDir = $myStoragePath;
          // Rename each file with unique name
          $uniqe_filename = uniqid() . '-' . $filename;

          foreach ($_FILES as $key => $value) {
            $fileUploadHandle = $key;
          }
          $postData[$fileUploadHandle] = $uniqe_filename;
        }
      }
    }

    $formBuilderEntry->formId = $form->id;
    $formBuilderEntry->title = $form->name;
    $formBuilderEntry->data = $postData;

    $verified = true;
    $validated = true;

    // Use reCaptcha
    $useCaptcha = $form->useReCaptcha;
    if ($useCaptcha) {
      $captchaPlugin = craft()->plugins->getPlugin('recaptcha');
      if ($captchaPlugin && $captchaPlugin->isEnabled) {
        $captcha = craft()->request->getPost('g-recaptcha-response');
        $verified = craft()->recaptcha_verify->verify($captcha);
      } else {
        $verified = false;
      }
    }

    if($verified == true) {
      $validation = craft()->formBuilder_entries->validateForm($form, $postData);
      if($validation != true){
        $form->errorMessage = $validation;
        $validated = false;
      }
    }

    // Save Form Entry
    if ($verified && $validated && $fileupload && craft()->formBuilder_entries->saveFormEntry($formBuilderEntry)) {

      // Save Uploaded File
      if ($validExtension) {
        if (move_uploaded_file($file, $uploadDir . $uniqe_filename)) {
          IOHelper::deleteFile($file);

          $file = $uploadDir . $uniqe_filename;
          $fileModel = new AssetFileModel();

          $fileModel->sourceId = $form->uploadSource;

          // TODO: Set the folder ID based on the Source ID
          $fileModel->folderId = 7;

          $fileModel->filename = IOHelper::getFileName($uniqe_filename);
          $fileModel->originalName = IOHelper::getFileName($filename);
          $fileModel->kind = IOHelper::getFileKind(IOHelper::getExtension($uniqe_filename));
          $fileModel->size = filesize($file);
          $fileModel->dateModified = IOHelper::getLastTimeModified($file);

          if ($fileModel->kind == 'image') {
            list ($width, $height) = ImageHelper::getImageSize($file);
            $fileModel->width = $width;
            $fileModel->height = $height;
          }

          craft()->assets->storeFile($fileModel);

        }

      } // Valid extension


      if ($form->notifyFormAdmin && $form->toEmail != '') {
        $this->_sendEmailNotification($formBuilderEntry, $form);
      }

      if ($form->notifyRegistrant && $form->notificationFieldHandleName != '') {
        $emailField = craft()->fields->getFieldByHandle($form->notificationFieldHandleName);
        $submitterEmail = $formBuilderEntry->data[$emailField->handle];
        $this->_sendRegistrantEmailNotification($formBuilderEntry, $form, $submitterEmail);
      }

//        exit;

      if (!empty($form->successMessage)) {
        $successMessage = $form->successMessage;
      } else {
        $successMessage = Craft::t('Thank you, we have received your submission and we\'ll be in touch shortly.');
      }
      craft()->userSession->setFlash('success', $successMessage);

      if ($ajax) {
        $this->returnJson(
            ['success' => true, 'message' => $successMessage]
        );
      } else {
        if ($formRedirect) {
          $this->redirect($formRedirectUrl);
        }
      }

    } else {
      if (!$verified) {
        if (!$captchaPlugin) {
          craft()->userSession->setFlash('error', 'Please enable reCaptcha plugin!');
          $this->redirectToPostedUrl();
        }
        craft()->userSession->setFlash('error', 'Please check captcha!');
        $this->redirectToPostedUrl();
      }

      if (!empty($form->errorMessage)) {
        $errorMessage = $form->errorMessage;
      } else {
        $errorMessage = Craft::t('We\'re sorry, but something has gone wrong.');
      }

      if ($ajax) {
        $this->returnJson(
            ['error' => true, 'message' => $errorMessage]
        );
      } else {
        if ($formRedirect) {
          $this->redirectToPostedUrl();
        } else {
          craft()->userSession->setFlash('error', $errorMessage);
        }
      }
    }

  }

  //======================================================================
  // Delete Form Entry
  //======================================================================
  public function actionDeleteEntry()
  {
    $this->requirePostRequest();

    $entryId = craft()->request->getRequiredPost('entryId');

    if (craft()->elements->deleteElementById($entryId)) {
      craft()->userSession->setNotice(Craft::t('Entry deleted.'));
      $this->redirectToPostedUrl();
      craft()->userSession->setError(Craft::t('Couldn’t delete entry.'));
    }
  }

  //======================================================================
  // Send Email Notification to Admin
  //======================================================================
  protected function _sendEmailNotification($record, $form)
  {
    $data = new \stdClass($data);
    $postData = $record->data;
    $postData = $this->_filterPostKeys($postData);

//        foreach ($postData as $key => $value) {
//            $foo = $key;
//            $bar = $value;
//
//            $baz = craft()->fields->getFieldByHandle($key);
//        }

    if (craft()->templates->findTemplate($form->notificationTemplatePath)) {
      $template = $form->notificationTemplatePath;
    }

    if (!$template) {
      $template = $this->defaultEmailTemplate;
    }

    $variables = array(
        'data' => $postData,
        'form' => $form,
        'entry' => $record
    );

    $message = craft()->templates->render($template, $variables);

    if (craft()->formBuilder_entries->sendEmailNotification($form, $message, true, null)) {
      return true;
    } else {
      return false;
    }
  }

  //======================================================================
  // Send Email Notification to Submitter
  //======================================================================
  protected function _sendRegistrantEmailNotification($record, $form, $submitterEmail)
  {
    $data = new \stdClass($data);

    $postData = $record->data;
    $postData = $this->_filterPostKeys($postData);

    if (craft()->templates->findTemplate($form->notificationTemplatePathRegistrant)) {
      $template = $form->notificationTemplatePathRegistrant;
    }

    if (!$template) {
      $template = $this->defaultRegistrantEmailTemplate;
    }

    $variables = array(
        'data' => $postData,
        'form' => $form,
        'entry' => $record,
    );

    $message = craft()->templates->render($template, $variables);

    if (craft()->formBuilder_entries->sendRegistrantEmailNotification($form, $message, $submitterEmail, true, null)) {
      return true;
    } else {
      return false;
    }
  }

  //======================================================================
  // Filter Out Unused Post Data
  //======================================================================
  protected function _filterPostKeys($post)
  {
    $filterKeys = array(
        'action',
        'formredirect',
        'g-recaptcha-response',
        'formhandle',
        'craft_csrf_token',
        'redirect',
        'formhoneypot'
    );
    if (is_array($post)) {
      foreach ($post as $k => $v) {
        if (in_array(strtolower($k), $filterKeys)) {
          unset($post[$k]);
        }
      }
    }
    return $post;
  }

  //======================================================================
  // Get All Tabs
  //======================================================================
  protected function _getTabs()
  {
    return array(
        'forms' => array(
            'label' => "Forms",
            'url' => UrlHelper::getUrl('formbuilder/'),
        ),
        'entries' => array(
            'label' => "Entries",
            'url' => UrlHelper::getUrl('formbuilder/entries'),
        ),
    );
  }
}