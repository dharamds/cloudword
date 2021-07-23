<?php 
set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
include(APPPATH.'third_party/phpseclib/Net/SSH2.php');
include(APPPATH.'third_party/phpseclib/Net/SFTP.php');
class Ftpbackup{
	public $hostname = '';

	/**
	 * FTP Username
	 *
	 * @var	string
	 */
	public $username = '';

	/**
	 * FTP Password
	 *
	 * @var	string
	 */
	public $password = '';

	public $port = 21;

	/**
	 * Passive mode flag
	 *
	 * @var	bool
	 */
	public $passive = TRUE;

	/**
	 * Debug flag
	 *
	 * Specifies whether to display error messages.
	 *
	 * @var	bool
	 */
	public $debug = FALSE;

	protected $sftp; 
    public function __construct($config = array())
	{
		empty($config) OR $this->initialize($config);
		log_message('info', 'FTP Class Initialized');
	}


    public function initialize($config = array())
	{
		foreach ($config as $key => $val)
		{
			if (isset($this->$key))
			{
				$this->$key = $val;
			}
		}
		$this->sftp = new Net_SFTP($this->hostname);
		$this->sftp->login($this->username, $this->password);
		// Prep the hostname
		$this->hostname = preg_replace('|.+?://|', '', $this->hostname);
	}

    public function connect($config = array()){
    	if(count($config) > 0)
		{
			$this->initialize($config);
		}

    	$this->sftp = new Net_SFTP($this->hostname);
    	if($this->sftp->login($this->username, $this->password)){
			return TRUE;
		}else{
			return FALSE;
		}
    } 
	protected function checkconn()
	{
		if(!is_resource($this->sftp))
		{
			return FALSE;
		}
		return TRUE;
	}

    public function list_files($path = ''){

		 return $this->sftp->nlist($path);
    }
    public function raw_files($path = ''){
    	
		 return $this->sftp->rawlist($path);
    }
    public function download($remotepath,$localpath){

    	return $this->sftp->get($remotepath, $localpath);
    } 
    public function mirror($locpath, $rempath)
	{
		
		if ($fp = opendir($locpath))
		{
			// Attempt to open the remote file path and try to create it, if it doesn't exist
			if ( ! $this->changedir($rempath, TRUE) && ( ! $this->mkdir($rempath) OR ! $this->changedir($rempath)))
			{
				return FALSE;
			}

			// Recursively read the local directory
			while (FALSE !== ($file = readdir($fp)))
			{
				if (is_dir($locpath.$file) && $file[0] !== '.')
				{
					$this->mirror($locpath.$file.'/', $rempath.$file.'/');
				}
				else if ($file[0] !== '.')
				{
					// Get the file extension so we can se the upload type
					$ext = $this->_getext($file);
					$mode = $this->_settype($ext);
					$this->upload($locpath.$file, $rempath.$file, $mode);
				}
			}

			return TRUE;
		}
		return FALSE;
	}

	public function changedir($path, $suppress_debug = FALSE)
	{
		$result = $this->sftp->chdir($path);
		if ($result === FALSE)
		{
			if ($this->debug === TRUE && $suppress_debug === FALSE)
			{
				$this->_error('ftp_unable_to_changedir');
			}

			return FALSE;
		}
		return TRUE;
	}
	public function mkdir($path, $permissions = NULL)
	{
		if ($path === '')
		{
			return FALSE;
		}

		$result = $this->sftp->mkdir($path);

		if ($result === FALSE)
		{
			if ($this->debug === TRUE)
			{
				$this->_error('ftp_unable_to_mkdir');
			}

			return FALSE;
		}

		// Set file permissions if needed
		if ($permissions !== NULL)
		{
			$this->chmod($path, (int) $permissions);
		}

		return TRUE;
	}
	public function chmod($path, $perm)
	{
		if ( $this->sftp->chmod($perm, $path) === FALSE)
		{
			if ($this->debug === TRUE)
			{
				$this->_error('ftp_unable_to_chmod');
			}

			return FALSE;
		}
		return TRUE;
	}

	protected function _getext($filename)
	{
		return (($dot = strrpos($filename, '.')) === FALSE)
			? 'txt'
			: substr($filename, $dot + 1);
	}

	protected function _settype($ext)
	{ 
		return in_array($ext, array('txt', 'text', 'php', 'phps', 'php4', 'js', 'css', 'htm', 'html', 'phtml', 'shtml', 'log', 'xml', 'zip'), TRUE)
			? 'ascii'
			: 'binary';
	}


	public function upload($locpath, $rempath, $mode = 'auto', $permissions = NULL)
	{
		if ( ! file_exists($locpath))
		{
			$this->_error('ftp_no_source_file');
			return FALSE;
		}

		$result = $this->sftp->put($rempath, $locpath, NET_SFTP_LOCAL_FILE);
		
		if ($result === FALSE)
		{
			if ($this->debug === TRUE)
			{
				$this->_error('ftp_unable_to_upload');
			}

			return FALSE;
		}

		// Set file permissions if needed
		if ($permissions !== NULL)
		{
			$this->chmod($rempath, (int) $permissions);
		}

		return TRUE;
	}
	protected function _error($line)
	{
		$CI =& get_instance();
		$CI->lang->load('ftp');
		show_error($CI->lang->line($line));
	}

}

