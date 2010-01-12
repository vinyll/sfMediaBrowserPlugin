<?php

/*
 * This file is part of the sfMediaBrowser package.
 *
 * (c) 2009 Vincent Agnano <vincent.agnano@particul.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormInput represents an HTML input file browser tag.
 *
 * @package    sfMediaBrowser
 * @subpackage widget
 * @author     Vincent Agnano <vincent.agnano@particul.es>
 */
class sfWidgetFormInputMediaBrowser extends sfWidgetForm
{

  protected $context;
  
  /**
   * Constructor.
   *
   * Available options:
   *
   *  * type: The widget type (text by default)
   *
   * @param array $options     An array of options :
   *   - dir: The default directory to display if no value exists.
   *          The url is relative to app_sf_media_browser_root_dir
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addOption('type', 'text');
    $this->addOption('dir', null);
    
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
    $this->context = sfContext::getInstance();

    $attributes = array_merge(array('type' => $this->getOption('type'), 'name' => $name, 'value' => $value), $attributes);
    $attributes = $this->fixFormId($attributes);
    
    // Get default dir for popup window
    $dir = $value
         ? dirname($value)
         : sfConfig::get('app_sf_media_browser_root_dir').'/'.$this->getOption('dir');
    
    $url = $this->context->getRouting()->generate('sf_media_browser_select', array('dir' => $dir));

    $tag = $this->renderTag('input', $attributes)
           .$this->includeView()
           .$this->includeDelete();
    
    // Add javascripts and stylesheets as configure in app_sf_media_browser_assets_widget
    sfMediaBrowserUtils::loadAssets(sfConfig::get('app_sf_media_browser_assets_widget'));
    $tag .= $this->loadJavascript(array_merge($attributes, array('url' => $url)));

    return $this->wrapTag($tag);
  }
  

  /**
   * Insert dependant javascripts and include a <script> with sfMediaBrowserWindowManager.addListerner
   * @return string HTML formatted js code
   */
  protected function loadJavascript(array $params)
  {
    return <<<EOF
    <script type="text/javascript">
      sfMediaBrowserWindowManager.addListerner({target: '{$params['id']}', url: '{$params['url']}'});
    </script>
EOF;
  }


  /**
   * Includes a delete tag
   * @return string HTML formatted span class="delete"
   */
  protected function includeDelete()
  {
    $tag = '<a class="delete">delete</a>';
    return $tag;
  }
  
  
  /**
   * Includes a view tag
   * @return string HTML formatted span class="view"
   */
  protected function includeView()
  {
    $tag = '<a class="view">view</a>';
    return $tag;
  }
  

  /**
   * Wraps a tag within a <span class="sf_media_browser_input_file"></span>
   * @param string $tag tag to wrap
   * @return string HTML string
   */
  protected function wrapTag($tag)
  {
    return '<span class="sf_media_browser_input_file">'.$tag.'</span>';
  }
}
