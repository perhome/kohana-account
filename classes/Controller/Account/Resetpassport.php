<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Account_Resetpassport extends Controller_Account_Template {

  protected $redirect = '/';
  protected $cache = 'reset';

  public function before()
  {
    parent::before();
  }

  public function action_index()
  {
    $this->template->content = View::factory($this->tpl_dir.'resetpassport');
  }
  
  public function action_check()
  {
    $post = Validation::factory( Arr::extract($_POST, array('email', 'captcha')) );
    $post->rules('email', array(
            array('trim'),
            array('not_empty'),
            array('email'),
            array('max_length', array(':value', 30)),
            array('min_length', array(':value', 5))
          ))
        ->rules('captcha', array(
          array('not_empty'),
          array('Captcha::valid_once', array(':value')),
        ));


    if ($post->check()) {
      $data = $post->as_array();
      $ret = $this->model->resetpassport($data['email']);
      if ($ret) {
        $message = '重置成功';
      }
      else {
        $message = '';
      }
      $this->template->set_global('message', $message);
    }
    else {
      $error = $post->errors('resetpassport');
      $this->template->bind_global('error', $error);
    }
    $this->action_index();
  }

} // End Resetpasswd
