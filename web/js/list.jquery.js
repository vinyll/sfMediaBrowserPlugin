$().ready(function(){
  $('.file,.folder').draggable({revert: true, zIndex: 10});
  $('.folder,.up').droppable({accept: '.file,.folder', hoverClass: 'movehere',
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
  if(typeof(move_file_url) == 'undefined' || move_file_url == '')
    throw('The variable "move_file_url" is not specified or has no value. File cannot be moved.');
  
  var data = {file: file, dir: to};
  var callback = function(r, status){
    $('#sf_media_browser_user_message').hide().html('<p'+r.status+'>'+r.message+'</p>').show('slow');
    if(status == 'success' && typeof(options['source']) != 'undefined')
    {
      options['source'].hide('fast');
    }
  };  
  $.post(move_file_url, data, callback, 'json');
}