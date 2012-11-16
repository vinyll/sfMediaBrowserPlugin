<?php

require_once dirname(__FILE__) . '/../lib/BasesfMediaBrowserActions.class.php';

/**
 *
 *
 * @package     sfMediaBrowser
 * @author      Vincent Agnano <vincent.agnano@particul.es>
 */
class sfMediaBrowserActions extends BasesfMediaBrowserActions
{
    public function preExecute()
    {
        $this->getLogger()->info(">>> sfMediaBrowserActions::preExecute");
        $user = $this->getUser();
        if (!$user->isAuthenticated())
        {
            return $this->redirect('signin');
        }
        elseif ($user->isDomainUser())
        {
            if (!$user->getGuardUser()->getWebsite())
            {
                return $this->redirect('not_found');
            }
            elseif ($user->getGuardUser()->getSfGuardUserProfile()->getBlocked() || $user->getGuardUser()->getWebsite()->getIsBlocked())
            {
                return $this->redirect('blocked');
            }
            
            $this->root_dir = $user->getGuardUser()->getWebsite()->getRelativeMediaUploadPath();
            $this->root_path = $user->getGuardUser()->getWebsite()->getMediaUploadPath();
            
            // TODO: implement folder creation out of the web/
            $this->disable_dir_create = true;
            
            // TODO: implement subfolder-compliant callbacks for TinyMCE
            $this->callback_route_pattern = "@uploaded_media?file=%s";
        }
        elseif ($user->isPlatformAdmin())
        {
            $this->root_dir = sfConfig::get('app_sf_media_browser_root_dir');
            $this->root_path = realpath(sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . $this->root_dir);
        }

        $this->requested_dir = urldecode($this->getRequestParameter('dir'));
        $this->requested_dir = $this->checkPath($this->root_path . '/' . $this->requested_dir) ? preg_replace('`(/)+`', '/', $this->requested_dir) : '/';
    }
}