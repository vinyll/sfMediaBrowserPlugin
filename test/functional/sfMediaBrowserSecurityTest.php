<?php
require dirname(__FILE__).'/../bootstrap/functional.php';
include dirname(__FILE__).'/../../lib/test/sfTestFunctionalMediaBrowser.class.php';

$target_name = '/tests-uploads';
$target_path = sfConfig::get('sf_web_dir').$target_name;
chmod($target_path, 0777);
$root_path_name = sfConfig::get('sf_web_dir').'/'.sfConfig::get('app_sf_media_browser_root_dir');
$fixture_file = realpath(dirname(__FILE__).'/../fixtures/my_test.jpg');
if(!realpath($root_path_name))
{
  mkdir($root_path_name);
}
$root_path = realpath($root_path_name);



$b = new sfTestFunctionalMediaBrowser(new sfBrowser());
$t = $b->test();

# CREATE

$b->
  get('/sf_media_browser')->
  info('attempt to create a directory outside the root tricking the name')->
  createDirectory('/my functional test dir')->
  directoryExists('my-functional-test-dir')->
  deleteDirectory('my-functional-test-dir')->
  
  createDirectory('../my functional test dir2')->
  directoryExists('my-functional-test-dir2')->
  deleteDirectory('my-functional-test-dir2')
;

$b->info('attempt to create a file outside the root tricking the directory name')->
  uploadFile(dirname(__FILE__).'/../fixtures/my_test.jpg', $target_name, false)->
  existsOnDisk($target_path.'/my_test.jpg', false)
  ;
@unlink($target_path.'/my_test.jpg');
  

$b->
  info('attempt to create a directory outside the root tricking the destination dir name')->
  createDirectory('my functional test dir3', $target_name, false)->
  existsOnDisk($target_path.'/my-functional-test-dir3', false)->
  back()
;
@rmdir($target_path.'/my-functional-test-dir3');


# DELETE

@mkdir($target_path.'/system-dir');
$b->info('attempt to delete a directory outside the root tricking the directory name')->
    get('/sf_media_browser/dir_delete?directory='.urlencode($target_name.'/system-dir'));
$t->ok(file_exists($target_path.'/system-dir'), 'Directories outside root cannot be deleted')
;
@rmdir($target_path.'/system-dir');


$b->info('attempt to delete a file outside the root tricking the directory name');
touch($target_path.'/system-file.test');
try
{
  $b->get('/sf_media_browser/file_delete?file='.urlencode($target_name.'/system-file.test'));
}
catch(sfSecurityException $e)
{
  $t->pass($e->getMessage());
}
$t->ok(file_exists($target_path.'/system-file.test'), 'Files outside root still exists');
@unlink($target_path.'/system-file.test');
  
  
$b->info('attempt to delete the root itself');
try
{
  $b->get('/sf_media_browser/dir_delete?directory='.urlencode(sfConfig::get('app_sf_media_browser_root_dir')))
  ->with('response')->isStatusCode(500);
}
catch(sfSecurityException $e)
{
  $t->pass($e->getMessage());
}
$b->existsOnDisk($root_path);


# RENAME

copy($fixture_file, $target_path.'/file_to_rename.jpg');
$b->info('attempt to rename a file outside the root');
try
{
  $b->post('sf_media_browser/rename', array('file' => $target_name.'/file_to_rename.jpg', 'name' => 'name_changed'));
}
catch(sfSecurityException $e)
{
  $t->pass($e->getMessage());
}
$b->existsOnDisk($target_path.'/file_to_rename.jpg')->
  existsOnDisk($target_path.'/name_changed.jpg', false)
;
@unlink($target_path.'/file_to_rename.jpg');
@unlink($target_path.'/name_changed.jpg');

@mkdir($target_path.'/dir_to_rename');
$b->info('attempt to rename a folder outside the root');
try
{
  $b->post('sf_media_browser/rename', array('file' => $target_name.'/dir_to_rename', 'name' => 'dir_renamed'));
}
catch(sfSecurityException $e)
{
  $t->pass($e->getMessage());
}
$b->existsOnDisk($target_path.'/dir_to_rename')->
  existsOnDisk($target_path.'/dir_renamed', false)
;
@rmdir($target_path.'/dir_to_rename');
@rmdir($target_path.'/dir_renamed');


# MOVE
copy($fixture_file, $root_path.'/file_to_move.jpg');
$b->info('attempt to move a file outside the root tricking the directory name');
try
{
  $b->post('sf_media_browser', array('file' => $root_path_name.'/file_to_move.jpg', 'dir' => $target_name));
}
catch(sfSecurityException $e)
{
  $t->pass($e->getMessage());
}
$b->existsOnDisk($root_path.'/file_to_move.jpg')->
  existsOnDisk($target_path.'/file_to_move.jpg', false)
;
@unlink($root_path.'/file_to_move.jpg');
@unlink($target_path.'/file_to_move.jpg');

@mkdir($root_path.'/dir_to_move');
$b->info('attempt to move a folder outside the root tricking the directory name');
try
{
  $b->post('sf_media_browser/move', array('file' => $root_path_name.'/dir_to_move', 'dir' => $target_name));
}
catch(sfSecurityException $e)
{
  $t->pass($e->getMessage());
}
$b->existsOnDisk($root_path.'/dir_to_move')->
  existsOnDisk($target_path.'/dir_to_move', false)
;
@unlink($root_path.'/dir_to_move');
@unlink($target_path.'/dir_to_move');