/**
 * sfMediaBrowserWindowManager manages popup window opening and callbacks.
 * It also managed tinyMCE and should manage any other js based plugin extension.
 *
 * @WARNING : tinymceCallback url is hardcoded !!!!!!
 * @author: Vincent Agnano <vince@onanga.com>
 */
sfMediaBrowserWindowManager = {
  
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
    var window_manager = sfMediaBrowserWindowManager.open({
      target: win.document.getElementById(field_name),
      url:    '/backend_dev.php/sf_media_browser_select'
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

