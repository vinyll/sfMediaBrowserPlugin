<?php
$app = 'frontend';
require_once dirname(__FILE__).'/../bootstrap/functional.php';
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once dirname(__FILE__).'/../../modules/sfMediaBrowser/actions/components.class.php';

sfConfig::set('sf_web_dir', dirname(__FILE__).'/../../../../web');


class sfMediaBrowserComponentsTest extends sfMediaBrowserComponents
{
  public function __construct()
  {
    parent::__construct(sfContext::getInstance($app), 'sfMediaBrowser', 'index');
  }

  public function getIconByExtension($filename)
  {
    return parent::getIconByExtension($filename);
  }
}

$t = new lime_test(4, new lime_output_color());
$mb = new sfMediaBrowserComponentsTest();

$t->diag('getIconByExtension()');
$t->is($mb->getIconByExtension('/uploads/myfile.txt'), '/sfMediaBrowserPlugin/images/icons/txt.png', 'Retrieves if file exists as extension');
$t->is($mb->getIconByExtension('/uploads/myfile.png'), '/sfMediaBrowserPlugin/images/icons/image.png', 'Matches from icon type database');
$t->is($mb->getIconByExtension('/uploads/myfile.nonexisting'), '/sfMediaBrowserPlugin/images/icons/file.png', 'Retrieves default icon for unknown extension');
$t->is($mb->getIconByExtension('/uploads/myfile'), '/sfMediaBrowserPlugin/images/icons/file.png', 'Retrieves default icon for empty extension');

