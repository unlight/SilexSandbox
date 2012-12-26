<?php

/**
 * Modified by S.
 */

/**
 * Module base class
 *
 * Provides basic functionality when extended by real modules.
 * 
 * @author Mark O'Sullivan <markm@vanillaforums.com>
 * @author Todd Burry <todd@vanillaforums.com> 
 * @copyright 2003 Vanilla Forums, Inc
 * @license http://www.opensource.org/licenses/gpl-2.0.php GPL
 * @package Garden
 * @since 2.0
 */

class Module {
// class Gdn_Module extends Gdn_Pluggable implements Gdn_IModule {

	/** The name of the current asset that is being rendered.
	 *
	 * @var string
	 */
	public $assetName = '';


	/**
	 * The name of the application folder that this module resides within.
	 *
	 * @var string
	 */
	protected $applicationFolder;
	
	/**
	 * Data that is passed into the view.
	 * 
	 * @var array
	 */
	public $data = array();


	/**
	 * The object that constructed this object. Typically this should be a
	 * Controller object.
	 *
	 * @var Gdn_Controller
	 */
	protected $sender;


	/**
	 * The name of the theme folder that the application is currently using.
	 *
	 * @var string
	 */
	protected $themeFolder;
	
	public $visible = TRUE;


	/**
	 * Class constructor
	 *
	 * @param object $sender
	 */
	public function __construct($sender = '', $applicationFolder = false) {
		// if (!$sender) $sender = Gdn::Controller();
		
		if (is_object($sender)) {
			$this->applicationFolder = $sender->ApplicationFolder;
			$this->themeFolder = $sender->Theme;
		} else {
			$this->applicationFolder = 'dashboard';
			// $this->themeFolder = Gdn::Config('Garden.Theme');
		}
		if ($applicationFolder !== false)
			$this->applicationFolder = $applicationFolder;
		
		if (is_object($sender))
			$this->sender = $sender;
			
		// parent::__construct();
	}


	/**
	 * Returns the name of the asset where this component should be rendered.
	 */
	public function AssetTarget() {
		trigger_error(ErrorMessage("Any class extended from the Module class must implement it's own AssetTarget method.", get_class($this), 'AssetTarget'), E_USER_ERROR);
	}
	
	public function Data($name = null, $default = '') {
		if ($name == null)
			$result = $this->Data;
		else
			$result = GetValueR($name, $this->Data, $default);
		return $result;
	}

	/**
	 * Returns the xhtml for this module as a fully parsed and rendered string.
	 *
	 * @return string
	 */
	public function FetchView() {
		$viewPath = $this->FetchViewLocation();
		$string = '';
		ob_start();
		if(is_object($this->sender) && isset($this->sender->Data)) {
			$data = $this->sender->Data;
		} else {
			$data = array();
		}
		include ($viewPath);
		$string = ob_get_contents();
		@ob_end_clean();
		return $string;
	}


	/**
	 * Returns the location of the view for this module in the filesystem.
	 *
	 * @param string $view
	 * @param string $applicationFolder
	 * @return array
	 */
	public function FetchViewLocation($view = '', $applicationFolder = '') {
		if ($view == '')
			$view = strtolower($this->Name());
			
		if (substr($view, -6) == 'module')
			$view = substr($view, 0, -6);
					
		if (substr($view, 0, 4) == 'gdn_')
			$view = substr($view, 4);

		if ($applicationFolder == '')
			$applicationFolder = strpos($this->applicationFolder, '/') ? $this->applicationFolder : strtolower($this->applicationFolder);

		$themeFolder = $this->themeFolder;
		
		$viewPath = null;
		
		// Try to use Gdn_Controller's FetchViewLocation
		if (Gdn::Controller() instanceof Gdn_Controller) {
			try {
				$viewPath = Gdn::Controller()->FetchViewLocation($view, 'modules', $applicationFolder);
			} catch (Exception $ex) {}
		}
		
		if (!$viewPath) {
			
			$viewPaths = array();
			// 1. An explicitly defined path to a view
			if (strpos($view, '/') !== false)
				$viewPaths[] = $view;

			// 2. A theme
			if ($themeFolder != '') {
				// a. Application-specific theme view. eg. /path/to/application/themes/theme_name/app_name/views/modules/
				$viewPaths[] = CombinePaths(array(PATH_THEMES, $themeFolder, $applicationFolder, 'views', 'modules', $view . '.php'));
				
				// b. Garden-wide theme view. eg. /path/to/application/themes/theme_name/views/modules/
				$viewPaths[] = CombinePaths(array(PATH_THEMES, $themeFolder, 'views', 'modules', $view . '.php'));
			}

			// 3. Application default. eg. /path/to/application/app_name/views/controller_name/
			if ($this->applicationFolder)
				$viewPaths[] = CombinePaths(array(PATH_APPLICATIONS, $applicationFolder, 'views', 'modules', $view . '.php'));
			else
				$viewPaths[] = dirname($this->Path())."/../views/modules/$view.php";

			// 4. Garden default. eg. /path/to/application/dashboard/views/modules/
			$viewPaths[] = CombinePaths(array(PATH_APPLICATIONS, 'dashboard', 'views', 'modules', $view . '.php'));

			$viewPath = Gdn_FileSystem::Exists($viewPaths);
		}
		
		if ($viewPath === false)
			throw new Exception(ErrorMessage('Could not find a `' . $view . '` view for the `' . $this->Name() . '` module in the `' . $applicationFolder . '` application.', get_class($this), 'FetchView'), E_USER_ERROR);

		return $viewPath;
	}


	/**
	 * Returns the name of this module. Unless it is overridden, it will simply
	 * return the class name.
	 *
	 * @return string
	 */
	public function Name() {
		return get_class($this);
	}

	public function Path($newValue = false) {
		static $path = false;
		if ($newValue !== false)
			$path = $newValue;
		elseif ($path === false) {
			$ro = new ReflectionObject($this);
			$path = $ro->getFileName();
		}
		return $path;
	}
	
	public function Render() {
		echo $this->ToString();
	}
	
	public function SetData($name, $value) {
		$this->Data[$name] = $value;
	}

	/**
	 * Returns the component as a string to be rendered to the screen. Unless
	 * this method is overridden, it will attempt to find and return a view
	 * related to this module automatically.
	 *
	 * @return string
	 */
	public function ToString() {
		if ($this->Visible)
			return $this->FetchView();
	}

	/**
	 * Magic method for type casting to string.
	 *
	 * @todo check if you want to keep this.
	 * @return string
	 */
	public function __toString() {
		return $this->ToString();
	}
}