<?php 
/**
 * Plugin markitup
 *
 * @package PLX
 * @version 1.2.3
 * @date    01/03/2013
 * original script by jay salvat at http://markitup.jaysalvat.com
 * @author  Maguire Cyril
 **/
include_once '../../libs/markdown/markdown.php'; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>markItUp! preview template</title>
<link rel="stylesheet" type="text/css" href="~/../../templates/markdown/preview.css" />
</head>
<body>
		<?php echo Markdown(str_replace('\\', '', $_POST['data'])); ?>

<!-- content -->
</body>
</html>
