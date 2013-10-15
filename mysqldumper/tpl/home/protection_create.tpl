<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>MySqlDumper</title>
	<link rel="stylesheet" type="text/css" href="css/{THEME}/style.css">
	<script language = "javascript" type = "text/javascript" src = "js/prototype/prototype.js"></script>
	<script language = "javascript" type = "text/javascript" src = "js/scriptaculous/lib/scriptaculous.js?load=effects"></script>
	<script language = "javascript" type = "text/javascript">
	/* taken from http://ajaxorized.com/examples/scriptaculous/pastrength/ */
		var updateStrength = function(pw) {
			var strength = getStrength(pw);
			var width = (100/32)*strength;
			new Effect.Morph('psStrength', {style:'width:'+width+'px', duration:'0.4'}); 
		}
			
		var getStrength = function(passwd) {
			intScore = 0;
			if (passwd.match(/[a-z]/)) // [verified] at least one lower case letter
					{
					intScore = (intScore+1)
					} if (passwd.match(/[A-Z]/)) // [verified] at least one upper case letter
					{
					intScore = (intScore+5)
					} // NUMBERS
						if (passwd.match(/\d+/)) // [verified] at least one number
						{
						intScore = (intScore+5)
						} if (passwd.match(/(\d.*\d.*\d)/)) // [verified] at least three numbers
						{
						intScore = (intScore+5)
						} // SPECIAL CHAR
						if (passwd.match(/[!,@#$%^&*?_~]/)) // [verified] at least one special character
						{
						intScore = (intScore+5)
						} if (passwd.match(/([!,@#$%^&*?_~].*[!,@#$%^&*?_~])/)) // [verified] at least two special characters
						{
						intScore = (intScore+5)
						} // COMBOS
						if (passwd.match(/[a-z]/) && passwd.match(/[A-Z]/)) // [verified] both upper and lower case
						{
						intScore = (intScore+2)
						} if (passwd.match(/\d/) && passwd.match(/\D/)) // [verified] both letters and numbers
						{
						intScore = (intScore+2)
						} // [Verified] Upper Letters, Lower Letters, numbers and special characters
						if (passwd.match(/[a-z]/) && passwd.match(/[A-Z]/) && passwd.match(/\d/) && passwd.match(/[!,@#$%^&*?_~]/))
						{
						intScore = (intScore+2)
						}
						return intScore;
			}
			function checkPasswords()
			{
				if (document.getElementById('userpass1').value!=document.getElementById('userpass2').value)
				{
					alert('{PASSWORDS_UNEQUAL}');
					return false;
				}
				else return confirm('{HTACC_CONFIRM_DELETE}');
			}	
		</script>
		<style type="text/css">
			#myinput {
				border:1px solid #000000;	
				float:left;			
			}
			
			#psContainer {
				height:18px;
				width:100px;
				border:none;
				float:left;	
				margin:0;
				background-color:#DDDDDD;
				cursor: default;
			}
			
			#psStrength {
				background-image:url('js/strength.jpg');
				height:18px;
				width:0px;
				cursor: default;
			}
		</style>	
</head>
<body class="content" onload="if (document.forms[0]) document.forms[0].username.focus();">
{HEADLINE}
<!-- BEGIN MSG -->
{MSG.TEXT}<br><br>
<!-- END MSG -->

<!-- BEGIN INPUT -->
<form method="post" action="main.php?action=schutz" onSubmit="return checkPasswords();">
<table style="width:600px;" border="0">
<tr>
	<td>{L_USERNAME}:</td>
	<td colspan="2"><input type="text" name="username" id="username" size="50" value="{INPUT.USERNAME}" class="Formtext"></td>
</tr>
<tr>
	<td>{L_PASSWORD}:</td>
	<td>
		<input type="text" name="userpass1" id="userpass1" value="{USERPASS2}" size="50" class="Formtext" 
		onkeyup="updateStrength(this.value)">
	</td>
</tr>
<tr>
	<td>{L_PASSWORD_REPEAT}:</td>
	<td>
		<input type="text" name="userpass2" id="userpass2" value="{USERPASS2}" size="50" class="Formtext">
	</td>
</tr>
<tr>
	<td>{L_PASSWORD_STRENGTH}:</td>
	<td>
		<div id="psContainer" class="Formtext" style="cursor:default;"><div id = "psStrength"></div></div>
	</td>
</tr>
<tr>
	<td>{L_ENCRYPTION_TYPE}:</td>
	<td>
	<table>
		<tr>
			<td>
				<input class="radio" type="radio" name="type" id="type0" value="0"{INPUT.TYPE0_CHECKED}>
			</td>
			<td>
				<label for="type0">{L_HTACC_CRYPT}</label>
			</td>
		</tr>
		<tr>
			<td>
				<input class="radio" type="radio" name="type" id="type1" value="1"{INPUT.TYPE1_CHECKED}>
			</td>
			<td>
				<label for="type1">{L_HTACC_MD5}</label>
			</td>
		</tr>
		<tr>
			<td>
				<input class="radio" type="radio" name="type" id="type3" value="3"{INPUT.TYPE3_CHECKED}>
			</td>
			<td>
				<label for="type3">{L_HTACC_SHA1}</label>
			</td>
		</tr>
		<tr>
			<td>
				<input class="radio" type="radio" name="type" id="type2" value="2"{INPUT.TYPE2_CHECKED}>
			</td>
			<td>
				<label for="type2">{L_HTACC_NO_ENCRYPTION}</label>
			</td>
		</tr>
	</table>
	</td>
</tr>
<tr>
	<td class="center" colspan="2">
	<br>
		<input type="submit" class="Formbutton" name="htaccess" value="{L_HTACC_CREATE}">
		<br><br>
	</td>
</tr>
</table>
</form>
<!-- END INPUT -->

<!-- BEGIN CREATE_SUCCESS -->
	<strong>{L_HTACC_CONTENT} .htaccess:</strong><br><br>
	{CREATE_SUCCESS.HTACCESS}

	<br><br><strong>{L_HTACC_CONTENT} .htpasswd:</strong><br><br>
	{CREATE_SUCCESS.HTPASSWD}
	<br><br>
	<a href="main.php" class="Formbutton">{L_HOME}</a>
<!-- END CREATE_SUCCESS -->

<!-- BEGIN CREATE_ERROR -->
<p class="error"><STRONG>{L_HTACC_CREATE_ERROR}:</strong></p>

	<strong>{L_HTACC_CONTENT} .htaccess:</strong><br><br>
	<textarea cols="80" rows="5">{CREATE_ERROR.HTACCESS}</textarea>

	<br><strong>{L_HTACC_CONTENT} .htpasswd:</strong><br><br>
	<textarea cols="80" rows="2">{CREATE_ERROR.HTPASSWD}</textarea>
	
	<br><br>
	<a href="main.php" class="Formbutton">{L_HOME}</a>
<!-- END CREATE_ERROR -->
