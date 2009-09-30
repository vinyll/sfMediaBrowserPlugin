<?php
class sfMediaBrowserImageManager
{
  protected $manager;
  
  public function __construct($manager_class, $source_file)
  {
    if(!class_exists($manager_class))
    {
      throw new sfConfigurationException(sprintf('sfMediaBrowserImageManager cannot instanciate not found class "%s"', $manager_class));
    }
    $this->manager = new $manager_class($source_file);
  }
  
  
  public function resize($width = null, $height = null)
  {
    $this->getManager()->resize($width, $height);
    return $this;
  }
  
  public function save($filename)
  {
    $this->getManager()->saveAs($filename);
  }
  
  
  /**
   * Return the image manager instance
   * @return object
   */
  public function getManager()
  {
    return $this->manager;
  }
  
}