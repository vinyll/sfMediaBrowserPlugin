function sfMediaBrowserWindowManager() {
}

sfMediaBrowserWindowManager = {

  open: function(params) {
    var width = window.innerWidth ? window.innerWidth*.8 : 500;
    window.open(params['url'], params['target'], 'width='+width+',addressbar=0,scrollbars=1');
  },
  
  addListerner: function(params) {
    document.getElementById(params['target']).onclick = function() {
      sfMediaBrowserWindowManager.open(params);
    }
  }
  
};

