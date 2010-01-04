/**
 * This file is used for sfMediaBrowser/list template
 */
window.onload = function() {
  var tags = document.getElementById('sf_media_browser_list').getElementsByTagName('a');
  var tag;
  for(var i=0; i<tags.length; ++i) {
    tag = tags[i];
    if(tag.getAttribute('class') && tag.getAttribute('class').indexOf('delete') != -1)
    {
      tag.onclick = function() {
        return window.confirm(delete_msg);
      }
    }
  }
}