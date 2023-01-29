<?php
/**
 * Twiggy - Twig template engine implementation for CodeIgniter
 *
 * Twiggy is not just a simple implementation of Twig template engine 
 * for CodeIgniter. It supports themes, layouts, templates for regular
 * apps and also for apps that use HMVC (module support).
 *
 * Original Project https://github.com/edmundask/codeigniter-twiggy
 * But, some development reference come from https://github.com/kenjis/codeigniter-ss-twig
 * 
 * @package   			noraziz
 * @subpackage			ci4-twiggy
 * @category  			Libraries
 * @author    			Nor Aziz <tonoraziz@gmail.com>
 * @license   			http://www.opensource.org/licenses/MIT
 * @version   			0.1
 * @copyright 			Copyright (c) 2023 Nor Aziz <tonoraziz@gmail.com>
 */

namespace noraziz\ci4-twiggy;

use noraziz\ci4-twiggy\Config\Twiggy as TwiggyConfig

class Twiggy
{
	/**
     * The configuration instance.
     *
     * @var TwiggyConfig
     */
	protected $_config = array();
	
	
	/**
     * Private Vars.
     *
     */
	private $_template_locations = array();
	private $_data = array();
	private $_globals = array();
	private $_themes_base_dir;
	private $_theme;
	private $_layout;
	private $_template;
	private $_twig;
	private $_twig_loader;
	private $_module;
	private $_meta = array();
	private $_rendered = FALSE;


	/**
	* Constructor
	*/
    public function __construct(?TwiggyConfig $config = null)
    {
        // If no configuration was supplied then load one
        $this->_config = $config ?? config('Twiggy');
		
		// init working directory
		$this->_themes_base_dir = ($this->_config['include_apppath']) ? APPPATH . $this->_config['themes_base_dir'] : $this->_config['themes_base_dir'];
		$this->_set_template_locations($this->_config['default_theme']);
		
		try {
			$this->_twig_loader = new \Twig\Loader\FilesystemLoader($this->_template_locations);
		}
		catch(Twig_Error_Loader $e) {
			log_message('error', 'Twiggy: failed to load the default theme');
			show_error($e->getRawMessage());
		}
    }
	
	
	/**
	* Set template locations
	*
	* @access	private
	* @param 	string	name of theme to load
	* @return	void
	*/
	private function _set_template_locations($theme)
	{
		// Check if HMVC is installed.
		// NOTE: there may be a simplier way to check it but this seems good enough.
		if(method_exists($this->CI->router, 'fetch_module'))
		{
			$this->_module = $this->CI->router->fetch_module();

			// Only if the current page is served from a module do we need to add extra template locations.
			if(!empty($this->_module))
			{
				$module_locations = Modules::$locations;

				foreach($module_locations as $loc => $offset)
				{
					/* Only add the template location if the same exists, otherwise
					you'll need always a directory for your templates, even your module
					won't use templates */
					if ( is_dir($loc . $this->_module . '/' . $this->_config['themes_base_dir'] . $theme) )
						$this->_template_locations[] = $loc . $this->_module . '/' . $this->_config['themes_base_dir'] . $theme;
				}
			}
		}

		$this->_template_locations[] =  $this->_themes_base_dir . $theme;

		// Reset the paths if needed.
		if(is_object($this->_twig_loader))
		{
			$this->_twig_loader->setPaths($this->_template_locations);
		}
	}

    protected function reset()
    {
        $this->_twig = null;
        $this->createTwig();
    }

    protected function createTwig()
    {
        if ($this->_twig !== null) {
            return;
        }

        if ($this->_twig_loader === null) {
            $this->_twig_loader = new \Twig\Loader\FilesystemLoader($this->paths);
        }

        $twig = new \Twig\Environment($this->_twig_loader, $this->_config);

        if ($this->_config['debug']) {
            $twig->addExtension(new \Twig\Extension\DebugExtension());
        }

        $this->_twig = $twig;
    }
}
