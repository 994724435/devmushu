<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1,maximum-scale=1,user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	<title>我的下级</title>
	<link rel="stylesheet" href="/devmushu/Public/Home/css/aui.min.css">
	<link rel="stylesheet" type="text/css" href="/devmushu/Public/Home/css/swiper.min.css">
	<link rel="stylesheet" type="text/css" href="/devmushu/Public/Home/css/style.css">
	<script type="text/javascript" src="/devmushu/Public/Home/js/swiper.min.js"></script>
	<script type="text/javascript" src="/devmushu/Public/Home/js/aui.js"></script>
	<script type="text/javascript" src="/devmushu/Public/Home/js/jquery-3.1.1.min.js"></script>
	
</head>
<body>
	<header class="mui-bar mui-bar-nav">
	  <a class="mui-icon mui-icon-left-nav mui-pull-left mui-action-back"></a>
	  <h1 class="mui-title">我的下级</h1>
	</header>
	<div class="mui-content">
	    <table class="my_group_table">
	    	<tr>
	    		<th>下级账号</th>
	    		<th>下线等级</th>
	    		<th>注册时间</th>
	    	</tr>

				<!--<ul class="flex">-->
					<!--<li><?php echo ($v["reson"]); ?></li>-->
					<!--<li><?php echo ($v["times"]); ?>级下线<?php if($v["reson"] == '下级购买MIF'): echo ($v["orderid"]); ?> 共<?php echo ($v["cont"]); ?>个<?php endif; ?></li>-->
					<!--<li> 上级编号<?php echo ($v["shang"]); ?></li>-->
				<!--</ul>-->

			<?php if(is_array($res)): foreach($res as $key=>$v): ?><tr>
	    		<td>177725689</td>
	    		<td><?php echo ($v["times"]); ?>级下线</td>
	    		<td>2016-10-10</td>
	    	</tr><?php endforeach; endif; ?>
	    </table>
	</div>
</body>
</html>