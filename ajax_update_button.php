<?php
header("Cache-Control: no-cache, must-revalidate"); 
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(dirname(__FILE__).'/../../classes/db/Db.php');

function traduct($string, $lang = 2) {
	global $_MODULES, $_MODULE, $cookie;
	
	$id_lang = $lang;

	$file = _PS_MODULE_DIR_.'navmegadrownevo/'.Language::getIsoById($id_lang).'.php';
	if (file_exists($file) AND include_once($file))
		$_MODULES = !empty($_MODULES) ? array_merge($_MODULES, $_MODULE) : $_MODULE;

	if (!is_array($_MODULES))
		return (str_replace('"', '&quot;', $string));

	$source  = strtolower($specific);
	$string2 = str_replace('\'', '\\\'', $string);
	$currentKey = '<{navmegadrownevo}'._THEME_NAME_.'>navmegadrownevo'.$source.'_'.md5($string2);
	$defaultKey = '<{navmegadrownevo}prestashop>navmegadrownevo'.$source.'_'.md5($string2);

	if (key_exists($currentKey, $_MODULES))
		$ret = stripslashes($_MODULES[$currentKey]);
	elseif (key_exists($defaultKey, $_MODULES))
		$ret = stripslashes($_MODULES[$defaultKey]);
	else
		$ret = $string;
	return str_replace('"', '&quot;', $ret);
}
function displayFlagsMD($languages, $defaultLanguage, $ids, $id, $return = false)
{
		if (sizeof($languages) == 1)
			return false;
		$defaultIso = Language::getIsoById($defaultLanguage);
		$output = '
		<div class="display_flags" style="float: left">
			<img src="../img/l/'.$defaultLanguage.'.jpg" class="pointer" id="language_current_'.$id.'" onclick="showLanguagesMD(\''.$id.'\');" alt="" />
		</div>
		<div id="languages_'.$id.'" class="language_flags">';
		foreach ($languages as $language)
			$output .= '<img src="../img/l/'.intval($language['id_lang']).'.jpg" class="pointer" alt="'.$language['name'].'" title="'.$language['name'].'" onclick="changeLanguageMD(\''.$id.'\', \''.$ids.'\', '.$language['id_lang'].', \''.$language['iso_code'].'\');" /> ';
		$output .= '</div>';

		if ($return)
			return $output;
		echo $output;
}
if(isset($_GET['action'])) {
	switch($_GET['action']) {	
		case "CustomMenuAdd":
			$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
			$languages = Language::getLanguages();
			$iso = Language::getIsoById($defaultLanguage);
			Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'admevo_custom_menu  
				  (id_button, num_column, num_ligne) VALUES 
				  ('.$_GET['idButton'].', 10, 10)
				  ');	
			$IdCustom = Db::getInstance()->Insert_ID();
			foreach ($languages as $language) {
				Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'admevo_custom_menu_lang  
					  (id_button, id_custom, id_lang, name_menu) VALUES 
					  ('.$_GET['idButton'].', '.$IdCustom.', '.$language['id_lang'].', "'.$_GET['MenuName_'.$language['id_lang']].'")
					  ');	
			}
		break;
		case "CustomMenuDelete":
			Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'admevo_custom_menu  
				  WHERE id_button = '.$_GET['idButton'].' 
				  AND (id_parent = '.$_GET['idCustomMenu'].' 
				  OR id_custom = '.$_GET['idCustomMenu'].')
				  ');	
		break;
		case "CustomMenuSave":
			$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
			$languages = Language::getLanguages();
			$iso = Language::getIsoById($defaultLanguage);
			Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'admevo_custom_menu SET 
				  num_column='.$_GET['columnMenu'].', 
				  num_ligne='.$_GET['lineMenu'].', link="'.addslashes($_GET['linkMenu']).'" 
				  WHERE id_button = '.$_GET['idButton'].' AND id_custom = '.$_GET['idCustomMenu'].' 
				  ');	
			Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'admevo_custom_menu_lang 
									   WHERE id_button = '.$_GET['idButton'].' AND id_custom = '.$_GET['idCustomMenu']);
			foreach ($languages as $language) {
				Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'admevo_custom_menu_lang 
					  (id_button, id_custom, id_lang, name_menu) VALUES 
					  ('.$_GET['idButton'].', '.$_GET['idCustomMenu'].', 
					   '.$language['id_lang'].', "'.addslashes($_GET['nameMenu_'.$language['id_lang']]).'")');	
			}
		break;
		case "CustomSubMenuAdd":
			$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
			$languages = Language::getLanguages();
			$iso = Language::getIsoById($defaultLanguage);
			Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'admevo_custom_menu  
				  (id_button, id_parent, name_menu, num_column, num_ligne) VALUES 
				  ('.$_GET['idButton'].', '.$_GET['idCustomMenu'].', "'.$_GET['SubMenuName'].'", 10, 10)
				  ');	
			$IdCustom = Db::getInstance()->Insert_ID();
			foreach ($languages as $language) {
				Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'admevo_custom_menu_lang  
					  (id_button, id_custom, id_lang, name_menu) VALUES 
					  ('.$_GET['idButton'].', '.$IdCustom.', '.$language['id_lang'].', "'.$_GET['SubMenuName_'.$language['id_lang']].'")
					  ');	
			}
		break;
		
		case "DetailMenu":
			$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
			$languages = Language::getLanguages();
			$iso = Language::getIsoById($defaultLanguage);
			$MenuHtml = '';
			
			$MenuDetail = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'admevo_custom_menu 
						    	WHERE id_button='.$_GET['idButton'].' AND id_parent=0  
								ORDER BY num_column, num_ligne');
			if(sizeof($MenuDetail)) {
				$MenuHtml .= '<table cellpadding="2" cellspacing="0" border="0" class="table" width="100%">';
				$MenuHtml .= "<TR>";
				$MenuHtml .= '<TH width="30">ID</TH>';
				$MenuHtml .= '<TH>'.traduct('Menu').'</TH>';
				$MenuHtml .= '<TH width="66">'.traduct('Actions').'</TH>';
				$MenuHtml .= '</TR>';
				foreach($MenuDetail as $KMd=>$ValMd) {
					$MenuHtml .= '<TR>';
					$MenuHtml .= '<TD align="center">'.$ValMd['id_custom'].'</TD>';
					$MenuHtml .= '<TD>';
					$MenuHtml .= '<table style="font-size:10px" width="100%"><TR><TD width="100">'.traduct('Name', $_GET['idLang']).'&nbsp;:&nbsp;</TD><TD width="290">&nbsp;';
					foreach ($languages as $language) {
						$LangMenu = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'admevo_custom_menu_lang  
						WHERE id_button='.$ValMd['id_button'].' 
						AND id_custom='.$ValMd['id_custom'].' 
						AND id_lang='.$language['id_lang']);
						$MenuHtml .= '<div id="DivMenuLang'.$ValMd['id_custom'].'_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left" class="divLang">';
						$MenuHtml .= '<input type="text" id="MenuName_'.$ValMd['id_button'].'_'.$ValMd['id_custom'].'_'.$language['id_lang'].'" name="MenuName_'.$ValMd['id_button'].'_'.$ValMd['id_custom'].'_'.$language['id_lang'].'" style="font-size : 10px; width: 250px" value="'.$LangMenu[0]['name_menu'].'"></div>';
					}
					$MenuHtml .= displayFlagsMD($languages, $defaultLanguage, 'DivMenuLang'.$ValMd['id_custom'], 'DivMenuLang'.$ValMd['id_custom'], true);
					$MenuHtml .= '</TD>';
$MenuHtml .= '<TD align="left"><select id="column_'.$ValMd['id_button'].'_'.$ValMd['id_custom'].'" style="font-size : 10px">
<option value="1" '.($ValMd['num_column'] == 1 ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Column', $_GET['idLang']).' 1&nbsp;&nbsp;</option>
<option value="2" '.($ValMd['num_column'] == 2 ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Column', $_GET['idLang']).' 2&nbsp;&nbsp;</option>
<option value="3" '.($ValMd['num_column'] == 3 ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Column', $_GET['idLang']).' 3&nbsp;&nbsp;</option>
<option value="4" '.($ValMd['num_column'] == 4 ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Column', $_GET['idLang']).' 4&nbsp;&nbsp;</option>
<option value="5" '.($ValMd['num_column'] == 5 ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Column', $_GET['idLang']).' 5&nbsp;&nbsp;</option>
<option value="6" '.($ValMd['num_column'] == 6 ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Column', $_GET['idLang']).' 6&nbsp;&nbsp;</option>
<option value="7" '.($ValMd['num_column'] == 7 ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Column', $_GET['idLang']).' 7&nbsp;&nbsp;</option>
<option value="8" '.($ValMd['num_column'] == 8 ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Column', $_GET['idLang']).' 8&nbsp;&nbsp;</option>
<option value="9" '.($ValMd['num_column'] == 9 ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Column', $_GET['idLang']).' 9&nbsp;&nbsp;</option>
<option value="10" '.($ValMd['num_column'] == 10 ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Column', $_GET['idLang']).' 10&nbsp;&nbsp;</option>
</select><select id="line_'.$ValMd['id_button'].'_'.$ValMd['id_custom'].'" style="font-size : 10px">
<option value="1"  '.($ValMd['num_ligne'] == 1  ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Line', $_GET['idLang']).' 1&nbsp;&nbsp;</option>
<option value="2"  '.($ValMd['num_ligne'] == 2  ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Line', $_GET['idLang']).' 2&nbsp;&nbsp;</option>
<option value="3"  '.($ValMd['num_ligne'] == 3  ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Line', $_GET['idLang']).' 3&nbsp;&nbsp;</option>
<option value="4"  '.($ValMd['num_ligne'] == 4  ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Line', $_GET['idLang']).' 4&nbsp;&nbsp;</option>
<option value="5"  '.($ValMd['num_ligne'] == 5  ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Line', $_GET['idLang']).' 5&nbsp;&nbsp;</option>
<option value="6"  '.($ValMd['num_ligne'] == 6  ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Line', $_GET['idLang']).' 6&nbsp;&nbsp;</option>
<option value="7"  '.($ValMd['num_ligne'] == 7  ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Line', $_GET['idLang']).' 7&nbsp;&nbsp;</option>
<option value="8"  '.($ValMd['num_ligne'] == 8  ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Line', $_GET['idLang']).' 8&nbsp;&nbsp;</option>
<option value="9"  '.($ValMd['num_ligne'] == 9  ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Line', $_GET['idLang']).' 9&nbsp;&nbsp;</option>
<option value="10" '.($ValMd['num_ligne'] == 10 ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Line', $_GET['idLang']).' 10&nbsp;&nbsp;</option>
</select></TD></TR>';
					$MenuHtml .= '</TABLE><table style="font-size:10px" width="100%">';
					$MenuHtml .= '<TR><TD width="100">'.traduct('Link', $_GET['idLang']).'&nbsp;:&nbsp;</TD><TD>&nbsp;<input type="text" id="MenuLink_'.$ValMd['id_button'].'_'.$ValMd['id_custom'].'" name"MenuLink_'.$ValMd['id_button'].'_'.$ValMd['id_custom'].'" style="font-size : 10px; width: 462px" value="'.$ValMd['link'].'"></TD></TR>';
					$MenuHtml .= '<TR><TD width="100">'.traduct('Sub-menu', $_GET['idLang']).'&nbsp;:&nbsp;</TD><TD>&nbsp;';
					foreach ($languages as $language) {
						$MenuHtml .= '<div id="CustomSubName'.$ValMd['id_custom'].'_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left" class="divLang">';
						$MenuHtml .= '<input type="text" id="CustomSubMenuName_'.$ValMd['id_custom'].'_'.$language['id_lang'].'" width="200" style="font-size : 10px"></div>';
					}
					$MenuHtml .= displayFlagsMD($languages, $defaultLanguage, 'CustomSubName'.$ValMd['id_custom'], 'CustomSubName'.$ValMd['id_custom'], true);
					$MenuHtml .= '&nbsp;<input type="button" value="'.traduct('Add', $_GET['idLang']).'" onClick=\'addCustomSubMenu("'.$ValMd['id_button'].'","'.$ValMd['id_custom'].'" )\' class="button" style="font-size : 10px"></TD></TR></TABLE>';
					$MenuHtml .= '<div id="customSubMenu_'.$ValMd['id_custom'].'" width="100%">';
					$SubMenuDetail = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'admevo_custom_menu 
					WHERE id_button='.$ValMd['id_button'].' AND id_parent='.$ValMd['id_custom'].'  
					ORDER BY num_column, num_ligne');
					if(sizeof($SubMenuDetail)) {
						$MenuHtml .= '<table cellpadding="2" cellspacing="0" border="0" class="table" width="100%"style="font-size:10px">';
						foreach($SubMenuDetail as $KSMd=>$ValSMd) {
							$MenuHtml .= '<TR>';
							$MenuHtml .= '<TD align="center" rowspan="2">'.$ValSMd['id_custom'].'</TD>';
							$MenuHtml .= '<TD>'.traduct('Name', $_GET['idLang']).'&nbsp;:&nbsp;</TD><TD>';
							foreach ($languages as $language) {
								$LangMenu = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'admevo_custom_menu_lang  
								WHERE id_button='.$ValSMd['id_button'].' 
								AND id_custom='.$ValSMd['id_custom'].' 
								AND id_lang='.$language['id_lang']);
								$MenuHtml .= '<div id="DivMenuLang'.$ValSMd['id_custom'].'_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left" class="divLang">';
								$MenuHtml .= '<input type="text" id="MenuName_'.$ValSMd['id_button'].'_'.$ValSMd['id_custom'].'_'.$language['id_lang'].'" name="MenuName_'.$ValSMd['id_button'].'_'.$ValSMd['id_custom'].'_'.$language['id_lang'].'" style="font-size : 10px; width: 400px" value="'.$LangMenu[0]['name_menu'].'"></div>';
							}
			$MenuHtml .= displayFlagsMD($languages, $defaultLanguage, 'DivMenuLang'.$ValSMd['id_custom'], 'DivMenuLang'.$ValSMd['id_custom'], true);
			$MenuHtml .= '&nbsp;<input type="hidden" id="column_'.$ValSMd['id_button'].'_'.$ValSMd['id_custom'].'" value="0">';
			$MenuHtml .= '&nbsp;<select id="line_'.$ValSMd['id_button'].'_'.$ValSMd['id_custom'].'" style="font-size : 10px">
				<option value="1"  '.($ValSMd['num_ligne'] == 1  ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Line', $_GET['idLang']).' 1&nbsp;&nbsp;</option>
				<option value="2"  '.($ValSMd['num_ligne'] == 2  ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Line', $_GET['idLang']).' 2&nbsp;&nbsp;</option>
				<option value="3"  '.($ValSMd['num_ligne'] == 3  ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Line', $_GET['idLang']).' 3&nbsp;&nbsp;</option>
				<option value="4"  '.($ValSMd['num_ligne'] == 4  ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Line', $_GET['idLang']).' 4&nbsp;&nbsp;</option>
				<option value="5"  '.($ValSMd['num_ligne'] == 5  ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Line', $_GET['idLang']).' 5&nbsp;&nbsp;</option>
				<option value="6"  '.($ValSMd['num_ligne'] == 6  ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Line', $_GET['idLang']).' 6&nbsp;&nbsp;</option>
				<option value="7"  '.($ValSMd['num_ligne'] == 7  ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Line', $_GET['idLang']).' 7&nbsp;&nbsp;</option>
				<option value="8"  '.($ValSMd['num_ligne'] == 8  ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Line', $_GET['idLang']).' 8&nbsp;&nbsp;</option>
				<option value="9"  '.($ValSMd['num_ligne'] == 9  ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Line', $_GET['idLang']).' 9&nbsp;&nbsp;</option>
				<option value="10" '.($ValSMd['num_ligne'] == 10 ? " selected" : false).'>&nbsp;&nbsp;'.traduct('Line', $_GET['idLang']).' 10&nbsp;&nbsp;</option>
							</select></TD>';
							$MenuHtml .= '<TD rowspan="2" align="center">';
							$MenuHtml .= '<img src="../img/admin/delete.gif" style="cursor: pointer" onClick=\'deleteCustomMenu("'.$ValSMd['id_button'].'", "'.$ValSMd['id_custom'].'")\'/>
										  <img src="../modules/navmegadrownevo/views/img/disk.png" style="cursor: pointer" onClick=\'saveCustomMenu("'.$ValSMd['id_button'].'", "'.$ValSMd['id_custom'].'")\'/>';
							$MenuHtml .= '</TD></TR>';
							$MenuHtml .= '<TR><TD>Lien&nbsp;:&nbsp;</TD><TD><input type="text" id="MenuLink_'.$ValSMd['id_button'].'_'.$ValSMd['id_custom'].'" name"MenuLink_'.$ValSMd['id_button'].'_'.$ValSMd['id_custom'].'" style="font-size : 10px; width: 400px" value="'.$ValSMd['link'].'"></TD>';
							$MenuHtml .= '</TR>';
						}
						$MenuHtml .= '</table>';
					}
					$menuHtml .= '</div>';
					$MenuHtml .= '</TD>';
					$MenuHtml .= '<TD valign="top" style="padding-top : 5px">';
					$MenuHtml .= '<img src="../img/admin/delete.gif" style="cursor: pointer" onClick=\'deleteCustomMenu("'.$ValMd['id_button'].'", "'.$ValMd['id_custom'].'")\'/>
								  <img src="../modules/navmegadrownevo/views/img/disk.png" style="cursor: pointer" onClick=\'saveCustomMenu("'.$ValMd['id_button'].'", "'.$ValMd['id_custom'].'")\'/>';
					$MenuHtml .= '</TD></TR>';
				}
				$MenuHtml .= "</table>";
			}
			echo $MenuHtml;
		break;
	}
}

?>