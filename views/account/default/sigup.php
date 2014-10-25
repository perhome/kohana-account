<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="pure-u-1-3">
  <br />
  <br />
  <br />
  <br />
  <p>已有账户，请 <?php echo HTML::anchor('test_sigin', '登陆', array('class'=>'pure-button pure-button-success')); ?></p>
</div><div class="pure-u-2-3">
  <?php echo Form::open('test_sigup/check', array('class'=>'pure-form pure-form-aligned')); ?>
    <fieldset>
      <legend>用户注册</legend>
      <?php if (isset($message)): ?>
      <div class="pure-control-group info info-error"><?php  echo $message; ?></div>
      <?php endif;?>
      <div class="pure-control-group">
        <label for="email">邮件</label>
        <?php echo Form::input('email', Arr::get($_POST, 'email'), array('id'=>'email', 'placeholder'=>'请输入你的邮件账户')); ?>
        <?php if(isset($error['email'])): ?><span class="info-error"><?php echo $error['email']; ?></span><?php endif; ?>
      </div>
      <div class="pure-control-group">
        <label for="password">密码</label>
        <?php echo Form::password('password', Arr::get($_POST, 'password'), array('id'=>'password', 'placeholder'=>'请输入你的密码')); ?>
        <?php if(isset($error['password'])): ?><span class="info-error"><?php echo $error['password']; ?></span><?php endif; ?>
      </div>
      <div class="pure-control-group">
        <label for="repassword">确认密码</label>
        <?php echo Form::password('repassword', Arr::get($_POST, 'repassword'), array('id'=>'repassword', 'placeholder'=>'确认密码')); ?>
        <?php if(isset($error['repassword'])): ?><span class="info-error"><?php echo $error['repassword'];  ?></span><?php endif; ?>
      </div>
      <?php if($has_captcha): ?>
      <div class="pure-control-group">
        <label for="password">验证码</label>
        <?php echo Form::input('captcha', '', array('id'=>'captcha', 'placeholder'=>'请输入验证码')); ?>
        <?php if(isset($error['captcha'])): ?><span class="info-error">验证码错误</span><?php endif; ?>
      </div>
      <div class="pure-control-group">
        <label> </label>
        <?php echo Captcha::instance('default')->html_render(); ?>
      </div>
      <?php endif; ?>
      <div class="pure-controls">
        <label for="cb" class="pure-checkbox">
        <input id="cb" type="checkbox" name="agree"> 同意并遵守  用户协议 <?php echo HTML::anchor('', '?'); ?>
        <?php if(isset($error['agree'])): ?><br /><span class="info-error">必须同意协议内容</span><?php endif; ?>
        </label>
        <?php echo Form::hidden('redirect', Arr::get($_POST, 'redirect', urlencode($redirect))); ?>
        <button type="submit" class="pure-button pure-button-primary">注册</button>
      </div>
    </fieldset>
  </form>
</div>
