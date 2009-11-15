<?php

/**
 *
 *
 * @package     sfMediaBrowser
 * @author      Vincent Agnano <vincet.agnano@particul.es>
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
      throw new sfConfigurationException(sprintf('The root directory "%s" does not exists.', $this->root_path));
    }
  }


  public function executeIndex(sfWebRequest $request)
  {
    $dir = urldecode($request->getParameter('dir'));
    // @TODO this is a fix for dirs like "//mydir". Fix the source why it sometimes makes it.
    $relative_dir = preg_replace('`^//?(.*)`', '/$1', $dir);

    // browser dir relative to app_sf_media_browser_root_dir
    $this->relative_dir = $relative_dir;
    // real browser dir
    $this->real_dir = sfConfig::get('app_sf_media_browser_root_dir').$relative_dir;
    // browser parent dir
    $this->parent_dir = $this->getParentDir($relative_dir);
    // system path for current dir
    $this->path = $this->root_path.$relative_dir;
    
    // list of sub-directories in current dir
    $this->dirs = $this->getDirectories($this->path);
    // list of files in current dir
    $this->files = $this->getFiles($this->path);
    $this->current_route = $this->getContext()->getRouting()->getCurrentRouteName();
    $this->current_params = $request->getGetParameters();
    
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
      $this->getUser()->setFlash($created ? 'notice' : 'error', 'directory.create');
    }
    $this->redirect($request->getReferer());
  }


  public function executeDeleteDirectory(sfWebRequest $request)
  {
    $deleted = sfMediaBrowserUtils::deleteRecursive(urldecode(sfConfig::get('sf_web_dir').'/'.sfConfig::get('app_sf_media_browser_root_dir').$request->getParameter('directory')));
    $this->getUser()->setFlash($deleted ? 'notice' : 'error', 'directory.delete');
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
      $post_file = $form->getValue('file');
      $filename = $post_file->getOriginalName();
      $name = sfMediaBrowserStringUtils::slugify(sfMediaBrowserUtils::getNameFromFile($filename));
      $ext = pathinfo($filename, PATHINFO_EXTENSION);
      $fullname = $ext ? $name.'.'.$ext : $name;
      $destination_dir = sfConfig::get('sf_web_dir').'/'.sfConfig::get('app_sf_media_browser_root_dir').$upload['directory'];
      // thumbnail
      if(sfConfig::get('app_sf_media_browser_thumbnails_enabled', false) && sfMediaBrowserUtils::getTypeFromExtension($ext) == 'image')
      {
        $this->generateThumbnail($post_file->getTempName(), $fullname, $destination_dir);
      }
      $this->getUser()->setFlash('notice', 'file.create');
      $post_file->save($destination_dir.'/'.$fullname);
    }
    else
    {
      $this->getUser()->setFlash('error', 'file.create');
    }
    $this->redirect($request->getReferer());
  }

  
  /**
   * @TODO
   * @param $file
   */
  protected function generateThumbnail($source_file, $destination_name, $destination_dir)
  {
    if(!class_exists('sfImage'))
    {
      throw new sfException('sfImageTransformPlugin must be installed in order to generate thumbnails.');
    }
    $thumb = new sfImage($source_file);
    $thumb->thumbnail(sfConfig::get('app_sf_media_browser_thumbnails_max_width', 64),
                     sfConfig::get('app_sf_media_browser_thumbnails_max_height', 64));
    $destination_dir = $destination_dir.'/'.sfConfig::get('app_sf_media_browser_thumbnails_dir', '.uploads');
    if(!file_exists($destination_dir))
    {
      mkdir($destination_dir);
      chmod($destination_dir, 0777);
    }
    return $thumb->saveAs($destination_dir.'/'.$destination_name);
  }


  public function executeDeleteFile(sfWebRequest $request)
  {
    $file = $this->createFileObject(urldecode($request->getParameter('file')));
    $file->delete();
    $this->getUser()->setFlash('notice', 'file.delete');
    $this->redirect($request->getReferer());
  }
  
  
  protected function createFileObject($file)
  {
    $class = sfMediaBrowserUtils::getTypeFromExtension(sfMediaBrowserUtils::getExtensionFromFile($file)) =='image'
            ? 'sfMediaBrowserImageObject'
            : 'sfMediaBrowserFileObject'
            ;
    return new $class($file);
  }


  public function executeEdit(sfWebRequest $request)
  {
    $this->file = $this->createFileObject(urldecode($request->getParameter('file')));
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
