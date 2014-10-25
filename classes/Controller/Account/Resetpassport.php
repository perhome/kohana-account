<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Account_Resetpassport extends Controller_Account_Template {

  protected $redirect = '/';

  public function before()
  {
    parent::before();
  }

  public function action_index()
  {
    $view = View::factory($this->tpl_dir.'resetpassport');
    $view->set('has_captcha', $this->has_captcha);
    $this->template->content = View::factory($this->tpl_dir.'resetpassport');
  }
  
  public function action_check()
  {
    $post = Validation::factory( Arr::extract($_POST, array('email', 'captcha')) );
    $post->rules('email', array(
        array('not_empty'),
        array('email'),
        array('max_length', array(':value', 30)),
        array('min_length', array(':value', 5))
      ));

    // captcha
    if ($this->has_captcha) {
      $post->rules('captcha', array(
          array('not_empty'),
          array('Captcha::valid', array(':value')),
        ));
    }

    if ($post->check()) {
      $data = $post->as_array();
      $ret = $this->model_account->reset_passport($data['email']);
      if ($ret === true) {
        $message = '请查收你的邮箱，按照说明设置你的新密码。';
      }
      $this->template->set_global('message', $ret);
    }
    else {
      $error = $post->errors('resetpassport');
      $this->template->bind_global('error', $error);
    }
    Cache::instance()->set($this->asip, $this->asip_count + 1, 3600);
    $this->action_index();
  }

} // End Resetpasswd
