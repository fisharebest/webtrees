<!DOCTYPE html>
<html <?php echo WT_I18N::html_markup(); ?>>
<head>
<title>Help window</title>
<script>
/*
	function printToPage()
	{
		var pos;
		var searchStr = window.location.search;
		var searchArray = searchStr.substring(1,searchStr.length).split('&');
		var htmlOutput = '';
		for (var i=0; i<searchArray.length; i++) {
			htmlOutput += searchArray[i] + '<br>';
		}
		return(htmlOutput);
	}
*/
</script>
</head>

<body>
<b>Census Assistant - Help window</b>
<br><br>
Here will be the help for the Census Assistant window:
<p>
<script>
document.write(printToPage());
</script>
</p>
</body>
</html>
