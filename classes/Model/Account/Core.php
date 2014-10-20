<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Account_Core extends Model {

  protected static $group = 'a';
  protected static $driver = 'nosql';

  public static $user;

  public static function instance($driver = null)
  {
    if ($driver === null) {
      $driver = static::$driver;
    }
    $model = 'Account_Driver_'.ucfirst($driver);
    return parent::factory($model);
  }

  public function check_exists_by_email($email) {}
  public function check_exists_by_name($name) {}

  public function get_one_by_uid($uid) {}
  public function get_one_by_name($name) {}
  public function get_one_by_email($email) {}
  public function get_one_by_passport($passport) {}

  public function get_list() {}
  public function save($data) {}
  public function update($uid, $passport) {}
  public function update_password($uid, $password) {}
  public function delete($uid) {}
  public function actived($uid, $actived = null) {}

  public function check_passport($user, $check_password = true) {
    $message = true;
    $passport = $user['passport'];
    $one = $this->get_one_by_passport($passport);
    if ($one == false) {
      $message = '账户或密码不正确';
    }
    elseif (Arr::get($one, 'actived', FALSE)) {
      $message = '你的账户还未激活';
    }
    elseif ( $check_password 
      && Arr::get($one, 'password') <> Auth::instance()->hash_password($user['password'])){
      $message = '账户或密码不正确';
    }
    else {
      if (isset($user['expires']) && $user['expires'] > 0) {
        $one['expires'] = $user['expires'];
      }
      $one['passport'] = $passport;
      $this->register($one);
    }
    return $message;
  }

  public function register($user, $force=true) 
  {
    if ($force) {
      $expires = 0;
      if (isset($user['expires'])) {
        $expires = (int) $user['expires'];
        Cookie::set(static::$group.'expires', 1, $expires);
      }
      Cookie::set(static::$group.'passport', $user['passport'], $expires);
    }
    static::$user = array(
      'id' => Arr::get($user, 'id'), 
      'auth' => Arr::get($user, 'auth'), 
      'passport' => isset($user['passport'])?$user['passport']:$user['email'],
      'ip' => Request::$client_ip);
    Session::instance()->set(static::$group, static::$user);
  }

  public static function auth($id) { 
    return isset(static::$user['auth'][$id]);
  }

  public function check_login($expires=false) {
    static::$user = Session::instance()->get(static::$group, false); 
    if (empty(static::$user) and $expires==true) {
      $has_expires = Cookie::get(static::$group.'expires', false);
      $has_passport = Cookie::get(static::$group.'passport', false);
      if ($has_expires && $has_passport) {
        $user = $this->get_one_by_passport($has_passport);
        if ($user) {
          $user['passport'] = $has_passport;
          $this->register($user, false);
          return $user;
        }
      }
    }
    if (Request::$client_ip == static::$user['ip']) {
      return static::$user;
    }
    return false;
  }

  public function logout() {
    Cookie::delete('expires');
    Session::instance()->destroy();
    return TRUE;
  }

}
