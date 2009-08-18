/**
 * sfMediaBrowserFilePicker is a class that enables picking
 * a file in a "select" popup context in an unobstrusive way.
 * It exists as such for no dependency with any JS framework.
 */
function sfMediaBrowserFilePicker(){}
sfMediaBrowserFilePicker = {
  findFiles: function(container_id) {
    if(container_id == null)
      throw new Error('sfMediaBrowserFilePicker.findFiles(container_id) requires a string parameter that matches a document DOM id');
    var tags = document.getElementById(container_id).getElementsByTagName('*');
    var li, as, a;
    for(var i=0; i<tags.length; ++i) {
      li = tags[i];
      if(sfMediaBrowserFilePicker.hasClass(li, 'file')) {
        as = li.getElementsByTagName('a');
        a = as[0];
        a.onclick = function() {
          sfMediaBrowserFilePicker.callback(this.getAttribute('href'));
          return false;
        }
      }
    }
  },
  hasClass: function(element, class_name) {
    if(!element.hasAttribute('class'))
      return false;
    return element.getAttribute('class').indexOf(class_name) != -1;
  },
  callback: function(url) {
    var window_manager = window.opener.window_manager;
    window_manager.callback(url);
  }
};
