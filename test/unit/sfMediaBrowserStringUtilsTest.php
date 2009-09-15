<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';
require_once dirname(__FILE__).'/../../lib/sfMediaBrowserStringUtils.class.php';

$t = new lime_test(1, new lime_output_color());

class U extends sfMediaBrowserStringUtils{}

$t->is(U::slugify('This is a long text'), 'this-is-a-long-text', '::slugify() corrects a non-valid slug string');