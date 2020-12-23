<?php session_start();?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Texas Gymnastics Conference</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<!--[if lte IE 8]><script src="css/ie/html5shiv.js"></script><![endif]-->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<!--script src="js/jquery.min.js"></script-->
		<script src="js/skel.min.js"></script>
		<script src="js/skel-layers.min.js"></script>
		<script src="js/init.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
		<link rel="stylesheet" href="tabulator-master/dist/css/tabulator.min.css">
		<noscript>
			<link rel="stylesheet" href="css/skel.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-desktop.css" />
			<link rel="stylesheet" href="css/style-wide.css" />
		</noscript>
		<!--[if lte IE 8]><link rel="stylesheet" href="css/ie/v8.css" /><![endif]-->
		<!--Date Picker-->
		<script type="text/javascript">
			var datefield=document.createElement("input")
			datefield.setAttribute("type", "date")
			if (datefield.type!="date"){ //if browser doesn't support input type="date", load files for jQuery UI Date Picker
				document.write('<link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" />\n')
				//document.write('<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"><\/script>\n')
				//document.write('<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"><\/script>\n')
			}
					
			function toggleDiv(divId){
				if (document.getElementById(divId).style.display == 'none'){
					document.getElementById(divId).style.display = 'inline';
				}else{
					document.getElementById(divId).style.display = 'none';
				}
			}
		</script>

		<style>
			td {
			    position: relative;
			}
			tr.cancelled td:before {
			    content: "";
			    position: absolute;
			    top: 50%;
					color: #ff0000;
			    left: 0;
			    border-bottom: 1px solid #111111;
			    width: 100%;
			}			
			.tabulator-col .tabulator-col-content {
				padding: 4px 4px 0px 4px !important;
				line-height: normal !important;
			}
			
		</style>
	</head>

	<!-- Facebook -->
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.0";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>

	<!--Twitter-->
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>

	<!--Youtube-->
	<script src="https://apis.google.com/js/platform.js"></script>
	
	<!--Instagram-->
	<!--script>(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.src="//x.instagramfollowbutton.com/follow.js";s.parentNode.insertBefore(g,s);}(document,"script"));</script-->
