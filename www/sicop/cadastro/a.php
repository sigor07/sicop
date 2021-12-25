<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sem título</title>
</head>
<script type="text/javascript">
function ow (URL, w, h){ 
    var winl = (screen.width - w) / 2;
    var wint = (screen.height - h) / 2;
    
    window.open(URL,"","width="+w+",height="+h+",top="+wint+",left="+winl+",toolbar=no, location=no, directories=no, menubar=no, scrollbars=0, resizable=0, status=0") 
}

<!--
function Babu(mypage, myname, w, h, scroll) {
var winl = (screen.width - w) / 2;
var wint = (screen.height - h) / 2;
winprops = 'height='+h+',width='+w+',top='+wint+',left='+winl+',scrollbars='+scroll+',resizable'
win = window.open(mypage, myname, winprops)
if (parseInt(navigator.appVersion) >= 4) { win.window.focus(); }
}
//-->

</script>
<body>

<p>&nbsp;</p>

<?php
    foreach ($_POST['imp'] as $valor) {
?>
        <script type="text/javascript">
            javascript: ow ('../print/of_apr.php?idaud=<?php echo $valor; ?>', '600', '600');
        </script>
<?php
        
        //if ($valor == NULL) continue;
        //echo "$valor <br>";
    }
?>
        <script type="text/javascript">
            history.go(-1);
        </script>
</body>
</html>