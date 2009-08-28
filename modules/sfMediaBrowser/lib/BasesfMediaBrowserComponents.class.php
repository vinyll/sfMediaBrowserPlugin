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
    $file = $this->path.'/'.$this->file_name;
    $this->file = sfMediaBrowserUtils::getTypeFromExtension(sfMediaBrowserUtils::getExtensionFromFile($file)) == 'image'
                ? new sfMediaBrowserImageObject($this->file_name)
                : new sfMediaBrowserFileObject($this->file_name)
                ;
    }
    /*
    if(!file_exists(sfConfig::get('sf_web_dir').'/'.$this->icon))
    {
      $this->icon = '/sfMediaBrowserPlugin/images/icons/file.png';
    }
     */
 
}
