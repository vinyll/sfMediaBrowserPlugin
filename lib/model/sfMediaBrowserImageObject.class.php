<?php

/*
 * This file is part of the sfMediaBrowserPlugin package.
 * (c) Vincent Agnano <vincent.agnano@particul.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormInput represents an HTML input file browser tag.
 *
 * @package    sfMediaBrowser
 * @subpackage model
 * @author     Vincent Agnano <vincent.agnano@particul.es>
 */
class sfMediaBrowserImageObject extends sfMediaBrowserFileObject
{
  protected $image_size,
            $thumbnail = null
            ;

  
  public function __construct($file, $dir = null, $dir_is_absolute = false)
  {
    parent::__construct($file, $dir, $dir_is_absolute);
    if($this->getType() != 'image')
    {
      throw new sfException(sprintf('The file "%s" is not an image', $file));
    }
  }

  public function isImage()
  {
    return true;
  }
  
  
  public function getImageSize()
  {
    if(!$this->image_size)
    {
      $this->image_size = getimagesize($this->getSystemPath());
    }
    return $this->image_size;
  }

  public function getWidth()
  {
    $image_size = $this->getImageSize();
    return $image_size[0];
  }

  public function getHeight()
  {
    $image_size = $this->getImageSize();
    return $image_size[1];
  }
  
  
  public function getThumbnail()
  {
    if(!$this->thumbnail)
    {
      $this->thumbnail = new self($this->getName(),
                              $this->getRootDir().'/'.sfConfig::get('app_sf_media_browser_thumbnails_dir', '.thumbnails')
                         );
    }
    return $this->thumbnail;
  }
  
  
  public function getIcon()
  {
    $thumbnail = $this->getThumbnail();
    if($thumbnail)
    {
      return $thumbnail->getWebPath();
    }
    return parent::getIcon();
  }
  
  
  
  public function delete()
  {
    // try to delete the thumbnail if exists
    try
    {
      $thumbnail = $this->getThumbnail();
      if($thumbnail)
      {
        $thumbnail->delete();
      }
    }
    catch(sfException $e){}
    
    // delete current file
    parent::delete();
  }
}
