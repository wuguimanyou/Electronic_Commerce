<?php
//微信JSK
require('../common/jssdk.php');
$jssdk = new JSSDK($customer_id);
$signPackage = $jssdk->GetSignPackage();
//微信JSK End
?>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.1.0.js"></script>
<script type="text/javascript">
	wx.config({
	debug: false,
	appId: '<?php echo $signPackage["appId"];?>',
	timestamp: <?php echo $signPackage["timestamp"];?>,
	nonceStr: '<?php echo $signPackage["nonceStr"];?>',
	signature: '<?php echo $signPackage["signature"];?>',
	jsApiList: [
	// 所有要调用的 API 都要加到这个列表中
	'hideOptionMenu',

	]
	});
	wx.ready(function () {
		// 在这里调用 API
		wx.hideOptionMenu();

	});
</script>