/**
 * sfMediaBrowserWindowManager manages popup window opening and callbacks.
 * It also managed tinyMCE and should manage any other js based plugin extension.
 *
 * @WARNING : tinymceCallback url is hardcoded !!!!!!
 * @author: Vincent Agnano <vincent.agnano@particul.es>
 */
sfMediaBrowserWindowManager = {

  browser_url:  null,

  init: function(browser_url) {
    if(browser_url == null || browser_url == '')
      throw new Error('sfMediaBrowserWindowManager.init() requires one parameter that is the url for browser popup window');
    sfMediaBrowserWindowManager.browser_url = browser_url;
  },
  
  open: function(params) {
    var width = window.innerWidth ? window.innerWidth*.8 : 500;
    params['popup'] = window.open(params['url'], 'sfMediaBrowser', 'width='+width+',addressbar=0,scrollbars=1');
    return new sfMediaBrowserWindowManagerObject(params);
  },
  
  addListerner: function(params) {
    var event = params['event'] ? params['event'] : 'onclick';
    params['target'] = document.getElementById(params['target']);
    params['target'][event] = function() {
      var window_manager = sfMediaBrowserWindowManager.open(params);
      window.window_manager = window_manager;
    }
  },
  
  tinymceCallback: function(field_name, url, type, win) {
    if(sfMediaBrowserWindowManager.browser_url == '')
      throw new Error('You must initialise sfMediaBrowserWindowManager.init($browser_url) when calling browser for tinymce');
    var window_manager = sfMediaBrowserWindowManager.open({
      target: win.document.getElementById(field_name),
      url:    sfMediaBrowserWindowManager.browser_url
    });
    win.onunload = function() {
      window_manager.popup.close();
    }
    window_manager.popup.opener = win;
    window_manager.popup.opener.window_manager = window_manager;
  }
  
};

function sfMediaBrowserWindowManagerObject(params) {
  this.target = params['target'];
  this.popup = params['popup'];
}

sfMediaBrowserWindowManagerObject.prototype.callback = function(value) {
  this.getTarget().value = value;
  this.popup.close();
}
sfMediaBrowserWindowManagerObject.prototype.getTarget = function() {
  if(this.target.getAttribute)
    return this.target;
  else
    return this.popup.opener.document.getElementById(this.target);
}

