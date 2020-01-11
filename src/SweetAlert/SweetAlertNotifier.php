<?php

namespace UxWeb\SweetAlert;

class SweetAlertNotifier
{
    const ICON_WARNING = 'warning';
    const ICON_ERROR = 'error';
    const ICON_SUCCESS = 'success';
    const ICON_INFO = 'info';
    const TIMER_MILLISECONDS = 1800;

    /**
     * @var \UxWeb\SweetAlert\SessionStore
     */
    protected $session;

    /**
     * Configuration options.
     *
     * @var array
     */
    protected $config;

    protected $defaultButtonConfig = [
        'text'       => '',
        'visible'    => false,
        'value'      => null,
        'className'  => '',
        'closeModal' => true,
    ];

    /**
     * Create a new SweetAlertNotifier instance.
     *
     * @param \UxWeb\SweetAlert\SessionStore $session
     */
    public function __construct(SessionStore $session)
    {
        $this->setDefaultConfig();

        $this->session = $session;
    }

    /**
     * Sets all default config options for an alert.
     *
     * @return void
     */
    protected function setDefaultConfig()
    {
        $this->config = [
            'timer'   => config('sweet-alert.autoclose', self::TIMER_MILLISECONDS),
            'text'    => '',
            'showConfirmButton' => false,
            'showCancelButton' => false,
        ];
    }

    /**
     * Display an alert message with a text and an optional title.
     *
     * By default the alert is not typed.
     *
     * @param string $text
     * @param string $title
     * @param string $icon
     *
     * @return \UxWeb\SweetAlert\SweetAlertNotifier $this
     */
    public function message($text = '', $title = null, $icon = null)
    {
        $this->config['text'] = $text;

        if (!is_null($title)) {
            $this->config['title'] = $title;
        }

        if (!is_null($icon)) {
            $this->config['type'] = $icon;
        }

        $this->flashConfig();

        return $this;
    }

    /**
     * Display a not typed alert message with a text and a title.
     *
     * @param string $text
     * @param string $title
     *
     * @return \UxWeb\SweetAlert\SweetAlertNotifier $this
     */
    public function basic($text, $title)
    {
        $this->message($text, $title);

        return $this;
    }

    /**
     * Display an info typed alert message with a text and an optional title.
     *
     * @param string $text
     * @param string $title
     *
     * @return \UxWeb\SweetAlert\SweetAlertNotifier $this
     */
    public function info($text, $title = '')
    {
        $this->message($text, $title, self::ICON_INFO);

        return $this;
    }

    /**
     * Display a success typed alert message with a text and an optional title.
     *
     * @param string $text
     * @param string $title
     *
     * @return \UxWeb\SweetAlert\SweetAlertNotifier $this
     */
    public function success($text, $title = '')
    {
        $this->message($text, $title, self::ICON_SUCCESS);

        return $this;
    }

    /**
     * Display an error typed alert message with a text and an optional title.
     *
     * @param string $text
     * @param string $title
     *
     * @return \UxWeb\SweetAlert\SweetAlertNotifier $this
     */
    public function error($text, $title = '')
    {
        $this->message($text, $title, self::ICON_ERROR);

        return $this;
    }

    /**
     * Display a warning typed alert message with a text and an optional title.
     *
     * @param string $text
     * @param string $title
     *
     * @return \UxWeb\SweetAlert\SweetAlertNotifier $this
     */
    public function warning($text, $title = '')
    {
        $this->message($text, $title, self::ICON_WARNING);

        return $this;
    }

    /**
     * Set the duration for this alert until it autocloses.
     *
     * @param int $milliseconds
     *
     * @return \UxWeb\SweetAlert\SweetAlertNotifier $this
     */
    public function autoClose($milliseconds = null)
    {
        if (!is_null($milliseconds)) {
            $this->config['timer'] = $milliseconds;
        }

        $this->flashConfig();

        return $this;
    }

    /**
     * Add a confirmation button to the alert.
     *
     * @param string $buttonText
     *
     * @return \UxWeb\SweetAlert\SweetAlertNotifier $this
     */
    public function confirmButton($buttonText = 'OK')
    {
        $this->config['showConfirmButton'] = true;
        $this->config['confirmButtonText'] = $buttonText;

        $this->config['allowOutsideClick'] = false;
        $this->removeTimer();
        $this->flashConfig();

        return $this;
    }

    /**
     * Add a cancel button to the alert.
     *
     * @param string $buttonText
     *
     * @return \UxWeb\SweetAlert\SweetAlertNotifier $this
     */
    public function cancelButton($buttonText = 'Cancel')
    {
        $this->config['showCancelButton'] = true;
        $this->config['cancelButtonText'] = $buttonText;

        $this->config['allowOutsideClick'] = false;
        $this->removeTimer();
        $this->flashConfig();

        return $this;
    }

    /**
     * Add a new custom button to the alert.
     * @todo Probably obsolete method
     *
     * @param string $buttonText
     * @param string $type
     *
     * @return \UxWeb\SweetAlert\SweetAlertNotifier $this
     */
    public function addButton($buttonText, $type = 'confirm')
    {
        switch ($type) {
            case 'cancel':
                $this->config['showCancelButton'] = true;
                $this->config['cancelButtonText'] = $buttonText;
                break;
            default:
                $this->config['showConfirmButton'] = true;
                $this->config['confirmButtonText'] = $buttonText;
        }

        $this->config['allowOutsideClick'] = false;
        $this->removeTimer();
        $this->flashConfig();

        return $this;
    }

    /**
     * Toggle close the alert message when clicking outside.
     *
     * @param bool $value
     *
     * @return \UxWeb\SweetAlert\SweetAlertNotifier $this
     */
    public function allowOutsideClick($value = true)
    {
        $this->config['allowOutsideClick'] = $value;

        $this->flashConfig();

        return $this;
    }

    /**
     * Make this alert persistent with a confirmation button.
     *
     * @param string $buttonText
     *
     * @return \UxWeb\SweetAlert\SweetAlertNotifier $this
     */
    public function persistent($buttonText = 'OK')
    {
        return $this->confirmButton($buttonText);
    }

    /**
     * Remove the timer config option.
     *
     * @return void
     */
    protected function removeTimer()
    {
        if (array_key_exists('timer', $this->config)) {
            unset($this->config['timer']);
        }
    }

    /**
     * Make Message HTML view.
     *
     * @return \UxWeb\SweetAlert\SweetAlertNotifier $this
     */
    public function html()
    {
        $this->config['html'] = $this->config['text'];
        unset($this->config['text']);

        $this->flashConfig();

        return $this;
    }

    /**
     * Flash the current alert configuration to the session store.
     *
     * @return void
     */
    protected function flashConfig()
    {
        foreach ($this->config as $key => $value) {
            $this->session->flash("sweet_alert.{$key}", $value);
        }

        $this->session->flash('sweet_alert.alert', $this->buildJsonConfig());
    }

    /**
     * Build the configuration as Json.
     *
     * @return string
     */
    protected function buildJsonConfig()
    {
        return json_encode($this->config);
    }

    /**
     * Return the current alert configuration.
     *
     * @param null|string $key
     *
     * @return array
     */
    public function getConfig($key = null)
    {
        if (is_null($key)) {
            return $this->config;
        }

        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }
    }

    /**
     * Customize alert configuration "by hand".
     *
     * @param array $config
     *
     * @return \UxWeb\SweetAlert\SweetAlertNotifier
     */
    public function setConfig($config = [])
    {
        $this->config = array_merge($this->config, $config);

        $this->flashConfig();

        return $this;
    }

    /**
     * Return the current alert configuration as Json.
     *
     * @return string
     */
    public function getJsonConfig()
    {
        return $this->buildJsonConfig();
    }
}
