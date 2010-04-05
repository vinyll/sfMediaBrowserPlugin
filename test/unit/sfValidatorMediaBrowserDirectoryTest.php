<?php
require dirname(__FILE__).'/../bootstrap/unit.php';
require dirname(__FILE__).'/../../lib/validator/sfValidatorMediaBrowserDirectory.class.php';

$t = new lime_test(12, new lime_output_color());

$v = new sfValidatorMediaBrowserDirectory();

$dir = dirname(__FILE__);

$t->diag('->clean()');

# valid value
try
{
  $t->is($v->clean($dir), $dir, '->clean() checks that the directory passed exists.');
}
catch(sfValidatorError $e)
{
  $t->fail('->clean() checks that the directory passed exists.');
}


# cleans a misspelled directory
try
{
  $t->is($v->clean($dir.'//./'), $dir, '->clean() cleans an existing misspelled directory.');
}
catch(sfValidatorError $e)
{
  $t->fail('->clean() cleans an existing misspelled directory.');
}

# invalid value
try
{
  $v->clean($dir.'/non-existing-dir');
  $t->fail('->clean() throw an sfValidatorError if the value is not an existing directory');
  $t->skip('', 1);
}
catch(sfValidatorError $e)
{
  $t->pass('->clean() throw an sfValidatorError if the value is not an existing directory');
  $t->is($e->getCode(), 'invalid', '->clean() throw an "invalid" sfValidatorError');
}


# value is inside root path
try
{
  $v->setOption('root', $dir);
  $v->clean(__FILE__);
  $t->pass('->clean() accepts a value that in inside the root path');
}
catch(sfValidatorError $e)
{
  $t->fail('->clean() accepts a value that in inside the root path');
}

#value is relative
try
{
  $v->setOption('root', dirname($dir));
  $v->setOption('relative', true);
  $t->is($v->clean(basename($dir)), $dir, '->clean() accepts a relative value that in inside the root path');
}
catch(sfValidatorError $e)
{
  $t->fail('->clean() accepts a relative value that in inside the root path');
}
$v->setOption('relative', false);


#value is '/' but relative
try
{
  $v->setOption('root', $dir);
  $v->setOption('relative', true);
  $t->is($v->clean('/'), $dir, '->clean() accepts a relative value that in empty as the root path');
}
catch(sfValidatorError $e)
{
  $t->fail('->clean() accepts a relative value that in empty as the root path');
}
$v->setOption('relative', false);


# invalid root path
try
{
  $v->setOption('root', $dir.'/non-existing-directory');
  $v->clean($dir);
  $t->fail('->clean() throws an sfConfigurationException if the root option is a non existing directory');
}
catch(sfConfigurationException $e)
{
  $t->pass('->clean() throws an sfConfigurationException if the root option is a non existing directory');
}


# misspelled directory with a root path
try
{
  $v->setOption('root', dirname($dir));
  $t->is($v->clean($dir.'//./'), $dir, '->clean() accepts an valid though misspelled directory.');
}
catch(sfValidatorError $e)
{
  $t->fail('->clean() accepts an valid though misspelled directory.');
}

# value is outside root path
try
{
  $v->setOption('root', $dir);
  $v->clean(dirname($dir));
  $t->fail('->clean() throws an sfValidatorError if the value is out of root path');
}
catch(sfValidatorError $e)
{
  $t->pass('->clean() throws an sfValidatorError if the value is out of root path');
}

# Root is included
try
{
  $v->setOption('root', $dir);
  $t->is($v->clean($dir), $dir, '->clean() allows a value that equals the root path by default');
}
catch(sfValidatorError $e)
{
  $t->fail('->clean() allows a value that equals the root path by default');
}

# Root is excluded by default
try
{
  $v->setOption('root', $dir);
  $v->setOption('root_allowed', false);
  $v->clean($dir);
  $t->fail('->clean() forbids a value that equals the root path with "root_allowed" option to false');
}
catch(sfValidatorError $e)
{
  $t->pass('->clean() forbids a value that equals the root path with "root_allowed" option to false');
}
