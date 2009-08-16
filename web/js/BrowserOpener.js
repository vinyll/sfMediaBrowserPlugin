function BrowserOpener() {
}

BrowserOpener = {

  openBrowser: function(params) {
    var width = window.innerWidth ? window.innerWidth*.8 : 500;
    window.open(params['url'], params['target'], 'width='+width+',addressbar=0,scrollbars=1');
  },
  
  addOpenBrowserListerner: function(params) {
    document.getElementById(params['target']).onclick = function() {
      BrowserOpener.openBrowser(params);
    }
  }
  
};

