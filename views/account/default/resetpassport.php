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
        <div class="pure-controls">
          <button type="submit" class="pure-button pure-button-primary">确认</button>
        </div>
    </fieldset>
  </form>
</div>
