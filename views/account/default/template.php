<?php defined('SYSPATH') or die('No direct script access.');?><!DOCTYPE html>
<head>
  <meta charset="utf-8"/>
  <title>账户</title>
  <link rel="shortcut icon" href="/media/favicon.ico?ver=0.1" /> 
  <?php echo HTML::style(STATIC_HOME_URL.'/media/pure/pure-min.css'); ?>
  <?php echo HTML::style(STATIC_HOME_URL.'/awesome/css/font-awesome.min.css'); ?>
</head>
<body>
<div class="pure-g" style="width: 960px; margin: 0 auto;">
  <div class="pure-u-1">
    <h1>账户</h1>
  </div>
  <div class="pure-u-1">
  <?php if (isset($content)): echo $content; endif; ?> 
  </div>
  <div class="pure-u-1">
  版权所有 (C) 2013
  </div>    
</div>
</body>
</html>
