<?php
include dirname(__FILE__).'/../bootstrap/functional.php';
include dirname(__FILE__).'/../../lib/test/sfTestFunctionalMediaBrowser.class.php';

$upload_dir = sfConfig::get('sf_web_dir').'/'.sfConfig::get('app_sf_media_browser_root_dir', 'tests-uploads');
$delete_upload_dir = false;
if(!realpath($upload_dir))
{
  $delete_upload_dir = true;
  mkdir($upload_dir, 0777, true);
  chmod($upload_dir, 0777);
}

$browser = new sfTestFunctionalMediaBrowser(new sfBrowser());

$browser->
  get('/sf_media_browser')->
  with('response')->begin()->
    isStatusCode(200)->
    checkElement('h2', 'Current directory : /')->
  end()->
  
  createDirectory('my functional test dir')->
  directoryExists('my-functional-test-dir')->
  with('response')->checkElement('div.notice', '/The directory was successfully created./')->
  
  info('Attempting to create a directory that already exists.')->
  createDirectory('my functional test dir')->
  with('response')->checkElement('div.error', '/The directory could not be created./')->
  
  deleteDirectory('my-functional-test-dir')->
  directoryExists('my-functional-test-dir', false)->
  with('response')->checkElement('div.notice', '/The directory was successfully deleted/')->
  
  info('Making subdirectories')->
  createDirectory('my functional test dir')->
  click('#sf_media_browser_list .folder a[title="my-functional-test-dir"]')->
  with('response')->checkElement('h2', 'Current directory : /my-functional-test-dir')->
  createDirectory('sub folder')->
  directoryExists('sub-folder')->
  
  info('Upload a file into the subdirectory')->
  uploadFile(dirname(__FILE__).'/../fixtures/my_test.jpg')->fileExists('my_test.jpg')->
  deleteFile('my_test.jpg')->fileExists('my_test.jpg', false)->
  
  click('li.up a')->with('response')->checkElement('h2', 'Current directory : /')->
  deleteDirectory('my-functional-test-dir')->
  directoryExists('my-functional-test-dir', false)
  
  ;

if($delete_upload_dir)
{
  sfMediaBrowserUtils::deleteRecursive($upload_dir);
}