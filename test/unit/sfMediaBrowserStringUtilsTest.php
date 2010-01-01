<?php
require_once dirname(__FILE__).'/../bootstrap/unit.php';

$t = new lime_test(3, new lime_output_color());

class U extends sfMediaBrowserStringUtils{}

$t->is(U::slugify(' This is a long text '), 'this-is-a-long-text', '::slugify() replaces spaces by -');
$t->is(U::slugify(' This Is a lOnG teXt '), 'this-is-a-long-text', '::slugify() replaces capital letters by lowsercase');
$t->is(U::slugify(' This? is à Long  text!'), 'this-is-a-long-text', '::slugify() replaces non letters by valid characters');