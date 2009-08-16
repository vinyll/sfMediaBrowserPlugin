<?php
$app = 'backend';
require_once dirname(__FILE__).'/../bootstrap/functional.php';
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once dirname(__FILE__).'/../../modules/sfMediaBrowser/actions/actions.class.php';

class sfMediaBrowserActionsTest extends sfMediaBrowserActions
{
  public function __construct()
  {
    parent::__construct(sfContext::getInstance($app), 'sfMediaBrowser', 'index');
  }

  public function getParentDir($path)
  {
    return parent::getParentDir($path);
  }
}

$t = new lime_test(6, new lime_output_color());
$mb = new sfMediaBrowserActionsTest();

$t->diag('->getParentDir()');
$t->is($mb->getParentDir(''), null, 'Empty path has no parent');
$t->is($mb->getParentDir('/'), null, 'Root path has empty parent');
$t->is($mb->getParentDir('/uploads'), '/', 'First directory s parent is root');
$t->is($mb->getParentDir('/uploads/dir1'), '/uploads', 'Second level has first level as parent');
$t->is($mb->getParentDir('/uploads/dir1/subdir1'), '/uploads/dir1', 'Third level has second level as parent');
$t->is($mb->getParentDir('/uploads/dir1/subdir1/'), '/uploads/dir1', 'Removes trailing slash');
