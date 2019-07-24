<?php include_once('/var/www/html/develop/cyc/modules/crm/components/CargaLib.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>
    <title> E D U X </title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="es" />
    <link rel="icon" href="/images/icons/1.png">
    <script language="Javascript">
      var baseUrl = 'http://<?=$_SERVER['SERVER_NAME'];?>';
      var webservice = baseUrl;
    </script>
    <link rel="stylesheet" type="text/css" media="screen" href="themes/df/css/crm_cell.css" />
    <?php $aux = new CargaLib(); ?>
</head>
<body><div style="position: relative;">

    <div style="padding: 0.5em;">
      <div style="position: relative; height: auto; padding: 0em; width: 100%;">
        <?=$content;?>
      </div>
    </div>

</div></body></html>