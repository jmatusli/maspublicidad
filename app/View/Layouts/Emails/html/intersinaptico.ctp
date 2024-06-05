<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<style>
	img.resize {
		height: auto;
		width:300px;
	}
	
	img.smallimage {
		height: auto;
		width:75px;
	}
</style>
<html>
<?php 
	echo "<head>";		echo "<title>".COMPANY_NAME."</title>";
	echo "</head>";
	echo "<body style='background:#8CACFF;'>";
		echo $this->fetch('content'); 
		$url="img/logo_pdf.jpg";
		//$imageurl="http://bordadosapolo.com".$this->App->assetUrl($url);
		$imageurl=$this->App->assetUrl(,array(),true);
		//echo "<p>".$imageurl."</p>";
		echo "<img src='".$imageurl."' class='resize' alt='logo'></img>";		
		echo "<p>".COMPANY_URL." &#183; ".COMPANY_MAIL."</p>";
		echo "<p>Dir:".COMPANY_ADDRESS."</p>";
		echo "<p>Tel:".COMPANY_PHONE."</p>";
		echo "<p>Este correo se generó utilizando la solución de correo de <a href='http://www.intersinaptico.com'>Intersinaptico</a></p>";
	echo "</body>";
?>
</html>