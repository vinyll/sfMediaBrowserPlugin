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
class sfMediaBrowserFileObject
{
  protected $file,
            $name,
            $type,
            $extension,
            $size,
            $icon,
            $root_dir;

  /**
   *
   * @param string $file the file path from under web_root
   * @param string $relative_dir The directory relative to web_root. default : app_sf_media_browser_root_dir
   */
  public function __construct($file, $relative_dir = null)
  {
    $this->file = $file;
    if(!$this->getName())
    {
      throw new sfException(sprintf('The file "%s" is not a valid file name.', $file));
    }
    $this->root_dir = $relative_dir !== null
                    ? $relative_dir
                    : sfConfig::get('app_sf_media_browser_root_dir')
                    ;
    if(!file_exists($this->getSystemPath()))
    {
      throw new sfException(sprintf('The file "%s" does not exist.', $this->getSystemPath()));
    }
  }

  public function __toString()
  {
    return $this->getName();
  }

  public function getType()
  {
    return sfMediaBrowserUtils::getTypeFromExtension($this->getExtension());
  }

  
  /**
   *
   * @return boolean
   */
  public function isImage()
  {
    return false;
  }

  /**
   *
   * @return string icon file name
   */
  public function getIcon()
  {
    if(!$this->icon)
    {
      $this->icon = sfMediaBrowserUtils::getIconFromExtension($this->getExtension());
    }
    return $this->icon;
  }


  public function getFile()
  {
    return $this->cleanFolder($this->file);
  }
  

  public function getExtension()
  {
    if(!$this->extension)
    {
      $this->extension = sfMediaBrowserUtils::getExtensionFromFile($this->file);
    }
    return $this->extension;
  }


  public function getSystemPath()
  {
    return sfConfig::get('sf_web_dir').$this->getRootDir().$this->getFile();
  }


  public function getRootDir()
  {
    return $this->cleanFolder($this->root_dir);
  }


  public function getWebPath()
  {
    return $this->getRootDir().$this->getFile();
  }

  
  public function getName($without_extension = false)
  {
    if(!$this->name)
    {
      $this->name = substr($this->cleanFolder($this->file), 1);
    }
    return $without_extension === false
			      ? $this->name
			      : substr($this->name, 0, strrpos($this->name, '.'))
			      ;
  }
  
  /**
   * Get a filesize
   * @param int $round A divider to round with
   * @return int The rounded value
   */
  public function getSize($round = 1000)
  {
    if(!$this->size)
    {
      $this->size = filesize($this->getSystemPath());
    }
    return $round >= 1 ? round($this->size/$round) : $this->size;
  }


  protected function cleanFolder($folder)
  {
    $cleaned = substr($folder, 0, 1) != '/' ? '/'.$folder : $folder;
    $cleaned = substr($cleaned, -1, 1) == '/' ? substr($cleaned, 0, -1) : $cleaned;
    return $cleaned;
  }
}
