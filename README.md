# Google recaptcha for laravel

Easily add google recaptcha

## Requirements

- PHP >=5.4
- Laravel >= 5.0
- GuzzleHttp >= 5.3


## Installation

Add astritzeqiri/g-recaptcha to your composer.json file:

```json
"require": {
    "astritzeqiri/g-recaptcha": "~1.0"
}
```

And do:

```
$ composer update
```

Or get composer to install the package:

```
$ composer require astritzeqiri/g-recaptcha
```

Then you run php artisan vendor:publish to publish the start_captchas script and also the recaptcha config file.

```
$ php artisan vendor:publish
```


## Configuration

Now you go and generate a new Google recaptcha on the link below:

[Google Recaptcha Generate](https://www.google.com/recaptcha/intro/index.html ).

Then you got to your .env file and set these variables

```
# If you want to disable captchas put this to false by default this is true.
GRECAPTCHA_ENABLED=true

# The google recaptcha site key
GRECAPTCHA_KEY=SITE_KEY

# The google recaptcha secret key
GRECAPTCHA_SECRET=SECRET_KEY
```

You can also change these variables on the config file on config/grecaptcha.php file.

```php
return [
	'enabled' => env("GRECAPTCHA_ENABLED", true),
	'site_key' => env("GRECAPTCHA_KEY"),
	'secret_key' => env("GRECAPTCHA_SECRET"),
];
```

### Registering the Package

Register the service provider within the `providers` array found in `app/config/app.php`:

```php
'providers' => array(
    // ...
    AstritZeqiri\GRecaptcha\GRecaptchaServiceProvider::class
)
```

Then you need to add GRecaptcha class within the `aliases` array found in `app/config/app.php`:


```php
'aliases' => array(
    // ...
    'GRecaptcha' => AstritZeqiri\GRecaptcha\GRecaptcha::class,
)
```

## Usage


First of all you need to call the grecaptcha scripts on the footer. The scripts are rendered only if you have an active captcha somewhere on you html.

```php
// in blade.php files
{!! \GRecaptcha::renderScripts() !!}

// or in .php files
<?php echo GRecaptcha::renderScripts(); ?>
```

### Basic Example

Now to render a new GRecaptcha you call the render method.

```php
// by default it echo's it out
GRecaptcha::render();

// if you want to save the html in a variable you call
$grecaptchaHtml = GRecaptcha::render([], false);
```

If you want to get a new recaptcha instance:
```php
$grecaptcha = GRecaptcha::generate();

// to render it you call
$grecaptcha->renderHtml();

// if you dont want it to be rendered but store the html you call
$grecaptchaHtml = $grecaptcha->build();
```
