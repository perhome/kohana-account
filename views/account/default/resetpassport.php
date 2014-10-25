<?php defined('SYSPATH') or die('No direct script access.');?>
<div class="pure-u-1">
  <?php echo Form::open('test_resetpassport/check', array('class'=>'pure-form pure-form-aligned')); ?>
    <fieldset>
      <legend>重置账户</legend>
      <?php if (isset($message)) :?>
      <div class="pure-control-group info info-error"><?php  echo $message; ?></div>
      <?php endif;?>
      <div class="pure-control-group">
        <label for="email">你的电子邮件</label>
        <?php echo Form::input('email', Arr::get($_POST, 'email'), array('id'=>'email', 'placeholder'=>'请输入你的邮件账户')); ?>
        <?php if(isset($error['email'])): ?><span class="info-error"><?php echo $error['email']; ?></span><?php endif; ?>
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
          <button type="submit" class="pure-button pure-button-primary">确认</button>
        </div>
    </fieldset>
  </form>
</div>
