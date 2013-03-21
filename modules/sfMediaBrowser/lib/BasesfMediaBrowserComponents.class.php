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
    $class = sfMediaBrowserUtils::getTypeFromExtension(sfMediaBrowserUtils::getExtensionFromFile($this->file_url)) == 'image'
           ? 'sfMediaBrowserImageObject'
           : 'sfMediaBrowserFileObject'
           ;
    $this->file = new $class($this->file_url, $this->root_path);
    
    if ($this->callback_route_pattern)
    {
        $url = url_for(sprintf($this->callback_route_pattern, $this->file->getName()));
        $this->file->setCallbackUrl($url);
    }
  }
}