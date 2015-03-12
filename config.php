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

if(!defined('PLX_ROOT')) exit; 

	# Control du token du formulaire
	plxToken::validateFormToken($_POST);

	$aEditor = array(
		'default'=>'html',
		'markdown'=>'markdown'
	);
	$preview = PLX_PLUGINS.'markitup/markitup/templates/'.$plxPlugin->getParam('editor').'/preview.css';

	if(!empty($_POST)) {
		if (!empty($_POST['editor']) ) {
			$plxPlugin->setParam('editor', plxUtils::strCheck($_POST['editor']), 'cdata');
			$plxPlugin->saveParams();
		}
		if (!empty($_POST['css'])) {
			file_put_contents($preview, plxUtils::strCheck($_POST['css']));
		}
		header('Location: parametres_plugin.php?p=markitup');
		exit;
	}
	$css = file_get_contents($preview);
?>

<script type="text/javascript" src="<?php echo PLX_PLUGINS ?>markitup/editarea/edit_area_full.js"></script>
<script type="text/javascript" >
	editAreaLoader.init({
		id : "id_css"        // textarea id
		,syntax: "css"            // syntax to be uses for highgliting
		,start_highlight: true        // to display with highlight mode on start-up
		,language: "fr"
		,toolbar:"syntax_selection, | ,search, go_to_line, fullscreen, |, undo, redo, |, select_font,|, change_smooth_selection, highlight, reset_highlight, word_wrap, |, help"
	});
</script>
<h2><?php $plxPlugin->lang('L_TITLE') ?></h2>

<form action="parametres_plugin.php?p=markitup" method="post">
	<fieldset class="withlabel">
		<label><?php echo $plxPlugin->getLang('L_CONFIG_EDITOR') ?></label>
		<p><?php plxUtils::printSelect('editor',$aEditor, $plxPlugin->getParam('editor'));?></p>
		<p>&nbsp;</p>
		<label><?php echo $plxPlugin->getLang('L_CONFIG_CSS_PREVIEW') ?></label>
		<p><?php plxUtils::printArea('css', $css, 10, 50);?></p>

	</fieldset>
	<br />
	<?php echo plxToken::getTokenPostMethod() ?>
	<input type="submit" name="submit" value="<?php echo $plxPlugin->getLang('L_CONFIG_SAVE') ?>" />
</form>
