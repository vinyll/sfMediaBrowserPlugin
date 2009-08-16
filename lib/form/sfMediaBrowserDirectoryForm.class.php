<?php

/**
 *
 *
 * @package     sfMediaBrowser
 * @subpackage  form
 * @author      Vincent Agnano <vince@onanga.com>
 */
class sfMediaBrowserDirectoryForm extends sfForm
{
  protected $parent_dir;

  public function configure()
  {
    $this->setWidgets(array(
      'name'      => new sfWidgetFormInput(),
      'directory' => new sfWidgetFormInputHidden(array('default' => $this->parent_dir)),
    ));

    $this->widgetSchema->setNameFormat('directory[%s]');

    $this->setValidators(array(
      'name'      => new sfValidatorString(array('trim' => true)),
      'directory' => new sfValidatorString(array('required' => false)),
    ));
  }

  public function setParentDirectory($parent_dir)
  {
    $this->parent_dir = $parent_dir;
  }
}