<?php
$app = 'backend';
require_once dirname(__FILE__).'/../bootstrap/functional.php';
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once dirname(__FILE__).'/../../lib/sfMediaBrowserUtils.class.php';


sfConfig::set('sf_plugins_dir', dirname(__FILE__).'/../../..');
$icon_dir = '/sfMediaBrowserPlugin/images/icons';
$t = new lime_test(18, new lime_output_color());

class U extends sfMediaBrowserUtils{}


$t->diag('->getTypeFromExtension()');
$t->is(U::getTypeFromExtension('png'), 'image');
$t->is(U::getTypeFromExtension('jpeg'), 'image');
$t->is(U::getTypeFromExtension('jpg'), 'image');
$t->is(U::getTypeFromExtension('doc'), 'document');
$t->is(U::getTypeFromExtension('xls'), 'document');
$t->is(U::getTypeFromExtension('pdf'), 'pdf');
$t->is(U::getTypeFromExtension('unknown'), 'file');

$t->diag('->getIconFromType()');
$t->is(U::getIconFromType('image'), $icon_dir.'/img.png');
$t->is(U::getIconFromType('document'), $icon_dir.'/doc.png');
$t->is(U::getIconFromType('pdf'), $icon_dir.'/pdf.png');
$t->is(U::getIconFromType('unknown'), $icon_dir.'/file.png');

$t->diag('->getIconFromExtension()');
$t->is(U::getIconFromExtension('png'), $icon_dir.'/img.png');
$t->is(U::getIconFromExtension('jpeg'), $icon_dir.'/img.png');
$t->is(U::getIconFromExtension('jpg'), $icon_dir.'/img.png');
$t->is(U::getIconFromExtension('doc'), $icon_dir.'/doc.png');
$t->is(U::getIconFromExtension('xls'), $icon_dir.'/doc.png');
$t->is(U::getIconFromExtension('pdf'), $icon_dir.'/pdf.png');
$t->is(U::getIconFromExtension('unknown'), $icon_dir.'/file.png');
