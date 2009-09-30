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
    $class = sfMediaBrowserUtils::getTypeFromExtension(sfMediaBrowserUtils::getExtensionFromFile($this->filename)) == 'image'
           ? 'sfMediaBrowserImageObject'
           : 'sfMediaBrowserFileObject'
           ;
    $this->file = new $class($this->filename, $this->dir);
  }
  
}
