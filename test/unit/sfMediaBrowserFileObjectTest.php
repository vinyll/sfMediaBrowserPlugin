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

sfConfig::set('sf_plugins_dir', realpath(dirname(__FILE__).'/../../..'));
sfConfig::set('sf_web_dir', realpath(dirname(__FILE__).'/../../../../web'));
sfConfig::set('sf_upload_dir', realpath(dirname(__FILE__).'/../../../../web/uploads'));
sfConfig::set('app_sf_media_browser_root_dir', '/uploads');

copy('http://www.google.fr/intl/fr_fr/images/logo.gif', sfConfig::get('sf_upload_dir').'/google.gif');


$t = new lime_test(12, new lime_output_color());

$file = new sfMediaBrowserFileObjectMock('/uploads/google.gif', sfConfig::get('sf_web_dir'));

$t->is($file->cleanFolder('/my/folder'), '/my/folder', '->cleanFolder() preserves well formatted string');
$t->is($file->cleanFolder('my/folder'), '/my/folder', '->cleanFolder() adds misssing start slash');
$t->is($file->cleanFolder('/my/folder/'), '/my/folder', '->cleanFolder() removes ending extra slash');
$t->is($file->cleanFolder('my/folder/'), '/my/folder', '->cleanFolder() reformats missing and extra slashes');
$t->is($file->getPath(), realpath(sfConfig::get('sf_upload_dir').'/google.gif'), '->getPath() returns well formatted root dir');
$t->is($file->getUrl(), '/uploads/google.gif', '->getUrl() valid url');
$t->is($file->getUrlDir(), '/uploads', '->getUrlDir() retrieves the directory part of url');
$t->is($file->getType(), 'image', '->getType() matches correct file type');
$t->is($file->getExtension(), 'gif', '->getExtension() return the correct extension');
$t->is($file->getSize(), '9', '->getSize() return the correct file size');
$t->is($file->getName(), 'google.gif', '->getName() return the file name');
$t->is($file->getName(false), 'google', '->getName(false) return the file name without extension');

