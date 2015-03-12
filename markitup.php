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
class markitup extends plxPlugin {

	private $editor = 'default';

	/**
	 * Constructeur de la classe markitup
	 *
	 * @param	default_lang	langue par défaut utilisée par PluXml
	 * @return	null
	 * @author	Maguire Cyril
	 **/
	public function __construct($default_lang) {

		# Appel du constructeur de la classe plxPlugin (obligatoire)
		parent::__construct($default_lang);
		
		# Déclarations des hooks
		//$this->addHook('ThemeEndHead', 'addMarkitup');
		$this->setConfigProfil(PROFIL_ADMIN);
		if(!preg_match('/(article)/', basename($_SERVER['SCRIPT_NAME']))) {
			$this->editor = 'default';
		} else {
			$this->editor = $this->getParam('editor');
		}	
		$this->addHook('AdminTopEndHead', 'addMarkitup');
		$this->addHook('AdminMediasPrepend', 'AdminMediasPrepend');
		$this->addHook('AdminMediasFoot', 'AdminMediasFoot');
		$this->addHook('AdminArticlePrepend', 'AdminArticlePrepend');	
		$this->addHook('AdminArticleParseData', 'AdminArticleParseData');	
	}

	/**
	 * Méthode permettant d'activer l'éditeur par défaut
	 * 
	 * @return void
	 * @author Cyril MAGUIRE
	 */
	public function onActivate() {
		$this->setParam('editor', 'default', 'cdata');
		$this->saveParams();
	}

	/**
	 * Méthode qui ajoute l'insertion du fichier javascript de markitup dans la partie <head> du site
	 *
	 * @return	stdio
	 * @author	Maguire Cyril
	 **/	
	public function addMarkitup() {
		echo "\t".'<script type="text/javascript">
				/* <![CDATA[ */
				!window.jQuery && document.write(\'<script  type="text/javascript" src="'.PLX_PLUGINS.'markitup/markitup/libs/jquery/jquery-1.8.0.min.js"><\/script>\');
				/* !]]> */
			</script>'."\n";
		echo "\t".'<link rel="stylesheet" type="text/css" href="'.PLX_PLUGINS.'markitup/markitup/skins/'.$this->editor.'/style.css" />'."\n";
		echo "\t".'<link rel="stylesheet" type="text/css" href="'.PLX_PLUGINS.'markitup/markitup/sets/'.$this->editor.'/style.css" />'."\n";
		echo "\t".'<link rel="stylesheet" type="text/css" href="'.PLX_PLUGINS.'markitup/markitup/libs/simplemodal/simplemodal.css" />'."\n";
		echo "\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'markitup/markitup/libs/markitup/jquery.markitup.js"></script>'."\n";
		echo "\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'markitup/markitup/sets/'.$this->editor.'/set.js"></script>'."\n";
		echo "\t".'<script type="text/javascript" src="'.PLX_PLUGINS.'markitup/markitup/libs/simplemodal/simplemodal.js"></script>'."\n";
		echo "\t".'<script type="text/javascript" >'."\n";
		    echo "\t\t".'$(document).ready(function() {'."\n";
		    echo "\t\t".'$("#id_chapo").markItUp(mySettings);'."\n";
		    echo "\t\t".'$("#id_content").markItUp(mySettings);'."\n";
		    echo "\t\t".'});'."\n";
		    echo "\t\t".'</script>'."\n";
	}

	/**
	 * Méthode qui transforme les données du formulaire en html
	 * 
	 * @return string 	les données au format html
	 * @author Cyril MAGUIRE
	 */
	public function AdminArticlePrepend() {
		if ($this->getParam('editor') == 'markdown') {
			echo '<?php
			require PLX_PLUGINS."markitup/markitup/libs/markdown/markdown.php";
			if (isset($_POST[\'content\']) && isset($_POST[\'chapo\'])) {
				$_POST[\'content\'] = Markdown($_POST[\'content\']);
				$_POST[\'chapo\'] = Markdown($_POST[\'chapo\']);
			}
			?>';
		}
	}

	/**
	 * Méthode qui transforme les données html au format markdown
	 * 
	 * @return string 	les données au format markdown
	 * @author Cyril MAGUIRE
	 */
	public function AdminArticleParseData() {
		if ($this->getParam('editor') == 'markdown') {
			echo '<?php
			require PLX_PLUGINS."markitup/markitup/libs/markdownify/markdownify_extra.php";
				$md = new Markdownify(MDFY_LINKS_EACH_PARAGRAPH, MDFY_BODYWIDTH, MDFY_KEEPHTML);
		
				if (ini_get(\'magic_quotes_gpc\')) {
					$chapo = stripslashes($chapo);
					$content = stripslashes($content);
				}
				$chapo = $md->parseString($chapo);
				$content = $md->parseString($content);	
			?>';
		}
	}

	public function AdminMediasPrepend() {
		if (isset($_GET['id']) && $_GET['id'] == 'content') {//markItUpId_
			$plxMotor = plxMotor::getInstance();
			echo '<?php

# Recherche du type de medias à afficher via la session
if(empty($_SESSION[\'medias\']) OR !empty($_POST[\'btn_images\'])) {
	$_SESSION[\'medias\'] = $plxAdmin->aConf[\'images\'];
	$_SESSION[\'folder\'] = \'\';
}
elseif(!empty($_POST[\'btn_documents\'])) {
	$_SESSION[\'medias\'] = $plxAdmin->aConf[\'documents\'];
	$_SESSION[\'folder\'] = \'\';
}
elseif(!empty($_POST[\'folder\'])) {
	$_SESSION[\'currentfolder\']= (isset($_SESSION[\'folder\'])?$_SESSION[\'folder\']:\'\');
	$_SESSION[\'folder\'] = ($_POST[\'folder\']==\'.\'?\'\':$_POST[\'folder\']);
}
# Nouvel objet de type plxMedias
if($plxAdmin->aConf[\'userfolders\'] AND $_SESSION[\'profil\']==PROFIL_WRITER)
	$plxMedias = new plxMedias(PLX_ROOT.$_SESSION[\'medias\'].$_SESSION[\'user\'].\'/\',$_SESSION[\'folder\']);
else
	$plxMedias = new plxMedias(PLX_ROOT.$_SESSION[\'medias\'],$_SESSION[\'folder\']);

#----

if(!empty($_POST[\'btn_newfolder\']) AND !empty($_POST[\'newfolder\'])) {
	$newdir = plxUtils::title2filename(trim($_POST[\'newfolder\']));
	if($plxMedias->newDir($newdir)) {
		$_SESSION[\'folder\'] = $_SESSION[\'folder\'].$newdir.\'/\';
	}
	header(\'Location: medias.php?id='.$_GET['id'].'&editor='.$this->editor.'\');ob_start();
	exit;
}
elseif(!empty($_POST[\'btn_delete\']) AND !empty($_POST[\'folder\']) AND $_POST[\'folder\']!=\'.\') {
	if($plxMedias->deleteDir($_POST[\'folder\'])) {
		$_SESSION[\'folder\'] = \'\';
	}
	header(\'Location: medias.php?id='.$_GET['id'].'&editor='.$this->editor.'\');ob_start();
	exit;
}
elseif(!empty($_POST[\'btn_upload\'])) {
	$plxMedias->uploadFiles($_FILES, $_POST);
	header(\'Location: medias.php?id='.$_GET['id'].'&editor='.$this->editor.'\');ob_start();
	exit;
}
elseif(isset($_POST[\'selection\']) AND ($_POST[\'selection\'][0] == \'delete\' OR $_POST[\'selection\'][1] == \'delete\') AND isset($_POST[\'idFile\'])) {
	$plxMedias->deleteFiles($_POST[\'idFile\']);
	header(\'Location: medias.php?id='.$_GET['id'].'&editor='.$this->editor.'\');ob_start();
	exit;
}
elseif(isset($_POST[\'selection\']) AND ($_POST[\'selection\'][0] == \'move\' OR $_POST[\'selection\'][1] == \'move\') AND isset($_POST[\'idFile\'])) {
	$plxMedias->moveFiles($_POST[\'idFile\'], $_SESSION[\'currentfolder\'], $_POST[\'folder\']);
	header(\'Location: medias.php?id='.$_GET['id'].'&editor='.$this->editor.'\');ob_start();
	exit;
}
elseif(isset($_POST[\'selection\']) AND ($_POST[\'selection\'][0] == \'thumbs\' OR $_POST[\'selection\'][1] == \'thumbs\') AND isset($_POST[\'idFile\'])) {
	$plxMedias->makeThumbs($_POST[\'idFile\'], $plxAdmin->aConf[\'miniatures_l\'], $plxAdmin->aConf[\'miniatures_h\']);
	header(\'Location: medias.php?id='.$_GET['id'].'&editor='.$this->editor.'\');ob_start();
	exit;
}

# Tri de l\'affichage des fichiers
if(isset($_POST[\'sort\']) AND !empty($_POST[\'sort\'])) {
	$sort = $_POST[\'sort\'];
} else {
	$sort = isset($_SESSION[\'sort_medias\']) ? $_SESSION[\'sort_medias\'] : \'title_asc\';
}

$sort_title = \'title_desc\';
$sort_date = \'date_desc\';
switch ($sort) {
	case \'title_asc\':
		$sort_title = \'title_desc\';
		usort($plxMedias->aFiles, create_function(\'$b, $a\', \'return strcmp($a["name"], $b["name"]);\'));
		break;
	case \'title_desc\':
		$sort_title = \'title_asc\';
		usort($plxMedias->aFiles, create_function(\'$a, $b\', \'return strcmp($a["name"], $b["name"]);\'));
		break;
	case \'date_asc\':
		$sort_date = \'date_desc\';
		usort($plxMedias->aFiles, create_function(\'$b, $a\', \'return strcmp($a["date"], $b["date"]);\'));
		break;
	case \'date_desc\':
		$sort_date = \'date_asc\';
		usort($plxMedias->aFiles, create_function(\'$a, $b\', \'return strcmp($a["date"], $b["date"]);\'));
		break;
}
$_SESSION[\'sort_medias\']=$sort;

# Contenu des 2 listes déroulantes
$selectionList = array(\'\' =>L_FOR_SELECTION, \'move\'=>L_PLXMEDIAS_MOVE_FOLDER, \'thumbs\'=>L_MEDIAS_RECREATE_THUMB, \'-\'=>\'-----\', \'delete\' =>L_DELETE);
# On inclut le header
include(dirname(__FILE__).\'/top.php\');
?>
			<script type="text/javascript" src="<?php echo PLX_CORE ?>lib/multifiles.js"></script>
			<script type="text/javascript">
			function toggle_divs(){
				var medias_back = document.getElementById(\'medias_back\');
				var uploader = document.getElementById(\'files_uploader\');
				var manager = document.getElementById(\'files_manager\');
				if(uploader.style.display == \'none\') {
					medias_back.style.display = \'block\';
					uploader.style.display = \'block\';
					manager.style.display = \'none\';
				} else {
					medias_back.style.display = \'none\';
					uploader.style.display = \'none\';
					manager.style.display = \'block\';
				}
			}
			</script>

			<style type="text/css">
				body {padding:0;}
				div#sidebar {display:none;width:0;height:0;}
				#content {margin-left:0;}
			</style>
			<script type="text/javascript">
				function insertPicture(name, path, title)	{
					path = path.replace(\'../../data/\',\''.$plxMotor->racine.'data/\');
					try {';
					if(isset($_GET['editor']) && $_GET['editor'] == 'markdown') :
					echo 'parent.$.markItUp( { replaceWith:\'![[![Texte alternatif:!:\'+name+\']!]]([![Url:!:\'+path+\']!] "[![Titre:!:\'+title+\']!]")\' } );';
					else :
					echo 'parent.$.markItUp( { replaceWith:\'<img alt="[![Texte alternatif:!:\'+name+\']!]" src="[![Url:!:\'+path+\']!]" title="[![Titre:!:\'+title+\']!]" />\' } );';
					endif;
					echo 'parent.$.modal.close();
					} catch(e) {
						alert("No markItUp! Editor found");
					}
				}
				function insertDocument(name, path, title)	{
					path = path.replace(\'../../data/\',\''.$plxMotor->racine.'data/\');
					try {';
					if(isset($_GET['editor']) && $_GET['editor'] == 'markdown') :
					echo 'parent.$.markItUp( { replaceWith:\'[[![Texte alternatif:!:\'+name+\']!]]([![Url:!:\'+path+\']!] "[![Titre:!:\'+title+\']!]")\' } );';
					else :
					echo 'parent.$.markItUp( { replaceWith:\'<a href="[![Url:!:\'+path+\']!]" title="[![Titre:!:\'+title+\']!]">[![Texte alternatif:!:\'+name+\']!]</a>\' } );';
					endif;
					echo 'parent.$.modal.close();
					} catch(e) {
						alert("No markItUp! Editor found");
					}
				}
			</script>
<p id="medias_back" style="display:none"><a href="javascript:void(0)" onclick="toggle_divs();return false"><?php echo L_MEDIAS_BACK ?></a></p>

<h2><?php echo L_MEDIAS_TITLE ?></h2>

<p class="path"><?php echo L_MEDIAS_DIRECTORY.\' : /\'.plxUtils::strCheck(basename($_SESSION[\'medias\']).\'/\'.$_SESSION[\'folder\']) ?></p>

<div id="files_uploader" style="display:none">
	<p style="margin-bottom:15px"><?php echo L_MEDIAS_MAX_UPOLAD_FILE ?> : <?php echo $plxMedias->maxUpload[\'display\'] ?></p>
	<form action="medias.php?id='.$_GET['id'].'&editor='.$this->editor.'" method="post" id="form_uploader" class="form_uploader" enctype="multipart/form-data">
		<div class="manager">
			<input id="selector" type="file" name="selector" />
			<div class="files_list" id="files_list"></div>
			<?php if($_SESSION[\'medias\']==$plxAdmin->aConf[\'images\']) : ?>
			<div class="options1">
				<?php echo L_MEDIAS_RESIZE ?>&nbsp;:&nbsp;
				<ul>
					<li><input type="radio" name="resize" value="" />&nbsp;<?php echo L_MEDIAS_RESIZE_NO ?></li>
					<?php
						foreach($img_redim as $redim) {
							echo \'<li><input type="radio" name="resize" value="\'.$redim.\'" />&nbsp;\'.$redim.\'</li>\';
						}
					?>
					<li>
						<input type="radio" checked="checked" name="resize" value="<?php echo intval($plxAdmin->aConf[\'images_l\' ]).\'x\'.intval($plxAdmin->aConf[\'images_h\' ]) ?>" />&nbsp;<?php echo intval($plxAdmin->aConf[\'images_l\' ]).\'x\'.intval($plxAdmin->aConf[\'images_h\' ]) ?>
						&nbsp;&nbsp;(<a href="parametres_affichage.php"><?php echo L_MEDIAS_MODIFY ?>)</a>
					</li>
					<li>
						<input type="radio" name="resize" value="user" />&nbsp;
						<input type="text" size="2" maxlength="4" name="user_w" />&nbsp;x&nbsp;
						<input type="text" size="2" maxlength="4" name="user_h" />
					</li>
				</ul>
			</div>
			<div class="options2">
				<?php echo L_MEDIAS_THUMBS ?>&nbsp;:&nbsp;
				<ul>
					<li>
						<?php $sel = (!$plxAdmin->aConf[\'thumbs\'] ? \' checked="checked"\' : \'\') ?>
						<input<?php echo $sel ?> type="radio" name="thumb" value="" />&nbsp;<?php echo L_MEDIAS_THUMBS_NONE ?>
					</li>
					<?php
						foreach($img_thumb as $thumb) {
							echo \'<li><input type="radio" name="thumb" value="\'.$thumb.\'" />&nbsp;\'.$thumb.\'</li>\';
						}
					?>
					<li>
						<?php $sel = ($plxAdmin->aConf[\'thumbs\'] ? \' checked="checked"\' : \'\') ?>
						<input<?php echo $sel ?> type="radio" name="thumb" value="<?php echo intval($plxAdmin->aConf[\'miniatures_l\' ]).\'x\'.intval($plxAdmin->aConf[\'miniatures_h\' ]) ?>" />&nbsp;<?php echo intval($plxAdmin->aConf[\'miniatures_l\' ]).\'x\'.intval($plxAdmin->aConf[\'miniatures_h\' ]) ?>
						&nbsp;&nbsp;(<a href="parametres_affichage.php"><?php echo L_MEDIAS_MODIFY ?>)</a>
					</li>
					<li>
						<input type="radio" name="thumb" value="user" />&nbsp;
						<input type="text" size="2" maxlength="4" name="thumb_w" />&nbsp;x&nbsp;
						<input type="text" size="2" maxlength="4" name="thumb_h" />
					</li>
				</ul>
			</div>
			<?php endif; ?>
			<?php eval($plxAdmin->plxPlugins->callHook(\'AdminMediasUpload\')) # Hook Plugins ?>
			<input class="button submit" type="submit" name="btn_upload" id="btn_upload" value="<?php echo L_MEDIAS_SUBMIT_FILE ?>" />
			<?php echo plxToken::getTokenPostMethod() ?>
		</div>
	</form>
	<script type="text/javascript">
		var multi_selector = new MultiSelector(document.getElementById(\'files_list\'), -1, \'<?php echo $plxAdmin->aConf[\'racine\'] ?>\');
		multi_selector.addElement(document.getElementById(\'selector\'));
	</script>
	<div class="clearer"></div>
</div>

<div id="files_manager">
	<form action="medias.php?id='.$_GET['id'].'&editor='.$this->editor.'" method="post" id="form_medias" class="form_medias">
		<div class="manager">
			<div class="create">
				<?php echo L_MEDIAS_NEW_FOLDER ?>&nbsp;:&nbsp;
				<input class="newfolder" id="id_newfolder" type="text" name="newfolder" value="" maxlength="50" size="10" />
				<input class="button new" type="submit" name="btn_newfolder" value="<?php echo L_MEDIAS_CREATE_FOLDER ?>" />
			</div>
			<input class="button submit<?php echo basename($_SESSION[\'medias\'])==\'images\'?\' select\':\'\' ?>" type="submit" name="btn_images" value="<?php echo L_MEDIAS_IMAGES ?>" />
			<input class="button submit<?php echo basename($_SESSION[\'medias\'])==\'documents\'?\' select\':\'\' ?>" type="submit" name="btn_documents" value="<?php echo L_MEDIAS_DOCUMENTS ?>" />
			<input class="button submit" type="submit" onclick="toggle_divs();return false" value="<?php echo L_MEDIAS_ADD_FILE ?>" />
			<?php echo plxToken::getTokenPostMethod() ?>
		</div>

		<div class="browser">
			<?php echo L_MEDIAS_FOLDER ?>&nbsp;:&nbsp;
			<?php echo $plxMedias->contentFolder() ?>&nbsp;
			<input class="button submit" type="submit" name="btn_ok" value="<?php echo L_OK ?>" />&nbsp;
			<?php if(!empty($_SESSION[\'folder\'])) : ?>
			<input class="button delete" type="submit" name="btn_delete" onclick="Check=confirm(\'<?php echo L_MEDIAS_DELETE_FOLDER_CONFIRM ?>\');if(Check==false) return false;" value="<?php echo L_MEDIAS_DELETE_FOLDER ?>" />
			<?php endif; ?>
		</div>
		<div class="files">
			<p style="margin-bottom:15px">
				<?php plxUtils::printSelect(\'selection[]\', $selectionList, \'\', false, \'\', false) ?>
				<input class="button submit" type="submit" name="btn_action" value="<?php echo L_OK ?>" />
			</p>
			<table class="table">
			<thead>
			<tr>
				<th class="checkbox"><input type="checkbox" onclick="checkAll(this.form, \'idFile[]\')" /></th>
				<th class="image">&nbsp;</th>
				<th><a href="javascript:void(0)" class="hcolumn" onclick="document.forms[1].sort.value=\'<?php echo $sort_title ?>\';document.forms[1].submit();return true;"><?php echo L_MEDIAS_FILENAME ?></a></th>
				<th class="infos"><?php echo L_MEDIAS_EXTENSION ?></th>
				<th class="infos"><?php echo L_MEDIAS_FILESIZE ?></th>
				<th class="infos"><?php echo L_MEDIAS_DIMENSIONS ?></th>
				<th class="date"><a href="javascript:void(0)" class="hcolumn" onclick="document.forms[1].sort.value=\'<?php echo $sort_date ?>\';document.forms[1].submit();return true;"><?php echo L_MEDIAS_DATE ?></a></th>
			</tr>
			</thead>
			<tbody>
			<?php
			# Initialisation de l\'ordre
			$num = 0;
			# Si on a des fichiers
			if($plxMedias->aFiles) {
				foreach($plxMedias->aFiles as $v) { # Pour chaque fichier
					$ordre = ++$num;
					echo \'<tr class="line-\'.($num%2).\'">\';
					echo \'<td><input type="checkbox" name="idFile[]" value="\'.$v[\'name\'].\'" /></td>\';
					
					echo "<td class=\"icon\"><a onclick=\""; echo basename($_SESSION[\'medias\'])==\'documents\'? "insertDocument(\'".plxUtils::strCheck($v[\'name\'])."\', \'".$v[\'path\']."\', \'\');": "insertPicture(\'".plxUtils::strCheck($v[\'name\'])."\', \'".$v[\'path\']."\', \'\');"; echo "self.close();return false;\" title=\"".plxUtils::strCheck($v[\'name\'])."\" href=\"".$v[\'path\']."\"><img alt=\"\" src=\"".$v[\'.thumb\']."\" class=\"thumb\" /></a></td>";

					echo \'<td>\';
					echo "<a onclick=\""; echo basename($_SESSION[\'medias\'])==\'documents\'? "insertDocument(\'".plxUtils::strCheck($v[\'name\'])."\', \'".$v[\'path\']."\', \'\');": "insertPicture(\'".plxUtils::strCheck($v[\'name\'])."\', \'".$v[\'path\']."\', \'\');"; echo "self.close();return false;\" title=\"".plxUtils::strCheck($v[\'name\'])."\" href=\"".$v[\'path\']."\">".plxUtils::strCheck($v[\'name\'])."</a><br />";
					if($v[\'thumb\']) {
						echo "<a onclick=\"insertPicture(\'".L_MEDIAS_THUMB." : ".plxUtils::strCheck($v[\'name\'])."\', \'".plxUtils::thumbName($v[\'path\'])."\', \'\');self.close();return false;\" title=\"".L_MEDIAS_THUMB." : ".plxUtils::strCheck($v[\'name\'])."\" href=\"".plxUtils::thumbName($v[\'path\'])."\">".L_MEDIAS_THUMB."</a> : ".$v[\'thumb\'][\'infos\'][0]." x ".$v[\'thumb\'][\'infos\'][1]. " (".plxUtils::formatFilesize($v[\'thumb\'][\'filesize\']).")";
					}
					echo \'</td>\';
					echo \'<td>\'.strtoupper($v[\'extension\']).\'</td>\';
					echo \'<td>\'.plxUtils::formatFilesize($v[\'filesize\']).\'</td>\';
					$dimensions = \'&nbsp;\';
					if(isset($v[\'infos\']) AND isset($v[\'infos\'][0]) AND isset($v[\'infos\'][1])) {
						$dimensions = $v[\'infos\'][0].\' x \'.$v[\'infos\'][1];
					}
					echo \'<td>\'.$dimensions.\'</td>\';
					echo \'<td>\'.plxDate::formatDate(plxDate::timestamp2Date($v[\'date\'])).\'</td>\';
					echo \'</tr>\';					
				}
			}
			else echo \'<tr><td colspan="7" class="center">\'.L_MEDIAS_NO_FILE.\'</td></tr>\';
			?>
			</tbody>
			</table>
			<p>
				<?php plxUtils::printSelect(\'selection[]\', $selectionList , \'\', false, \'\', false) ?>
				<input class="button submit" type="submit" name="btn_action" value="<?php echo L_OK ?>" />
				<input type="hidden" name="sort" value="" />
			</p>
		</div>
	</form>
	<div class="clear"></div>
</div>
<?php ob_start();?>';
		}
			
	}

	public function AdminMediasFoot() {
		if (isset($_GET['id']) && $_GET['id'] == 'content') {//markItUpId_
			echo '<?php ob_end_clean();?>';
		}
	}
	
}
?>