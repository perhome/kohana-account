<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Controller_Account_Template extends Controller {

  protected $redirect;

  public $tpl_dir;
  public $name = 'default';
  public $template = 'account';
  public $auto_render = TRUE;

  protected $asip;
  protected $asip_count;
  protected $asip_count_max = 6;

  public function before()
  {
    parent::before();

    $this->asip_count = Cache::instance()->get($this->asip, 0);
    $this->model_account = Model_Account::instance();
    if ($this->auto_render === TRUE)
    {
      // Load the template
      $this->tpl_dir = $this->template.DIRECTORY_SEPARATOR.$this->name.DIRECTORY_SEPARATOR;
      $this->template = View::factory($this->tpl_dir.'template');
    }
  }

  public function safe_redirect($url)
  {
    $url = urldecode($url);
    $this->redirect($url);
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
