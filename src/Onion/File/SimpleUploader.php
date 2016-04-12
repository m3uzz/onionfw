<?php
/**
 * Simple Ajax Uploader
 * Version 1.8.2
 * https://github.com/LPology/Simple-Ajax-Uploader
 *
 * Copyright 2013 LPology, LLC
 * Released under the MIT license
 *
 * View the documentation for an example of how to use this class.
 */

namespace Onion\File;
use Onion;
use Onion\Log\Debug;

/**
* Handles XHR uploads
* Used by FileUpload below -- don't call this class directly.
*/
final class FileUploadXHR 
{
	public $_sUploadName;
	
  	final public function Save($psSavePath)
  	{
    	if (false !== file_put_contents($psSavePath, fopen('php://input', 'r'))) 
    	{
      		return true;
    	}
    
	    return false;
  	}
  
  	final public function getFileName() 
  	{
    	return $_GET[$this->_sUploadName];
  	}
  
  	final public function getFileSize() 
  	{
    	if (isset($_SERVER['CONTENT_LENGTH'])) 
    	{
      		return (int)$_SERVER['CONTENT_LENGTH'];
    	}
    	else
    	{
      		throw new Exception('Content length not supported.');
    	}
  	}
}

/**
* Handles form uploads through hidden iframe
* Used by FileUpload below -- don't call this class directly.
*/
final class FileUploadPOSTForm 
{
  	public $_sUploadName;
  	
  	final public function Save($psSavePath) 
  	{
    	if (move_uploaded_file($_FILES[$this->_sUploadName]['tmp_name'], $psSavePath)) 
    	{
      		return true;
    	}
    	
    	return false;
  	}
  
  	final public function getFileName() 
  	{
    	return $_FILES[$this->_sUploadName]['name'];
  	}
  
  	final public function getFileSize() 
  	{
    	return $_FILES[$this->_sUploadName]['size'];
  	}
}

/**
* Main class for handling file uploads
*/
class SimpleUploader
{
  	protected $_sUploadDir;                    // File upload directory (include trailing slash)
  	protected $_aAllowedExtensions;            // Array of permitted file extensions
  	protected $_nSizeLimit = 10485760;         // Max file upload size in bytes (default 10MB)
  	protected $_sNewFileName;                  // Optionally save uploaded files with a new name by setting this
  	protected $_sTitle;						// Original file name
  	protected $_sFileName;                    // Filename of the uploaded file
  	protected $_nFileSize;                    // Size of uploaded file in bytes
  	protected $_sFileExtension;               // File extension of uploaded file
  	protected $_sSavedFile;                   // Path to newly uploaded file (after upload completed)
  	protected $_sErrorMsg;                    // Error message if handleUpload() returns false (use getErrorMsg() to retrieve)
  	protected $_oHandler;

	/**
	 * Magic setter to save protected properties.
	 *
	 * @param string $psProperty
	 * @param mixed $pmValue
	 */
	public function __set ($psProperty, $pmValue)
	{
		$this->set($psProperty, $pmValue);
	}
	
	public function set ($psProperty, $pmValue)
	{
		if (property_exists($this, $psProperty))
		{
			$this->$psProperty = $pmValue;
		}
	}
	
	/**
	 * Magic getter to expose protected properties.
	 *
	 * @param string $psProperty
	 * @return mixed
	 */
	public function __get ($psProperty)
	{
		return $this->get($psProperty);
	}
	
	public function get ($psProperty)
	{
		if (property_exists($this, $psProperty))
		{
			return $this->$psProperty;
		}
	}
  	
  	public function uploader($psUploadName) 
  	{
    	if (isset($_FILES[$psUploadName])) 
    	{
      		$this->_oHandler = new FileUploadPOSTForm(); // Form-based upload
    	}
    	elseif (isset($_GET[$psUploadName])) 
    	{
      		$this->_oHandler = new FileUploadXHR(); // XHR upload
    	} 
    	else 
    	{
      		$this->_oHandler = false;
    	}

    	if ($this->_oHandler) 
    	{
      		$this->_oHandler->_sUploadName = $psUploadName;
      		$this->_sFileName = $this->_oHandler->getFileName();
      		$this->_nFileSize = $this->_oHandler->getFileSize();
      		
      		$laFileInfo = pathinfo($this->_sFileName);
      		
      		if (array_key_exists('extension', $laFileInfo)) 
      		{
        		$this->_sFileExtension = strtolower($laFileInfo['extension']);
      		}
    	}
  	}

  	public function getTitle()
  	{
  		return $this->_sTitle;
  	}
  	
  	public function getFileName() 
  	{
    	return $this->_sFileName;
  	}

  	public function getFileSize() 
  	{
    	return $this->_nFileSize;
  	}

  	public function getExtension() 
  	{
    	return $this->_sFileExtension;
  	}

  	public function getErrorMsg() 
  	{
    	return $this->_sErrorMsg;
  	}

  	public function getSavedFile() 
  	{
    	return $this->_sSavedFile;
  	}

  	private function checkExtension($psExt, $paAllowedExtensions) 
  	{
    	if (!is_array($paAllowedExtensions)) 
    	{
      		return false;
    	}
    	if (!in_array(strtolower($psExt), array_map('strtolower', $paAllowedExtensions))) 
    	{
      		return false;
    	}
    	
    	return true;
  	}

  	private function setErrorMsg($psMsg) 
  	{
    	$this->_sErrorMsg = $psMsg;
  	}

  	private function fixDir($psDir) 
  	{
    	$lsLast = substr($psDir, -1);
    	
    	if ($lsLast == '/' || $lsLast == '\\') 
    	{
      		$psDir = substr($psDir, 0, -1);
    	}
    	
    	return $psDir . DS;
  	}

  	public function handleUpload($psUploadDir = null, $paAllowedExtensions = null) 
  	{
    	if ($this->_oHandler === false) 
    	{
      		$this->setErrorMsg('Incorrect upload name or no file uploaded');
      		return false;
    	}

    	if (!empty($psUploadDir)) 
    	{
      		$this->_sUploadDir = $psUploadDir;
    	}
    	
    	if (is_array($paAllowedExtensions)) 
    	{
      		$this->_aAllowedExtensions = $paAllowedExtensions;
    	}

    	$this->_sUploadDir = $this->fixDir($this->_sUploadDir);

    	$this->_sTitle = strtolower($this->getFileName());
    	$laTitle = pathinfo($this->_sTitle);
    	$this->_sTitle = $laTitle['filename'];
    	$this->_sTitle = preg_replace(array('/\s\s+/', '/_/'), ' ', $this->_sTitle);
    	$this->_sTitle = ucfirst($this->_sTitle);
    	 
    	if (!empty($this->_sNewFileName)) 
    	{
      		$this->_sFileName = $this->_sNewFileName;
      		$this->_sSavedFile = $this->_sUploadDir.$this->_sNewFileName;
    	}
    	else 
    	{
      		$this->_sSavedFile = $this->_sUploadDir.$this->_sFileName;
    	}

    	if ($this->_nFileSize == 0) 
    	{
      		$this->setErrorMsg('File is empty');
      		return false;
    	}
    	
    	if (!is_writable($this->_sUploadDir)) 
    	{
      		$this->setErrorMsg('Upload directory is not writable');
      		return false;
    	}
    	
    	if ($this->_nFileSize > $this->_nSizeLimit) 
    	{
      		$this->setErrorMsg('File size exceeds limit (' . $this->_nFileSize . '>' . $this->_nSizeLimit .')');
      		return false;
    	}
    	
    	if (!empty($this->_aAllowedExtensions)) 
    	{
      		if (!$this->checkExtension($this->_sFileExtension, $this->_aAllowedExtensions)) 
      		{
        		$this->setErrorMsg('Invalid file type');
        		return false;
      		}
    	}
    	
    	if (!$this->_oHandler->Save($this->_sSavedFile)) 
    	{
      		$this->setErrorMsg('File could not be saved');
      		return false;
    	}

    	return true;
  	}
  	
  	
  	public function uploadToTemp($psVarName, $psValidExtensions = null, $psNewFileName = null)
  	{
  		$lsUploadDir = UPLOAD_APATH . DS . "temp";
  		$lsUploadUrl = UPLOAD_RPATH . "/temp/";
  		
  		$laValidExtensions = array('pdf','doc','docx','odt','xls','xlsx','ods','ppt','pptx','odp','txt','csv','xml','jpg','png','gif','mp3');
  		
  		if($psValidExtensions != null)
  		{
  			$laValidExtensions = explode(",",$psValidExtensions);
  		}	
  	
  		$this->uploader($psVarName);
  		
  		if($psNewFileName == null)
  		{
  			$lsFile = $this->getFileName();
  			$lsFile .= microtime();
  			$this->_sNewFileName = md5($lsFile) . "." .$this->getExtension();
  		}
  		else 
  		{
  			$this->_sNewFileName = $psNewFileName;
  		}
  	
  		$lbResult = $this->handleUpload($lsUploadDir, $laValidExtensions);
  	
  		if(!$lbResult)
  		{
  			echo json_encode(array('success' => false, 'msg' => $this->getErrorMsg()));
  		}
  		else
  		{
  			echo json_encode(array('success' => true, 'url' => $lsUploadUrl . $this->getFileName(), 'title' => $this->getTitle(), 'file' => $this->getFileName(), 'ext' => $this->getExtension()));
  		}
  	}
  	 
  	
  	public function moveFromTemp($psFile, $psBaseDir)
  	{
  		$lsOrigin = UPLOAD_APATH . DS . 'temp' . DS . $psFile;
  		
  		if(file_exists($lsOrigin))
  		{
  			$lsSavePath = NTG_SYS::createBalancedDir($psBaseDir, $psFile);
  			$lsSavePath .= DS . $psFile;
  		
  			if(file_put_contents($lsSavePath, fopen($lsOrigin, 'r')))
  			{
  				return true;
  			}
  		}
  		
  		return false;
  	}
  	
  	
  	public function removeFromTemp($psFile)
  	{
  		return $this->remove(UPLOAD_APATH . DS . 'temp' . DS . $psFile);	
  	}
  	
  	
  	public function remove($psFile)
  	{
  		if(file_exists($psFile))
  		{
  			NTG_Log::debug('rm ' . $psFile );
  			return NTG_SYS::remove_arq($psFile);
  		}
  		
  		return false;
  	}
  	
  	
  	public function progress()
  	{
  		if(isset($_REQUEST['progresskey']))
  		{
  			$laStatus = apc_fetch('upload_'.$_REQUEST['progresskey']);
  		}
  		else
  		{
  			exit(json_encode(array('success' => false)));
  		}
  	
  		$lnPct = 0;
  		$lnSize = 0;
  	
  		if(is_array($laStatus))
  		{
  			if(array_key_exists('total', $laStatus) && array_key_exists('current', $laStatus))
  			{
  				if($laStatus['total'] > 0)
  				{
  					$lnPct = round( ( $laStatus['current'] / $laStatus['total']) * 100 );
  					$lnSize = round($laStatus['total'] / 1024);
  				}
  			}
  		}
  	
  		echo json_encode(array('success' => true, 'pct' => $lnPct, 'size' => $lnSize));
  	}
}

/**
 * Implementação no add form
 */
/*
	<div id="uploadArea" class="form element" style="margin:30px; width:57%;">
		<div id="statusBox" style="margin-top:20px;"></div>
		<div id="actionBox" style="margin-top:20px;">
			<button id="btnFileUpload" class="btn btn-inverse btn-large"><i class="icon-upload icon-white"></i> Selecione um arquivo</button>
		</div>
	</div>
	
	<script type="text/javascript" src="/library/simpleUpload/SimpleAjaxUploader.min.js"></script>	
	<script type="text/javascript">
		var idPrefix = 'componentes-file-files-add-';
		var btn = document.getElementById(idPrefix + 'btnFileUpload'); 
		var statusBox = document.getElementById(idPrefix + 'statusBox'); // container for file size info
     	var actionBox = document.getElementById(idPrefix + 'actionBox'); // the element we're using for a progress bar
     	
		var uploader = new ss.SimpleUpload({
	      button: btn, // file upload button
	      url: 'backend/file/add', // server side handler
	      name: 'uploadFile', // upload parameter name        
	      progressUrl: 'backend/file/add?progress', // enables cross-browser progress support (more info below)
	      responseType: 'json',
	      multiple: false,
	      maxUploads: 1,
	      allowedExtensions: ['pdf','doc','docx','odt','xls','xlsx','ods','ppt','pptx','odp','txt','csv','xml','jpg','png','gif'],
	      maxSize: 5120, // kilobytes
	      hoverClass: 'ui-state-hover',
	      focusClass: 'ui-state-focus',
	      disabledClass: 'ui-state-disabled',

	      onExtError: function(filename, extension) {
	    	  statusBox.innerHTML = '<div class="alert alert-error"> <button type="button" class="close" data-dismiss="alert">&times;</button><h4>Erro!</h4>' + filename + ' não é um tipo de arquivo permitido.</div>';
		  },
		        
		  onSizeError: function(filename, fileSize) {
			  statusBox.innerHTML = '<div class="alert alert-error"> <button type="button" class="close" data-dismiss="alert">&times;</button><h4>Erro!</h4>' + filename + ' é muito grande. (5120kb é o tamanho máximo) </div>';
		  },
	      
	      onSubmit: function(filename, extension){
	    	// Create the elements of our progress bar
	          var progress = document.createElement('div'), // container for progress bar
	              bar = document.createElement('div'), // actual progress bar
	              fileSize = document.createElement('div'), // container for upload file size
	              wrapper = document.createElement('div'); // container for this progress bar
	          
	          // Assign each element its corresponding class
	          progress.className = 'progress progress-striped active';
	          bar.className = 'bar';            
	          fileSize.className = 'size';
	          wrapper.className = 'wrapper';
	          
	          // Assemble the progress bar and add it to the page
	          progress.appendChild(bar); 
	          wrapper.innerHTML = '<div class="name">'+filename+'</div>'; // filename is passed to onSubmit()
	          wrapper.appendChild(fileSize);
	          wrapper.appendChild(progress);                                       
	          actionBox.replaceChild(wrapper, btn); // just an element on the page to hold the progress bars    
	          
	          // Assign roles to the elements of the progress bar
	          this.setProgressBar(bar); // will serve as the actual progress bar
	          this.setFileSizeBox(fileSize); // display file size beside progress bar
	          this.setProgressContainer(wrapper); // designate the containing div to be removed after upload
	      },
	      
	      onComplete: function(filename, response){
	          if (!response) {
	        	  statusBox.innerHTML = '<div class="alert alert-error"> <button type="button" class="close" data-dismiss="alert">&times;</button><h4>Erro!</h4>Desculpe, ocorreu um erro ao tentar enviar o arquivo ' + filename + '. Tente novamente mais tarde.</div>';
	        	  actionBox.appendChild(btn);
	              return false;            
	          }
	          else if (response.success === false){
	        	  statusBox.innerHTML = '<div class="alert alert-error"> <button type="button" class="close" data-dismiss="alert">&times;</button><h4>Erro!</h4>' + filename + ' ' + response.msg + '</div>';
	        	  actionBox.appendChild(btn);
	              return false;
	          }	          
	          else if (response.success === true){
	        	  actionBox.innerHTML = '<a href=" ' + response.url + ' " target="_blank" class="btn btn-large btn-primary"><i class="icon-file icon-white"></i> ' + filename + '</a>';
	        	  actionBox.innerHTML += '<button onclick="btnAlterar();" class="btn btn-link btn-small"><i class="icon-repeat"></i> Alterar</button>';
	        	  statusBox.innerHTML = '<div class="alert alert-success"> <button type="button" class="close" data-dismiss="alert">&times;</button>O arquivo <span class="label label-success">' + filename + '</span> foi enviado com sucesso.</div>';

	        	  document.getElementById(idPrefix + 'stTitle').value = response.title;
	        	  document.getElementById(idPrefix + 'stPath').value = response.file;
	        	  document.getElementById(idPrefix + 'stType').value = response.ext;
	        	  document.getElementById(idPrefix + 'stVersion').value = '1';
			  }
	      }
		});

     	function btnAlterar()
     	{
     		statusBox.innerHTML = '';
			actionBox.innerHTML = '';
			actionBox.appendChild(btn);         	

	        document.getElementById(idPrefix + 'stTitle').value = '';
    	  	document.getElementById(idPrefix + 'stPath').value = '';
    	  	document.getElementById(idPrefix + 'stType').value = '';
    	  	document.getElementById(idPrefix + 'stVersion').value = '1';
		}
	</script>
*/
 
/**
 * Implementação no edit form
 */
/*
	<div id="uploadArea" class="form element" style="margin:30px; width:57%;">
		<div id="statusBox" style="margin-top:20px;"></div>
		<div id="actionBox" style="margin-top:20px;">
			<button id="btnFileUpload" class="btn btn-inverse btn-large" style="display:none"><i class="icon-upload icon-white"></i> Selecione um arquivo</button>
			<a href="<?php echo $gsUrl; ?>" target="_blank" class="btn btn-large btn-primary"><i class="icon-file icon-white"></i> <?php echo $gsFileName; ?></a>
	        <button onclick="btnAlterar();" class="btn btn-link btn-small"><i class="icon-repeat"></i> Alterar</button>
		</div>
	</div>
	
	<script type="text/javascript" src="/library/simpleUpload/SimpleAjaxUploader.min.js"></script>	
	<script type="text/javascript">
		var fileName = '<?php echo $gsFileName; ?>';
		var idPrefix = 'componentes-file-files-edit-id-<?php echo $gsId; ?>-';
		var btn = document.getElementById(idPrefix + 'btnFileUpload'); 
		var statusBox = document.getElementById(idPrefix + 'statusBox'); // container for file size info
     	var actionBox = document.getElementById(idPrefix + 'actionBox'); // the element we're using for a progress bar
     	
		var uploader = new ss.SimpleUpload({
	      button: btn, // file upload button
	      url: 'backend/file/edit', // server side handler
	      name: 'uploadFile', // upload parameter name        
	      progressUrl: 'backend/file/edit?progress', // enables cross-browser progress support (more info below)
	      responseType: 'json',
	      multiple: false,
	      maxUploads: 1,
	      allowedExtensions: ['pdf','doc','docx','odt','xls','xlsx','ods','ppt','pptx','odp','txt','csv','xml','jpg','png','gif'],
	      maxSize: 5120, // kilobytes
	      hoverClass: 'ui-state-hover',
	      focusClass: 'ui-state-focus',
	      disabledClass: 'ui-state-disabled',

	      onExtError: function(filename, extension) {
	    	  statusBox.innerHTML = '<div class="alert alert-error"> <button type="button" class="close" data-dismiss="alert">&times;</button><h4>Erro!</h4>' + filename + ' não é um tipo de arquivo permitido.</div>';
		  },
		        
		  onSizeError: function(filename, fileSize) {
			  statusBox.innerHTML = '<div class="alert alert-error"> <button type="button" class="close" data-dismiss="alert">&times;</button><h4>Erro!</h4>' + filename + ' é muito grande. (5120kb é o tamanho máximo) </div>';
		  },
	      
	      onSubmit: function(filename, extension){
	    	// Create the elements of our progress bar
	          var progress = document.createElement('div'), // container for progress bar
	              bar = document.createElement('div'), // actual progress bar
	              fileSize = document.createElement('div'), // container for upload file size
	              wrapper = document.createElement('div'); // container for this progress bar
	          
	          // Assign each element its corresponding class
	          progress.className = 'progress progress-striped active';
	          bar.className = 'bar';            
	          fileSize.className = 'size';
	          wrapper.className = 'wrapper';
	          
	          // Assemble the progress bar and add it to the page
	          progress.appendChild(bar); 
	          wrapper.innerHTML = '<div class="name">'+filename+'</div>'; // filename is passed to onSubmit()
	          wrapper.appendChild(fileSize);
	          wrapper.appendChild(progress);                                       
	          actionBox.replaceChild(wrapper, btn); // just an element on the page to hold the progress bars    
	          
	          // Assign roles to the elements of the progress bar
	          this.setProgressBar(bar); // will serve as the actual progress bar
	          this.setFileSizeBox(fileSize); // display file size beside progress bar
	          this.setProgressContainer(wrapper); // designate the containing div to be removed after upload
	      },
	      
	      onComplete: function(filename, response){
	          if (!response) {
	        	  statusBox.innerHTML = '<div class="alert alert-error"> <button type="button" class="close" data-dismiss="alert">&times;</button><h4>Erro!</h4>Desculpe, ocorreu um erro ao tentar enviar o arquivo ' + filename + '. Tente novamente mais tarde.</div>';
	        	  actionBox.appendChild(btn);
	              return false;            
	          }
	          else if (response.success === false){
	        	  statusBox.innerHTML = '<div class="alert alert-error"> <button type="button" class="close" data-dismiss="alert">&times;</button><h4>Erro!</h4>' + filename + ' ' + response.msg + '</div>';
	        	  actionBox.appendChild(btn);
	              return false;
	          }	          
	          else if (response.success === true){
	        	  actionBox.innerHTML = '<a href=" ' + response.url + ' " target="_blank" class="btn btn-large btn-primary"><i class="icon-file icon-white"></i> ' + filename + '</a>';
	        	  actionBox.innerHTML += '<button onclick="btnAlterar();" class="btn btn-link btn-small"><i class="icon-repeat"></i> Alterar</button>';
	        	  statusBox.innerHTML = '<div class="alert alert-success"> <button type="button" class="close" data-dismiss="alert">&times;</button>O arquivo <span class="label label-success">' + filename + '</span> foi enviado com sucesso.</div>';

	        	  document.getElementById(idPrefix + 'stTitle').value = response.title;
	        	  document.getElementById(idPrefix + 'stPath').value = response.file + '#CHANGED#' + fileName;
	        	  document.getElementById(idPrefix + 'stType').value = response.ext;
			  }
	      }
		});

     	function btnAlterar()
     	{
     		statusBox.innerHTML = '';
			actionBox.innerHTML = '';
			actionBox.appendChild(btn);
			btn.style = "display:block;";

    	  	document.getElementById(idPrefix + 'stPath').value = '';
    	  	document.getElementById(idPrefix + 'stType').value = '';
		}
	</script> 
 */