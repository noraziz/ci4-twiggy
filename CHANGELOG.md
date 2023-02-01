# v0.2
_2023-02-02_

This is the latest working package.
Almost all Twiggy Engine have been ported.

### Key Features:
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

### Default Functions:
* as-is: base_url, site_url.
* safe: form_open, form_close, form_error, form_hidden, set_value, csrf_field.
* built-in: getConfig, getLang, safe_anchor, validation_list_errors.

### Drawback:
* You must initialize twiggy engine manually: $twiggy->init(CLASS); // supply params with full classname, for example: CLASS.

### To-Do:
* setLexer.


\
# v0.1.1-alpha
_2023-02-01_

Update namespace.


\
# v0.1
_2023-01-30_

This is very first release.
Only testing on development stage.
