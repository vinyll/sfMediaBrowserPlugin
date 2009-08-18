<?php

/**
 *
 *
 * @package
 * @subpackage
 * @author      Vincent Agnano <vince@onanga.com>
 */
class BasesfMediaBrowserComponents extends sfComponents
{
  public function executeIcon(sfWebRequest $request)
  {
    $this->file = '/'.$this->path.'/'.$this->file_name;
    $extension = sfMediaBrowserUTils::getExtensionFromFile($this->file);
    $this->icon = sfMediaBrowserUtils::getIconFromExtension($extension);
    if(!file_exists(sfConfig::get('sf_web_dir').'/'.$this->icon))
    {
      $this->icon = '/sfMediaBrowserPlugin/images/icons/file.png';
    }

    $abs_file = sfConfig::get('sf_web_dir').'/'.$this->file;
    $this->size = round(filesize($abs_file)/1000);
    $this->dimensions = @getimagesize($abs_file);
  }
}