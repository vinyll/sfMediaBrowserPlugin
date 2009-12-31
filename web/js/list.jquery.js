function requireVar(variable, message) {
  try {
    var to_check = eval(variable);
    if(to_check == '')
      throw(message+' (empty value)');
  } catch (e) {
    throw(message+' (undeclared variable)');
  }
}


function alertUser(response, status){
  $('#sf_media_browser_user_message').hide().html('<p class="'+response.status+'">'+response.message+'</p>').show('slow');
  return status;
};  


/* Move a file */

$(document).ready(function(){
  $('#sf_media_browser_list .file,#sf_media_browser_list .folder').draggable({revert: true, zIndex: 10});
  $('#sf_media_browser_list .folder,#sf_media_browser_list .up').droppable({accept: '#sf_media_browser_list .file,#sf_media_browser_list .folder', hoverClass: 'movehere',
    drop: function(event, ui) {
      var source = $(ui.draggable);
      var target = $(this);
      var source_value = source.find('.icon a').attr('href');
      if(!source.hasClass('file')) source_value = getDirFromUrl(source_value);
      var target_value = getDirFromUrl(target.find('.icon a').attr('href'));
      moveFile(source_value, target_value, {source: source, target: target});
    }
  });
  
});


function getDirFromUrl(url) {
  var regex = new RegExp("[\\?&]dir=([^&#]*)");
  results = regex.exec(url);
  return results[1] ? unescape(results[1]) : '/';
};


function moveFile(file, to, options) {
  //alert('moving file from "'+file+'" to "'+to+'"');
  requireVar('move_file_url', 'The variable "move_file_url" should specify the url to call for moving a file. File cannot be moved.');
  
  var data = {file: file, dir: to};
  var callback = function(r, s){
    var status = alertUser(r,s);
    if(status == 'success' && typeof(options['source']) != 'undefined')
    {
      options['source'].hide('fast');
    }
  };
  $.post(move_file_url, data, callback, 'json');
}


/* Rename a file */

$(document).ready(function(){
  $('#sf_media_browser_list label.name').data('editing', false);
  $('#sf_media_browser_list label.name').dblclick(function() {
    requireVar('rename_file_url', 'The variable "rename_file_url" should specify the url to call for renaming a file. File cannot be renamed.');
    var l = $(this);
    if(l.data('editing') == true) return;
    l.data('original_value', l.html());
    l.data('editing', true);
    var curr = l.html();
    var clean_name = curr.lastIndexOf('.') > 0 ? curr.substr(0, curr.lastIndexOf('.')) : curr;
    l.html('<input type="text" id="rename_tag" value="'+clean_name+'" /><span id="validate">V</span><span id="cancel">X</span>');
    l.children('#cancel').click(function(){
      l.html(l.data('original_value'));
      l.data('editing', false);
    });
    l.children('#validate').click(function(){
      l.data('editing', false);
      var new_name = l.children('#rename_tag').attr('value');
      l.html(l.data('original_value'));
      
      var link = l.siblings('.icon').children('a');
      if(!l.parent().hasClass('file')) file = getDirFromUrl(link.attr('href'));
      else file = link.attr('href');
      var data = {file: file, name: new_name};
      var callback = function(r,s){
        alertUser(r,s);
        if(typeof(r['name']) != 'undefined') {
          l.html(r['name']);
          if(!l.parent().hasClass('file')) document.location.href = document.location.href;
          else file = link.attr('href', r['url']);
        }
      };
      if(l.data('original_value') != new_name) {
        $.post(rename_file_url, data, callback, 'json');
      }
    });
    
  });
});