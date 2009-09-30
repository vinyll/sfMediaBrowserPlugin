<?php

/**
 *
 *
 * @package     sfMediaBrowser
 * @subpackage  form
 * @author      Vincent Agnano <vincent.agnano@particul.es>
 */
class sfMediaBrowserUploadForm extends sfForm
{

  protected $upload_dir;

  public function configure()
  {
    $this->setWidgets(array(
      'file'      => new sfWidgetFormInputFile(),
      'directory' => new sfWidgetFormInputHidden(),
    ));

    $this->widgetSchema->setNameFormat('upload[%s]');

    $this->setValidators(array(
      'file'      => new sfValidatorFile(array('path' => $this->getUploadDir())),
      'directory' => new sfValidatorString(array('required' => false)),
    ));
  }

  public function getUploadDir()
  {
    if(!$this->upload_dir)
    {
      $this->upload_dir = sfConfig::get('sf_upload_dir');
    }
    return $this->upload_dir;
  }

  public function setUploadDir($dir)
  {
    $this->upload_dir = $dir;
  }
}