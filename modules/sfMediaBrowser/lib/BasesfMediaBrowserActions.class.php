<?php

/**
 *
 *
 * @package
 * @subpackage
 * @author      Vincent Agnano <vince@onanga.com>
 *
 * Note :
 * - ***_path = system directory
 * - ***_dir = browser directory
 */
class BasesfMediaBrowserActions extends sfActions
{
  public function preExecute()
  {
    // symfony web path
    $this->web_path = sfConfig::get('sf_web_dir');

    // Configured root dir
    $this->root_dir = sfconfig::get('app_sf_media_browser_root_dir');

    $this->root_path = $this->web_path.$this->root_dir;

    if(!is_dir($this->root_path))
    {
      throw new sfConfigurationException(sprintf('The root directory "%s" does not exists', $this->root_path));
    }
  }


  public function executeIndex(sfWebRequest $request)
  {
    // Dir relative to root
    $current_dir = urldecode($request->getParameter('dir'));
    $relative_dir = substr($current_dir, 0, 1) == '/' ? $current_dir : '/'.$current_dir;
    

    // browser dir relative to app_sf_media_browser_root_dir
    $this->relative_dir = $relative_dir;
    // real browser dir
    $this->real_dir = sfConfig::get('app_sf_media_browser_root_dir').$current_dir;
    // browser parent dir
    $this->parent_dir = $this->getParentDir($relative_dir);
    // system path for current dir
    $this->path = $this->root_path.$relative_dir;
    // list of sub-directories in current dir
    $this->dirs = $this->getDirectories($this->path);
    // list of files in current dir
    $this->files = $this->getFiles($this->path);
    $this->current_route = $this->getContext()->getRouting()->getCurrentRouteName();
    // @TODO : find a better way to retrieve current url parameters (any ?)
    $this->current_params = $_GET;

    // forms
    $this->upload_form = new sfMediaBrowserUploadForm(array('directory' => $relative_dir));
    $this->dir_form = new sfMediaBrowserDirectoryForm(array('directory' => $relative_dir));
  }


  public function executeSelect(sfWebRequest $request)
  {
    $this->setLayout(dirname(__FILE__).'/../templates/popupLayout');
    $this->setTemplate('index');
    $this->executeIndex($request);
  }


  public function executeCreateDirectory(sfWebRequest $request)
  {
    $form = new sfMediaBrowserDirectoryForm();
    $form->bind($request->getParameter('directory'));
    if($form->isValid())
    {
      $real_path = realpath(sfConfig::get('sf_web_dir').'/'.sfConfig::get('app_sf_media_browser_root_dir').'/'.$form->getValue('directory'));
      $full_name = $real_path.'/'.$form->getValue('name');
      $created = mkdir($full_name);
      chmod($full_name, 0777);
      $notice = $created
              ? sprintf('The directory "%s" was succesfully created', $form->getValue('name'))
              : 'Some error occured'
              ;
        $this->getUser()->setFlash('notice', $notice);
      
    }
    $this->redirect($request->getReferer());
  }


  public function executeDeleteDirectory(sfWebRequest $request)
  {
    rmdir(urldecode(sfConfig::get('sf_web_dir').'/'.sfConfig::get('app_sf_media_browser_root_dir').$request->getParameter('directory')));
    $this->redirect($request->getReferer());
  }


  public function executeCreateFile(sfWebRequest $request)
  {
    $upload = $request->getParameter('upload');
    $form = new sfMediaBrowserUploadForm();
    $form->setUploadDir($upload['directory']);
    $form->bind($upload, $request->getFiles('upload'));
    if($form->isValid())
    {
      $file = $form->getValue('file');
      $filename = $file->getOriginalName();
      if(sfConfig::get('app_sf_media_browser_thumbnails_enabled', false))
      {
        $this->generateThumbnail($file);
      }
      $name = sfMediaBrowserStringUtils::slugify(sfMediaBrowserUtils::getNameFromFile($filename));
      $ext = sfMediaBrowserUtils::getExtensionFromFile($filename);
      $full_name = $ext ? $name.'.'.$ext : $name;
      $file->save(sfConfig::get('sf_web_dir').'/'.sfConfig::get('app_sf_media_browser_root_dir').$upload['directory'].'/'.$full_name);
    }
    $this->redirect($request->getReferer());
  }

  
  /**
   * @TODO
   * @param $file
   */
  protected function generateThumbnail($file)
  {
    $class_name = sfConfig::get('app_sf_media_browser_thumbnails_class', 'sfThumbnail');
    if(!class_exists($class_name))
    {
      $exception = $class_name == 'sfThumbnail'
                 ? "You should install sfThumbnailPlugin first."
                 : sprintf('Cannot find class "%s". Make sure to configure "app_sf_media_browser_thumbnails_class" properly.', $class_name)
                 ;
      throw new sfPluginException($exception);
    }
  }


  public function executeDeleteFile(sfWebRequest $request)
  {
    unlink($this->web_path.'/'.urldecode($request->getParameter('file')));
    $this->redirect($request->getReferer());
  }


  public function executeEdit(sfWebRequest $request)
  {
    $filename = urldecode($request->getParameter('file'));
    $this->file = sfMediaBrowserUtils::getTypeFromExtension(sfMediaBrowserUtils::getExtensionFromFile($filename)) =='image'
                ? new sfMediaBrowserImageObject($filename)
                : new sfMediaBrowserFileObject($filename)
                ;
    $this->rename_form = new sfMediaBrowserFileRenameForm(array('new_name' => $this->file->getName(), 'current_name' => $this->file->getName()));
  }


  public function executeRename(sfWebRequest $request)
  {
    $type = $request->getParameter('type');
    $form = new sfMediaBrowserFileRenameForm();
    $form->bind($request->getParameter('rename'));
    if($form->isValid())
    {
      $file = new sfMediaBrowserFileObject($form->getValue('directory').'/'.$form->getValue('current_name'));
      rename($file->getSystemPath(), dirname($file->getSystemPath()).'/'.$form->getValue('new_name'));
      $this->redirect($this->generateUrl('sf_media_browser_edit', array('file' => $form->getValue('new_name'))));
    }
    //$this->redirect($request->getReferer());

  }


# Protected

  protected function getDirectories($path)
  {
    return sfFinder::type('dir')->
            maxdepth(0)->
            prune('.*')->
            discard('.*')->
            relative()->
            in($path)
            ;
  }

  
  protected function getFiles($path)
  {
    return sfFinder::type('files')->
             maxdepth(0)->
             prune('.*')->
             discard('.*')->
             relative()->
             in($path)
             ;
  }

  protected function getParentDir($path)
  {
    // Remove trailing slash
    if(substr($path, -1, 1) == '/')
    {
      $path = substr($path, 0, -1);
    }
    // Find last slash
    $slash_pos = strrpos($path, '/');

    // return root if path is a root subfolder
    if($slash_pos === 0)
    {
      return '/';
    }

    return (string) substr($path, 0, $slash_pos);
  }
  
}
