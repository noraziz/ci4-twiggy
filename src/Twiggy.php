<?php

namespace noraziz\ci4twiggy;

/**
 * Twiggy - Twig template engine implementation for CodeIgniter
 *
 * Twiggy is not just a simple implementation of Twig template engine 
 * for CodeIgniter. It supports themes, layouts, templates for regular
 * apps and also for apps that use HMVC (module support).
 *
 * Original Project https://github.com/edmundask/codeigniter-twiggy
 * But, some development reference come from https://github.com/kenjis/codeigniter-ss-twig & https://github.com/raizdev/twig-codeigniter4.
 * 
 * @package   			noraziz
 * @subpackage			ci4-twiggy
 * @category  			Libraries
 * @author    			Nor Aziz <tonoraziz@gmail.com>
 * @license   			http://www.opensource.org/licenses/MIT
 * @version   			0.2
 * @copyright 			Copyright (c) 2023 Nor Aziz <tonoraziz@gmail.com>
 */

use noraziz\ci4twiggy\Config\Twiggy as TwiggyConfig;

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
	private $_functions_added= false;
	private $_template_locations = array();
	private $_data = array();
	private $_globals = array();
	private $_themes_base_dir;
	private $_theme;
	private $_layout;
	private $_template;
	private $_twig;
	private $_twig_loader;
	private $_module_name;
	private $_module_list = array();
	private $_meta = array();
	private $_rendered = FALSE;


	/**
	* Constructor
	*
	*/
    public function __construct(?TwiggyConfig $config = null)
    {
        // If no configuration was supplied then load one
        $this->_config = $config ?? config('Twiggy');
		
		// fetch modules path from autoload > psr4.
		$tmp_cnf= config('Autoload');
		$this->_module_list= $tmp_cnf->psr4;
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
		// Check module name.
		// NOTE: default module name is "app". Some additional modules may be added by editing 'Autoload' config file.
		if(!empty($this->_module_name)) {
			foreach($this->_module_list as $m_idx => $m_loc) {
				/* Only add the template location if the same exists, otherwise
				you'll need always a directory for your templates, even your module
				won't use templates */
				if ( is_dir($m_loc . $this->_module_name . '/' . $this->_config->themes_base_dir . $theme) )
					$this->_template_locations[] = $m_loc . $this->_module_name . '/' . $this->_config->themes_base_dir . $theme;
			}
		}
		
		// default theme location.
		$this->_template_locations[] = $this->_themes_base_dir . $theme;

		// Reset the paths if needed.
		if(is_object($this->_twig_loader)) {
			$this->_twig_loader->setPaths($this->_template_locations);
		}
	}
	
	/**
	 * Compile meta data into pure HTML
	 * 
	 * @access	private
	 * @return	string	HTML
	 */
	private function _compile_metadata()
	{
		$html = '';

		foreach($this->_meta as $meta) {
			$html .= $this->_meta_to_html($meta);
		}

		return $html;
	}

	/**
	 * Convert meta tag array to HTML code
	 * 
	 * @access	private
	 * @param 	array 	meta tag
	 * @return	string	HTML code
	 */
	private function _meta_to_html($meta)
	{
		return "<meta " . $meta['attribute'] . "=\"" . $meta['name'] . "\" content=\"" . $meta['value'] . "\">\n";
	}

	/**
	 * Load template and return output object
	 * 
	 * @access	private
	 * @return	object	output
	 */
	private function _load()
	{
		$this->set('meta', $this->_compile_metadata(), TRUE);
		$this->_rendered = TRUE;

		return $this->_twig->loadTemplate($this->_template . $this->_config->template_file_ext);
	}
	
	/**
	* Set module name.
	*
	* @access	private
	* @param 	string	class_name or namespace.
	* @return	void
	*/
	private function _set_sender($className=null)
	{
		if ($className === null)
			return;
		
		$tmp_cls= explode('\\', $className);
		
		if ( count($tmp_cls)>0 ) {
			if ( array_key_exists($tmp_cls[0], $this->_module_list) ) {
				$this->_module_name= $tmp_cls[0];
			}
		}
	}

	/**
	* Reset twig engine.
	*
	* @access	protected
	* @param 	void
	* @return	void
	*/
    protected function reset()
    {
        $this->_twig = null;
        $this->init();
    }

	/**
	* Initialize twig engine.
	*
	* @access	public
	* @param 	string	class_name or namespace, recommended value = '__CLASS__'.
	* @return	void
	*/
    public function init($cls_sender = null)
    {
        if ($this->_twig !== null)
            return;
		
		if ($cls_sender !== null)
			$this->_set_sender($cls_sender);
		
		// init working directory
		$this->_themes_base_dir = ($this->_config->include_apppath) ? APPPATH . $this->_config->themes_base_dir : $this->_config->themes_base_dir;
		$this->_set_template_locations($this->_config->default_theme);
		
		try {
			$this->_twig_loader = new \Twig\Loader\FilesystemLoader($this->_template_locations);
		}
		catch(Twig_Error_Loader $e) {
			log_message('error', 'Twiggy: failed to load the default theme');
			show_error($e->getRawMessage());
		}
		
        $this->_twig = new \Twig\Environment($this->_twig_loader, $this->_config->environment);
		// @future: $this->_twig->setLexer(new \Twig_Lexer($this->_twig, $this->_config->delimiters));

        if ($this->_config->debug) {
			$this->_twig->addExtension(new \Twig\Extension\DebugExtension());
        }

		// Initialize defaults
		$this->theme($this->_config->default_theme)
			 ->layout($this->_config->default_layout)
			 ->template($this->_config->default_template);

		// Auto-register functions
		if ($this->_functions_added)
            return;
		
		if(count($this->_config->list_functions_asis) > 0) {
			foreach($this->_config->list_functions_asis as $i_fn)
				$this->register_function_asis($i_fn);
		}
		
		if(count($this->_config->list_functions_safe) > 0) {
			foreach($this->_config->list_functions_safe as $i_fn) 
				$this->register_function_safe($i_fn);
		}

		// Default additional functions:
        if( function_exists( 'anchor' ) ) {
            $this->_twig->addFunction( new \Twig\TwigFunction( 'anchor', [ $this, 'safe_anchor' ], [ 'is_safe' => [ 'html' ] ] ) );
        }
		$this->_twig->addFunction( new \Twig\Twigfunction( 'config', [$this, 'getConfig']));
        $this->_twig->addFunction( new \Twig\Twigfunction( 'lang', [$this, 'getLang']));
        $this->_twig->addFunction( new \Twig\TwigFunction( 'validation_list_errors', [ $this, 'validation_list_errors' ], ['is_safe' => [ 'html' ] ] ) );
		
		$this->_functions_added = true;
		
		
		// Auto-register filters
		if(count($this->_config->list_filters) > 0) {
			foreach($this->_config->list_filters as $i_filter) $this->register_filter($i_filter);
		}
		

		$this->_globals['title'] = NULL;
		$this->_globals['meta'] = NULL;
    }
	
	/**
	 * Set data
	 * 
	 * @access	public
	 * @param 	mixed  	key (variable name) or an array of variable names with values
	 * @param 	mixed  	data
	 * @param 	boolean	(optional) is this a global variable?
	 * @return	object 	instance of this class
	 */
	public function set($key, $value = NULL, $global = FALSE)
	{
		if(is_array($key)) {
			foreach($key as $k => $v) $this->set($k, $v, $global);
		}
		else {
			if($global) {
				$this->_twig->addGlobal($key, $value);
				$this->_globals[$key] = $value;
			}
			else {
			 	$this->_data[$key] = $value;
			}	
		}

		return $this;
	}

	/**
	 * Unset a particular variable
	 * 
	 * @access	public
	 * @param 	mixed  	key (variable name)
	 * @return	object 	instance of this class
	 */
	public function unset_data($key)
	{
		if(array_key_exists($key, $this->_data)) unset($this->_data[$key]);

		return $this;
	}

	/**
	 * Set title
	 * 
	 * @access	public
	 * @param 	string	
	 * @return	object 	instance of this class
	 */
	public function title()
	{
		if(func_num_args() > 0) {
			$args = func_get_args();

			// If at least one parameter is passed in to this method, 
			// call append() to either set the title or append additional
			// string data to it.
			call_user_func_array(array($this, 'append'), $args);
		}

		return $this;
	}

	/**
	 * Append string to the title
	 * 
	 * @access	public
	 * @param 	string	
	 * @return	object 	instance of this class
	 */
	public function append()
	{
		$args = func_get_args();
		$title = implode($this->_config->title_separator, $args);

		if(empty($this->_globals['title'])) {
			$this->set('title', $title, TRUE);
		}
		else {
			$this->set('title', $this->_globals['title'] . $this->_config->title_separator . $title, TRUE);
		}

		return $this;
	}

	/**
	 * Prepend string to the title
	 * 
	 * @access	public
	 * @param 	string	
	 * @return	object 	instance of this class
	 */
	public function prepend()
	{
		$args = func_get_args();
		$title = implode($this->_config->title_separator, $args);

		if(empty($this->_globals['title'])) {
			$this->set('title', $title, TRUE);
		}
		else {
			$this->set('title', $title . $this->_config->title_separator . $this->_globals['title'], TRUE);
		}

		return $this;
	}

	/**
	 * Set title separator
	 * 
	 * @access	public
	 * @param 	string	separator
	 * @return	object 	instance of this class
	 */

	public function set_title_separator($separator = ' | ')
	{
		$this->_config->title_separator = $separator;

		return $this;
	}

	/**
	 * Set meta data
	 * 
	 * @access	public
	 * @param 	string	name
	 * @param	string	value
	 * @param	string	(optional) name of the meta tag attribute
	 * @return	object 	instance of this class
	 */
	public function meta($name, $value, $attribute = 'name')
	{
		$this->_meta[$name] = array('name' => $name, 'value' => $value, 'attribute' => $attribute);

		return $this;
	}

	/**
	 * Unset meta data
	 * 
	 * @access	public
	 * @param 	string	(optional) name of the meta tag
	 * @return	object	instance of this class
	 */
	public function unset_meta()
	{
		if(func_num_args() > 0) {
			$args = func_get_args();

			foreach($args as $arg) {
				if(array_key_exists($arg, $this->_meta)) unset($this->_meta[$arg]);
			}
		}
		else {
			$this->_meta = array();
		}

		return $this;
	}

	/**
	 * Register a function As-Is in Twig environment
	 * 
	 * @access	public
	 * @param 	string	the name of an existing function
	 * @return	object	instance of this class
	 */
	public function register_function_asis($fn_name)
	{
		if ( function_exists( $fn_name ) )
			$this->_twig->addFunction(
				new \Twig\TwigFunction(
					$fn_name,
					$fn_name
				)
			);
		
		return $this;
	}
	
	/**
	 * Register a function SAFE in Twig environment
	 * 
	 * @access	public
	 * @param 	string	the name of an existing function
	 * @return	object	instance of this class
	 */
	public function register_function_safe($fn_name)
	{
		if ( function_exists( $fn_name ) )
			$this->_twig->addFunction(
				new \Twig\TwigFunction(
					$fn_name,
					$fn_name,
					[ 'is_safe' => [ 'html' ] ]
				)
			);
		
		return $this;
	}

	/**
	 * Register a filter in Twig environment
	 * 
	 * @access	public
	 * @param 	string	the name of an existing function
	 * @return	object	instance of this class
	 */
	public function register_filter($flt_name)
	{
		//$this->_twig->addFilter($name, new Twig_Filter_Function($name));
		$this->_twig->addFilter(
			new \Twig\TwigFilter(
				$flt_name,
				$flt_name
			)
		);

		return $this;
	}

	/**
	* Load theme
	*
	* @access	public
	* @param 	string	name of theme to load
	* @return	object	instance of this class
	*/       	
	public function theme($theme)
	{
		if(!is_dir(realpath($this->_themes_base_dir. $theme))) {
			log_message('error', 'Twiggy: requested theme '. $theme .' has not been loaded because it does not exist.');
			show_error("Theme does not exist in {$this->_themes_base_dir}{$theme}.");
		}

		$this->_theme = $theme;
		$this->_set_template_locations($theme);

		return $this;
	}

	/**
	 * Set layout
	 * 
	 * @access	public
	 * @param 	string	name of the layout
	 * @return	object	instance of this class
	 */
	public function layout($name)
	{
		$this->_layout = $name;
		$this->_twig->addGlobal('_layout', '_layouts/'. $this->_layout . $this->_config->template_file_ext);

		return $this;
	}

	/**
	 * Set template
	 * 
	 * @access	public
	 * @param 	string	name of the template file
	 * @return	object	instance of this class
	 */
	public function template($name)
	{
		$this->_template = $name;

		return $this;
	}

	/**
	 * Render and return compiled HTML
	 * 
	 * @access	public
	 * @param 	string	(optional) template file
	 * @return	string	compiled HTML
	 */
	public function render($template = '')
	{
		if(!empty($template)) $this->template($template);

		try {
			//return $this->_load()->render($this->_data);
			return $this->_twig->render($this->_template . $this->_config->template_file_ext, $this->_data);
		}
		catch(Twig_Error_Loader $e) {
			show_error($e->getRawMessage());
		}
	}

	/**
	 * Display the compiled HTML content
	 *
	 * @access	public
	 * @param 	string	(optional) template file
	 * @return	void
	 */
	public function display($template = '')
	{
		if(!empty($template)) $this->template($template);

		try {
			//$this->_load()->display($this->_data);
			echo $this->_twig->render($this->_template . $this->_config->template_file_ext, $this->_data);
		}
		catch(Twig_Error_Loader $e) {
			show_error($e->getRawMessage());
		}
	}

	/**
	* Get current theme
	*
	* @access	public
	* @return	string	name of the currently loaded theme
	*/
	public function get_theme()
	{
		return $this->_theme;
	}

	/**
	* Get current layout
	*
	* @access	public
	* @return	string	name of the currently used layout
	*/
	public function get_layout()
	{
		return $this->_layout;
	}

	/**
	* Get template
	*
	* @access	public
	* @return	string	name of the loaded template file (without the extension)
	*/
	public function get_template()
	{
		return $this->_template;
	}

	/**
	* Get metadata
	*
	* @access	public
	* @param 	string 	(optional) name of the meta tag
	* @param 	boolean	whether to compile to html
	* @return	mixed  	array of tag(s), string (HTML) or FALSE
	*/
	public function get_meta($name = '', $compile = FALSE)
	{
		if(empty($name)) {
			return ($compile) ? $this->_compile_metadata() : $this->_meta;
		}
		else {
			if(array_key_exists($name, $this->_meta)) {
				return ($compile) ? $this->_meta_to_html($this->_meta[$name]) : $this->_meta[$name];
			}

			return FALSE;
		}
	}

	/**
	* Check if template is already rendered
	*
	* @access	public
	* @return	boolean
	*/
	public function rendered()
	{
		return $this->_rendered;
	}

	/**
	* Magic method __get()
	*/
	public function __get($variable)
	{
		if($variable == 'twig') return $this->_twig;

		if(array_key_exists($variable, $this->_globals)) {
			return $this->_globals[$variable];
		}
		elseif(array_key_exists($variable, $this->_data)) {
			return $this->_data[$variable];
		}

		return FALSE;
	}
	
	
	
	/* ------------------------------------------------------------------------
	 * To be injected to twig.
	 */
	
	public function getConfig($var) 
    {
        return service('config')->get($var);
    }

    public function getLang($var) 
    {
	    return lang($var);
    }
	
	/**
     * @param string $uri
     * @param string $title
     * @param array $attributes only array is acceptable
     * @return string
     */
    public function safe_anchor(
        $uri = '',
        $title = '',
        $attributes = []
    ): string {
        $uri = esc($uri, 'url');
        $title = esc($title);

        $new_attr = [];
        foreach ($attributes as $key => $val) {
            $new_attr[esc($key)] = $val;
        }

        return anchor($uri, $title, $new_attr);
    }

    public function validation_list_errors(): string
    {
        return \Config\Services::validation()->listErrors();
    }
	/* ------------------------------------------------------------------------ */
}
