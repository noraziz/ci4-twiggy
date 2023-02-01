<?php

namespace noraziz\ci4twiggy\Config;

/**
 * Twiggy - Twig template engine implementation for CodeIgniter 4
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
 * @version   			0.1
 * @copyright 			Copyright (c) 2023 Nor Aziz <tonoraziz@gmail.com>
 */

use CodeIgniter\Config\BaseConfig;

class Twiggy extends BaseConfig
{
	/*
	|--------------------------------------------------------------------------
	| Debug Mode
	|--------------------------------------------------------------------------
	|
	| Activate debug mode when codeigniter is in development mode.
	|
	*/
	public $debug = ENVIRONMENT !== 'production';
	
	
	/*
	|--------------------------------------------------------------------------
	| Template file extension
	|--------------------------------------------------------------------------
	|
	| This lets you define the extension for template files. It doesn't affect
	| how Twiggy deals with templates but this may help you if you want to
	| distinguish different kinds of templates. For example, for CodeIgniter
	| you may use *.html.twig template files and *.html.jst for js templates.
	|
	*/
	public $template_file_ext = '.html.twig';


	/*
	|--------------------------------------------------------------------------
	| Syntax Delimiters
	|--------------------------------------------------------------------------
	|
	| If you don't like the default Twig syntax delimiters or if they collide 
	| with other languages (for example, you use handlebars.js in your
	| templates), here you can change them.
	|
	| Ruby erb style:
	|
	|	'tag_comment' 	=> array('<%#', '#%>'),
	|	'tag_block'   	=> array('<%', '%>'),
	|	'tag_variable'	=> array('<%=', '%>')
	|
	| Smarty style:
	|
	|    'tag_comment' 	=> array('{*', '*}'),
	|    'tag_block'   	=> array('{', '}'),
	|    'tag_variable'	=> array('{$', '}'),
	|
	*/


	public $delimiters =
	[
		'tag_comment' 	=> array('{#', '#}'),
		'tag_block'   	=> array('{%', '%}'),
		'tag_variable'	=> array('{{', '}}')
	];


	/*
	|--------------------------------------------------------------------------
	| Environment Options
	|--------------------------------------------------------------------------
	|
	| These are all twig-specific options that you can set. To learn more about
	| each option, check the official documentation.
	|
	| NOTE: cache option works slightly differently than in Twig. In Twig you
	| can either set the value to FALSE to disable caching, or set the path
	| to where the cached files should be stored (which means caching would be
	| enabled in that case). This is not entirely convenient if you need to 
	| switch between enabled or disabled caching for debugging or other reasons.
	|
	| Therefore, here the value can be either TRUE or FALSE. Cache directory
	| can be set separately.
	|
	*/
	public $environment = [
		'cache'              	=> FALSE,
		'debug'              	=> FALSE,
		'charset'            	=> 'utf-8',
		'base_template_class'	=> 'Twig_Template',
		'auto_reload'        	=> NULL,
		'strict_variables'   	=> FALSE,
		'autoescape'         	=> FALSE,
		'optimizations'      	=> -1
	];


	/*
	|--------------------------------------------------------------------------
	| Twig Cache Dir
	|--------------------------------------------------------------------------
	|
	| Path to the cache folder for compiled twig templates. It is relative to
	| CodeIgniter's base directory.
	|
	*/
	public $twig_cache_dir = APPPATH . 'cache/twig/';


	/*
	|--------------------------------------------------------------------------
	| Themes Base Dir
	|--------------------------------------------------------------------------
	|
	| Directory where themes are located at. This path is relative to 
	| CodeIgniter's base directory OR module's base directory. For example:
	|
	| $config['themes_base_dir'] = 'themes/';
	|
	| It will actually mean that themes should be placed at:
	|
	| {APPPATH}/themes/ and {APPPATH}/modules/{some_module}/themes/.
	|
	| NOTE: modules do not necessarily need to be in {APPPATH}/modules/ as
	| Twiggy will figure out the paths by itself. That way you can package 
	| your modules with themes.
	|
	| Also, do not forget the trailing slash!
	|
	*/
	public $themes_base_dir = 'Themes/';


	/*
	|--------------------------------------------------------------------------
	| Include APPPATH
	|--------------------------------------------------------------------------
	|
	| This lets you include the APPPATH for the themes base directory (only for
	| the application itself, not the modules). See the example below.
	|
	| Suppose you have:
	| $config['themes_base_dir'] = 'themes/'
	| $config['include_apppath'] = TRUE
	|
	| Then the path will be {APPPATH}/themes/ but if you set this option to
	| FALSE, then you will have themes/.
	|
	| This is useful for when you want to have the themes folder outside the
	| application (APPPATH) folder.
	|
	*/
	public $include_apppath = TRUE;


	/*
	|--------------------------------------------------------------------------
	| Default theme
	|--------------------------------------------------------------------------
	*/
	public $default_theme = 'default';


	/*
	|--------------------------------------------------------------------------
	| Default layout
	|--------------------------------------------------------------------------
	*/
	public $default_layout = 'index';


	/*
	|--------------------------------------------------------------------------
	| Default template
	|--------------------------------------------------------------------------
	*/
	public $default_template = 'index';


	/*
	|--------------------------------------------------------------------------
	| Auto-register functions
	|--------------------------------------------------------------------------
	|
	| Here you can list all the functions that you want Twiggy to automatically
	| register them for you.
	|
	| NOTE: only registered functions can be used in Twig templates. 
	|
	*/
	public $list_functions_asis = 
	[
		'base_url',
		'site_url'
	];
	
	public $list_functions_safe = 
	[
		'form_open',
		'form_close',
		'form_error',
		'form_hidden',
		'set_value'
	];


	/*
	|--------------------------------------------------------------------------
	| Auto-register filters
	|--------------------------------------------------------------------------
	|
	| Much like with functions, list filters that you want Twiggy to 
	| automatically register them for you.
	|
	| NOTE: only registered filters can be used in Twig templates. Also, keep
	| in mind that a filter is nothing more than just a regular function that
	| acceps a string (value) as a parameter and outputs a modified/new string.
	|
	*/
	public $list_filters = 
	[

	];


	/*
	|--------------------------------------------------------------------------
	| Title separator
	|--------------------------------------------------------------------------
	|
	| Lets you specify the separator used in separating sections of the title 
	| variable.
	|
	*/
	public $title_separator = ' | ';
}
