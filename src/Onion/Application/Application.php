<?php
/**
 * This file is part of Onion
 *
 * Copyright (c) 2014-2016, Humberto Lourenço <betto@m3uzz.com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Humberto Lourenço nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   PHP
 * @package    Onion
 * @author     Humberto Lourenço <betto@m3uzz.com>
 * @copyright  2014-2016 Humberto Lourenço <betto@m3uzz.com>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link       http://github.com/m3uzz/onionfw
 */

namespace Onion\Application;
use Onion\Log;
use Onion\Lib\Util;
use Onion\Config\Config;
use Onion\File\System;
use Onion\I18n\Translator;
use Onion\Mvc;

class Application extends AbstractApp
{

	const ONION_VERSION = '1.0';

	const PHP_VERSION_REQUIRED = '5.3.10';

	/**
	 * Indicate if the object was initialized.
	 *
	 * @var boolean
	 */
	protected $_active = false;

	/**
	 * Define if the site will present the maintenance screen.
	 * default false
	 *
	 * @var boolean
	 */
	protected $_maintenance = false;

	/**
	 * Define de Environment of the application, if it is a production or
	 * development.
	 * If it is a development environment the debug mod as set true.
	 * @default 'production'
	 *
	 * @var string
	 */
	protected $_environment = 'production';

	/**
	 * Define if the application is enable to run
	 * @default false
	 *
	 * @var boolean
	 */
	protected $_enable = false;

	/**
	 * Define if the application instance is a clone of the original object
	 * In this case every change will not affect the original object
	 * @default false
	 *
	 * @var boolean
	 */
	protected $_clone = false;

	/**
	 * Define the client folder
	 *
	 * @example onion.com
	 * @var string
	 */
	protected $_resource = '';

	/**
	 * Application token id. It should be a hash
	 *
	 * @var string
	 */
	protected $_id = '';

	/**
	 * The project client name
	 *
	 * @var string
	 */
	protected $_name = '';

	/**
	 * The project title
	 *
	 * @var string
	 */
	protected $_title = '';

	/**
	 * The project description
	 *
	 * @var string
	 */
	protected $_description = '';

	/**
	 * An url to the application help
	 *
	 * @var string
	 */
	protected $_help = '';

	/**
	 * The application default icon
	 * @default icon-th
	 *
	 * @var string
	 */
	protected $_icon = 'icon-th';

	/**
	 * Define the project area keywords or categories
	 * 
	 * @var string
	 */
	protected $_keywords = '';

	/**
	 * Define the project start
	 * 
	 * @var string
	 */
	protected $_date = '';

	/**
	 * The access urls to the application
	 *  
	 * @var array
	 */
	protected $_url = array();

	/**
	 * The settins for system
	 * 
	 * @var array
	 */
	protected $_settings = array();

	/**
	 * The settins for use i18n translator
	 * 
	 * @var array
	 */
	protected $_translator = array();

	/**
	 * The settins for output
	 * 
	 * @var array
	 */
	protected $_output = array();

	/**
	 * The list of modules enables
	 * 
	 * @var array
	 */
	protected $_modules = array();

	/**
	 * The list of plugins enable
	 * 
	 * @var array
	 */
	protected $_plugins = array();

	/**
	 * The admin area settings
	 * 
	 * @var array
	 */
	protected $_admin = array();

	/**
	 * The frontend area settings
	 * 
	 * @var array
	 */
	protected $_front = array();

	/**
	 * The layout settings
	 * 
	 * @var array
	 */
	protected $_layout = array();

	/**
	 * A list of hooks available
	 * 
	 * @var array
	 */
	protected $_hooks = array();

	/**
	 * The settins for use cache
	 * 
	 * @var array
	 */
	protected $_cache = array();

	/**
	 * The settings for db access
	 * 
	 * @var array
	 */
	protected $_db = array();

	/**
	 * The settings for service access
	 * 
	 * @var array
	 */
	protected $_service = array();

	/**
	 * The settings for send mail
	 * 
	 * @var array
	 */
	protected $_mail = array();

	/**
	 * The settings for log and debug
	 * 
	 * @var array
	 */
	protected $_log = array();

	/**
	 * The modules status mods available
	 * 
	 * @var array
	 */
	protected $_status = array();

	/**
	 * The settings to the form exceptions
	 *  
	 * @var array
	 */
	protected $_form = array();

	/**
	 * The table default values
	 * 
	 * @var array
	 */
	protected $_table = array();

	/**
	 * The access level roles
	 * 
	 * @var array
	 */
	protected $_acl = null;
	
	/**
	 * 
	 * @var array
	 */
	protected $_menu = null;
	
	/**
	 * The complete settings
	 * 
	 * @var array
	 */
	protected $_onionConfig = array();

	/**
	 * The general zend settings
	 * 
	 * @var array
	 */
	protected $_generalConfig = array();

	/**
	 * Application msgs
	 * 
	 * @var array
	 */
	protected $_msgs = array();

	protected $_template = null;
	
	protected $_router = null;
	
	
	// Setando variáveis
	
	protected function __construct ()
	{}
	
	protected function __clone ()
	{}
	
	protected function __wakeup ()
	{}
	
	/**
	 * Return a Singleton instance of the object
	 *
	 * @return object Onion\Application\Application
	 */
	public static function getInstance ()
	{
		static $loInstance = null;
	
		if (null === $loInstance)
		{
			$loInstance = new Application();
		}
	
		return $loInstance;
	}
		
	/**
	 * Define if the site will present the maintenance screen.
	 *
	 * @param boolean $pbMaintenance        	
	 * @throws Exception
	 */
	public function setMaintenance ($pbMaintenance)
	{
		if (is_bool($pbMaintenance))
		{
			$this->_maintenance = $pbMaintenance;
		}
		else
		{
			throw new Exception('The value of "maintenance" property need to be a boolean!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param string $psResource        	
	 */
	public function setResource ($psResource)
	{
		if (! empty($psResource))
		{
			$this->_resource = $psResource;
			// insert cliente module dir into the beggining zend module listener path
			// it'll make every client module overwrite the default module in root module dir
			array_unshift($this->_generalConfig['module_listener_options']['module_paths'], "./client/" . $this->_resource . "/module");
		}
		else
		{
			throw new Exception('The value of "resource" property need to be a non empty string!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param string $psEnvironment        	
	 */
	public function setEnvironment ($psEnvironment)
	{
		$this->_environment = $psEnvironment;
		
		if ($this->_environment === "development")
		{
			error_reporting(E_ALL);
			ini_set("display_errors", 1);
			
			$this->_onionConfig['admin']['meta']['siteTitle'] = "[DEV] " . $this->_onionConfig['admin']['meta']['siteTitle'];
			$this->_onionConfig['front']['meta']['siteTitle'] = "[DEV] " . $this->_onionConfig['front']['meta']['siteTitle'];
			
			$this->_onionConfig['log']['debug']['JS'] = true;
			$this->_onionConfig['log']['debug']['PHP']['enable'] = true;
		}
		
		return $this;
	}

	/**
	 *
	 * @param string $psKeywords        	
	 */
	public function setKeywords ($psKeywords)
	{
		$this->_keywords = $psKeywords;
		
		return $this;
	}

	/**
	 *
	 * @param string $psDate        	
	 */
	public function setDate ($psDate)
	{
		$this->_date = $psDate;
		
		return $this;
	}

	/**
	 *
	 * @param array $paUrl        	
	 * @throws Exception
	 */
	public function setUrl ($paUrl)
	{
		if (is_array($paUrl))
		{
			$this->_url = $paUrl;
		}
		else
		{
			throw new Exception('The value of "url" property need to be an array!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param array $paSettings        	
	 * @throws Exception
	 */
	public function setSettings ($paSettings)
	{
		if (is_array($paSettings))
		{
			$this->_settings = $paSettings;
			
			defined('FILE_CHMOD') || define('FILE_CHMOD', $this->_settings['CHMOD']);
			defined('PUSH_MESSAGE') || define('PUSH_MESSAGE', $this->_settings['pushMensage']);

			Config::cacheConfig($this->_onionConfig);
		}
		else
		{
			throw new Exception('The value of "settings" property need to be an array!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param array $paTranslator        	
	 * @throws Exception
	 */
	public function setTranslator ($paTranslator)
	{
		if (is_array($paTranslator))
		{
			$this->_translator = $paTranslator;
			
			// Setting the application time zone
			date_default_timezone_set($this->_translator['datetime']);
		}
		else
		{
			throw new Exception('The value of "translator" property need to be an array!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param array $paOutput        	
	 * @throws Exception
	 */
	public function setOutput ($paOutput)
	{
		if (is_array($paOutput))
		{
			$this->_output = $paOutput;
		}
		else
		{
			throw new Exception('The value of "output" property need to be an array!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param array $paModules        	
	 * @throws Exception
	 */
	public function setModules ($paModules)
	{
		if (is_array($paModules))
		{
			$this->_modules = $paModules;

			if (is_array($paModules['available']))
			{
				foreach ($paModules['available'] as $lsMolude => $lbEnable)
				{
					if ($lbEnable)
					{
						$this->_generalConfig['modules'][] = $lsMolude;
					}
				}
			}
			
			if ($this->_environment == 'development')
			{
				$this->_generalConfig['modules'][] = 'ZendDeveloperTools';
			}
		}
		else
		{
			throw new Exception('The value of "modules" property need to be an array!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param array $paPlugins        	
	 * @throws Exception
	 */
	public function setPlugins ($paPlugins)
	{
		if (is_array($paPlugins))
		{
			$this->_plugins = $paPlugins;
		}
		else
		{
			throw new Exception('The value of "plugins" property need to be an array!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param array $paAdmin        	
	 * @throws Exception
	 */
	public function setAdmin ($paAdmin)
	{
		if (is_array($paAdmin))
		{
			$this->_admin = $paAdmin;
		}
		else
		{
			throw new Exception('The value of "admin" property need to be an array!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param array $paFront        	
	 * @throws Exception
	 */
	public function setFront ($paFront)
	{
		if (is_array($paFront))
		{
			$this->_front = $paFront;
		}
		else
		{
			throw new Exception('The value of "front" property need to be an array!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param array $paLayout        	
	 * @throws Exception
	 */
	public function setLayout ($paLayout)
	{
		if (is_array($paLayout))
		{
			$this->_layout = $paLayout;
			defined('DIRECT_ASSETS') || define('DIRECT_ASSETS', $this->_layout['directAssets']);
		}
		else
		{
			throw new Exception('The value of "layout" property need to be an array!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param array $paHooks        	
	 * @throws Exception
	 */
	public function setHooks ($paHooks)
	{
		if (is_array($paHooks))
		{
			$this->_hooks = $paHooks;
		}
		else
		{
			throw new Exception('The value of "hooks" property need to be an array!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param array $paCache        	
	 * @throws Exception
	 */
	public function setCache ($paCache)
	{
		if (is_array($paCache))
		{
			$this->_cache = $paCache;
			
			defined('CACHE_RPATH') || define('CACHE_RPATH', $this->_cache['cacheDir']);
		}
		else
		{
			throw new Exception('The value of "cache" property need to be an array!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param array $paDb        	
	 * @throws Exception
	 */
	public function setDb ($paDb)
	{
		if (is_array($paDb))
		{
			$this->_db = $paDb;
		}
		else
		{
			throw new Exception('The value of "db" property need to be an array!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param array $paService        	
	 * @throws Exception
	 */
	public function setService ($paService)
	{
		if (is_array($paService))
		{
			$this->_service = $paService;
		}
		else
		{
			throw new Exception('The value of "service" property need to be an array!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param array $paMail        	
	 * @throws Exception
	 */
	public function setMail ($paMail)
	{
		if (is_array($paMail))
		{
			$this->_mail = $paMail;
		}
		else
		{
			throw new Exception('The value of "mail" property need to be an array!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param array $paLog        	
	 * @throws Exception
	 */
	public function setLog ($paLog)
	{
		if (is_array($paLog))
		{
			$this->_log = $paLog;
		}
		else
		{
			throw new Exception('The value of "log" property need to be an array!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param array $paStatus        	
	 * @throws Exception
	 */
	public function setStatus ($paStatus)
	{
		if (is_array($paStatus))
		{
			$this->_status = $paStatus;
		}
		else
		{
			throw new Exception('The value of "status" property need to be an array!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param array $paForm        	
	 * @throws Exception
	 */
	public function setForm ($paForm)
	{
		if (is_array($paForm))
		{
			$this->_form = $paForm;
		}
		else
		{
			throw new Exception('The value of "form" property need to be an array!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param array $paTable        	
	 * @throws Exception
	 */
	public function setTable ($paTable)
	{
		if (is_array($paTable))
		{
			$this->_table = $paTable;
		}
		else
		{
			throw new Exception('The value of "table" property need to be an array!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param array $paAcl
	 * @throws Exception
	 */
	public function setAcl ($paAcl)
	{
		if (is_array($paAcl))
		{
			$this->_acl = $paAcl;
		}
		else
		{
			throw new Exception('The value of "acl" property need to be an array!');
		}
		
		return $this;
	}

	/**
	 *
	 * @param array $paMenu
	 * @throws Exception
	 */
	public function setMenu ($paMenu)
	{
		if (is_array($paMenu))
		{
			$this->_menu = $paMenu;
		}
		else
		{
			throw new Exception('The value of "menu" property need to be an array!');
		}
	
		return $this;
	}
		
	/**
	 * Init Onion Application:
	 * Test if PHP version is ok;
	 * Load the Onion default configs and Client configs to set the application
	 * object;
	 * Check if the application is available to run or it is in maintenance;
	 * Set the modules enable;
	 * Construct the Onion application map;
	 *
	 * @param array $paGeneralConfig        	
	 */
	public function init (array $paGeneralConfig = array())
	{
		$this->_generalConfig = $paGeneralConfig;
		
		$this->testPhpVersion();
		$this->setConfigs();
		
		// if not a php file, it is an external file and need to be routed
		if (! System::fileRoute($this->_front['meta']['pragma']))
		{
			$this->isEnable();
			$this->enableModules();
			
			/*
			 * Session::clearRegister('resource');
			 * Session::clearRegister('app');
			 * Session::clearRegister('moduleRegistered');
			 * //Session::clearRegister('acl'); Register::getModules();
			 * Register::getCompostModules();
			 * Integration::getIntegrateModules();
			 * Register::registreApplication();
			 * Register::replaceSectionIntegration();
			 * //Log\Debug::display(Session::getRegister('app')); die;
			 * Log\Debug::debug(Register::getResourcesList());
			 * Log\Debug::debug(Register::getApplicationArray($legacy
			 * = true));
			 */
			
			// Log\Debug::display($this->_generalConfig);
			// Log\Debug::display($this->_onionConfig);
			// die();
		}
		
		return $this;
	}

	/**
	 * Running the MVC Application.
	 */
	public function run ()
	{
		$loApp = Mvc\Application::init($this->_generalConfig)->run();
	}

	/**
	 * Return an object of a class name given
	 *  
	 * @param string $psClassName
	 * @param system $psNamespace
	 * @throws Exception
	 * @return object
	 */
	public static function factory ($psClassName, $psNamespace = null)
	{
		if ($psNamespace !== null)
		{
			$psClassName = $psNamespace . "\\" . $psClassName;
		}
		
		if (class_exists($psClassName))
		{
			return new $psClassName();
		} 
		else
		{
			throw new Exception ('Class "' . $psClassName . '" not found!');
		}
	}
	
	/**
	 * After load the object properties are setted.
	 *
	 * @param array $paConfigs        	
	 */
	public function setConfigs ()
	{
		$this->_onionConfig = Config::loadConfigs();
		$this->set($this->_onionConfig);
		$this->_active = true;
	}

	public function enableModules ()
	{}

	/**
	 * Verify if the application is available or in maintenance.
	 * If desable or under maintenance, the application is finished and a page
	 * exception is showed.
	 *
	 * @return boolean
	 */
	public function isEnable ()
	{
		if (! $this->get('enable'))
		{
	
			$this->_msgs = sprintf(
					Translator::i18n('%s is not available!'), 
					$this->get('title')
			);
			
			$lsType = 'NOT AVAILABLE - ';
			include 'layout' . DS . 'theme' . DS . 'exception' . DS . 'disable.phtml';
			die(0);
		}
		
		if ($this->get('maintenance'))
		{
			$this->_msgs = sprintf(
					Translator::i18n('%s is under maintenance. Please came back later.'), 
					$this->get('title')
			);
			
			$lsType = 'MAINTENANCE - ';
			include 'layout' . DS . 'theme' . DS . 'exception' . DS . 'disable.phtml';
			die(0);
		}
		
		return true;
	}

	/**
	 * Verify if the PHP version is right.
	 *
	 * @throws Exception
	 */
	public function testPhpVersion ()
	{
		// Checking the PHP version installed
		if (version_compare(phpversion(), self::PHP_VERSION_REQUIRED, '<') === true)
		{
			header('HTTP/1.1 500 Internal Server Error', true, 500);
			
			throw new Exception(
					sprintf(
							Translator::i18n('Your PHP version is %s. <br/> This application requere PHP %s or latest.'), 
							phpversion(), 
							self::PHP_VERSION_REQUIRED
					), 
					500
			);
		}
	}

	/**
	 * Return the Onion CMS version.
	 *
	 * @return string
	 */
	public function onionVersion ()
	{
		return self::ONION_VERSION;
	}
}