<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Generis Portal</title>
	<link rel="shortcut icon" href="<?=BASE_WWW?>img/favicon.ico" type="image/x-icon" />
	
	<script type='text/javascript'>
		var imgPath = '<?=BASE_WWW?>img/';
	</script>
	
	<?=helpers_Scriptloader::render()?>
	
</head>
<body>
	<div id="ajax-loading">
		<img src="<?=BASE_WWW?>img/ajax-loader.gif" alt="loading" />
	</div>

	<div id="loginDialog" ></div>
	<div id="TipOfTheDay" ></div>
		
		
		<?if(get_data('errorMessage')):?>
			<div class="ui-widget ui-corner-all ui-state-error error-message">
				<?=urldecode(get_data('errorMessage'))?>
			</div>
		<?endif?>
		
			</div>

	
	<div id="footer">
		Generis®<sup>&reg;</sup>is a registered trademark
	</div>
</body>
</html>
