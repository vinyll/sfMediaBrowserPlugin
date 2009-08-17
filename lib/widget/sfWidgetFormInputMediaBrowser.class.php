<?php

/*
 * This file is part of the sfMediaBrowserPlugin package.
 * (c) Vincent Agnano <vince@onanga.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormInput represents an HTML input file browser tag.
 *
 * @package    sfMediaBrowser
 * @subpackage widget
 * @author     Vincent Agnano <vince@onanga.com>
 */
class sfWidgetFormInputMediaBrowser extends sfWidgetForm
{
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * type: The widget type (text by default)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addOption('type', 'text');
    
    $this->setOption('is_hidden', false);
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The value displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $context = sfContext::getInstance();
    if(!isset($attributes['load_javascript']) || $attributes['load_javascript'] !== false)
    {
      $context->getResponse()->addJavascript('/sfMediaBrowserPlugin/js/WindowManager.js');
    }
    if(!isset($attributes['load_stylesheet']) || $attributes['load_stylesheet'] !== false)
    {
      $context->getResponse()->addStylesheet('/sfMediaBrowserPlugin/css/form_widget.css');
    }
    $attributes['class'] = isset($attributes['class']) ? $attributes['class'].' sf_media_upload' : 'sf_media_upload';
    $attributes = array_merge(array('type' => $this->getOption('type'), 'name' => $name, 'value' => $value), $attributes);
    $attributes = $this->fixFormId($attributes);
    $tag_id = $attributes['id'];
    $url = $context->getRouting()->generate('sf_media_browser_select');

    $tag = $this->renderTag('input', $attributes);
    $tag .= <<<EOF
    <script type="text/javascript">
      sfMediaBrowserWindowManager.addListerner({target: '{$tag_id}', url: '{$url}'});
    </script>
EOF;
    return $tag;
  }
}
