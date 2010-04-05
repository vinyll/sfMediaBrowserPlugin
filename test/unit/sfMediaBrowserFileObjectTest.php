<?php
$app = 'backend';
require_once dirname(__FILE__).'/../bootstrap/functional.php';
require_once dirname(__FILE__).'/../bootstrap/unit.php';

class sfMediaBrowserFileObjectMock extends sfMediaBrowserFileObject
{
  public function cleanFolder($folder)
  {
    return parent::cleanFolder($folder);
  }
}

sfConfig::set('sf_plugins_dir', realpath(dirname(__FILE__).'/../../..'));
sfConfig::set('sf_web_dir', realpath(dirname(__FILE__).'/../../../../web'));
sfConfig::set('sf_upload_dir', realpath(dirname(__FILE__).'/../../../../web/uploads'));
sfConfig::set('app_sf_media_browser_root_dir', '/uploads');

copy(dirname(__FILE__).'/../fixtures/my_test.jpg', sfConfig::get('sf_upload_dir').'/my_test.jpg');


$t = new lime_test(14, new lime_output_color());

$file = new sfMediaBrowserFileObjectMock('/uploads/my_test.jpg', sfConfig::get('sf_web_dir'));
$file->setDirectorySeparator('/');

$t->is($file->cleanFolder('/my/folder'), '/my/folder', '->cleanFolder() preserves well formatted string');
$t->is($file->cleanFolder('my/folder'), '/my/folder', '->cleanFolder() adds misssing start slash');
$t->is($file->cleanFolder('/my/folder/'), '/my/folder', '->cleanFolder() removes ending extra slash');
$t->is($file->cleanFolder('my/folder/'), '/my/folder', '->cleanFolder() reformats missing and extra slashes');
$t->is($file->cleanFolder('/my//folder'), '/my/folder', '->cleanFolder() reformats double slashes to simple one');
$file->setDirectorySeparator('\\');
$t->is($file->cleanFolder('C:\Program Files\\Apache2\apache.exe'), 'C:\Program Files\Apache2\apache.exe', '->cleanFolder() cleans Windows path format');
$file->setDirectorySeparator('/');
$t->is($file->getPath(), realpath(sfConfig::get('sf_upload_dir').'/my_test.jpg'), '->getPath() returns well formatted root dir');
$t->is($file->getUrl(), '/uploads/my_test.jpg', '->getUrl() valid url');
$t->is($file->getUrlDir(), '/uploads', '->getUrlDir() retrieves the directory part of url');
$t->is($file->getType(), 'image', '->getType() matches correct file type');
$t->is($file->getExtension(), 'jpg', '->getExtension() return the correct extension');
$t->is($file->getSize(), '54', '->getSize() return the correct file size');
$t->is($file->getName(), 'my_test.jpg', '->getName() return the file name');
$t->is($file->getName(false), 'my_test', '->getName(false) return the file name without extension');

