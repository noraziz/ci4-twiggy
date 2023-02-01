# CI4-Twiggy - Twig template engine implementation for CodeIgniter 4

This library is for Codeigniter 4, forked from [twiggy](https://github.com/edmundask/codeigniter-twiggy). But, some development reference come from [ss-twig](https://github.com/kenjis/codeigniter-ss-twig) & [raizdev](https://github.com/raizdev/twig-codeigniter4).


Twiggy is not just a simple implementation of Twig template engine for CodeIgniter. It supports themes, layouts, templates for regular apps and also for apps that use HMVC (module support).


It is supposed to make life easier for developing and maitaining CodeIgniter applications where themes and nicely structured templates are necessary.

## Why Should I Care?

For some reason, the original twiggy library is moved to new structure so you can add this library to your projects using composer.
Twig by itself is a very powerful and flexible templating system but with CodeIgniter it is even cooler! With Twiggy you can separately set the theme, layout and template for each page. 
What is even more interesting, this does not replace CodeIgniter's default Views, so you can still load views as such: `$this->load->view()`.

## Requirements

* PHP 7.3 or later
* CodeIgniter 4.2 or later
* Twig 3.3.8 or later

## Installation

### With Composer

~~~
$ cd /path/to/codeigniter/
$ composer require aitimasi/ci4-twiggy
~~~

## Usage

### Set up dir structure

1. Create a directory structure:

	```
    +-{APPPATH}/
    | +-themes/
    | | +-default/
    | | | +-_layouts/
	```

	NOTE: `{APPPATH}` is the folder where all your controllers, models and other neat stuff is placed.
	By default that folder is called `application`.

2. Create a default layout `index.html.twig` and place it in _layouts  folder:

	```
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

	```
	{% extends _layout %}

	{% block content %}

		Default template file.

	{% endblock %}
	```

4. You should end up with a structure like this:

	```
    +-{APPPATH}/
    | +-themes/
    | | +-default/
    | | | +-_layouts/
    | | | | +-index.hml.twig
    | | | +-index.html.twig
	```

### Display the template

Render Twig template and output to browser:

~~~php
$this->twiggy->display();
~~~

### What's next?

In the example above we only displayed the default template and layout. Obviously, you can create as many layouts and templates as you want.
For example, create a new template file `welcome.html.twig` and load it before sending the output to the browser.

~~~php
// Whoah, methoding chaining FTW!
$this->twiggy->template('welcome')->display();
~~~

Notice that you only need to specify the name of the template (without the extension `*.html.twig`).

There is much more cool stuff that you should check out by visiting the [wiki](https://github.com/edmundask/codeigniter-twiggy/wiki).

## CHANGELOG

### 0.1

* First release.

### References
* https://github.com/edmundask/codeigniter-twiggy
* https://github.com/kenjis/codeigniter-ss-twig

## Documentation

* https://twig.symfony.com/doc/3.x/
* https://github.com/edmundask/codeigniter-twiggy/wiki

@TODO

* https://github.com/noraziz/ci4-twiggy/wiki
* https://github.com/noraziz/ci4-twiggy-demo

## COPYRIGHT

Copyright (c) 2023 Nor Aziz

Permission is hereby granted, free of charge, to any person obtaining a copy 
of this software and associated documentation files (the "Software"), to deal 
in the Software without restriction, including without limitation the rights 
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell 
copies of the Software, and to permit persons to whom the Software is 
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in 
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN 
THE SOFTWARE.
