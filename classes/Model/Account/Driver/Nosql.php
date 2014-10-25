<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Account_Driver_Nosql extends Model_Account_Core {

  public $key_user = 'account:user';
  public $key_user_index = 'account:user:index';
  public $key_user_reset = 'account:user:reset:';
  public $key_user_map_by_email = 'account:user:email';
  public $key_user_map_by_name = 'account:user:name';
  public $key_user_sort_by_created = 'account:user:list:created';

  public function check_exists_by_email($email) 
  {
    return Kv::instance()->hexists($this->key_user_map_by_email, $email);
  }

  public function check_exists_by_name($name) 
  {
    return Kv::instance()->hexists($this->key_user_map_by_name, $name);
  }

  public function get_one_by_email($email) 
  {
    $user = Kv::instance()->hget($this->key_user_map_by_email, $email);
    if ($user) {
      return json_decode($user, true);
    }
    return false;
  }
  
  public function get_one_by_name($name) 
  {
    $user = kv::instance()->hget($this->key_user_map_by_name, $name);
    if ($user) {
      return json_decode($user, true);
    }
    return false;
  }

  public function get_one_by_uid($uid)
  {
    $user = kv::instance()->hget($this->key_user, $uid);
    if ($user) {
      return json_decode($user, true);
    }
    return false;
  }

  public function get_one_by_passport($passport) 
  {
    $key = Kv::instance()->hget($this->key_user_map_by_email, $passport);
    if ($key == null) {
      $key = Kv::instance()->hget($this->key_user_map_by_name, $passport);
    }
    if ($key) {
      return $this->get_one_by_uid($key);
    }
    return false;
  }

  public function get_list()
  {
    return ;
  } 
  public function save($passport)
  {
    if (!isset($passport['name']) and empty($passport['name'])) {
      $passport['name'] = $passport['email'];
    }
    if ($this->check_exists_by_email($passport['email']) == true
      or $this->check_exists_by_email($passport['name']) == true) {
      return false;
    }
    $index = Kv::instance()->incr($this->key_user_index, 1);
    if ($index) {
      if (isset($passport['password'])) {
        $passport['password'] = Auth::instance()->hash_password($passport['password']);
      }
      $passport['id'] = $index;
      $ret = Kv::instance()->hset($this->key_user, $index, json_encode($passport));
      Kv::instance()->hset($this->key_user_map_by_email, $passport['email'], $index);
      Kv::instance()->hset($this->key_user_map_by_name, $passport['name'], $index);
      Kv::instance()->zset($this->key_user_map_by_email, $passport['email'], $index);
      return $ret?$index:false;
    }
    return false;
  }

  public function update($uid, $passport) 
  {
    $user = $this->get_one_by_uid($uid);
    if ($user) {
      $user = array_merge($user, $passport);
      $ret = Kv::instance()->hset($this->key_user, $uid, json_encode($user));
      return $ret?:false;
    }
    return false;
  }
  
  public function update_password($uid, $password) 
  {
    $password = Auth::instance()->hash_password($password);
    $user = $this->get_one_by_uid($uid);
    if ($user) {
      $user['password'] = $password;
      $ret = $this->update($uid, $user);
      return $ret?:false;
    }
    return false;
  }

  public function reset_passport($email) 
  {
    $user = $this->get_one_by_email($email);
    if ($user) {
      $random = Text::random(NULL, 30);
      $html = <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
　<head>
　　<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
　　<title>普华网账户重置</title>
　　<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
　</head>
  <body>
  请点击下面链接，按照提示进行账户密码重置。
  <a href="http://account.puhua.co/resetpassport?key=$random">点击这里 http://account.puhua.co/resetpassport?key=$random</a>
  </body>
</html>
EOT;
      $headers = "MIME-Version: 1.0" . "\r\n";
      $headers .= "Content-Type: Multipart/Alternative" . "\r\n";
      $headers .= "Content-type:text/html;charset=utf-8" . "\r\n";
      $headers .= 'From: <service@puhua.co>' . "\r\n";
      if (mail($email, '普华网账户重置', $html, $headers)) {
        Kv::instance()->setx($random, $email, 3600*24);
        return true;
      }
      else {
        return '邮件系统故障！';
      }
    }
    return '账户不存在';
  }

  public function delete($uid) 
  {
    $user = $this->get_one_by_uid($uid);
    if ($user) {
      if (Kv::instance()->zdel($this->key_user_map_by_email, $user['email']) &&
        Kv::instance()->zdel($this->key_user_map_by_name, $user['name'])) {
        Kv::instance()->hdel($this->key_user, $uid);
        Kv::instance()->hdel($this->key_user_map_by_name, $user['name']);
        Kv::instance()->hdel($this->key_user_map_by_name, $user['email']);
      }
      else {
        return false;
      }
    }
    return false;
  }

  public function actived($uid, $actived = true) 
  {
    $user = $this->get_one_by_uid($uid);
    if ($user) {
      $user['actived'] = $actived;
      $ret = $this->update($uid, $user);
      return $ret?:false;
    }
    return false;
  }

}
