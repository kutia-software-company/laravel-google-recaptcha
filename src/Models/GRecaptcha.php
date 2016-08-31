<?php

namespace AstritZeqiri\GRecaptcha;

use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class GRecaptcha
{
    /**
     * The randomly generated recaptcha id.
     *
     * @var string
     */
    private $id;

    /**
     * The recaptcha options.
     *
     * @var array
     */
    private $options = [];

    /**
     * The url to verify the recaptcha validity.
     */
    const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * The global variables for grecaptcha
     * These will apear before the startcaptchas script after the recaptcha.
     */
    const CAPTCHA_GLOBALS = ['site_key'];

    /**
     * All the recaptchas collection.
     *
     * @var Collection
     */
    public static $recaptchas = null;

    /**
     * The name of the config file.
     *
     * @var string
     */
    protected static $configName = 'grecaptcha';

    /**
     * The scripts that have to be loaded a captcha is needed they are loaded by,
     * calling the static method renderScripts() of this class.
     *
     * @var array
     */
    protected static $scripts = [
        ['type' => 'url', 'url' => 'https://www.google.com/recaptcha/api.js?onload=CaptchaCallback&render=explicit'],
        ['type' => 'inline', 'method' => 'captchaGlobals'],
        ['type' => 'url', 'method' => 'startCaptchasScriptUrl'],
    ];

    /**
     * The captcha constructor.
     *
     * @param array $options
     */
    private function __construct($options = [])
    {
        $this->options = $options;
        $this->id = Str::random(30);

        if (!static::$recaptchas) {
            static::$recaptchas = new Collection();
        }

        static::$recaptchas->push($this);
    }

    /**
     * Generate a new instance of the recaptcha if it is enabled.
     *
     * @return mixed self|null
     */
    public static function generate()
    {
        if (!static::isEnabled()) {
            return;
        }

        return new static();
    }

    /**
     * Get the grecaptcha id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Create and render a new recaptcha.
     *
     * @param array $options
     * @param bool  $render  [if true the captcha is ecod out if not it is only returned as html]
     *
     * @return string
     */
    public static function render($options = [], $render = true)
    {
        $recaptcha = static::generate();

        $method = 'renderHtml';

        if ($render === false) {
            $method = 'build';
        }

        return $recaptcha->$method();
    }

    /**
     * Echo out the recpatcha.
     */
    public function renderHtml()
    {
        echo $this->build();
    }

    /**
     * Build the recaptcha html.
     *
     * @return string
     */
    public function build()
    {
        return '<div class="google-recaptcha-insert" id="'.$this->id.'"></div>';
    }

    /**
     * Check if the captcha is enabled or disabled on the recaptcha config file.
     *
     * @return bool
     */
    public static function isEnabled()
    {
        $enabledKey = static::getConfig('enabled');

        return !is_null($enabledKey) && $enabledKey === true;
    }

    /**
     * Check if the given captcha response from the submitted form is valid.
     *
     * @param string $g_recaptcha_response
     *
     * @return bool
     */
    public static function check($g_recaptcha_response = '')
    {
        if (!static::isEnabled()) {
            return true;
        }

        if (empty($g_recaptcha_response) || is_null($g_recaptcha_response)) {
            return false;
        }

        $client = new Client();
        $response = $client->request('POST', static::VERIFY_URL, [
            'form_params' => [
                'secret'   => static::getConfig('secret_key'),
                'response' => $g_recaptcha_response,
            ],
        ]);
        $body = json_decode($response->getBody(), true);

        return (bool) $body['success'];
    }

    /**
     * Render the scripts if there is any captcha created.
     *
     * @return string
     */
    public static function renderScripts()
    {
        if (!static::isEnabled() || !static::$recaptchas) {
            return '';
        }

        return collect(static::$scripts)
                ->map(function ($script) {
                    return static::renderScript($script);
                })
                ->implode('');
    }

    /**
     * Render a script based on its attibutes.
     *
     * @param array $script
     *
     * @return string
     */
    public static function renderScript($script)
    {
        if (!isset($script['type'])) {
            return '';
        }

        $url = '';
        $inline = '';

        if ($script['type'] == 'url') {
            $url = isset($script['url']) ? $script['url'] : '';

            $method = isset($script['method']) && method_exists(static::class, $script['method']) ? $script['method'] : '';

            if (!empty($method)) {
                $url = static::$method();
            }
        } elseif ($script['type'] == 'inline') {
            if (isset($script['value'])) {
                $inline = $script['value'];
            } elseif (isset($script['method']) && method_exists(static::class, $script['method'])) {
                $method = $script['method'];
                $inline = static::$method();
            }
        }

        if (!empty($url)) {
            return '<script type="text/javascript" src="'.$url.'"></script>';
        }
        if (!empty($inline)) {
            return '<script>'.$inline.'</script>';
        }


        return '';
    }

    /**
     * Get the captcha globals from the config file.
     *
     * @return Collection.
     */
    public static function getGlobals()
    {
        return collect(static::CAPTCHA_GLOBALS)
        ->map(function ($item) {
            return ['key' => $item];
        })
        ->keyBy('key')
        ->map(function ($item) {
            return static::getConfig($item['key']);
        });
    }

    /**
     * Get a config variable from the config file.
     *
     * @param string $name
     *
     * @return mixed
     */
    public static function getConfig($name = '')
    {
        return config(static::$configName.'.'.$name);
    }

    /**
     * Get the captcha globals which render on a script tag as json.
     *
     * @return string
     */
    public static function captchaGlobals()
    {
        return ' var captcha_globals = '.static::getGlobals()->toJson().';';
    }

    /**
     * Get the link of the script that starts the captchas.
     *
     * @return string
     */
    public static function startCaptchasScriptUrl()
    {
        return asset('vendor/grecaptcha/js/start_captchas.js');
    }
}
