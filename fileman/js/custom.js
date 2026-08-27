/*
  RoxyFileman - web based file manager. Ready to use with CKEditor, TinyMCE. 
  Can be easily integrated with any other WYSIWYG editor or CMS.

  Copyright (C) 2013, RoxyFileman.com - Lyubomir Arsov. All rights reserved.
  For licensing, see LICENSE.txt or http://RoxyFileman.com/license

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.

  Contact: Lyubomir Arsov, liubo (at) web-lobby.com
*/
function FileSelected(file){
  /**
   * file is an object containing following properties:
   * 
   * fullPath - path to the file - absolute from your site root
   * path - directory in which the file is located - absolute from your site root
   * size - size of the file in bytes
   * time - timestamo of last modification
   * name - file name
   * ext - file extension
   * width - if the file is image, this will be the width of the original image, 0 otherwise
   * height - if the file is image, this will be the height of the original image, 0 otherwise
   * 
   */
  var url_string = window.location.href;
  var url = new URL(url_string);
  var env = url.searchParams.get("env");
  var win = window;
  if(env == 'iframe'){
    win = parent.window;
  } else {
    win = opener.window;
  }
  var integration = url.searchParams.get("integration");
  if(integration == 'input'){
    win.filemanUpdate(file);
    return;
  }
  var CKEditorFuncNum = url.searchParams.get("CKEditorFuncNum");
  var image_exts = ['bmp', 'gif', 'jpg', 'jpeg', 'png'];
  var cke_ob = win.CKEDITOR; 
  if(image_exts.indexOf(file.ext)>-1){
    cke_ob.tools.callFunction(CKEditorFuncNum, file.fullPath, '');
    self.close();
    return;
  }
  for(var ckid in cke_ob.instances) { 
    if(cke_ob.instances[ckid].focusManager.hasFocus) break;
  }
  cke_ob.instances[ckid].insertHtml('<a href="' + file.fullPath + '">' + file.name + '</a>', 'unfiltered_html'); 
  var dialog = cke_ob.dialog.getCurrent();
  // Close file manager if it's opened in separate window. 
  dialog.hide(); 
  self.close();
}
function GetSelectedValue(){
  /**
  * This function is called to retrieve selected value when custom integration is used.
  * Url parameter selected will override this value.
  */
  
  return "";
}
