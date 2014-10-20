<?php defined('SYSPATH') OR die('No direct script access.');

class Model_Account_Driver_Postgres extends Model_Account_Core {

  public $table = '"user"';

  public function check_exists_by_email($email) {
    $query = DB::query(Database::SELECT, 
        'select email from :table where email=:email limit 1')
      ->param(':email', $email) 
      ->paraw(':table', $this->table) 
      ->execute();
    return $query->count()?true:false;
  }

  public function check_exists_by_name($name) {
    $query = DB::query(Database::SELECT, 
        'select name from :table where name=:name limit 1')
      ->param(':name', $name) 
      ->paraw(':table', $this->table) 
      ->execute();
    return $query->count()?true:false;
  }
  
  public function get_one_by_email($email) {
    $query = DB::query(Database::SELECT, 
        'select * from :table where email=:email limit 1')
      ->param(':email', $email) 
      ->paraw(':table', $this->table) 
      ->execute();
    return $query->count()?$query->current():null;
  }
  
  public function get_one_by_name($name) {
    $query = DB::query(Database::SELECT, 
        'select * from :table where name=:name limit 1')
      ->param(':name', $name) 
      ->paraw(':table', $this->table) 
      ->execute();
    return $query->count()==0?false:$query->current();
  }

  public function get_one_by_passport($passport) {
    $query = DB::query(Database::SELECT, 
        'select * from :table where email=:passport or name=:passport limit 1')
      ->param(':passport', $passport)
      ->paraw(':table', $this->table)
      ->execute(); 
    return $query->count()?$query->current():null;
  }
  
  public function get_one_by_uid($uid)
  {
    $query = DB::query(Database::SELECT, 
                  'SELECT * FROM "'. $this->table .'" WHERE id=:id LIMIT 1')
                ->param(':id', $uid)
                ->execute();
    return $query?$query->current():false;
  }

  public function save($passport)
  {
    if (!isset($passport['name']) or empty($passport['name'])) {
      $passport['name'] = $passport['email'];
    }
    if ($this->get_one_by_email($passport['email'])
      or $this->get_one_by_name($passport['name'])) {
      return false;
    }
    $query = DB::query(Database::SELECT, 'insert into :table (email, name, password, actived, created) 
          values (:email, :name, :password, :actived, now()) returning id')
      ->param(':email', $passport['email'])
      ->param(':name', $passport['name'])
      ->param(':password', Auth::instance()->hash_password($passport['password']))
      ->param(':actived', $this->actived)
      ->paraw(':table', $this->table)
      ->execute();
    return $query->get('id', false);
  }

  public function update($uid, $passport) {}
  public function update_password($uid, $password)
  {
    $query = DB::query(Database::UPDATE, 'update "'. $this->table .'" set 
        username=:username '.(isset($passport['photo'])? ', photo=:photo':'').' where id=:id')
      ->param(':id', $passport['id'])
      ->param(':username', $passport['username'])
      ->param(':photo', $passport['photo'])
      ->param(':actived', FALSE)
      ->execute();
    return $query? TRUE: FALSE;
  }
  

  public function get_list($data)
  {
    $where = 'WHERE true ';
    $params = array();
    if ($data['actived']) {
      $where .= 'AND actived=:actived';
      $params[':actived'] = $data['actived'];
    }
    if ($keyword = trim($data['keyword'])) {
      $where .= ' AND (id=:id OR username like :keyword OR email like :keyword)';
      $params[':id'] = (int)$keyword;
      $params[':keyword'] = '%'.$keyword.'%';
    }
    $query = DB::query(Database::SELECT, 
      'SELECT * FROM "'. $this->table .'"'. $where)
                ->parameters($params)
                ->as_object()
                ->execute();
    return $query;
  }

  public function auth_update($data)
  {
    $id = $data['id'];
    $user = Session::instance()->get('accounts.manager');
    $user['auth'] = $data['auth'];
    Session::instance()->set('accounts.manager', $user);
    $auth = json_encode($data['auth']); 
    $query = DB::query(Database::UPDATE, 
                  'UPDATE  "'. $this->table .'" SET auth = :auth WHERE id=:id')
                ->param(':id', $id)
                ->param(':auth', $auth)
                ->execute();
    return $query;
  }

  public function delete($uid) {
    $query = DB::query(Database::DELETE, 
        'delete from :table WHERE id=:id limit 1')
      ->param(':id', $uid)
      ->paraw(':table', $this->table)
      ->execute();
    return $query;
  }

  public function actived($uid, $actived = null) {
    if ($actived === null) {
      $actived = $this->actived;
    }
    $query = DB::query(Database::UPDATE, 
      'update :table set actived=:actived where id=:id limit 1')
      ->param(':id', $id)
      ->param(':actived', $actived)
      ->paraw(':table', $this->table)
      ->execute();
    return $query;
  }
}
