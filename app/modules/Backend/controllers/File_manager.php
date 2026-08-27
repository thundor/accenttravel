<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class File_manager extends MX_Controller {
  function __construct() {
    parent :: __construct();
  }
  public function index() {
    if(!$this->user->can('backend-access')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    if(!$this->user->can('backend-config-save')){
      $this->redirect('backend','Acces restrictionat','error');
    }
    // $this->redirect('/backend/trip/orders');
    // $this->theme->view('backend/index', $this->data);
  }
  public function upload() {
    // if (!$this->input->is_ajax_request()) {
      // $this->redirect('backend','Acces restrictionat','error');
    // }
    if(!$this->user->can('backend-access')){
      $this->outputError('Acces restrictionat');
    }
    if(!$this->user->can('backend-config-save')){
      $this->outputError('Acces restrictionat');
    }
    
    $upload_dir = array(
      'img'=> 'resources/images/',
      'files'=> 'resources/files/',
    );
    $imgset = array(
      'maxsize' => 10000,
      'maxwidth' => 5000,
      'maxheight' => 5000,
      'minwidth' => 1,
      'minheight' => 1,
      'type' => array('bmp', 'gif', 'jpg', 'jpeg', 'png'),
    );
    $fileset = array(
      'maxsize' => 10000,
      'type' => array('7z','aiff','asf','avi','bmp','csv','doc','docx','fla','flv','gif','gz','gzip','jpeg','jpg','mid','mov','mp3','mp4','mpc','mpeg','mpg','ods','odt','pdf','png','ppt','pptx','pxd','qt','ram','rar','rm','rmi','rmvb','rtf','sdc','sitd','swf','sxc','sxw','tar','tgz','tif','tiff','txt','vsd','wav','wma','wmv','xls','xlsx','zip'),
    );
    /** If 0, will OVERWRITE the existing file **/
    define('RENAME_F', 1);
    $re = '';
    @header('Content-type: text/html; charset=utf-8'); 
    ?><script type='text/javascript'><?php
    if(isset($_FILES['upload']) && strlen($_FILES['upload']['name']) >1) {
      define('F_NAME', preg_replace('/\.(.+?)$/i', '', basename($_FILES['upload']['name'])));  
      /** get filename without extension **/
      /** get protocol and host name to send the absolute image path to CKEditor **/
      $site = '';
      $sepext = explode('.', strtolower($_FILES['upload']['name']));
      $type = end($sepext);    /** gets extension **/
      $upload_dir = in_array($type, $imgset['type']) ? $upload_dir['img'] : $upload_dir['files'];
      /** checkings for image **/
      if(in_array($type, $imgset['type'])){
        list($width, $height) = getimagesize($_FILES['upload']['tmp_name']);  /** image width and height **/
        if(isset($width) && isset($height)) {
          if($width > $imgset['maxwidth'] || $height > $imgset['maxheight']) $re .= '\\n Width x Height = '. $width .' x '. $height .' \\n The maximum Width x Height must be: '. $imgset['maxwidth']. ' x '. $imgset['maxheight'];
          if($width < $imgset['minwidth'] || $height < $imgset['minheight']) $re .= '\\n Width x Height = '. $width .' x '. $height .'\\n The minimum Width x Height must be: '. $imgset['minwidth']. ' x '. $imgset['minheight'];
          if($_FILES['upload']['size'] > $imgset['maxsize']*1024) $re .= '\\n Maximum file size must be: '. $imgset['maxsize']. ' KB.';
        }
      } elseif(in_array($type, $fileset['type'])){
        if($_FILES['upload']['size'] > $fileset['maxsize']*1024) $re .= '\\n Maximum file size must be: '. $fileset['maxsize']. ' KB.';
      } else $re .= 'The file: '. $_FILES['upload']['name']. ' has not the allowed extension type.';
      /** set filename; if file exists, and RENAME_F is 1, set "img_name_I" **/
      /** $p = dir-path, $fn=filename to check, $ex=extension $i=index to rename **/
      function setFName($p, $fn, $ex, $i){
        if(RENAME_F ==1 && file_exists($p .$fn .$ex)) return setFName($p, F_NAME .'_'. ($i +1), $ex, ($i +1));
        else return $fn .$ex;
      }
      $f_name = setFName(FCPATH . $upload_dir, F_NAME, ".$type", 0);
      $uploadpath = FCPATH . $upload_dir . $f_name;  /** full file path **/
      /** If no errors, upload the image, else, output the errors **/
      if($re === '') {
        /** print_r($_FILES);exit; **/
        if(move_uploaded_file($_FILES['upload']['tmp_name'], $uploadpath)) {
          $CKEditorFuncNum = $_GET['CKEditorFuncNum'];
          $url = $upload_dir . $f_name;
          $msg = F_NAME .'.'. $type .' successfully uploaded: \\n- Size: '. number_format($_FILES['upload']['size']/1024, 2, '.', '') .' KB';
          if(in_array($type, $imgset['type'])){
            echo "window.parent.CKEDITOR.tools.callFunction($CKEditorFuncNum, '$url', '$msg');";  /** for img **/
          } else { ?>
            var cke_ob = window.parent.CKEDITOR; 
            for(var ckid in cke_ob.instances) { 
              if(cke_ob.instances[ckid].focusManager.hasFocus) break;
            }
            var link = <?php echo json_encode($url); ?>;
            var content = <?php echo json_encode($f_name); ?>;
            cke_ob.instances[ckid].insertHtml('<a href="' + link + '">' + content + '</a>', 'unfiltered_html'); 
            var dialog = cke_ob.dialog.getCurrent();
            dialog.hide();
            <?php
          }
        } else {
          $re = 'Unable to upload the file';
          ?>alert(<?php echo json_encode($re); ?>);<?php
        }
      } else { 
        ?>alert(<?php echo json_encode($re); ?>);<?php
      }
    }
    ?></script><?php
    exit;
  }
  public function files() {
    // if (!$this->input->is_ajax_request()) {
      // $this->redirect('backend','Acces restrictionat','error');
    // }
    if(!$this->user->can('backend-access')){
      $this->outputError('Acces restrictionat');
    }
    if(!$this->user->can('backend-config-save')){
      $this->outputError('Acces restrictionat');
    }
    require_once(FCPATH . 'ckfinder/core/connector/php/connector.php');
    exit;
    $this->output();
  }
}