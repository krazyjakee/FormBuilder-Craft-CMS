![image](http://vadim-dev.s3.amazonaws.com/formbuilder/formBuilder_logo.png)

[![image](https://img.shields.io/badge/version-1.4.3-brightgreen.svg)](https://github.com/roundhouse/FormBuilder-Craft-CMS)
[![image](https://img.shields.io/packagist/v/roundhouse/formbuilder.svg)](https://packagist.org/packages/roundhouse/formbuilder)
[![image](https://img.shields.io/github/license/mashape/apistatus.svg)](http://opensource.org/licenses/MIT)

***

**Note** - I have updated a lot of the code so if you have already been using this pluging you'll need to uninstall it first and install it again. Sorry, but there were a lot of changes and creating db migrations was not an option. 

# Installation

* Clone this repo `https://github.com/roundhouse/FormBuilder-Craft-CMS`
* Rename the folder to `FormBuilder` and place it into your Craft CMS Plugin directory.
* Navigate to your plugins page `/settings/plugins` and click Install

***

# Usage

* Checkout `sampleForm.html` for code sample.

***


# Create Fields

* Got to `/settings/fields` and hit `+ New Field`
* Fill out the field's information. Make sure to use `| FormBuilder |` fields only.

#### Available Fields

* | FormBuilder | Checkboxes
* | FormBuilder | Dropdown
* | FormBuilder | Email Address
* | FormBuilder | File Upload
* | FormBuilder | Multi-Select
* | FormBuilder | Plain Text
* | FormBuilder | Radio Buttons

***

# Create Form

* Go back to FormBuilder forms page and hit `+ New Form`. [See Screenshot](http://vadim-dev.s3.amazonaws.com/formbuilder/1.3.png)

  * ##### Form Settings
  
    **Form Name** - Enter name of your form<br />
    **Hand** - Will be generated automatically<br />
    **Email Subje** - This will be subject line for email notifications<br />
    **Use AJAX?** - Check this if you want the form submission via ajax, this will use javascript to validate `required` fields<br />
    **Use reCaptcha?** - Check this if you want to use Google reCaptcha. Requires this following plugin to be installed: [Craft reCaptcha](https://github.com/aberkie/craft-recaptcha/) <br />
    **Redirect To Success Page** - Check if you want to redirect to a success page (ex: "/thank-you")<br />
    **Redirect URL** - Enter url to a success page<br />
    
  * ##### Messages
    **Success Message** - Enter success message for form submissions<br />
    **Error Message** - Enter error message for form submissions<br />
    
  * ##### Notifications
    **Send Notification?** - Check this if you want to notify form owner of form submission<br />
    **To Email** - Enter email where the submission notification will be sent to?<br />
    **Email Template Path** - Path where the email template lives<br />
    **Notify Registrant?** - Check this if you want to notify the form submitter of their successful submission. *You need to first save the form for this to show up.*<br />
    **Notification Field** - Path where the email template lives. *You must first add some fields to the fields section and save the form for this to show up. Also this field requires `| FormBuilder | Email Address` fieldtype to be used.*<br />
  
  * ##### Fields
    **Fieldsets** - You can create many fieldsets by hitting `+ Add Fieldset`<br />
    **Create New Field** - This will redirect you to `/settings/fields` where you can create a new field

***

# Required Fields

* If you want to make required fields you can do that by clicking on the "cog" icon in the Fields section. This will add HTML5 "required" attribute to the input field. If your form uses Ajax to post submissions, javascript will validate those fields (using Parsleyjs). [See Screenshot](http://vadim-dev.s3.amazonaws.com/formbuilder/1.4.png)

***

# reCaptcha

* Go to `https://github.com/aberkie/craft-recaptcha/` and follow installation instructions.
* Add this `{{craft.recaptcha.render()}}` anywhere inside your form on the front-end.
* Check `Use reCaptcha?` in the Form Settings section

***

# File Uploads

`Note:` For now this only works with regular post requests. If your form uses AJAX, file uplodas will not work. If you're updating your plugin please add this to your `<form>` tag `{% if form.hasFileUploads %} enctype="multipart/form-data" {% endif %}`, check `sampleForm.html` for example.

* First you need to create an Asset Source. Go to `settings//assets` and hit `+ New Source`
* Give your source a name, for `Type` please select `Local Folder`. Local Folder is the only thing that's supported for now.
* For `File System Path` please enter `formbuilder`.
* For `URL` enter `http://yourwebsite.com/craft/storage/`
* Save!

Now go create a new field `settings/fields`. For `Field Type` make sure to select `| FormBuilder | File Upload`. 

Now go to your form and in the Form Settings section check `Has File Uploads`. A drop down will appear, please select the Asset Source you created earlier [See Screenshot](http://vadim-dev.s3.amazonaws.com/formbuilder/file_upload_example.png). Now go to Fields section and drag the field you created to your form [See Gif](http://vadim-dev.s3.amazonaws.com/formbuilder/dragUploadField.gif). 

Save form.


***

# TODO

* Create more fieldtypes (dates, lightswitches, etc)
* Add export/import functionality for the entries
