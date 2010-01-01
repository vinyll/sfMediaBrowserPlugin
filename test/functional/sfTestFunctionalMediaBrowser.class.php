<?php
class sfTestFunctionalMediaBrowser extends sfTestFunctional
{
  /**
   * Fills in and submits the form for creating a new directory
   * @param string $dirname
   * @return sfTestFunctionalMediaBrowser
   */
  public function createDirectory($dirname)
  {
    $this->
      info(sprintf(' Creating a directory "%s"', $dirname))->
      click('#sf_media_browser_mkdir form input.submit', array('directory' => array('name' => $dirname)))->
      followRedirect();
    return $this;
  }
  
  
  /**
   * Checks if a directory is visible in the list
   * @param string $dirname
   * @return sfTestFunctionalMediaBrowser
   */
  public function directoryExists($dirname, $expected = true)
  {
    $this->with('response')->checkElement(
      sprintf('#sf_media_browser_list li.folder label.name:contains("%s")', $dirname),
      $expected
    );
    return $this;
  }
  
  
  /**
   * Deletes a directory using the user interface
   * @todo point the dirname. Currently the first directory or file of the lest is deleted !
   * @param string $dirname
   * @return sfTestFunctionalMediaBrowser
   */
  public function deleteDirectory($dirname)
  {
    $this->info(sprintf(' Deleting directory "%s"', $dirname))->
    click('#sf_media_browser_list li.folder .action a.delete')->
    followRedirect();
    return $this;
  }
}