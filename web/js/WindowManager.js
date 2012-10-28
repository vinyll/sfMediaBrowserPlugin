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
  
    tinymceCallback: function(field_name, url, type, win) 
    {
        if (sfMediaBrowserWindowManager.browser_url == '')
            throw new Error('You must initialise sfMediaBrowserWindowManager.init($browser_url) when calling browser for tinymce');
        
        tinyMCE.activeEditor.windowManager.open(
        {
            file: sfMediaBrowserWindowManager.browser_url,
            title: 'File Browser',
            width: 420,
            height: 400,
            resizable: "yes",
            inline: "yes",
            close_previous: "no"
        }, 
        {
            window: win,
            input: field_name
        });
    }
};