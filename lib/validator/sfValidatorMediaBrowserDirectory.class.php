<?php

/*
 * This file is part of the sfMediaBrowser package.
 *
 * (c) 2010 Vincent Agnano <vincent.agnano@particul.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfValidatorMediaBrowser validates a directory.
 *
 * @package    sfMediaBrowserPlugin
 * @subpackage validator
 * @author     Vincent Agnano <vincent.agnano@particul.es>
 */
class sfValidatorMediaBrowserDirectory extends sfValidatorBase
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * root string full system path to root directory
   *  * relative boolean|false wether the value to test will be relative to root instead of an absolute file system path.
   *  * root_allowed boolean|true wether the root itself is an allowed value
   *
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   * @return return an absolute system path or null
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->setMessage('invalid', '"%directory%" is not a valid directory path.');
    $this->addOption('root');
    $this->addOption('relative', false);
    $this->addOption('root_allowed', true);
    if(!isset($options['trim']))
    {
      $this->setOption('trim', true);
    }
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    $full_path = $this->getOption('relative')
               ? realpath($this->getOption('root').'/'.$value)
               : realpath($value);
    // check directory exists
    if(!$full_path)
    {
      throw new sfValidatorError($this, 'invalid', array('directory' => $full_path));
    }
    // check directory is inside the specified root
    elseif($this->getOption('root'))
    {
      $root = realpath($this->getOption('root'));
      // specified root does not exist
      if(!file_exists($root))
      {
        throw new sfConfigurationException(sprintf('sfValidatorMediaBrowserDirectory root option "%s" is not an existing directory.', $this->getOption('root')));
      }
      // value is not inside root
      $root_mask = $this->getOption('root_allowed') ? $root : $root.'/';
      if(mb_strpos($full_path, $root_mask) !== 0)
      {
        throw new sfValidatorError($this, 'invalid', array('directory' => $full_path));
      }
    }
    return $full_path;
  }
}
