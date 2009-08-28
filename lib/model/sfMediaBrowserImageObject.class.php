<?php

/*
 * This file is part of the sfMediaBrowserPlugin package.
 * (c) Vincent Agnano <vince@onanga.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormInput represents an HTML input file browser tag.
 *
 * @package    sfMediaBrowser
 * @subpackage model
 * @author     Vincent Agnano <vince@onanga.com>
 */
class sfMediaBrowserImageObject extends sfMediaBrowserFileObject
{
  protected $image_size;

  
  public function __construct($file)
  {
    parent::__construct($file);
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

}
