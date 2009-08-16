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
    $this->icon = $this->getIconByExtension($this->file_name);
    $abs_file = sfConfig::get('sf_web_dir').'/'.$this->file;
    $this->size = round(filesize($abs_file)/1000);
    $this->dimensions = @getimagesize($abs_file);
  }


  protected function getIconByExtension($filename)
  {
    $extension = strtolower(substr(strrchr($filename, '.'), 1));

    $path_mask = sprintf("%s/%s.png", '/sfMediaBrowserPlugin/images/icons', '%s');
    if(file_exists(sfConfig::get('sf_web_dir').'/'.sprintf($path_mask, $extension)))
    {
      return sprintf($path_mask, $extension);
    }
    $ext_icon = array(
      'image' => array('png', 'jpg', 'jpeg', 'gif'),
    );
    foreach($ext_icon as $icon => $extensions)
    {
      if(in_array($extension, $extensions))
      {
        return sprintf($path_mask, $icon);
      }
    }
    return sprintf($path_mask, 'file');
  }
}