<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<title>积分提现</title>
	<link rel="stylesheet" href="/devmushu/Public/Home/css/aui.min.css">
	<link rel="stylesheet" type="text/css" href="/devmushu/Public/Home/css/swiper.min.css">
	<link rel="stylesheet" type="text/css" href="/devmushu/Public/Home/css/style.css">
	<script type="text/javascript" src="/devmushu/Public/Home/js/swiper.min.js"></script>
	<script type="text/javascript" src="/devmushu/Public/Home/js/aui.js"></script>
</head>
<body>
	<header class="mui-bar mui-bar-nav">
	  <a class="mui-icon mui-icon-left-nav mui-pull-left mui-action-back"></a>
	  <h1 class="mui-title">积分提现</h1>
	</header>
	<div class="mui-content">
	    <p class="tipTitle">提现需手续费20%</p>
        <form class="mui-input-group" method="post"  enctype="multipart/form-data">
		    <div class="mui-input-row">
		        <label>提现金额</label>
		        <input type="number" class="mui-input-clear" name="num" placeholder="请输入提现金额">
		    </div>
		    <div class="mui-input-row">
		        <label>二级密码</label>
		        <input type="password" class="mui-input-password" name="pwd2" placeholder="请输入二级密码">
		    </div>
			<button class="mui-btn mui-btn-block">确认</button>
		</form>		
	</div>
</body>
</html>