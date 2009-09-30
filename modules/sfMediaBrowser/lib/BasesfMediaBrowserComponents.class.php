<?php

/**
 *
 *
 * @package     sfMediaBrowser
 * @author      Vincent Agnano <vincent.agnano@particul.es>
 */
class BasesfMediaBrowserComponents extends sfComponents
{
  public function executeIcon(sfWebRequest $request)
  {
    $file = $this->dir.'/'.$this->filename;
    $class = sfMediaBrowserUtils::getTypeFromExtension(sfMediaBrowserUtils::getExtensionFromFile($file)) == 'image'
           ? 'sfMediaBrowserImageObject'
           : 'sfMediaBrowserFileObject'
           ;
    $this->file = new $class($this->filename, $this->dir);
  }
  
}
