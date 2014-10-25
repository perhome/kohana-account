<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Controller_Account_Template extends Controller {

  protected $redirect;
  protected $has_captcha = false;

  public $tpl_dir;
  public $name = 'default';
  public $template = 'account';

  public $auto_render = TRUE;

  protected $asip;
  protected $asip_count;
  protected $asip_count_max = 3;

  public function before()
  {
    parent::before();
    
    $this->asip = $this->get_cache_id();
    $this->asip_count = Cache::instance()->get($this->asip, 0);
    $this->model_account = Model_Account::instance();
    if ($this->asip_count > $this->asip_count_max) {
      $this->has_captcha = TRUE;
    }
    if ($this->auto_render === TRUE)
    {
      // Load the template
      $this->tpl_dir = $this->template.DIRECTORY_SEPARATOR.$this->name.DIRECTORY_SEPARATOR;
      $this->template = View::factory($this->tpl_dir.'template');
      $this->template->set_global('has_captcha', $this->has_captcha);
    }
  }

  public function safe_redirect($url)
  {
    $url = urldecode($url);
    $this->redirect($url);
  }

  public function get_cache_id()
  {
    $class = get_called_class();
    $cache = 'cache_asip_'.$class.Request::$client_ip;
    return $cache;
  }

  /**
   * Assigns the template [View] as the request response.
   */
  public function after()
  {
    if ($this->auto_render === TRUE)
    {
      $this->response->body($this->template->render());
    }

    parent::after();
  }

}
