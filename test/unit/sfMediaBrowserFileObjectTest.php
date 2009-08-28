<?php
$app = 'backend';
require_once dirname(__FILE__).'/../bootstrap/functional.php';
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once dirname(__FILE__).'/../../lib/model/sfMediaBrowserFileObject.class.php';

class sfMediaBrowserFileObjectMock extends sfMediaBrowserFileObject
{
  public function cleanFolder($folder)
  {
    return parent::cleanFolder($folder);
  }
}

sfConfig::set('sf_plugins_dir', dirname(__FILE__).'/../../..');
sfConfig::set('sf_web_dir', dirname(__FILE__).'/../../../../web');
sfConfig::set('sf_upload_dir', dirname(__FILE__).'/../../../../web/uploads');

copy('http://www.google.fr/intl/fr_fr/images/logo.gif', sfConfig::get('sf_upload_dir').'/google.gif');


$t = new lime_test(12, new lime_output_color());

try
{
  $file = new sfMediaBrowserFileObjectMock('google.gif', 'uploads');
  $t->pass('both parameter pass correctly');
}
catch(sfException $e)
{
  $t->fail('parameters are not considered as they should : '.$e);
}
$file = new sfMediaBrowserFileObjectMock('google.gif', 'uploads');

$t->is($file->cleanFolder('/my/folder'), '/my/folder', '->cleanFolder() preserves well formatted string');
$t->is($file->cleanFolder('my/folder'), '/my/folder', '->cleanFolder() adds misssing start slash');
$t->is($file->cleanFolder('/my/folder/'), '/my/folder', '->cleanFolder() removes ending extra slash');
$t->is($file->cleanFolder('my/folder/'), '/my/folder', '->cleanFolder() reformats missing and extra slashes');
$t->is($file->getRootDir(), '/uploads', '->getRootDir() returns well formatted root dir');
$t->is($file->getWebPath(), '/uploads/google.gif', '->getWebPath() valid web path');
$t->is($file->getType(), 'image', '->getType() matches correct file type');
$t->is($file->getExtension(), 'gif', '->getExtension() return the correct extension');
$t->is($file->getSize(), '9', '->getSize() return the correct file size');
$t->is($file->getName(), 'google.gif', '->getName() return the file name');
$t->is($file->getName(true), 'google', '->getName(true) return the file name without extension');

