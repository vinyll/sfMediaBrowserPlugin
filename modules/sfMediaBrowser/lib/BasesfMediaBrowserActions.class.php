<?php

/**
 *
 *
 * @package
 * @subpackage
 * @author      Vincent Agnano <vince@onanga.com>
 */
class BasesfMediaBrowserActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    // Configured root dir
    $root_dir = sfConfig::get('app_sf_media_browser_root_dir');
    if(!is_dir($root_dir))
    {
      throw new sfConfigurationException(sprintf('The root directory "%s" does not exists', $root_dir));
    }

    // Dir relative to root
    $relative_dir = urldecode($request->getParameter('dir'));

    $this->parent_dir = $this->getParentDir($relative_dir);
    $this->relative_dir = $relative_dir;
    $this->path = $root_dir.$relative_dir;
    $this->dirs = $this->getDirectories($this->path);
    $this->files = $this->getFiles($this->path);
    $this->current_route = $this->getContext()->getRouting()->getCurrentRouteName();
    $this->current_params = $_GET;

    // forms
    $this->upload_form = new sfMediaBrowserUploadForm(array('directory' => $relative_dir));
    $this->dir_form = new sfMediaBrowserDirectoryForm(array('directory' => $relative_dir));
  }


  public function executeSelect(sfWebRequest $request)
  {
    $this->setLayout(dirname(__FILE__).'/../templates/popupLayout');
    $this->getResponse()->addJavascript('/sfMediaBrowserPlugin/js/filePicker.js');
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
      $created = mkdir($full_name, 0777);
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
      $name = $file->getOriginalName();
      $file->save(sfConfig::get('sf_web_dir').'/'.sfConfig::get('app_sf_media_browser_root_dir').$upload['directory'].'/'.$name);
    }
    $this->redirect($request->getReferer());
  }


  public function executeDeleteFile(sfWebRequest $request)
  {
    unlink(urldecode(sfConfig::get('sf_web_dir').$request->getParameter('file')));
    $this->redirect($request->getReferer());
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