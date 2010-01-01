<?php
$app = 'backend';
require_once dirname(__FILE__).'/../bootstrap/functional.php';
require_once dirname(__FILE__).'/../bootstrap/unit.php';

$icon_dir = '/sfMediaBrowserPlugin/images/icons';
$t = new lime_test(23, new lime_output_color());

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
$t->is(U::getIconFromType('image'), $icon_dir.'/image.png');
$t->is(U::getIconFromType('document'), $icon_dir.'/doc.png');
$t->is(U::getIconFromType('pdf'), $icon_dir.'/pdf.png');
$t->is(U::getIconFromType('unknown'), $icon_dir.'/file.png');

$t->diag('->getIconFromExtension()');
$t->is(U::getIconFromExtension('png'), $icon_dir.'/image.png');
$t->is(U::getIconFromExtension('jpeg'), $icon_dir.'/image.png');
$t->is(U::getIconFromExtension('jpg'), $icon_dir.'/image.png');
$t->is(U::getIconFromExtension('doc'), $icon_dir.'/doc.png');
$t->is(U::getIconFromExtension('xls'), $icon_dir.'/doc.png');
$t->is(U::getIconFromExtension('pdf'), $icon_dir.'/pdf.png');
$t->is(U::getIconFromExtension('unknown'), $icon_dir.'/file.png');

$t->is(U::getExtensionFromFile('test.png'), 'png', '::getExtensionFromFile() retrieves file extension');
$t->is(U::getNameFromFile('test.png'), 'test', '::getNameFromFile() retrieves file name without extension');
$t->is(U::getNameFromFile('test-without-extension'), 'test-without-extension', '::getNameFromFile() retrieve full name if no extension');


$t->diag('->deleteRecursive()');
$root_dir = dirname(__FILE__).'/../../../../web/uploads/deleteRecursive';
$deep_dir = $root_dir.'/deleteRecursive1/deleteRecursive11';
mkdir($deep_dir, 0777, true);
mkdir($root_dir.'/deleteRecursive2');
touch($deep_dir.'/text.txt');
touch($deep_dir.'/text2.txt');
$t->is(U::deleteRecursive($root_dir), true, 'return true if task was supposed to be done');
$t->is(file_exists($root_dir), false, 'successfully deleted the selected directory and its subfolders');



