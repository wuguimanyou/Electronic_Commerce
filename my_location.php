<!DOCTYPE html> 
<html lang="zh-cn"> 
<head> 
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" /> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<title></title> 
<style type="text/css"> 
*{ 
height: 80%; 
} 
</style> 
<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script> 
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=wpOSBVKmcMMUL73v4i0ashoW"></script> 
<script type="text/javascript" src="http://developer.baidu.com/map/jsdemo/demo/convertor.js"></script> 
<script> 
openmap();
// $(function(){ 
// navigator.geolocation.getCurrentPosition(translatePoint); //定位 
// }); 
// function translatePoint(position){ 
// var currentLat = position.coords.latitude; 
// var currentLon = position.coords.longitude; 
// var gpsPoint = new BMap.Point(currentLon, currentLat); 
// BMap.Convertor.translate(gpsPoint, 0, initMap); //转换坐标 
// } 
// function initMap(point){ 
// //初始化地图 
// map = new BMap.Map("map"); 
// map.addControl(new BMap.NavigationControl()); 
// map.addControl(new BMap.ScaleControl()); 
// map.addControl(new BMap.OverviewMapControl()); 
// map.centerAndZoom(point, 15); 
// map.addOverlay(new BMap.Marker(point)) 
// } 
// marker.addEventListener("dragend",function(){
// 	alert('您的位置：'+r.point.lng+','+r.point.lat);
// })
	function openmap() {
		var map = new BMap.Map("map");
		var point = new BMap.Point();
		map.centerAndZoom(point, 18);
		var gc = new BMap.Geocoder();
		var realpoint;
		var address;
		var geolocation = new BMap.Geolocation();
		geolocation.getCurrentPosition(function(r) {
			if (this.getStatus() == 0) {
				var mk = new BMap.Marker(r.point);
				map.addOverlay(mk);
				map.panTo(r.point);
				realpoint = new BMap.Point(r.point.lng, r.point.lat);
				//根据经纬度获取地址
				gc.getLocation(r.point, function(rs) {
					var addComp = rs.addressComponents;
					address = addComp.province + addComp.city
							+ addComp.district + addComp.street
							+ addComp.streetNumber;
					document.getElementById('label').value = address;
					//alert("address:"+address);
					var opts = {
						width : 200, // 信息窗口宽度
						height : 60, // 信息窗口高度
						title : "您所在的位置", // 信息窗口标题
						enableMessage : false,//设置允许信息窗发送短息
						offset : new BMap.Size(6, -25)
					}
					var infoWindow = new BMap.InfoWindow(address, opts); // 创建信息窗口对象
					map.openInfoWindow(infoWindow, r.point); //开启信息窗口

				});

				//alert('您的位置：'+r.point.lng+','+r.point.lat);
				$("#longitude").attr("value", r.point.lng);
				$("#latitude").attr("value", r.point.lat);
			} else {
				alert('failed' + this.getStatus());
			}
		}, {
			enableHighAccuracy : true
		});

	}
</script> 
</head> 
<body> 
<div id="map"></div> 
</body> 
</html> 