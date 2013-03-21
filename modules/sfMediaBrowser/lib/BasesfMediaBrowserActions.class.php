<?php

/**
 *
 *
 * @package     sfMediaBrowser
 * @author      Vincent Agnano <vincent.agnano@particul.es>
 *
 * Note :
 * - ***_path = system directory
 * - ***_dir = browser directory
 */
class BasesfMediaBrowserActions extends sfActions
{
  public function preExecute()
  {
    // Configured root dir
    $this->root_dir = sfconfig::get('app_sf_media_browser_root_dir');
    
    // Calculated root path
    $this->root_path = realpath(sfConfig::get('sf_web_dir').'/'.$this->root_dir);
    
    $this->requested_dir = urldecode($this->getRequestParameter('dir'));
    
    $this->requested_dir = $this->checkPath($this->root_path.'/'.$this->requested_dir)
                         ? preg_replace('`(/)+`', '/', $this->requested_dir)
                         : '/';
  }


  public function executeList(sfWebRequest $request)
  {

    $display_dir = preg_replace('`^('.$this->root_dir.')`', '', $this->requested_dir);

    // dir relative to root_dir
    $this->relative_dir = $this->requested_dir;
    // dir relative to /web
    $this->relative_url = $this->root_dir.$this->requested_dir;
    // User dispay dir
    $this->display_dir = $display_dir ? $display_dir : '/';
    // browser parent dir
    $this->parent_dir = dirname($this->relative_dir) && dirname($this->relative_dir) != $this->relative_dir
                      ? dirname($this->relative_dir)
                      : null;
    // system path for current dir
    $this->path = $this->root_path.$this->requested_dir;
    
    // list of sub-directories in current dir
    $this->dirs = $this->getDirectories($this->path);
    // list of files in current dir
    $this->files = $this->getFiles($this->path);
    $this->current_route = $this->getContext()->getRouting()->getCurrentRouteName();
    $this->current_params = $request->getGetParameters();

    // forms
    $this->upload_form = new sfMediaBrowserUploadForm(array('directory' => $this->display_dir));
    $this->dir_form = new sfMediaBrowserDirectoryForm(array('directory' => $this->display_dir));
  }


  public function executeSelect(sfWebRequest $request)
  {
    $this->setLayout(dirname(__FILE__).'/../templates/popupLayout');
    $this->setTemplate('list');
    $this->executeList($request);
  }


  public function executeCreateDirectory(sfWebRequest $request)
  {
    $form = new sfMediaBrowserDirectoryForm();
    $form->bind($request->getParameter('directory'));
    if($form->isValid())
    {
      $real_path = $form->getValue('directory');
      $new_dir = $form->getValue('directory').'/'.$form->getValue('name');
      $created = @mkdir($new_dir);
      @chmod($new_dir, 0777);
      $this->getUser()->setFlash($created ? 'notice' : 'error', 'directory.create');
    }
    else
    {
      $this->getUser()->setFlash('error', 'directory.create');
    }
   $this->redirect($request->getReferer());
  }


  public function executeDeleteDirectory(sfWebRequest $request)
  {
    $path = $this->root_path.'/'.urldecode($request->getParameter('directory'));
    $this->checkPath($path);
  
    $deleted = sfMediaBrowserUtils::deleteRecursive($path);
    $this->getUser()->setFlash($deleted ? 'notice' : 'error', 'directory.delete');
    $this->redirect($request->getReferer());
  }


  public function executeCreateFile(sfWebRequest $request)
  {
    $upload = $request->getParameter('upload');
    $this->checkPath($this->root_path.'/'.$upload['directory']);
    $form = new sfMediaBrowserUploadForm();
    $form->bind($upload, $request->getFiles('upload'));
    if($form->isValid())
    {
      $post_file = $form->getValue('file');
      $filename = $post_file->getOriginalName();
      $name = sfMediaBrowserStringUtils::slugify(pathinfo($filename, PATHINFO_FILENAME));
      $ext = pathinfo($filename, PATHINFO_EXTENSION);
      $fullname = $ext ? $name.'.'.$ext : $name;
      $destination_dir = realpath($this->root_path.'/'.$upload['directory']);
      
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
   *@todo Get rid of the purpose of urldecode for 'dir' parameter
   */
  public function executeMove(sfWebRequest $request)
  {
    $current_path = $this->root_path.'/'.$request->getParameter('file');
    $new_path = $this->root_path.'/'.urldecode($request->getParameter('dir'));
    $this->checkPath($current_path);
    $this->checkPath(dirname($new_path));
    
    $error = null;
    $moved = @rename($current_path, $new_path);
    if(!$moved)
    {
      $this->logMessage(sprintf('Failed renaming "%s" to "%s".', $curent_path, $new_path), 'err');
    }
    
    if($request->isXmlHttpRequest())
    {
      sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N'));
      if($error)
      {
        $reponse = array('status' => 'error', 'message' => __('Some error occured.'));
      }
      elseif($moved)
      {
        $response = array('status' => 'notice', 'message' => __('The file was successfully moved.'));
      }
      elseif(file_exists($new_path))
      {
        $response = array('status' => 'error', 'message' => __('A file with the same name already exists in this folder.'));
      }
      else
      {
        $response = array('status' => 'error', 'message' => __('Some error occured.'));
      }
      return $this->renderText(json_encode($response));
    }
    $this->redirect($request->getReferer());
  }
  
  
  public function executeRename(sfWebRequest $request)
  {
    $file = new sfMediaBrowserFileObject($request->getParameter('file'));
    $name = sfMediaBrowserStringUtils::slugify(pathinfo($request->getParameter('name'), PATHINFO_FILENAME));
    $ext = $file->getExtension();
    $valid_filename = $ext ? $name.'.'.$ext : $name;
    $new_name = dirname($file->getPath()).'/'.$valid_filename;
    
    $error = null;
    try
    {
      $renamed = rename($file->getPath(), $new_name);
    }
    catch(Exception $e)
    {
      $error = $e;
      $this->logMessage($e, 'err');
    }
    
    if($request->isXmlHttpRequest())
    {
      sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N'));
      if($error)
      {
        $reponse = array('status' => 'error', 'message' => __('Some error occured.'));
      }
      elseif($renamed)
      {
        $response = array('status' => 'notice', 'message' => __('The file was successfully renamed.'), 'name' => $valid_filename, 'url' => dirname($file->getUrl()).'/'.$valid_filename);
      }
      elseif(file_exists($new_name))
      {
        $response = array('status' => 'error', 'message' => __('A file with the same name already exists in this folder.'));
      }
      else
      {
        $response = array('status' => 'error', 'message' => __('Some error occured.'));
      }
      return $this->renderText(json_encode($response));
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
    $destination_dir = $destination_dir.'/'.sfConfig::get('app_sf_media_browser_thumbnails_dir');
    if(!file_exists($destination_dir))
    {
      mkdir($destination_dir);
      chmod($destination_dir, 0777);
    }
    return $thumb->saveAs($destination_dir.'/'.$destination_name);
  }


  public function executeDeleteFile(sfWebRequest $request)
  {
    $path = urldecode($request->getParameter('file'));
    $this->checkPath($this->root_path.'/'.$path);
    $file = $this->createFileObject($this->root_path.'/'.$path);
    $file->delete();
    $this->getUser()->setFlash('notice', 'file.delete');
    $this->redirect($request->getReferer());
  }
  
  
# Protected

  protected function checkPath($path)
  {
    $validator = new sfValidatorMediaBrowserDirectory(array('root' => $this->root_path));
    try
    {
      $validator->clean($path);
      return true;
    }
    catch(sfValidatorError $e)
    {}
    return false;
  }
  
  /**
   *
   * @param $file string path
   * @return sfMediaBrowserFileObject
   */
  protected function createFileObject($file)
  {
    $class = sfMediaBrowserUtils::getTypeFromExtension(pathinfo($file, PATHINFO_EXTENSION)) == 'image'
            ? 'sfMediaBrowserImageObject'
            : 'sfMediaBrowserFileObject'
            ;
    return new $class($file, $this->root_path);
  }


  protected function getDirectories($path)
  {
    return sfFinder::type('dir')->
            maxdepth(0)->
            prune('.*')->
            discard(sfConfig::get('app_sf_media_browser_thumbnails_dir'))->
            sort_by_name()->
            relative()->
            in($path)
            ;
  }

  
  protected function getFiles($path)
  {
    return sfFinder::type('files')->
             maxdepth(0)->
             relative()->
             sort_by_name()->
             in($path)
             ;
  }
  
}
