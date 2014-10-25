<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_Account_Sigup extends Controller_Account_Template {
  
  protected $redirect = '/';

  public function before()
  {
    parent::before();
  }

  public function action_index()
  {
    $redirect = Arr::get($_GET, 'redirect', $this->redirect);
    $view = View::factory($this->tpl_dir.'sigup')->set('redirect', $redirect);
    $this->template->content = $view;
  }

  public function action_check()
  {
    $post = Validation::factory( Arr::extract($_POST, 
      array('email', 'password', 'repassword', 'agree', 'captcha')) );
    $post->rules('email', array(
        array('not_empty'),
        array('email'),
        array('max_length', array(':value', 30)),
        array('min_length', array(':value', 5))
      ))
      ->rules('password', array(
        array('not_empty'),
        array('max_length', array(':value', 32)),
        array('min_length', array(':value', 6))
      ))
      ->rules('repassword', array(
        array('matches', array(':validation', 'repassword', 'password'))
      ))
      ->rules('agree', array(
        array('not_empty'),
        array('equals', array(':value', 'on'))
      ));

    // captcha
    if ($this->has_captcha) {
      $post->rules('captcha', array(
          array('not_empty'),
          array('Captcha::valid', array(':value')),
        ));
    }

    if ( $post->check() ) {
      $data = $post->as_array();
      unset($data['agree'], $data['captcha'], $data['repassword']);
      $ret = $this->model_account->save($data);
      if ($ret) {
        $this->_success($ret);
        return;
      }
      $this->template->set_global('message', '注册失败');
    }
    else {
      $error = $post->errors('sigup');
      $this->template->bind_global('error', $error);
    }
    Cache::instance()->set($this->asip, $this->asip_count + 1, 3600);
    $this->action_index();
  }  

  public function _success()
  {
    $this->template->content = View::factory($this->tpl_dir.'sigup-success');
  }
} // End Sigup
