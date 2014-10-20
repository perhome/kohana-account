<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Account_Sigin extends Controller_Account_Template {

  protected $redirect = '/';

  public function before()
  {
    $this->asip = $this->template.'_sigin_'. Request::$client_ip;
    parent::before();
    if ($this->model_account->check_login(true)) {
      $this->redirect($this->redirect);
    }
  }

  public function action_index()
  {
    $has_captcha = FALSE;
    if ($this->asip_count > $this->asip_count_max) {
      $has_captcha = TRUE;
    }
    $redirect = urlencode(Arr::get($_GET, 'redirect', $this->redirect));
    $view = View::factory($this->tpl_dir.'sigin')
      ->set('has_captcha', $has_captcha)
      ->set('redirect', $redirect);
    $this->template->content = $view;
  }

  public function action_check()
  {
    $post = Validation::factory(Arr::extract($_POST, 
      array('passport', 'password', 'expires', 'redirect', 'captcha')));
    $post->rules('passport', array(
          array('not_empty'),
          array('min_length', array(':value', 3)),
          array('max_length', array(':value', 30))
        ))
        ->rules('password', array(
          array('not_empty'),
          array('min_length', array(':value', 3)),
          array('max_length', array(':value', 30))
        ))
        ->rules('expires', array(
          array('is_numeric')
        ))
        ->rules('redirect', array());

    // captcha
    if ($this->asip_count > $this->asip_count_max) {
      $post->rules('captcha', array(
          array('not_empty'),
          array('Captcha::valid', array(':value')),
        ));
    }
    if ($post->check()) {
      $data = $post->as_array();
      $result = $this->model_account->check_passport($data);
      if ($result === true) {
        $this->safe_redirect($data['redirect']);
      }
      $this->template->set_global('message', $result);
    }
    else {
      $error = $post->errors('sigin');
      $this->template->bind_global('error', $error);
    }
    Cache::instance()->set($this->asip, $this->asip_count + 1, 3600);
    $this->action_index();
  }  

} // End Sigin
