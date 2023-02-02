# CI4-Twiggy - Twig template engine implementation for CodeIgniter 4

This library is for Codeigniter 4, forked from [twiggy](https://github.com/edmundask/codeigniter-twiggy). But, some development reference come from [ss-twig](https://github.com/kenjis/codeigniter-ss-twig) & [raizdev](https://github.com/raizdev/twig-codeigniter4).


Twiggy is not just a simple implementation of Twig template engine for CodeIgniter. It supports themes, layouts, templates for regular apps and also for apps that use modular support, see: [Code Modules](https://codeigniter4.github.io/CodeIgniter4/general/modules.html).


It is supposed to make life easier for developing and maitaining CodeIgniter applications where themes and nicely structured templates are necessary.

## Why Should I Care?

For some reason, the original twiggy library is moved to new structure so you can add this library to your projects using composer.
Twig by itself is a very powerful and flexible templating system but with CodeIgniter it is even cooler! With Twiggy you can separately set the theme, layout and template for each page. 
What is even more interesting, this does not replace CodeIgniter's default Views, so you can still load views as such: `$this->load->view()`.

## Demo
Visit our demo repository [github](https://github.com/noraziz/ci4-twiggy-demo).

Our [wiki](https://github.com/noraziz/ci4-twiggy/wiki) is coming soon.


## Features:

* Theme path & twig template may be placed on module. Default on 'app' folder.
* Set/Unset custom variable to be passed to template.
* Set/Unset meta (html template).
* Add Function (mode: as-is or safe).
* Add Filter.
* Set template title. You may combine with function 'append' or 'prepend' for formatting words.
* Set custom theme.
* Set custom layout, default layout path is: '{app-mod}\Themes{theme_name}_layout'.
* Set custom template, default file extension: 'html.twig'.
* Use 'display' method for directly output to browser. Else use 'render' method to capture parsed template.

## Default Functions:

* as-is: base_url, site_url.
* safe: form_open, form_close, form_error, form_hidden, set_value, csrf_field.
* built-in: getConfig, getLang, safe_anchor, validation_list_errors.

## Requirements

* PHP 7.3 or later
* CodeIgniter 4.2 or later
* Twig 3.3.8 or later

## Installation

### With Composer

~~~
$ cd /path/to/codeigniter/
$ composer require noraziz/ci4-twiggy
~~~

## Usage

### Set up dir structure

1. Create a directory structure:

	```
    +-{APPPATH}/
    | +-Themes/
    | | +-default/
    | | | +-_layouts/
	```

	NOTE: `{APPPATH}` is the folder where all your controllers, models and other neat stuff is placed.
	By default that folder is called `app`. Themes directory can also inside your module.

	```
    +-{APPPATH}/
    | +-Config/
    | +-Controllers/
    |
    +-Modules/
    | +-{Module-Name}/
    | | +-Themes/
    | | | +-default/
    | | | | +-_layouts/
	```

2. Create a default layout `index.html.twig` and place it in _layouts  folder:

	```twig
	<!DOCTYPE html>
	<html lang="en">
		<head>
			<meta charset="utf-8">
			<!--[if lt IE 9]>
			<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
			<![endif]-->
			<title>Default layout</title>
		</head>
		<body>

			{% block content %}



			{% endblock %}
			
		</body>
	</html>
	```

3. Create a default template file `index.html.twig` at the root of `default` theme folder:

	```twig
	{% extends _layout %}

	{% block content %}

		Default template file.

	{% endblock %}
	```

4. You should end up with a structure like this:

	```
    +-{APPPATH}/
    | +-Themes/
    | | +-default/
    | | | +-_layouts/
    | | | | +-index.hml.twig
    | | | +-index.html.twig
	```

### Initialize Twiggy

You must maually initialize twiggy engine.

~~~php
$twiggy = new \noraziz\ci4twiggy\Twiggy();
$twiggy->init(__CLASS__);
~~~

### Display the template

Render Twig template and output to browser:

~~~php
$twiggy->display();
~~~

### What's next?

In the example above we only displayed the default template and layout. Obviously, you can create as many layouts and templates as you want.
For example, create a new template file `page_welcome.html.twig` and load it before sending the output to the browser.

~~~php
$twiggy->layout('layout_model_1');
$twiggy->template('page_welcome');

//or using chaining
$twiggy->layout('layout_model_1')->template('page_welcome');
~~~

Notice that you only need to specify the name of the template (without the extension `*.html.twig`).

### References
* https://github.com/edmundask/codeigniter-twiggy
* https://github.com/kenjis/codeigniter-ss-twig
* https://github.com/raizdev/twig-codeigniter4

## Documentation

* https://twig.symfony.com/doc/3.x/

@TODO

* https://github.com/noraziz/ci4-twiggy/wiki
