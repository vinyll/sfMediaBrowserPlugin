<?php
$app = 'backend';
$debug = true;
include(dirname(__FILE__).'/../bootstrap/functional.php');



class UserTest extends myUser
{
  public function hasCredential($credential){return true;}
  public function isAuthenticated(){return true;}
}

class sfTestFunctionalUpload extends sfTestFunctional
{
  protected $last_file = array('filename' => null, 'dir' => '');

  public function upload($filename, $dir = '')
  {
    $this->last_file = array('filename' => $filename, 'dir' => $dir);
    $this->get('sf_media_browser')
         ->click('Save', array(
           'upload[file]'      => realpath(dirname(__FILE__).'/../fixtures/'.$filename),
           'upload[directory]' => $dir,
         ));
    $this->test()->is(is_file(sfConfig::get('sf_upload_dir').$dir.'/'.$filename), true, sprintf('File "%s" is actually uploaded in "%s"', $filename, $dir));
    return $this;
  }

  public function deleteLast()
  {
    unlink(sfConfig::get('sf_upload_dir').$this->last_file['dir'].'/'.$this->last_file['filename']);
    $this->test()->comment(sprintf('file "%s" deleted', $this->last_file['filename']));
    return $this;
  }


}

$browser = new sfTestFunctionalUpload(new sfBrowser());
$t = $browser->test();
$context = $browser->getContext();

$test_folder = sfConfig::get('sf_upload_dir').'/functionaltest';
@mkdir($test_folder);
chmod($test_folder, 0777);

// Mock the context user
$context->set('user', new UserTest($context->getEventDispatcher(), $context->getStorage()));


$browser->
  get('sf_media_browser')->
  with('request')->begin()->
    isParameter('module', 'sfMediaBrowser')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('#sf_media_browser_upload legend', 'Upload a file')->
  end()
  ->upload('my_test.jpg', '/')->deleteLast()
;
