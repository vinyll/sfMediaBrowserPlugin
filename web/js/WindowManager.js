sfMediaBrowserWindowManager = {
  getInstance: function() {
    alert('init');
  },
  open: function(params) {
    var width = window.innerWidth ? window.innerWidth*.8 : 500;
    var popup = window.open(params['url'], 'sfMediaBrowser', 'width='+width+',addressbar=0,scrollbars=1');
    params['popup'] = popup;
    window.window_manager = new sfMediaBrowserWindowManagerObject(params);
  },
  
  addListerner: function(params) {
    document.getElementById(params['target']).onclick = function() {
      sfMediaBrowserWindowManager.open(params);
    }
  }
  
};

function sfMediaBrowserWindowManagerObject(params) {
  this.target = params['target'];
  this.popup = params['popup'];
}

sfMediaBrowserWindowManagerObject.prototype.callback = function(value) {
  document.getElementById(this.target).value = value;
  this.popup.close();
}
