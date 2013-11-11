<?php
/**
 * Module MeGa DrOwN mEnU Evolution - Main file
 *
 * @category		Module / front_office_features
 * @author			Community (since GitHub)
 * @author			PrestaEdit <j.danse@prestaedit.com> (since 2.0)
 * @author			DevForEver (special thanks to him)
 * @copyright		2013 PrestaEdit
 * @version			2.0	
 * @link       		http://www.prestaedit.com/
 * @since			File available since Release 1.0
 */

// Security
if (!defined('_PS_VERSION_'))
	exit;
	
// Checking compatibility with older PrestaShop and fixing it
if (!defined('_MYSQL_ENGINE_'))
	define('_MYSQL_ENGINE_', 'MyISAM');

class navmegadrownEvo extends Module 
{
	private $_style = '';
	private $_menu = '';
	private $_html = '';
	
	private $eol = "\r\n";

	protected $link;
	
	public function __construct() 
	{
		$this->name = 'navmegadrownevo';
		$this->tab = 'front_office_features';
		$this->version = '2.3.9.4';
		$this->author = 'PrestaEdit';		
		$this->ps_versions_compliancy['min'] = '1.5.0.1'; 
		$this->need_instance = 0;
		
		parent::__construct();
		
		$this->displayName = $this->l('MeGa DrOwN mEnU Evolution');
		$this->description = $this->l('Add a MeGa DrOwN mEnU Evolution.');
		$this->confirmUninstall = $this->l('Are you sure you want to delete this module ?');
		$this->allow = intval(Configuration::get('PS_REWRITING_SETTINGS'));
		
		if(!$this->isRegisteredInHook('displayBackOfficeHeader'))
			$this->registerHook('displayBackOfficeHeader');	

		$this->link = new Link();
	}
	
	public function install()
	{
		// Install SQL
		include(dirname(__FILE__).'/sql/install.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->execute($s))
				return false;		
		
		// Install Module  
   	return 	parent::install()
						&& $this->registerHook('displayTop')			
						&& $this->registerHook('displayHeader')		
						&& $this->registerHook('displayBackOfficeHeader');					
	}
	
	public function uninstall()
	{
		// Uninstall SQL
		include(dirname(__FILE__).'/sql/uninstall.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->execute($s))
				return false;
				
		Configuration::deleteByName('MOD_MEGADROWN_ITEMS');
		
		// Uninstall Module
		if (!parent::uninstall())
			return false;

		return true;
	}
	
	public function getContent() 
	{
		global $ButtonIdInEdit;
		$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages();
		$iso = Language::getIsoById($defaultLanguage);
		$output = "";
		$errors = array();
		$errorsNb = 0;
				
		$update_cache = false;
		
		if(Tools::isSubmit('SubmitButton')) 
		{
			$result = Db::getInstance()->autoExecute(
				_DB_PREFIX_.'admevo_button',
				array(
				  'order_button'=>99
				),
				'INSERT'
			);
			$IdButton = Db::getInstance()->Insert_ID();
			if(!$result) $errorsNb++;
			$languages = Language::getLanguages();
			foreach ($languages as $language) {
				if(Tools::getValue('ButtonName_'.$language['id_lang']) != '') {
					$result = Db::getInstance()->autoExecute(
						_DB_PREFIX_.'admevo_button_lang',
						array(
						  'id_button'=>$IdButton, 
						  'id_lang'=>$language['id_lang'], 
						  'name_button'=>addslashes(Tools::getValue('ButtonName_'.$language['id_lang']))
						),
						'INSERT'
					);
					if(!$result) $errorsNb++;
				 }
			}			
			if($errorsNb!=0)
				$output .= $this->displayError($this->l('Unable to add this button'));
			else
				$output .= $this->displayConfirmation($this->l('Button added'));
		}
		else if(Tools::isSubmit('SubmitButtonParameters')) 
		{
			$result = Db::getInstance()->autoExecute(
				_DB_PREFIX_.'admevo_button', 
				array(
					"buttonColor"=>(Tools::getValue('noColorButton')=="on" ? "" : Tools::getValue('buttonColor')) 
				), 
				"UPDATE", "id_button=".Tools::getValue('ButttonIdToUpdate')); 

			Db::getInstance()->delete(_DB_PREFIX_.'admevo_button_link_cat', "id_button = ".Tools::getValue('ButttonIdToUpdate'));
			Db::getInstance()->delete(_DB_PREFIX_.'admevo_button_organization', "id_button = ".Tools::getValue('ButttonIdToUpdate'));
			Db::getInstance()->delete(_DB_PREFIX_.'admevo_button_langcat', "id_button = ".Tools::getValue('ButttonIdToUpdate'));
				if(is_array(Tools::getValue('categoryBox'))) {
					foreach(Tools::getValue('categoryBox') as $id_cat=>$cat) {
					  $numLigneCat = Tools::getValue('lineBox_'.$cat);
					  $numColumnCat = Tools::getValue('columnBox_'.$cat);
					  $result = Db::getInstance()->autoExecute(
						_DB_PREFIX_.'admevo_button_link_cat',
						array(
						  'id_button'=>Tools::getValue('ButttonIdToUpdate'),
						  'id_link_cat'=>$cat, 
						  'num_ligne'=>$numLigneCat, 
						  'num_column'=>$numColumnCat, 
						  'view_products'=>Tools::getValue('viewProducts_'.$cat)
						),

						'INSERT'
					  );
				}
				 foreach($_POST as $kPost=>$vPost) {
					if(substr($kPost, 0 , 7)=="lineBox") {
						$tabDatas = explode('_', $kPost);
						$idCat = $tabDatas[1];
						$result = Db::getInstance()->autoExecute(
							_DB_PREFIX_.'admevo_button_organization',
							array(
							  'id_button' => Tools::getValue('ButttonIdToUpdate'),
							  'id_link_cat' => $idCat, 
							  'state' => Tools::getValue('State_'.$idCat),
							  'num_ligne' => $vPost
							),
							'INSERT'
						  );
						foreach ($languages as $language) {
							if(Tools::getValue('textSubstitute_'.$idCat."_".$language['id_lang']) != '') {
								$result = Db::getInstance()->autoExecute(
									_DB_PREFIX_.'admevo_button_langcat',
									array(
									  'id_button' => Tools::getValue('ButttonIdToUpdate'),
									  'id_cat' => $idCat, 
									  'id_lang' => $language['id_lang'], 
									  'name_substitute' => addslashes(Tools::getValue('textSubstitute_'.$idCat."_".$language['id_lang']))
									),
									'INSERT'
								  );
							}
						}
					}
				 }
			  if(!$result) $errorsNb++;
			}		
			Db::getInstance()->delete(_DB_PREFIX_.'admevo_button_link', "id_button = ".Tools::getValue('ButttonIdToUpdate'));
			$result = Db::getInstance()->autoExecute(
				_DB_PREFIX_.'admevo_button_link',
				array(
				  'id_button'=>Tools::getValue('ButttonIdToUpdate'),
				  'link'=>Tools::getValue('LinkPage')
				),
				'INSERT'
			);
			if(!$result) $errorsNb++;
			$languages = Language::getLanguages();
			$detailSubProgress = $this->getButtonDetail(Tools::getValue('ButttonIdToUpdate'));
			if(sizeof($detailSubProgress)) {
				foreach($detailSubProgress as $kSub=>$ValSub) {
					$infoSub[$ValSub['id_lang']]['detailSub'] = html_entity_decode($ValSub['detailSub']);
					$infoSub[$ValSub['id_lang']]['detailSubLeft'] = html_entity_decode($ValSub['detailSubLeft']);
					$infoSub[$ValSub['id_lang']]['detailSubTR'] = html_entity_decode($ValSub['detailSubTR']);
				}
			}
			Db::getInstance()->delete(_DB_PREFIX_.'admevo_button_lang', "id_button = ".Tools::getValue('ButttonIdToUpdate'));
			foreach ($languages as $language) {
				//if(Tools::getValue('ButtonNameEdit_'.$language['id_lang']) != '') {
				if(1) {
					$result = Db::getInstance()->autoExecute(
						_DB_PREFIX_.'admevo_button_lang',
						array(
						  'id_button'=>Tools::getValue('ButttonIdToUpdate'), 
						  'id_lang'=>$language['id_lang'], 
						  'name_button'=>addslashes(Tools::getValue('ButtonNameEdit_'.$language['id_lang'])), 
						  'detailSub'=>htmlentities(addslashes((isset($infoSub[$language['id_lang']]['detailSub']) ? $infoSub[$language['id_lang']]['detailSub'] : ''))),
						  'detailSubLeft'=>htmlentities(addslashes((isset($infoSub[$language['id_lang']]['detailSubLeft']) ? $infoSub[$language['id_lang']]['detailSubLeft'] : ''))),
						  'detailSubTR'=>htmlentities(addslashes((isset($infoSub[$language['id_lang']]['detailSubTR']) ? $infoSub[$language['id_lang']]['detailSubTR'] : '')))
						),
						'INSERT'
					);
					if(!$result) $errorsNb++;
				 }
			}		
			if(!$result) $errorsNb++;
			
			if($errorsNb!=0)
				$output .= $this->displayError($this->l('Unable to update this button'));
			else
				$output .= $this->displayConfirmation($this->l('Button updated'));
			$ButtonIdInEdit = Tools::getValue('ButttonIdToUpdate');
		}
		else if(Tools::isSubmit('SubmitButtonOrganization')) 
		{
			if(trim(Tools::getValue('Organisation')) != '') {
				$ButtonOrganisation = explode(',', Tools::getValue('Organisation'));
					$position = 1;
				foreach($ButtonOrganisation as $KPos=>$VPos) {
					$result = Db::getInstance()->autoExecute(
						_DB_PREFIX_.'admevo_button', 
						array(
							"order_button"=>$position
						), 
						"UPDATE", 
						"id_button =".str_replace("button_", "", $VPos)
						);				
					$position++;
					if(!$result) $errorsNb++;
				}	
			}

			if($errorsNb!=0)
				$output .= $this->displayError($this->l('Unable to save the organization'));
			else
				$output .= $this->displayConfirmation($this->l('Organziation saved'));
		}
		else if(Tools::isSubmit('SubmitButtonDesign')) 
		{
			$errorsNb = 0;
			//Db::getInstance()->delete(_DB_PREFIX_.'admevo_parameters');
			$tabDesign = array();
			Tools::getValue('MenuWidth')=="" 			? false : $tabDesign["MenuWidth"] 			= Tools::getValue('MenuWidth');
			Tools::getValue('MenuHeight')=="" 			? false : $tabDesign["MenuHeight"] 			= Tools::getValue('MenuHeight');
			Tools::getValue('MinWidthButton')=="" 		? false : $tabDesign["MinButtonWidth"] 		= Tools::getValue('MinWidthButton');
			Tools::getValue('MaxWidthButton')=="" 		? false : $tabDesign["MaxButtonWidth"] 		= Tools::getValue('MaxWidthButton');
			Tools::getValue('ColumnSize')=="" 			? false : $tabDesign["columnSize"] 			= Tools::getValue('ColumnSize');
			Tools::getValue('paddingLeft')=="" 			? false : $tabDesign["paddingLeft"] 		= Tools::getValue('paddingLeft');
			Tools::getValue('marginTop')=="" 			? false : $tabDesign["marginTop"] 			= Tools::getValue('marginTop');
			Tools::getValue('marginBottom')=="" 		? false : $tabDesign["marginBottom"] 		= Tools::getValue('marginBottom');
			Tools::getValue('GeneralColor')=="" 		? false : $tabDesign["GeneralColor"] 		= Tools::getValue('GeneralColor');
			Tools::getValue('FontSizeMenu')=="" 		? false : $tabDesign["FontSizeMenu"] 		= Tools::getValue('FontSizeMenu');
			Tools::getValue('FontSizeSubmenu')=="" 		? false : $tabDesign["FontSizeSubmenu"] 	= Tools::getValue('FontSizeSubmenu');
			Tools::getValue('FontSizeSubsubmenu')=="" 	? false : $tabDesign["FontSizeSubsubmenu"] 	= Tools::getValue('FontSizeSubsubmenu');
			Tools::getValue('MenuColor')=="" 			? false : $tabDesign["ColorFontMenu"] 		= Tools::getValue('MenuColor');
			Tools::getValue('SubMenuColor')=="" 		? false : $tabDesign["ColorFontSubMenu"] 	= Tools::getValue('SubMenuColor');
			Tools::getValue('SubSubMenuColor')=="" 		? false : $tabDesign["ColorFontSubSubMenu"] = Tools::getValue('SubSubMenuColor');
			Tools::getValue('MenuColorHover')=="" 		? false : $tabDesign["ColorFontMenuHover"] 			= Tools::getValue('MenuColorHover');
			Tools::getValue('SubMenuColorHover')=="" 	? false : $tabDesign["ColorFontSubMenuHover"] 		= Tools::getValue('SubMenuColorHover');
			Tools::getValue('SubSubMenuColorHover')=="" ? false : $tabDesign["ColorFontSubSubMenuHover"] 	= Tools::getValue('SubSubMenuColorHover');
			Tools::getValue('VerticalPadding')=="" 		? false : $tabDesign["VerticalPadding"] 	= Tools::getValue('VerticalPadding');
			Tools::getValue('noColorTR1')=="on" 		? $tabDesign["backgroundTR1"] 	= "" 		: $tabDesign["backgroundTR1"] 	= Tools::getValue('backgroundTR1');	
			Tools::getValue('noColorTD1')=="on" 		? $tabDesign["backgroundTD1"] 	= "" 		: $tabDesign["backgroundTD1"] 	= Tools::getValue('backgroundTD1');	
			Tools::getValue('noColorTD2')=="on" 		? $tabDesign["backgroundTD2"] 	= "" 		: $tabDesign["backgroundTD2"] 	= Tools::getValue('backgroundTD2');	
			Tools::getValue('noColorTD3')=="on" 		? $tabDesign["backgroundTD3"] 	= "" 		: $tabDesign["backgroundTD3"] 	= Tools::getValue('backgroundTD3');	
			Tools::getValue('heightTR1')=="" 			? false : $tabDesign["heightTR1"] 	= Tools::getValue('heightTR1');
			Tools::getValue('widthTD1')=="" 			? false : $tabDesign["widthTD1"] 	= Tools::getValue('widthTD1');
			Tools::getValue('widthTD3')=="" 			? false : $tabDesign["widthTD3"] 	= Tools::getValue('widthTD3');
			Tools::getValue('stateTR1')=="" 			? $tabDesign["stateTR1"]="off" : $tabDesign["stateTR1"] 	= Tools::getValue('stateTR1');
			Tools::getValue('stateTD1')=="" 			? $tabDesign["stateTD1"]="off" : $tabDesign["stateTD1"] 	= Tools::getValue('stateTD1');
			Tools::getValue('stateTD3')=="" 			? $tabDesign["stateTD3"]="off" : $tabDesign["stateTD3"] 	= Tools::getValue('stateTD3');
			Tools::getValue('SearchBar')=="" 			? $tabDesign["SearchBar"]="off" : $tabDesign["SearchBar"] 	= Tools::getValue('SearchBar');

			$result = Db::getInstance()->autoExecuteWithNullValues(
						_DB_PREFIX_.'admevo_parameters', 
						$tabDesign, 
						"UPDATE"
						); 	
			$AutorizeExtensions = array(0=>".jpg", 1=>".gif", 2=>".png");
			if(isset($_FILES['PictureMenu']) && $_FILES['PictureMenu']['tmp_name']!="") {
				$extension = strtolower(substr($_FILES['PictureMenu']['name'], -4));
				if(in_array($extension, $AutorizeExtensions)) {
					$img = dirname(__FILE__).'/views/img/menu/bg_menu-'.$this->context->shop->id.$extension;
					if ( !move_uploaded_file( $_FILES['PictureMenu']['tmp_name'], $img ) )
						$errorsNb++;
					else
						Db::getInstance()->autoExecuteWithNullValues(
						_DB_PREFIX_.'admevo_parameters', 
						array(
							"extensionMenu"=> $extension
						), 
						"UPDATE"
						);
				}
				else
					$output .= $this->displayError($this->l('File ').$_FILES['PictureMenu']['name'].$this->l(' not uploaded, wrong type (only jpg).'));
			}
			if(isset($_FILES['PictureButton']) && $_FILES['PictureButton']['tmp_name']!="") {
				$extension = strtolower(substr($_FILES['PictureButton']['name'], -4));
				if(in_array($extension, $AutorizeExtensions)) {
					$img = dirname(__FILE__).'/views/img/menu/bg_bout-'.$this->context->shop->id.$extension;
					if ( !move_uploaded_file( $_FILES['PictureButton']['tmp_name'], $img ) )
						$errorsNb++;
					else
						Db::getInstance()->autoExecuteWithNullValues(
						_DB_PREFIX_.'admevo_parameters', 
						array(
							"extensionBout"=> $extension
						), 
						"UPDATE"
						);
				}
				else
					$output .= $this->displayError($this->l('File ').$_FILES['PictureButton']['name'].$this->l(' not uploaded, wrong type (only jpg).'));
			}
			if(isset($_FILES['PictureListArrow']) && $_FILES['PictureListArrow']['tmp_name']!="") {
				$extension = strtolower(substr($_FILES['PictureListArrow']['name'], -4));
				if(in_array($extension, $AutorizeExtensions)) {
					$img = dirname(__FILE__).'/views/img/menu/navlist_arrow-'.$this->context->shop->id.$extension;
					if ( !move_uploaded_file( $_FILES['PictureListArrow']['tmp_name'], $img ) )
						$errorsNb++;
					else
						Db::getInstance()->autoExecuteWithNullValues(
						_DB_PREFIX_.'admevo_parameters', 
						array(
							"extensionArro"=> $extension
						), 
						"UPDATE"
						);
				}
				else
					$output .= $this->displayError($this->l('File ').$_FILES['PictureListArrow']['name'].$this->l(' not uploaded, wrong type (only jpg).'));
			}
			if(isset($_FILES['PicturebackSubMenu']) && $_FILES['PicturebackSubMenu']['tmp_name']!="") {
				$extension = strtolower(substr($_FILES['PicturebackSubMenu']['name'], -4));
				if(in_array($extension, $AutorizeExtensions)) {
					$img = dirname(__FILE__).'/views/img/menu/sub_bg-'.$this->context->shop->id.$extension;
					if ( !move_uploaded_file( $_FILES['PicturebackSubMenu']['tmp_name'], $img ) )
						$errorsNb++;
					else
						Db::getInstance()->autoExecuteWithNullValues(
						_DB_PREFIX_.'admevo_parameters', 
						array(
							"extensionBack"=> $extension
						), 
						"UPDATE"
						);
				}
				else
					$output .= $this->displayError($this->l('File ').$_FILES['PicturebackSubMenu']['name'].$this->l(' not uploaded, wrong type (only jpg).'));
			}
			if($errorsNb!=0)
				$output .= $this->displayError($this->l('Error during the upload'));
			else
				$output .= $this->displayConfirmation($this->l('File uploaded and parameters saved'));
						
			$this->_clearCache('views/front/cssnavmegadrownevo.tpl');
		}
		else if(isset($_POST['Action']) && $_POST['Action']!="") 
		{
			switch($_POST['Action']) 
			{
				case "EditButton":
					$ButtonIdInEdit = $_POST['ButtonIdAction'];
					break;
				case "DeleteButton":
					Db::getInstance()->delete(_DB_PREFIX_.'admevo_button', "id_button = ".$_POST['ButtonIdAction']);
					Db::getInstance()->delete(_DB_PREFIX_.'admevo_button_lang', "id_button = ".$_POST['ButtonIdAction']);
					Db::getInstance()->delete(_DB_PREFIX_.'admevo_button_link', "id_button = ".$_POST['ButtonIdAction']);
					Db::getInstance()->delete(_DB_PREFIX_.'admevo_button_link_cat', "id_button = ".$_POST['ButtonIdAction']);
					Db::getInstance()->delete(_DB_PREFIX_.'admevo_custom_menu', "id_button = ".$_POST['ButtonIdAction']);
					Db::getInstance()->delete(_DB_PREFIX_.'admevo_custom_menu_lang', "id_button = ".$_POST['ButtonIdAction']);
					break;
			}
		}
		else if(isset($_POST['ActionFile']) && $_POST['ActionFile']!="") 
		{
			switch($_POST['ActionFile']) {
				case "DeleteFile":
					$FileToDelete = $_POST['FileToDelete'];
					if(is_file(dirname(__FILE__).'/views/img/menu/'.$FileToDelete))
						if(unlink(dirname(__FILE__).'/views/img/menu/'.$FileToDelete))
							$output .= $this->displayConfirmation($this->l('File deleted'));
						else
							$output .= $this->displayError($this->l('File not deleted'));
				break;
			}
		}
		else if(isset($_POST['ActionPicture']) && $_POST['ActionPicture']!="") 
		{
			switch($_POST['ActionPicture']) 
			{
				case "UploadPicture":
					$AutorizeExtensions = array(0=>".gif", 1=>".jpg", 2=>".png");
					$extension = strtolower(substr($_FILES['PictureFile']['name'], -4));
					$idButton = $_POST['idButton'];
					if(in_array($extension, $AutorizeExtensions)) {
						if ( isset($_FILES['PictureFile']['name']) ) {
							$img = dirname(__FILE__).'/views/img/menu/imgBout'.$idButton.$extension;
							if ( move_uploaded_file( $_FILES['PictureFile']['tmp_name'], $img ) ) {
								$result = Db::getInstance()->autoExecute(
								_DB_PREFIX_.'admevo_button', 
								array(
									"img_name"=>'imgBout'.$idButton.$extension, 
									"img_link"=>urlencode($_POST['imgLink']) 
								), 
								"UPDATE", "id_button=".$idButton); 
								$output .= $this->displayConfirmation($this->l('Picture has been uploaded'));
							}
							else
								$output .= $this->displayError($this->l('Error during the upload'));
						}
					}
					if(isset($_POST['imgLink'])) {
						$result = Db::getInstance()->autoExecute(
						_DB_PREFIX_.'admevo_button', 
						array(
							"img_link"=>urlencode($_POST['imgLink']) 
						), 
						"UPDATE", "id_button=".$idButton); 
					}
					$ButtonIdInEdit = $idButton;
				break;
				case "DeletePicture":
					$idButton = $_POST['idButton'];
					$result = Db::getInstance()->autoExecute(
						_DB_PREFIX_.'admevo_button', 
						array(
							"img_name"=>''								
						), 
						"UPDATE", "id_button=".$idButton); 
					if(is_file(dirname(__FILE__).'/views/img/menu/'.$_POST['NamePicture']))
						if(unlink(dirname(__FILE__).'/views/img/menu/'.$_POST['NamePicture']))
							$output .= $this->displayConfirmation($this->l('File deleted'));
						else
							$output .= $this->displayError($this->l('File not deleted'));
					$ButtonIdInEdit = $idButton;
				break;
			}
			foreach ($languages as $language) 
			{
				$result = Db::getInstance()->autoExecute(
					_DB_PREFIX_.'admevo_button_lang',
					array(
					  'detailSubLeft'=>htmlentities(addslashes(Tools::getValue('detailSubLeft_'.$language['id_lang'])))
					),
					'UPDATE',
					'id_button='.$_POST['idButton'].' AND 
					id_lang='.$language['id_lang']
				);
				if(!$result) $errorsNb++;
			}			
			$ButtonIdInEdit = $_POST['idButton'];
		}
		else if(isset($_POST['ActionPictureBackground']) && $_POST['ActionPictureBackground']!="") 
		{
			switch($_POST['ActionPictureBackground']) {
				case "UploadPictureBackground":
					$AutorizeExtensions = array(0=>".gif", 1=>".jpg", 2=>".png");
					$extension = strtolower(substr($_FILES['PictureFileBackground']['name'], -4));
					$idButton = $_POST['idButton'];
					if(in_array($extension, $AutorizeExtensions)) {
						if ( isset($_FILES['PictureFileBackground']['name']) ) {
							$img = dirname(__FILE__).'/views/img/menu/imgBackground'.$idButton.$extension;
							if ( move_uploaded_file( $_FILES['PictureFileBackground']['tmp_name'], $img ) ) {
								$result = Db::getInstance()->autoExecute(
								_DB_PREFIX_.'admevo_button', 
								array(
									"img_name_background"=>'imgBackground'.$idButton.$extension
								), 
								"UPDATE", "id_button=".$idButton); 
								$output .= $this->displayConfirmation($this->l('Picture has been uploaded'));
							}
							else
								$output .= $this->displayError($this->l('Error during the upload'));
						}
					}
					$ButtonIdInEdit = $idButton;
				break;
				case "DeletePicture":
					$idButton = $_POST['idButton'];
					$result = Db::getInstance()->autoExecute(
						_DB_PREFIX_.'admevo_button', 
						array(
							"img_name_background"=>''								
						), 
						"UPDATE", "id_button=".$idButton); 
					if(is_file(dirname(__FILE__).'/views/img/menu/'.$_POST['NamePictureBackground']))
						if(unlink(dirname(__FILE__).'/views/img/menu/'.$_POST['NamePictureBackground']))
							$output .= $this->displayConfirmation($this->l('File deleted'));
						else
							$output .= $this->displayError($this->l('File not deleted'));
					$ButtonIdInEdit = $idButton;
				break;
			}
			$ButtonIdInEdit = $_POST['idButton'];
		}
		else if(Tools::isSubmit('SubmitDetailSub')) 
		{
			foreach ($languages as $language) {
				$result = Db::getInstance()->autoExecute(
					_DB_PREFIX_.'admevo_button_lang',
					array(
					  'detailSub'=>htmlentities(addslashes(Tools::getValue('detailSub_'.$language['id_lang'])))
					),
					'UPDATE',
					'id_button='.Tools::getValue('idButton').' AND 
					id_lang='.$language['id_lang']
				);
				if(!$result) $errorsNb++;
			}			
			$ButtonIdInEdit = $_POST['idButton'];
		}
		else if(Tools::isSubmit('SubmitDetailSubTr')) 
		{
			foreach ($languages as $language) {
				$result = Db::getInstance()->autoExecute(
					_DB_PREFIX_.'admevo_button_lang',
					array(
					  'detailSubTR'=>htmlentities(addslashes(Tools::getValue('detailSubTr_'.$language['id_lang'])))
					),
					'UPDATE',
					'id_button='.Tools::getValue('idButton').' AND 
					id_lang='.$language['id_lang']
				);
				if(!$result) $errorsNb++;
			}			
			$ButtonIdInEdit = Tools::getValue('idButton');
		}
				
		$this->_clearCache('views/front/navmegadrownevo.tpl');
		
		return $output.$this->displayForm();
	}
	
	static public function getButtonDetail($IdButton) {
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM '._DB_PREFIX_.'admevo_button tb LEFT JOIN '._DB_PREFIX_.'admevo_button_lang tbl 
		ON (tb.id_button=tbl.id_button) 
		WHERE tb.id_button='.$IdButton.' ORDER BY order_button ASC, name_button ASC
		' );
	}	
	static public function getButtonLinksCat($IdButton) {
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM '._DB_PREFIX_.'admevo_button_link_cat 
		WHERE id_button='.$IdButton.' ORDER BY num_ligne ASC, num_column ASC, id_link_cat ASC 
		' );
	}	
	static public function getButtonLinksCustom($IdButton, $idLang) {
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM '._DB_PREFIX_.'admevo_custom_menu tb 
		INNER JOIN '._DB_PREFIX_.'admevo_custom_menu_lang tbl 
		ON (tb.id_button=tbl.id_button AND tb.id_custom=tbl.id_custom) 
		WHERE tb.id_button='.$IdButton.' AND tb.id_parent = 0 AND tbl.id_lang='.$idLang.' 
		ORDER BY tb.num_ligne ASC, tb.num_column ASC 
		' );
	}	
	static public function getButtonLinks($IdButton) {
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM '._DB_PREFIX_.'admevo_button_link WHERE id_button='.$IdButton.'
		' );
	}	
	static public function getButtonOrganization($IdButton) {
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM '._DB_PREFIX_.'admevo_button_organization WHERE id_button='.$IdButton.'
		' );
	}	
	static public function getNameCategory($IdCat, $IdLang, $IdButton) {
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM '._DB_PREFIX_.'category_lang tb INNER JOIN '._DB_PREFIX_.'admevo_button_organization tbl 
		ON (tb.id_category=tbl.id_link_cat) WHERE tb.id_category='.$IdCat.' AND tb.id_lang='.$IdLang.' and 
		tbl.id_button='.$IdButton.' 
		' );
	}	
	static public function getNameSubstitute($IdCat, $IdLang, $IdButton) {
		$return[0]['name_substitute'] = "";
		$result = Db::getInstance()->ExecuteS('
		SELECT *
		FROM '._DB_PREFIX_.'admevo_button_langcat WHERE id_cat='.$IdCat.' AND id_lang='.$IdLang.' and 
		id_button='.$IdButton.' 
		' );	
		if($result)
			return $result;
		return $return;
	}
	static public function getAllNameSubstitute($IdButton) {
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM '._DB_PREFIX_.'admevo_button_langcat WHERE  
		id_button='.$IdButton.' 
		' );	
	}
	static public function getNameCategoryUnder($IdCat, $IdButton) {
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM '._DB_PREFIX_.'category tb INNER JOIN '._DB_PREFIX_.'admevo_button_organization tbl 
		ON (tb.id_category=tbl.id_link_cat)
		WHERE tb.id_parent='.$IdCat.' and tbl.state=1 and tb.active=1 and 
		tbl.id_button='.$IdButton.' ORDER BY tbl.num_ligne ASC 
		' );
	}	
	static public function getProductsUnder($id_category, $id_lang, $id_shop = 1) {	
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM ('._DB_PREFIX_.'category_product cp INNER JOIN '._DB_PREFIX_.'product_lang pl 
		ON (cp.id_product=pl.id_product)) INNER JOIN '._DB_PREFIX_.'product p on (cp.id_product=p.id_product) 
		WHERE cp.id_category='.(int)$id_category.' 
		AND p.active=1 
		AND pl.id_lang = '.(int)$id_lang.'
		AND pl.id_shop = '.(int)$id_shop.'
		ORDER BY pl.name ASC 
		' );
	}	
	static public function getButtonLinksCustomUnder($IdButton, $IdParent, $idLang) {
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM '._DB_PREFIX_.'admevo_custom_menu tb 
		INNER JOIN '._DB_PREFIX_.'admevo_custom_menu_lang tbl 
		ON (tb.id_button=tbl.id_button AND tb.id_custom=tbl.id_custom) 
		WHERE tb.id_button='.$IdButton.' AND tb.id_parent = '.$IdParent.' AND tbl.id_lang = '.$idLang.' 
		ORDER BY tb.num_ligne ASC');
	}	
	private function getParameters() {
		return Db::getInstance()->ExecuteS('
		SELECT *
		FROM '._DB_PREFIX_.'admevo_parameters LIMIT 1
		' );
	}	
	private function getConfigurations() {
		return Db::getInstance()->ExecuteS('
				SELECT *  
				FROM '._DB_PREFIX_.'admevo_button tb LEFT JOIN '._DB_PREFIX_.'admevo_button_lang tbl 
				ON (tb.id_button=tbl.id_button) 
				WHERE tbl.id_lang='.(int)$this->context->cookie->id_lang.' ORDER BY order_button ASC, name_button ASC');	
	}
	static public function getMaxColumns($IdButton) {
		$maxCols = 0;
		$result = Db::getInstance()->ExecuteS('
		SELECT num_ligne, count(id_link_cat) as nbCat  
		FROM '._DB_PREFIX_.'admevo_button_link_cat WHERE id_button='.$IdButton.' GROUP BY num_ligne  
		' );
		if(sizeof($result)) {
			foreach($result as $kr=>$ValResult)
				if($maxCols<$ValResult['nbCat'])
					$maxCols = $ValResult['nbCat'];
		}
		return $maxCols;
	}
	static public function getNbColumns($IdButton, $Line) {
		return Db::getInstance()->ExecuteS('
		SELECT count(id_link_cat) as nbCols 
		FROM '._DB_PREFIX_.'admevo_button_link_cat WHERE id_button='.$IdButton.' AND num_ligne='.$Line);
	}
	
	function recurseCategory($indexedCategories, $categories, $current, $id_category = 1, $id_category_default = NULL, $CategorySelected, $CategoryLine, $CategoryColumn, $CategoryName, $CategoryState, $ViewProducts)
	{
		global $done;
		$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages();
		$iso = Language::getIsoById($defaultLanguage);
		
		static $irow;
		if(!is_array($CategorySelected))
			$CategorySelected = array();
		$id_obj = intval(Tools::getValue($this->identifier));
		if (!isset($done[$current['infos']['id_parent']]))
			$done[$current['infos']['id_parent']] = 0;
		$done[$current['infos']['id_parent']] += 1;

		$todo = sizeof($categories[$current['infos']['id_parent']]);
		$doneC = $done[$current['infos']['id_parent']];

		$level = $current['infos']['level_depth'] + 1;
		$img = $level == 1 ? 'lv1.png' : 'lv'.$level.'_'.($todo == $doneC ? 'f' : 'b').'.png';

		$this->_html .= '
		<tr class="'.($irow++ % 2 ? 'alt_row' : '').'">
			<td>
				<input type="checkbox" name="categoryBox[]" class="categoryBox'.($id_category_default != NULL ? ' id_category_default' : '').'" id="categoryBox_'.$id_category.'" value="'.$id_category.'"'.(((in_array($id_category, $indexedCategories) OR (intval(Tools::getValue('id_category')) == $id_category AND !intval($id_obj))) OR in_array($id_category, $CategorySelected)) ? ' checked="checked"' : '').' '.($current['infos']['active'] == 0 ? " disabled" : false).'/>
			</td>
			<td>
				'.$id_category.'
			</td>
			<td valign="top">
				<img src="../img/admin/'.$img.'" alt="" /> &nbsp;<label for="categoryBox_'.$id_category.'" class="t">'.stripslashes($current['infos']['name']).'</label>';
$this->_html .= '</td>';
$this->_html .= '<td>';
				$this->_html .= '<table cellpadding="0" cellspacing="0" width="100%">
				<tr><td>'.$this->l('Position').'&nbsp;:&nbsp;</td><td>'.$this->l('Category').'&nbsp;<select name="columnBox_'.$id_category.'" style="font-size : 10px">';
				for($c=1; $c<=10; $c++) 
	$this->_html .= '<option value="'.$c.'" '.(isset($CategoryColumn[$id_category]) && $CategoryColumn[$id_category] == $c ? " selected" : false).'>&nbsp;&nbsp;'.$this->l('Column').' '.$c.'&nbsp;&nbsp;</option>';
	$this->_html .= '</select>';
	
	// Under Category
	$this->_html .= '&nbsp;&bull;&nbsp;'.$this->l('Under category').'&nbsp;';
	$this->_html .= '<select name="lineBox_'.$id_category.'" style="font-size : 10px">';	
	for($i=1;$i<=10;$i++)
		$this->_html .= '<option value="'.$i.'" '.(isset($CategoryLine[$id_category]) && $CategoryLine[$id_category] == $i ? 'selected="selected"' : false).'>
											'.$this->l('Line').' '.$i.'
										</option>';	
	$this->_html .= '</select>';
	// End Under Category
		
	$this->_html .= '&nbsp;<select id="State_'.$id_category.'" name="State_'.$id_category.'" style="font-size : 10px">
					<option value="0" '.(isset($CategoryState[$id_category]) && $CategoryState[$id_category] == 0 ? 'selected="selected"' : false).'>'.$this->l('disabled').'</option>
					<option value="1" '.(isset($CategoryState[$id_category]) && $CategoryState[$id_category] == 1 ? 'selected="selected"' : false).'>'.$this->l('enabled').'</option>
				</select></td></tr>
				<tr><td>'.$this->l('Name').'&nbsp;:&nbsp;</td><td>';
				
			foreach ($languages as $language) {
						$this->_html .= '<div id="DivLang'.$id_category.'_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left" class="divLang">';
						$this->_html .= '<input type="text" id="textSubstitute_'.$id_category.'_'.$language['id_lang'].'" name="textSubstitute_'.$id_category.'_'.$language['id_lang'].'" value="'.(isset($CategoryName[$id_category][$language['id_lang']]) ? $CategoryName[$id_category][$language['id_lang']] : '').'" size = "45" style="font-size : 10px"></div>';
				 }
				$this->_html .= $this->displayFlagsMD($languages, $defaultLanguage, 'DivLang'.$id_category, 'DivLang'.$id_category, true);
				$this->_html .= '&nbsp;&nbsp;&nbsp;<input type="checkbox" id="viewProducts_'.$id_category.'" name="viewProducts_'.$id_category.'" '.(isset($ViewProducts[$id_category]) && $ViewProducts[$id_category]=='on' ? " checked=\"checked\"" : false).'>&nbsp;'.$this->l('view products');
				$this->_html .= '</td></tr></table>';

			$this->_html.= '</td>';
			$this->_html .= '</tr>';

		if (isset($categories[$id_category]))
			foreach ($categories[$id_category] AS $key => $row)
				if ($key != 'infos')
					$this->recurseCategory($indexedCategories, $categories, $categories[$id_category][$key], $key, NULL, $CategorySelected, $CategoryLine, $CategoryColumn, $CategoryName, $CategoryState, $ViewProducts);
	}	
	public function displayFlagsMD($languages, $defaultLanguage, $ids, $id, $return = false)
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
	
	public function displayForm() 
	{
		global $ButtonIdInEdit;
		$defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
		$languages = Language::getLanguages();
		$iso = Language::getIsoById($defaultLanguage);
		($ButtonIdInEdit!='' && $ButtonIdInEdit!=0) ? $languageIds = 'ButtonsEdit'.utf8_encode('�').'Buttons' :$languageIds = 'Buttons';
		$tabButtonsOrganizate = array();
		
		$MDConfiguration = $this->getConfigurations();
		$MDParameters = $this->getParameters();
				
		$this->_html .='
			<link rel="stylesheet" href="'.$this->_path.'views/css/colorpicker.css" type="text/css" />
			<link rel="stylesheet" href="'.$this->_path.'views/css/layout.css" type="text/css"/>
			<script type="text/javascript" src="'.$this->_path.'views/js/colorpicker.js"></script>
			<script type="text/javascript" src="'.$this->_path.'views/js/eye.js"></script>
			<script type="text/javascript" src="'.$this->_path.'views/js/layout.js?ver=1.0.2"></script>
		';
		$this->_html .='
			<script type="text/javascript">id_language = Number('.$defaultLanguage.');</script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
			
			<script type="text/javascript">
					tinyMCE.init({
						mode : "textareas",
						theme : "advanced",
						plugins : "safari,pagebreak,style,layer,table,advimage,advlink,inlinepopups,media,searchreplace,contextmenu,paste,directionality,fullscreen",
						// Theme options
						theme_advanced_buttons1 : "forecolor,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
						theme_advanced_buttons2 : "",
						theme_advanced_buttons3 : "",
						theme_advanced_buttons4 : "",
						theme_advanced_toolbar_location : "top",
						theme_advanced_toolbar_align : "left",
						theme_advanced_statusbar_location : "bottom",
						theme_advanced_resizing : false,
						content_css : "'.__PS_BASE_URI__.'themes/'._THEME_NAME_.'/css/global.css",
						document_base_url : "'.__PS_BASE_URI__.'",
						width: "600",
						height: "auto",
						font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
						// Drop lists for link/image/media/template dialogs
						template_external_list_url : "lists/template_list.js",
						external_link_list_url : "lists/link_list.js",
						external_image_list_url : "lists/image_list.js",
						media_external_list_url : "lists/media_list.js",
						elements : "nourlconvert",
						convert_urls : false,
						language : "'.(file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en').'"
						
					});
			//tinyMCEInit(\'textarea.rte\');
			function	showLanguagesMD(id)
				{
					getE("languages_" + id).style.display = "block";
				}
				function EditButton(ButtonId) {
					$("#Action").val("EditButton");
					$("#ButtonIdAction").val(ButtonId);
					$("#formnewbutton").submit();
				}
				function DeleteButton(ButtonId) {
					$("#Action").val("DeleteButton");
					$("#ButtonIdAction").val(ButtonId);
					$("#formnewbutton").submit();
				}
				function AfficheMasqueSlide(elt) {
					($("#"+elt).css("display") == "none") ? $("#"+elt).slideDown("normal") : $("#"+elt).slideUp("normal");
				}
				function deletePicture(pictureToDelete) {
					if(confirm("'.$this->l('Do you want to delete this file ?').'")) {
						$("#ActionFile").val("DeleteFile");
						$("#FileToDelete").val(pictureToDelete);
						$("#formdesign").submit();					
					}
				}
				var wait = "<div widht=\'100%\' align=\'center\' style=\'height : 190px\'><br /><br /><br /><br /><img src=\''.$this->_path.'views/img/ajaxLoading.gif\'><br />'.$this->l('Loading').'</div>";
				function displayDetailMenu() {
					$.ajax({
					  method : "GET",
					  url: "'.$this->_path.'ajax_update_button.php",
					  data: {
						  action: "DetailMenu",
						  idButton: $("#ButttonIdToUpdate").val(), 
						  idLang: '.$this->context->cookie->id_lang.'
					  },
					  success: function(data) {
							$("#customMenu").html(data)
					  }
					});	
				}
				function addCustomMenu(IdButton) {
					if($("#CustomMenuName").val() != "") {
						$("#customMenu").html(wait)
						$.ajax({
						  method : "GET",
						  url: "'.$this->_path.'ajax_update_button.php",
						  data: {
							  action: "CustomMenuAdd",
							  idButton: $("#ButttonIdToUpdate").val(),';
							foreach ($languages as $language)
								$this->_html .= 'MenuName_'.$language['id_lang'].' : $("#CustomMenuName_'.$language['id_lang'].'").val(),';
			   $this->_html .= 'idLang: '.$this->context->cookie->id_lang.'
						  },
						  success: function() {
							displayDetailMenu()
						  }
						});
					}
					else {
						alert("'.$this->l('Please entre Menu name.').'")
					}
				}
				function deleteCustomMenu(ButtonId, CustomMenuId) {
					$("#customMenu").html(wait)
					$.ajax({
					  method : "GET",
					  url: "'.$this->_path.'ajax_update_button.php",
					  data: {
						  action: "CustomMenuDelete",
						  idButton: ButtonId, 
						  idCustomMenu: CustomMenuId, 
						  idLang: '.$this->context->cookie->id_lang.'
					  },
					  success: function() {
						displayDetailMenu()
					  }
					});	
				}
				function saveCustomMenu(ButtonId, CustomMenuId) {
					';
					foreach ($languages as $language) {
						$this->_html .= 'NewNameMenu_'.$language['id_lang'].' = $("#MenuName_"+ButtonId+"_"+CustomMenuId+"_'.$language['id_lang'].'").val();
						';
					}
		$this->_html .= '
					NewMenuLink = $("#MenuLink_"+ButtonId+"_"+CustomMenuId).val();
					Newline		= $("#line_"+ButtonId+"_"+CustomMenuId).val();
					Newcolumn 	= $("#column_"+ButtonId+"_"+CustomMenuId).val();

					$("#customMenu").html(wait)
					$.ajax({
					  method : "GET",
					  url: "'.$this->_path.'ajax_update_button.php",
					  data: {
						  action: "CustomMenuSave",
						  idButton: ButtonId, 
						  idCustomMenu: CustomMenuId,
						  ';
						  foreach ($languages as $language)
						 	 $this->_html .= 'nameMenu_'.$language['id_lang'].': NewNameMenu_'.$language['id_lang'].', 
						  ';
		 $this->_html .= 'linkMenu: NewMenuLink, 
						  lineMenu: Newline, 
						  columnMenu: Newcolumn, 
						  idLang: '.$this->context->cookie->id_lang.'
					  },
					  success: function(data) {
						displayDetailMenu()
					  }
					});	
				}
				function addCustomSubMenu(ButtonId, CustomMenuId) {
					if($("#CustomSubMenuName" + CustomMenuId).val() != "") {
						';
						foreach ($languages as $language)
	   $this->_html .= 'NewSubMenu_'.$language['id_lang'].' = $("#CustomSubMenuName_" + CustomMenuId + "_'.$language['id_lang'].'").val();
	   					';
	   $this->_html .= '$("#customMenu").html(wait)
						$.ajax({
						  method : "GET",
						  url: "'.$this->_path.'ajax_update_button.php",
						  data: {
							  action: "CustomSubMenuAdd",
							  idButton: ButtonId, 
							  idCustomMenu: CustomMenuId,
							  ';
							  foreach ($languages as $language)
							  $this->_html .= 'SubMenuName_'.$language['id_lang'].' : NewSubMenu_'.$language['id_lang'].', 
						 	  ';
			 $this->_html .= 'idLang: '.$this->context->cookie->id_lang.'
						  },
						  success: function() {
							displayDetailMenu()
						  }
						});
					}
					else {
						alert("'.$this->l('Please entre Sub-Menu name.').'")
					}
				}
				function changeLanguageMD(field, fieldsString, id_language_new, iso_code)
				{
					var fields = $(".divLang");
					for (var i = 0; i < fields.length; ++i)
					{
						eltId = fields[i].id;
						eltTab = eltId.split("_");
						(eltTab[1] != id_language_new) ? getE(eltId).style.display = "none" : getE(eltId).style.display = "block";
						getE("language_current_" + eltTab[0]).src = "../img/l/" + id_language_new + ".jpg";
					}
					getE("languages_" + field).style.display = "none";
					id_language = id_language_new;
				}
				function deletePictureMenu() {
					$("#ActionPicture").val("DeletePicture")
					$("#form_upload_picture").submit();
				}
				function deletePictureBackground() {
					$("#ActionPictureBackground").val("DeletePicture")
					$("#form_upload_picture_background").submit();
				}
			</script>
			';
		$this->_html .= '
						<style>
						.Buttonlistitem
						{
							position: relative;
							display: block;
							float: left;	
							list-style-type: none;
							text-align: center;
						}
						.display_flags {
							padding-top : 3px;	
						}
						';
				$this->_html .= '
				.colorSelector {
					position: absolute;
					top: 0;
					left: 0;
					width: 36px;
					height: 36px;
					background: url('.$this->_path.'/views/img/select2.png);
				}
				.colorSelector div {
					position: absolute;
					top: 4px;
					left: 4px;
					width: 28px;
					height: 28px;
					background: url('.$this->_path.'/views/img/select2.png) center;
				}
				.colorpickerHolder {
					top: 32px;
					left: 0;
					width: 356px;
					height: 0;
					overflow: hidden;
					position: absolute;
				}
				.colorpickerHolder .colorpicker {
					background-image: url('.$this->_path.'/views/img/custom_background.png);
					position: absolute;
					bottom: 0;
					left: 0;
				}
				.colorpickerHolder .colorpicker_hue div {
					background-image: url('.$this->_path.'/views/img/custom_indic.gif);
				}
				.colorpickerHolder .colorpicker_hex {
					background-image: url('.$this->_path.'/views/img/custom_hex.png);
				}
				.colorpickerHolder .colorpicker_rgb_r {
					background-image: url('.$this->_path.'/views/img/custom_rgb_r.png);
				}
				.colorpickerHolder .colorpicker_rgb_g {
					background-image: url('.$this->_path.'/views/img/custom_rgb_g.png);
				}
				.colorpickerHolder .colorpicker_rgb_b {
					background-image: url('.$this->_path.'/views/img/custom_rgb_b.png);
				}
				.colorpickerHolder .colorpicker_hsb_ss {
					background-image: url('.$this->_path.'/views/img/custom_hsb_s.png);
				}
				.colorpickerHolder .colorpicker_hsb_h {
					background-image: url('.$this->_path.'/views/img/custom_hsb_h.png);
				}
				.colorpickerHolder .colorpicker_hsb_b {
					background-image: url('.$this->_path.'/views/img/custom_hsb_b.png);
				}
				.colorpickerHolder .colorpicker_submit {
					background-image: url('.$this->_path.'/views/img/custom_submit.png);
				}
				.colorpickerHolder .colorpicker input {
					color: #778398;
				}
				.customWidget {
					position: relative;
					height: 36px;
				}				
				';
			$this->_html.= '</style>';

		if (!is_writable(dirname(__FILE__).'/views/img/'))
			$this->_html .= $this->displayError($this->l('Folder "img" is not writable'));		
		$this->_html .= '<H2>'.$this->displayName.'</H2>';
                $this->_html .= '<H4>'.$this->l('Version').' : '.$this->version.'</H4>';
		$this->_html .= '
		<fieldset>
			<legend><img src="../img/admin/add.gif" alt="" title="" />'.$this->l('General').'&nbsp;</legend>';
		$this->_html .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post" name="formdesign" id="formdesign" enctype="multipart/form-data">';
		$this->_html .= '<table cellpadding="0" width="100%"><tr>';
		$this->_html .= '<td width="50%" valign="top">';
		$this->_html .= '<table cellpadding="0">';
		$this->_html .= '<tr><td>'.$this->l('Menu width').' : </td><td><input type="text" name="MenuWidth" value="'.$MDParameters[0]['MenuWidth'].'">&nbsp;px<sup>*</sup></td></tr>';
		$this->_html .= '<tr><td>'.$this->l('Menu height').' : </td><td><input type="text" name="MenuHeight" value="'.$MDParameters[0]['MenuHeight'].'">&nbsp;px<sup>*</sup></td></tr>';
		$this->_html .= '<tr><td>'.$this->l('Min width button').' : </td><td><input type="text" name="MinWidthButton" value="'.$MDParameters[0]['MinButtonWidth'].'">&nbsp;px<sup>*</sup></td></tr>';
		$this->_html .= '<tr><td>'.$this->l('Max width button').' : </td><td><input type="text" name="MaxWidthButton" value="'.$MDParameters[0]['MaxButtonWidth'].'">&nbsp;px<sup>*</sup> (0 = no limit)</td></tr>';
		$this->_html .= '<tr><td>'.$this->l('Padding left').' : </td><td><input type="text" name="paddingLeft" value="'.$MDParameters[0]['paddingLeft'].'">&nbsp;px<sup>*</sup></td></tr>';
		$this->_html .= '<tr><td>'.$this->l('Margin top').' : </td><td><input type="text" name="marginTop" value="'.$MDParameters[0]['marginTop'].'">&nbsp;px<sup>*</sup></td></tr>';
		$this->_html .= '<tr><td>'.$this->l('Margin bottom').' : </td><td><input type="text" name="marginBottom" value="'.$MDParameters[0]['marginBottom'].'">&nbsp;px<sup>*</sup></td></tr>';
		$this->_html .= '<tr><td>'.$this->l('Width column').' : </td><td><input type="text" name="ColumnSize" value="'.$MDParameters[0]['columnSize'].'">&nbsp;px<sup>*</sup></td></tr>';
		$this->_html .= '<tr><td>'.$this->l('General color').' : </td>';
		$this->_html .= '<td><input type="hidden" name="GeneralColor" value="'.$MDParameters[0]['GeneralColor'].'" id="HiddenColor2">
							<div id="customWidget2" class="customWidget">
								<div id="colorSelector2" class="colorSelector"><div style="background-color: #'.$MDParameters[0]['GeneralColor'].'"></div></div>
								<div id="colorpickerHolder2" class="colorpickerHolder" style="z-index : 1000">
								</div>
							</div>';
		$this->_html .= '</td></tr>';
		$this->_html .= '<tr><td>'.$this->l('Picture Menu').' : </td><td><input type="file" name="PictureMenu" style="font-size : 10px">&nbsp;</td></tr>';
		$this->_html .= '<tr><td>'.$this->l('Picture Button').' : </td><td><input type="file" name="PictureButton" style="font-size : 10px">&nbsp;</td></tr>';
		$this->_html .= '<tr><td>'.$this->l('Picture List arrow').' : </td><td><input type="file" name="PictureListArrow" style="font-size : 10px">&nbsp;</td></tr>';
		$this->_html .= '<tr><td>'.$this->l('Picture background submenu').' : </td><td><input type="file" name="PicturebackSubMenu" style="font-size : 10px">&nbsp;</td></tr>';
		$this->_html .= '<tr><td>'.$this->l('Search').' : </td><td><input type="checkbox" id="SearchBar" name="SearchBar" '.($MDParameters[0]['SearchBar']=="on" ? "checked='checked'": false).'></td></tr>';
		$this->_html .= '</table>';
		$this->_html .= '</td>';
		$this->_html .= '<td width="50%" valign="top">';
		$this->_html .= '<table cellpadding="0">';
			$this->_html .= '<tr><td>'.$this->l('Font size Menu').' : </td><td><input type="text" name="FontSizeMenu" value="'.$MDParameters[0]['FontSizeMenu'].'">&nbsp;px<sup>*</sup></td></tr>';
			$this->_html .= '<tr><td>'.$this->l('Font size Sub-menu').' : </td><td><input type="text" name="FontSizeSubmenu" value="'.$MDParameters[0]['FontSizeSubMenu'].'">&nbsp;px<sup>*</sup></td></tr>';
			$this->_html .= '<tr><td>'.$this->l('Font size Sub-sub-menu').' : </td><td><input type="text" name="FontSizeSubsubmenu" value="'.$MDParameters[0]['FontSizeSubSubMenu'].'">&nbsp;px<sup>*</sup></td></tr>';
			$this->_html .= '<tr><td>'.$this->l('Menu color').' : </td>';
			$this->_html .= '<td>';
				$this->_html .= '<table width="100%">
									<tr>
										<td width="50%">
											<input type="hidden" name="MenuColor" value="'.$MDParameters[0]['ColorFontMenu'].'" id="HiddenColor3">
											'.$this->l('Normal').'
											<div id="customWidget3" class="customWidget">
												<div id="colorSelector3" class="colorSelector">
													<div style="background-color: #'.$MDParameters[0]['ColorFontMenu'].'">
													</div>
												</div>
												<div id="colorpickerHolder3" class="colorpickerHolder" style="z-index : 1000"></div>
											</div>
										</td><td>
											'.$this->l('Hover').'
											<input type="hidden" name="MenuColorHover" value="'.$MDParameters[0]['ColorFontMenuHover'].'" id="HiddenColor6">
											<div id="customWidget6" class="customWidget">
												<div id="colorSelector6" class="colorSelector">
													<div style="background-color: #'.$MDParameters[0]['ColorFontMenuHover'].'">
													</div>
												</div>
												<div id="colorpickerHolder6" class="colorpickerHolder" style="z-index : 1000"></div>
											</div>
										</td>
									</tr>
								</table>';
			$this->_html .= '</td></tr>';
			$this->_html .= '<tr><td>'.$this->l('Sub-menu color').' : </td>';
			$this->_html .= '<td>'; /*** ici ***/
				$this->_html .= '<table width="100%">
									<tr>
										<td width="50%">
											<input type="hidden" name="SubMenuColor" value="'.$MDParameters[0]['ColorFontSubMenu'].'" id="HiddenColor4" />
											'.$this->l('Normal').'
											<div id="customWidget4" class="customWidget">
												<div id="colorSelector4" class="colorSelector">
													<div style="background-color: #'.$MDParameters[0]['ColorFontSubMenu'].'">
													</div>
												</div>
												<div id="colorpickerHolder4" class="colorpickerHolder" name="colorpickerHolder4" ></div>
											</div>							
										</td>
										<td>
											<input type="hidden" name="SubMenuColorHover" value="'.$MDParameters[0]['ColorFontSubMenuHover'].'" id="HiddenColor7" />
											'.$this->l('Hover').'
											<div id="customWidget7" class="customWidget">
												<div id="colorSelector7" class="colorSelector">
													<div style="background-color: #'.$MDParameters[0]['ColorFontSubMenuHover'].'">
													</div>
												</div>
												<div id="colorpickerHolder7" class="colorpickerHolder" name="colorpickerHolder7" ></div>
											</div>
										</td>
									</tr>
								</table>';
			$this->_html .= '</td></tr>';
			$this->_html .= '<tr><td>'.$this->l('Sub-sub-menu color').' : </td>';
			$this->_html .= '<td>'; /*** ici ***/
				$this->_html .= '<table width="100%">
									<tr>
										<td width="50%">
											<input type="hidden" name="SubSubMenuColor" value="'.$MDParameters[0]['ColorFontSubSubMenu'].'" id="HiddenColor5" />
											'.$this->l('Normal').'
											<div id="customWidget5" class="customWidget">
												<div id="colorSelector5" class="colorSelector">
													<div style="background-color: #'.$MDParameters[0]['ColorFontSubSubMenu'].'">
													</div>
												</div>
												<div id="colorpickerHolder5" class="colorpickerHolder" name="colorpickerHolder5" ></div>
											</div>							
										</td>
										<td>
											<input type="hidden" name="SubSubMenuColorHover" value="'.$MDParameters[0]['ColorFontSubSubMenuHover'].'" id="HiddenColor8" />
											'.$this->l('Hover').'
											<div id="customWidget8" class="customWidget">
												<div id="colorSelector8" class="colorSelector">
													<div style="background-color: #'.$MDParameters[0]['ColorFontSubSubMenuHover'].'">
													</div>
												</div>
												<div id="colorpickerHolder8" class="colorpickerHolder" name="colorpickerHolder8" ></div>
											</div>
										</td>
									</tr>
								</table>';
			$this->_html .= '</td></tr>';
			$this->_html .= '<tr><td>'.$this->l('Vertical padding').' : </td><td><input type="text" name="VerticalPadding" value="'.$MDParameters[0]['VerticalPadding'].'">&nbsp;px<sup>*</sup></td></tr>';
			$this->_html .= '</td></tr>';
		$this->_html .= '</table>';
		$this->_html .= '</td>';
		$this->_html .= '</tr>';
		$this->_html .= '<tr><td colspan="2" style="padding-top : 5px; padding-bottom : 5px" align="left">';
		$this->_html .= '<table width="100%">';
			$this->_html .= '<tr>';
				$this->_html .= '<td width="25%" valign="top"><b>'.$this->l('Line top').'</b>';
					$this->_html .= '<table cellpadding="0" cellspacing="0" border="0">
										<tr>
											<td style="border:0">'.$this->l('Active').' : </td>
											<td style="border:0"><input type="checkbox" id="stateTR1" name="stateTR1" '.($MDParameters[0]['stateTR1']=="on" ? "checked='checked'": false).'></td>
										</tr>
										<tr>
											<td style="border:0">'.$this->l('No color').' : </td>
											<td style="border:0"><input type="checkbox" id="noColorTR1" name="noColorTR1" '.($MDParameters[0]['backgroundTR1']=="" ? "checked='checked'": false).'></td>
										</tr>
										<tr>
											<td style="border:0">'.$this->l('Background Color').'&nbsp;</td>
											<td style="border:0"><input type="hidden" name="backgroundTR1" value="'.$MDParameters[0]['backgroundTR1'].'" id="HiddenColor13">
												<div id="customWidget13" class="customWidget">
													<div id="colorSelector13" class="colorSelector"><div style="background-color: #'.$MDParameters[0]['backgroundTR1'].'"></div></div>
													<div id="colorpickerHolder13" class="colorpickerHolder" style="z-index : 1000"></div>
												</div>
											</td>
										</tr>
										<tr>
											<td style="border:0">'.$this->l('Height line').' : </td>
											<td style="border:0"><input type="text" size="5" id="heightTR1" name="heightTR1" value="'.$MDParameters[0]['heightTR1'].'">&nbsp;px</td>
										</tr>
									 </table>';
				$this->_html .= '</td>';
				$this->_html .= '<td width="25%" valign="top"><b>'.$this->l('Left column').'</b>';
					$this->_html .= '<table cellpadding="0" cellspacing="0" border="0">
										<tr>
											<td style="border:0">'.$this->l('Active').' : </td>
											<td style="border:0"><input type="checkbox" id="stateTD1" name="stateTD1" '.($MDParameters[0]['stateTD1']=="on" ? "checked='checked'": false).'></td>
										</tr>
										<tr>
											<td style="border:0">'.$this->l('No color').' : </td>
											<td style="border:0"><input type="checkbox" id="noColorTD1" name="noColorTD1" '.($MDParameters[0]['backgroundTD1']=="" ? "checked='checked'": false).'></td>
										</tr>
										<tr>
											<td style="border:0">'.$this->l('Background Color').'&nbsp;</td>
											<td style="border:0"><input type="hidden" name="backgroundTD1" value="'.$MDParameters[0]['backgroundTD1'].'" id="HiddenColor9">
												<div id="customWidget9"  class="customWidget">
													<div id="colorSelector9" class="colorSelector"><div style="background-color: #'.$MDParameters[0]['backgroundTD1'].'"></div></div>
													<div id="colorpickerHolder9" class="colorpickerHolder" style="z-index : 1000"></div>
												</div>
											</td>
										</tr>
										<tr>
											<td style="border:0">'.$this->l('Width column').' : </td>
											<td style="border:0"><input type="text" size="5" id="widthTD1" name="widthTD1" value="'.$MDParameters[0]['widthTD1'].'">&nbsp;px</td>
										</tr>
									 </table>';
				$this->_html .= '</td>';
				$this->_html .= '<td width="25%" valign="top"><b>'.$this->l('Center column - Links detail').'</b>';
					$this->_html .= '<table cellpadding="0" cellspacing="0" style="border:0">
										<tr>
											<td style="border:0">'.$this->l('No color').' : </td>
											<td style="border:0">
												<input type="checkbox" id="noColorTD2" name="noColorTD2" '.($MDParameters[0]['backgroundTD2']=="" ? "checked='checked'": false).'>
											</td>
										</tr>
										<tr>
											<td>'.$this->l('Background Color').'&nbsp;:&nbsp;</td>
											<td style="border:0"><input type="hidden" name="backgroundTD2" value="'.$MDParameters[0]['backgroundTD2'].'" id="HiddenColor11">
												<div id="customWidget11" class="customWidget">
													<div id="colorSelector11" class="colorSelector"><div style="background-color: #'.$MDParameters[0]['backgroundTD2'].'"></div></div>
													<div id="colorpickerHolder11" class="colorpickerHolder" style="z-index : 1000"></div>
												</div>
											</td>
										</tr>
									 </table>';
				$this->_html .= '</td>';
				$this->_html .= '<td width="25%" valign="top"><b>'.$this->l('Right column').'</b>';
					$this->_html .= '<table cellpadding="0" cellspacing="0" border="0">
										<tr>
											<td style="border:0">'.$this->l('Active').' : </td>
											<td style="border:0"><input type="checkbox" id="stateTD3" name="stateTD3" '.($MDParameters[0]['stateTD3']=="on" ? "checked='checked'": false).'></td>
										</tr>
										<tr>
											<td style="border:0">'.$this->l('No color').' : </td>
											<td style="border:0"><input type="checkbox" id="noColorTD3" name="noColorTD3" '.($MDParameters[0]['backgroundTD3']=="" ? "checked='checked'": false).'></td>
										</tr>
										<tr>
											<td style="border:0">'.$this->l('Background Color').'&nbsp;</td>
											<td style="border:0"><input type="hidden" name="backgroundTD3" value="'.$MDParameters[0]['backgroundTD3'].'" id="HiddenColor10">
												<div id="customWidget10" class="customWidget">
													<div id="colorSelector10" class="colorSelector"><div style="background-color: #'.$MDParameters[0]['backgroundTD3'].'"></div></div>
													<div id="colorpickerHolder10" class="colorpickerHolder" style="z-index : 1000"></div>
												</div>
											</td>
										</tr>
										<tr>
											<td style="border:0">'.$this->l('Width column').' : </td>
											<td style="border:0"><input type="text" size="5" id="widthTD3" name="widthTD3" value="'.$MDParameters[0]['widthTD3'].'">&nbsp;px</td>
										</tr>
									 </table>';
				$this->_html .= '</td>';
			$this->_html .= '</tr>';
		$this->_html .= '</table>';
		$this->_html .= '</td></tr>
		</table>';
		$this->_html .= '<p align="center"><input name="SubmitButtonDesign" type="submit" value="'.$this->l('  Save  ').'" class="button"></p>';
		$this->_html .= '<div id="DetailActualPictures">
											<table cellpadding="0" cellspacing="2" border="0" width="100%" class="table">
												<tr>
													<td colspan="9" align="center" class="alt_row" onclick="AfficheMasqueSlide(\'ActualPictures\')" style="cursor: pointer">'.$this->l('Click here to view pictures').'</td>
												</tr>
											</table>
											<div width="100%" id="ActualPictures" style="display: none">
											<table width="100%">
												<tr>
													<td width="50%" align="center" style="font-weight: bold">
														'.$this->l('Menu').'&nbsp;
														<img src="../img/admin/disabled.gif" onclick="deletePicture(\'bg_menu-'.$this->context->shop->id.$MDParameters[0]['extensionMenu'].'\')" style="cursor: pointer" title="'.$this->l('delete').'">
													</td>
													<td align="center" style="font-weight: bold">
														'.$this->l('Button <i>(Height = 2 X Menu Height)</i>').'&nbsp;
														<img src="../img/admin/disabled.gif" onclick="deletePicture(\'bg_bout-'.$this->context->shop->id.$MDParameters[0]['extensionBout'].'\')" style="cursor: pointer" title="'.$this->l('delete').'">
													</td>
												</tr>';
				$this->_html .= '<tr>';
				if(is_file(dirname(__FILE__).'/views/img/menu/bg_menu-'.$this->context->shop->id.$MDParameters[0]['extensionMenu']))
					$this->_html .= '<td width="50%" align="center"><img src="'.$this->_path.'views/img/menu/bg_menu-'.$this->context->shop->id.$MDParameters[0]['extensionMenu'].'"></td>';
				else
					$this->_html .= '<td width="50%" align="center">&nbsp;</td>';
				if(is_file(dirname(__FILE__).'/views/img/menu/bg_bout-'.$this->context->shop->id.$MDParameters[0]['extensionBout']))
					$this->_html .= '<td align="center"><img src="'.$this->_path.'views/img/menu/bg_bout-'.$this->context->shop->id.$MDParameters[0]['extensionBout'].'"></td>';
				else
					$this->_html .= '<td width="50%" align="center">&nbsp;</td>';
				$this->_html .= '</tr>';
				$this->_html .= '<tr>';
				$this->_html .= '<td align="center" style="font-weight: bold">'.$this->l('Background Submenu').'&nbsp;<img src="../img/admin/disabled.gif" onclick="deletePicture(\'sub_bg-'.$this->context->shop->id.$MDParameters[0]['extensionBack'].'\')" style="cursor: pointer" title="'.$this->l('delete').'"></td><td align="center" style="font-weight: bold">'.$this->l('List Arrow').'&nbsp;<img src="../img/admin/disabled.gif" onclick="deletePicture(\'navlist_arrow-'.$this->context->shop->id.$MDParameters[0]['extensionArro'].'\')" style="cursor: pointer" title="'.$this->l('delete').'"></td></tr>';
				$this->_html .= '<tr>';
				if(is_file(dirname(__FILE__).'/views/img/menu/sub_bg-'.$this->context->shop->id.$MDParameters[0]['extensionBack'].''))
					$this->_html .= '<td width="50%" align="center"><img src="'.$this->_path.'views/img/menu/sub_bg-'.$this->context->shop->id.$MDParameters[0]['extensionBack'].'"></td>';
				else
					$this->_html .= '<td width="50%" align="center">&nbsp;</td>';
				if(is_file(dirname(__FILE__).'/views/img/menu/navlist_arrow-'.$this->context->shop->id.$MDParameters[0]['extensionArro'].''))
					$this->_html .= '<td align="center"><img src="'.$this->_path.'views/img/menu/navlist_arrow-'.$this->context->shop->id.$MDParameters[0]['extensionArro'].'"></td>';
				else
					$this->_html .= '<td width="50%" align="center">&nbsp;</td>';
				$this->_html .= '</tr>
							</table>
							</div>
						</div>';
		$this->_html .= '<input type="hidden" id="FileToDelete" name="FileToDelete">';
		$this->_html .= '<input type="hidden" id="ActionFile" name="ActionFile">';
		$this->_html .= '</form>';
		$this->_html .= '</fieldset><br />';
		$this->_html .= '
		<fieldset style="float: left; margin-right: 25px;">
			<legend><img src="../img/admin/add.gif" alt="" title="" />'.$this->l('Add button').'&nbsp;</legend>';
		$this->_html .= '<br /><form action="'.$_SERVER['REQUEST_URI'].'" method="post" name="formnewbutton" id="formnewbutton">';
		$this->_html .= '<table cellpadding="0"><tr><td>'.$this->l('Name').' : </td><td>';
		foreach ($languages as $language) {
			$this->_html .= '
			<div id="Buttons_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;" class="divLang">
				<input type="text" id="ButtonName_'.$language['id_lang'].'" name="ButtonName_'.$language['id_lang'].'">
			</div>';
		 }
		$this->_html .= $this->displayFlagsMD($languages, $defaultLanguage, 'Buttons', 'Buttons', true);
		$this->_html .= '</td></tr>';
		$this->_html .= '</table><br /><br />';
		$this->_html .= '<p align="center"><input name="SubmitButton" type="submit" value="'.$this->l('  Save  ').'" class="button"></p>';
		$this->_html .= '<input type="hidden" id="ButtonIdAction" name="ButtonIdAction" value="">';
		$this->_html .= '<input type="hidden" id="Action" name="Action" value="">';
		$this->_html .= '</form>';
		$this->_html .= '</fieldset>';
		$this->_html .= '<fieldset>
			<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('MeGa DrOwN mEnU organization').'&nbsp;';
		$this->_html .= '</legend>';
		$this->_html .= '<form action="'.$_SERVER['REQUEST_URI'].'" method="post" name="formorganizebutton" id="formorganizebutton"><br />';
		$this->_html .= '<div width="100%"><ul id="sortable">';
		if(sizeof($MDConfiguration)) {
			foreach($MDConfiguration as $kMDConf=>$ValMDConf) {
				$this->_html .= '<li style="float:left" class="Buttonlistitem" id="button_'.$ValMDConf['id_button'].'">';
				$this->_html .= '<table class="table"><tr><TH width="10" style="width: auto">&nbsp;&nbsp;'.$ValMDConf['name_button'].'&nbsp;&nbsp;';
				$this->_html .= '<img src="../img/admin/edit.gif" title="'.$this->l('Edit').'" onclick="EditButton(\''.$ValMDConf['id_button'].'\')" style="cursor:pointer">';
				$this->_html .= '&nbsp;';
				$this->_html .= '<img src="../img/admin/disabled.gif" title="'.$this->l('Delete').'" onclick="DeleteButton(\''.$ValMDConf['id_button'].'\')" style="cursor:pointer">';
				$this->_html .= '&nbsp;</TH></tr></table>';
				$this->_html .= '</li>';
				$tabButtonsOrganizate[] = "button_".$ValMDConf['id_button'];
			}
		}
		$this->_html .= '</ul></div><br /><br /><br /><br />';
		$this->_html .= '<p align="center"><input name="SubmitButtonOrganization" type="submit" value="'.$this->l('  Save  ').'" class="button"></p>';
		$this->_html .= '<input type="hidden" id="Organisation" name="Organisation" value="'.implode(',',$tabButtonsOrganizate).'">';
		$this->_html .= '<input type="hidden" value="'.$ButtonIdInEdit.'" id="ButttonIdToUpdateOrganization" name="ButttonIdToUpdateOrganization">';
		$this->_html .= '</form>';
		$this->_html .= '</fieldset><br />';
		$this->_html .= '<script>
						$(function() {
							$("#sortable").sortable({ opacity: 0.6, scroll: true ,position:"left", revert: true , cursor: "move", update: function() {
								$("#Organisation").val($(this).sortable("toArray"))
							}});
							$("#sortable").disableSelection();
						});
						</script>';
		if($ButtonIdInEdit!='' && $ButtonIdInEdit!=0) {
			$ButtonDetail 	 			= array();
			$ButtonLinksCat  			= array();
			$ButtonLinks	 			= array();
			$tabCategoryTextState		= array();
			$tabCategoryTextSubstitute 	= array();
			$tabCategoryColumn			= array();
			$tabCategoryLine			= array();
			$tabCategorySelected		= array();
			$tabCategoryViewProducts	= array();
			$ButtonDetail 	 = $this->getButtonDetail($ButtonIdInEdit);
			$ButtonLinksCat  = $this->getButtonLinksCat($ButtonIdInEdit);
			$ButtonLinks  	 = $this->getButtonLinks($ButtonIdInEdit);
			$ButtonOrganization  	 = $this->getButtonOrganization($ButtonIdInEdit);
			$ButtonNameSubstitute  	 = $this->getAllNameSubstitute($ButtonIdInEdit);
			if(sizeof($ButtonDetail)) {
				foreach($ButtonDetail as $kDetail=>$ValDetail) {
					$tabNameLang[$ValDetail['id_lang']] = $ValDetail['name_button'];
					$tabDetailSubLang[$ValDetail['id_lang']] = $ValDetail['detailSub'];
					$tabDetailSubLeftLang[$ValDetail['id_lang']] = $ValDetail['detailSubLeft'];
					$tabDetailSubTrLang[$ValDetail['id_lang']] = $ValDetail['detailSubTR'];
				}
			}
			if(sizeof($ButtonLinksCat) && is_array($ButtonLinksCat))
				foreach($ButtonLinksCat as $k) {
						$tabCategorySelected[$k['id_link_cat']] = $k['id_link_cat'];
						$tabCategoryColumn[$k['id_link_cat']] = $k['num_column'];
						$tabCategoryViewProducts[$k['id_link_cat']] = $k['view_products'];
				}
			if(sizeof($ButtonOrganization) && is_array($ButtonOrganization))
				foreach($ButtonOrganization as $k) {
						$tabCategoryLine[$k['id_link_cat']] = $k['num_ligne'];
						//$tabCategoryTextSubstitute[$k['id_link_cat']] = $k['name_substitute'];
						$tabCategoryTextState[$k['id_link_cat']] = $k['state'];
				}
			if(sizeof($ButtonNameSubstitute) && is_array($ButtonNameSubstitute))
				foreach($ButtonNameSubstitute as $k) {
					$tabCategoryTextSubstitute[$k['id_cat']][$k['id_lang']] = $k['name_substitute'];
				}

			$this->_html .= '<fieldset>
				<legend><img src="'.$this->_path.'views/img/parametres.gif">'.$this->l('Parameters').'&nbsp;';
			$this->_html .= '</legend><br />';
			$this->_html .= '<form enctype="multipart/form-data" id="form_subTR" name="form_subTR" method="post" action="" style="display : inline;float: left; margin-right: 20px;">
							 <input type="hidden" name="idButton" value="'.$ButtonIdInEdit.'">
									<div style="width: 100%;">
										<table cellpadding="0" cellspacing="0" width="510px" class="table">
										<tr><TH colspan="2" align="center">'.$this->l('Line top').'</TH></tr>';
						$this->_html .= '<tr><td colspan="2" width="100%">';
						foreach ($languages as $language) {
							$this->_html .= '
							<div id="detailSubTrDiv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;" class="divLang">
								<textarea class="rte" cols="90" rows="10" id="detailSubTr_'.$language['id_lang'].'" name="detailSubTr_'.$language['id_lang'].'">'.(isset($tabDetailSubTrLang[$language['id_lang']]) ?  $tabDetailSubTrLang[$language['id_lang']] : '').'</textarea>
							</div>';
						 }
						$this->_html .= $this->displayFlagsMD($languages, $defaultLanguage, 'detailSubTrDiv', 'detailSubTrDiv', true);
						$this->_html .= '<br /><br />
						';
						$this->_html .= '</td></tr>';
						$this->_html .= '<tr><td colspan="2" align="center"><input type="submit" value="'.$this->l('  Save  ').'" class="button" name="SubmitDetailSubTr"></td></tr>
									</table>
									</div>
							 </form>
							';
			$this->_html .= '<form enctype="multipart/form-data" id="form_upload_picture" name="form_upload_picture" method="post" action="" style="display : inline; float: left; margin-right: 20px;">
									<div style="width: 100%;">
										<table cellpadding="0" cellspacing="0" width="510px" class="table">
										<tr><TH colspan="2" align="center">'.$this->l('Left column').'</TH></tr>
										<tr><td width="50%">
										&nbsp;'.$this->l('Select your picture to upload').'&nbsp;<input type="file" id="PictureFile" name="PictureFile" style="font-size : 10px">
										<input type="hidden" value="'.urlencode($this->_path).'" name="AccessPath">
										<input type="hidden" value="UploadPicture" name="ActionPicture" id="ActionPicture">
										<input type="hidden" name="idButton" value="'.$ButtonIdInEdit.'">
										<input type="hidden" name="NamePicture" value="'.$ButtonDetail[0]['img_name'].'">
										<br /><br />
										&nbsp;'.$this->l('Link').' : <input type="text" id="imgLink" name="imgLink" value="'.urldecode($ButtonDetail[0]['img_link']).'" style="width: 310px">
										<br /><br />
										</td><td align="center">';
										if($ButtonDetail[0]['img_name'] != "") {
											$this->_html .= '<table width="50%" cellpadding="0" cellspacing="0"><tr><td width="50%" align="right" valign="top">';
											$this->_html .= '<img src="'.$this->_path.'views/img/menu/'.$ButtonDetail[0]['img_name'].'" border="0">';
											$this->_html .= '</td><td valign="bottom" align="left">';
											$this->_html .= '<img src="../img/admin/disabled.gif" onclick="deletePictureMenu()" style="cursor: pointer">';
											$this->_html .= '</td></tr></table>';
										}
					$this->_html .= '</td></tr>'.$this->eol;
					$this->_html .= '<tr><td colspan="2">';
					foreach ($languages as $language) {
						$this->_html .= '
						<div id="detailSubLeftDiv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;" class="divLang">
							<textarea class="rte" cols="90" rows="10" id="detailSubLeft_'.$language['id_lang'].'" name="detailSubLeft_'.$language['id_lang'].'">'.(isset($tabDetailSubLeftLang[$language['id_lang']]) ?  $tabDetailSubLeftLang[$language['id_lang']] : '').'</textarea>
						</div>';
					 }
					$this->_html .= $this->displayFlagsMD($languages, $defaultLanguage, 'detailSubLeftDiv', 'detailSubLeftDiv', true);
					$this->_html .= '<br /><br />
					';
					$this->_html .= '</td></tr>';
					$this->_html .= '<tr><td colspan="2" align="center"><input type="submit" value="'.$this->l('  Save  ').'" class="button"></td></tr>
									</table>
									</div>
							 </form>
							';
			$this->_html .= '
			<form enctype="multipart/form-data" id="form_upload_picture" name="formDetailSub" method="post" action="" style="display : inline">
			<table cellpadding="0" cellspacing="0" width="510px" class="table">
			<tr><TH align="center" colspan="2">'.$this->l('Right column').'</TH></tr>
			<tr><td>';
			foreach ($languages as $language) {
				$this->_html .= '
				<div id="detailSubDiv_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;" class="divLang">
					<textarea class="rte" cols="90" rows="10" id="detailSub_'.$language['id_lang'].'" name="detailSub_'.$language['id_lang'].'">'.(isset($tabDetailSubLang[$language['id_lang']]) ?  $tabDetailSubLang[$language['id_lang']] : '').'</textarea>
				</div>';
			 }
			$this->_html .= '</td><td width="250px" valign="top">';
			$this->_html .= $this->displayFlagsMD($languages, $defaultLanguage, 'detailSubDiv', 'detailSubDiv', true);
			$this->_html .= '<br /><br />
			';
			$this->_html .= '</td></tr>
							<tr><td align="center" colspan="2"><input type="hidden" name="idButton" value="'.$ButtonIdInEdit.'">
							<input type="submit" value="'.$this->l('  Save  ').'" class="button" name="SubmitDetailSub"></td></tr>
							</table><br /></form>';
			$this->_html .= '<br /><br /><br /><br /><br /><br /><br /><br />
			<table cellpadding="0" cellspacing="0" width="100%" class="table">
			<tr><TH align="center" colspan="2">'.$this->l('Center column - Links detail').'</TH></tr>
			</table><br />';
			$this->_html .= '<form enctype="multipart/form-data" id="form_upload_picture_background" name="form_upload_picture_background" method="post" action="" style="display : inline; ">
									<div style="width: 100%;">
										<table cellpadding="0" cellspacing="0" width="100%" class="table">
										<tr><td width="50%">
										&nbsp;'.$this->l('Select your picture to upload').'&nbsp;<input type="file" id="PictureFileBackground" name="PictureFileBackground" style="font-size : 10px">
										<input type="hidden" value="'.urlencode($this->_path).'" name="AccessPath">
										<input type="hidden" value="UploadPictureBackground" name="ActionPictureBackground" id="ActionPictureBackground">
										<input type="hidden" name="idButton" value="'.$ButtonIdInEdit.'">
										<input type="hidden" name="NamePictureBackground" value="'.$ButtonDetail[0]['img_name_background'].'">
										</td></tr>
										<tr><td align="center">';
										if($ButtonDetail[0]['img_name_background'] != "") {
											$this->_html .= '<table width="50%" cellpadding="0" cellspacing="0"><tr><td width="50%" align="right" valign="top">';
											$this->_html .= '<img src="'.$this->_path.'views/img/menu/'.$ButtonDetail[0]['img_name_background'].'" border="0">';
											$this->_html .= '</td><td valign="bottom" align="left">';
											$this->_html .= '<img src="../img/admin/disabled.gif" onclick="deletePictureBackground()" style="cursor: pointer">';
											$this->_html .= '</td></tr></table>';
										}
					$this->_html .= '</td></tr>'.$this->eol;
					$this->_html .= '<tr><td align="center"><input type="submit" value="'.$this->l('  Save  ').'" class="button"></td></tr>
									</table>
									</div>
							 </form>
							 <br />
							';
			
			$this->_html .='<form action="'.$_SERVER['REQUEST_URI'].'" method="post" name="formbuttonparameters" id="formbuttonparameters">';
			$this->_html .= '<table cellpadding="0" cellspacing="1" width="100%">';
			$this->_html .= '<tr><td>'.$this->l('Button Name').' : </td><td>';
			foreach ($languages as $language) {
				$this->_html .= '
				<div id="ButtonsEdit_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;" class="divLang">
					<input type="text" id="ButtonNameEdit_'.$language['id_lang'].'" name="ButtonNameEdit_'.$language['id_lang'].'" value="'.(isset($tabNameLang[$language['id_lang']]) ?  $tabNameLang[$language['id_lang']] : '') .'">
				</div>';
			 }
			$this->_html .= $this->displayFlagsMD($languages, $defaultLanguage, 'ButtonsEdit', 'ButtonsEdit', true);
			$this->_html .= '</td></tr>';
			$this->_html .= '<tr>
								<td style="border:0">'.$this->l('No color').' : </td>
								<td style="border:0"><input type="checkbox" id="noColorButton" name="noColorButton" '.($ButtonDetail[0]['buttonColor']=="" ? "checked='checked'": false).'></td>
							</tr>
							';
			$this->_html .= '<tr><td colspan="2" style="padding-top : 5px; padding-bottom : 5px" align="left">';
			$this->_html .= '<table cellpadding="0" cellspacing="0" style="border:0"><tr><td>'.$this->l('Button Color & background sub-menu').'&nbsp;:&nbsp;</td><td style="border:0"><input type="hidden" name="buttonColor" value="'.$ButtonDetail[0]['buttonColor'].'" id="HiddenColor12">
								<div id="customWidget12" class="customWidget">
									<div id="colorSelector12" class="colorSelector"><div style="background-color: #'.$ButtonDetail[0]['buttonColor'].'"></div></div>
									<div id="colorpickerHolder12" class="colorpickerHolder" style="z-index : 1000">
									</div>
								</div>
							</td></tr></table>';
			$this->_html .= '</td></tr>';
			$this->_html .= '<tr><td>'.$this->l('Link').' : </td><td><input type="text" id="LinkPage" name="LinkPage" value="'.(isset($ButtonLinks[0]['link']) ? $ButtonLinks[0]['link'] : '').'" size="100"></td></tr>';
			$this->_html .= '<tr><td colspan="2" align="center" style="padding-top : 5px; padding-bottom : 5px; font-weight : bold"><img src="'.$this->_path.'views/img/fleche_bas.png"/>&nbsp;'.$this->l('AND/OR').'&nbsp;&nbsp;<img src="'.$this->_path.'views/img/fleche_bas.png"/></td></tr>';
			$this->_html .= '<tr><td colspan="2">';
			$this->_html .='<label style="margin-left : -100px; font-weight : normal">'.$this->l('Categories').' : </label>
						<div class="margin-form" style="margin-left : -100px; width: 90%">
								<table cellspacing="0" cellpadding="0" class="table" width="100%">
										<tr>
											<th width="20"><input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, \'categoryBox[]\', this.checked)" /></th>
											<th width="20">'.$this->l('ID').'</th>
											<th width="220" nowrap><nobr>'.$this->l('Name').'</nobr></th>
											<th>&nbsp;</th>
										</tr>';
			$irow = 0;
			$done = array();
			$index = array();
			$indexedCategories = array();
			$categories = Category::getCategories(intval($this->context->cookie->id_lang), false);
			foreach ($indexedCategories AS $k => $row)
				$index[] = $row['id_category'];									
			$this->recurseCategory($index, $categories, $categories[0][1], 1, NULL, $tabCategorySelected, $tabCategoryLine, $tabCategoryColumn, $tabCategoryTextSubstitute, $tabCategoryTextState, $tabCategoryViewProducts);
			$this->_html .='
					</table>
					</div>';
			$this->_html .= '<p align="center"><input name="SubmitButtonParameters" type="submit" value="'.$this->l('  Save  ').'" class="button"></p>';
			$this->_html .= '<input type="hidden" value="'.$ButtonIdInEdit.'" id="ButttonIdToUpdate" name="ButttonIdToUpdate">';
			$this->_html .= '</td></tr>';
			$this->_html .= '<tr><td colspan="2" align="center" style="padding-top : 5px; padding-bottom : 5px; font-weight : bold"><img src="'.$this->_path.'views/img/fleche_bas.png"/>&nbsp;'.$this->l('AND/OR').'&nbsp;&nbsp;<img src="'.$this->_path.'views/img/fleche_bas.png"/></td></tr>';
			$this->_html .= '<tr><td colspan="2">&nbsp;<span style="float:left">'.$this->l('Custom Menu').'&nbsp;</span>';
			foreach ($languages as $language) {
				$this->_html .= '<div id="CustomName_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').'; float: left" class="divLang">';
				$this->_html .= '<input type="text" id="CustomMenuName_'.$language['id_lang'].'" name="CustomMenuName_'.$language['id_lang'].'">';
				$this->_html .= '</div>';
			}
			$this->_html .= $this->displayFlagsMD($languages, $defaultLanguage, 'CustomName', 'CustomName', true);
			$this->_html .= '&nbsp;<input type="button" value="'.$this->l('Add').'" onclick=\'addCustomMenu("'.$ButtonIdInEdit.'")\' class="button">&nbsp;</td></tr>';
			$this->_html .= '<tr><td colspan="2"><br />';
			$this->_html .= '<div id="customMenu" width="100%">';
			$this->_html .= '</div>';
			$this->_html .= '</td></tr>';
			$this->_html .= '</table>';
			$this->_html .= '</form>';
			$this->_html .= '</fieldset><br />';
			$this->_html .= '<script>';
			$this->_html .= '$(document).ready(function() { displayDetailMenu(); });';
			$this->_html .= '</script>';
		}
		
		return	$this->_html;
	}
        
	private function makeMegaDrown() 
	{
		$IdLang = $this->context->cookie->id_lang;
		
		if(Tools::getIsset('id_category'))
			$ActiveCategory = (int)Tools::getValue('id_category');
		else
			$ActiveCategory = null;
			
		if(Tools::getIsset('id_product'))
		{			
			if (!isset($this->context->cookie->last_visited_category) 
					OR !Product::idIsOnCategoryId(intval($_GET['id_product']), array('0' => array('id_category' => $this->context->cookie->last_visited_category))))
			{
				$product = new Product(intval($_GET['id_product']));
				if (isset($product) AND Validate::isLoadedObject($product))
					$ActiveCategory = (int)$product->id_category_default;
			}
			else
				$ActiveCategory = (int)$this->context->cookie->last_visited_category;
		}	
		
		if($ActiveCategory !== null) {
			$resultCat = Db::getInstance()->ExecuteS('
				SELECT *  
				FROM '._DB_PREFIX_.'admevo_button_link_cat 
				WHERE id_link_cat='.$ActiveCategory);
		}
		$MDParameters = $this->getParameters();
		$MDConfiguration = $this->getConfigurations();
		if(sizeof($MDConfiguration)) 
		{
			foreach($MDConfiguration as $kButton => $ValButton) {
				$tabLinkButton[$ValButton['id_button']] = array();
				$tabIdLinkCat[$ValButton['id_button']] = array();
				$tabLinkCustom[$ValButton['id_button']] = array();
				$CatMenu 	= array();
				$CatMenu 	= $this->getButtonLinksCat($ValButton['id_button']);
				$LinkButton = $this->getButtonLinks($ValButton['id_button']);
				if(array_key_exists( 0, $LinkButton))
					$linkButton = $LinkButton[0]['link'];
				else
					$linkButton = "";
				$tabLinkButton[$ValButton['id_button']][] = basename($linkButton);
				if(sizeof($CatMenu)) 
				{
					foreach($CatMenu as $kMenu=>$ValCat) 
					{
						$tabIdLinkCat[$ValButton['id_button']][$ValCat['id_link_cat']] = $ValCat['id_link_cat'];
						$DescendantCateogries = Db::getInstance()->ExecuteS('
							SELECT *  
							FROM '._DB_PREFIX_.'category  
							WHERE id_parent='.$ValCat['id_link_cat']);
							if(sizeof($DescendantCateogries))
								foreach($DescendantCateogries as $kDescCat=>$ValDescCat)
									$tabIdLinkCat[$ValButton['id_button']][$ValDescCat['id_category']] = $ValDescCat['id_category'];
					}
				}
				$CustomMenu = array();
				$CustomMenu = $this->getButtonLinksCustom($ValButton['id_button'], $this->context->cookie->id_lang);
				if(sizeof($CustomMenu)) 
				{
					foreach($CustomMenu as $kMenu=>$ValMenu) 
					{
						$tabLinkCustom[$ValButton['id_button']][$ValMenu['id_custom']] = basename($ValMenu['link']);
						$CustomMenuUnder = array();
						$CustomMenuUnder = $this->getButtonLinksCustomUnder($ValButton['id_button'], $ValMenu['id_custom'], $this->context->cookie->id_lang);
						if(sizeof($CustomMenuUnder))
							foreach($CustomMenuUnder as $kDescCustom=>$ValDescCustom)
								$tabLinkCustom[$ValButton['id_button']][$ValDescCustom['id_custom']] = basename($ValDescCustom['link']);
					}
				}
			}
		}
		if(sizeof($MDConfiguration)) 
		{
			$b = 0;
			foreach($MDConfiguration as $kButton=>$ValButton) 
			{
				$LinkButton = $this->getButtonLinks($ValButton['id_button']);
				(!array_key_exists(0 , $LinkButton)) ? $linkButton = "#" : $linkButton = $LinkButton[0]['link'];
				$this->_menu .= '<li style="background-color: #'.$ValButton['buttonColor'].'" class="liBouton liBouton'.$b.'">'.$this->eol;
				strpos(strtolower($ValButton['name_button']), "<br />") ? $decal="margin-top : -5px;" : $decal="" ;
				$this->_menu .= '<div'.($decal!=0 ? ' style="'.$decal.'"' : '').'><a href="'.$linkButton.'" '.($linkButton=="#" ? "onclick='return false'" : false).' class="buttons" '.(in_array($ActiveCategory, $tabIdLinkCat[$ValButton['id_button']]) || in_array(basename($_SERVER['REQUEST_URI']), $tabLinkCustom[$ValButton['id_button']]) || in_array(basename($_SERVER['REQUEST_URI']), $tabLinkButton[$ValButton['id_button']]) ? 'style="background-position : 0 -'.$MDParameters[0]['MenuHeight'].'px; color: #'.$MDParameters[0]['ColorFontMenuHover'].'"' : false ).'>'.$ValButton['name_button'].'</a></div>'.$this->eol;
				$CatMenu 	= array();
				$CatMenu 	= $this->getButtonLinksCat($ValButton['id_button']);
				$CustomMenu = array();
				$CustomMenu = $this->getButtonLinksCustom($ValButton['id_button'], $this->context->cookie->id_lang);
				$NbColsMax 	= $this->getMaxColumns($ValButton['id_button']);
				$MaxCols	= 0;
				$MaxLines	= 0;
				$tabLines	= array();
				$m=0;
				if(sizeof($CatMenu)) 
				{
					foreach($CatMenu as $kMenu=>$ValCat) 
					{
						$tabLines[$kButton][$ValCat['num_ligne']] 									= $ValCat['num_ligne'];
						$tabLinesOrder[$kButton][$ValCat['num_ligne']][$ValCat['num_column']] 		= $ValCat['num_column'];
						$tabLinesDatas[$kButton][$ValCat['num_ligne']][$ValCat['num_column']][$m]	= $ValCat;
						$tabLinesType[$kButton][$ValCat['num_ligne']][$ValCat['num_column']][$m]	= 'category';
						$tabColumn[$kButton][$ValCat['num_ligne']] 									= $ValCat['num_column'];
						$tabColumnOrder[$kButton][$ValCat['num_column']][$ValCat['num_ligne']] 		= $ValCat['num_ligne'];
						$tabColumnDatas[$kButton][$ValCat['num_column']][$ValCat['num_ligne']][$m]	= $ValCat;
						$tabColumnType[$kButton][$ValCat['num_column']][$ValCat['num_ligne']][$m]	= 'category';
						$m++;
						$MaxCols <($ValCat['num_column']*1) ? $MaxCols = $ValCat['num_column'] : false;
						$MaxLines <($ValCat['num_ligne']*1) ? $MaxLines = $ValCat['num_ligne'] : false;
					}
				}
				if(sizeof($CustomMenu)) 
				{
					foreach($CustomMenu as $kCustom=>$ValCustom) 
					{
						$tabLines[$kButton][$ValCustom['num_ligne']] 										= $ValCustom['num_ligne'];
						$tabLinesOrder[$kButton][$ValCustom['num_ligne']][$ValCustom['num_column']] 		= $ValCustom['num_column'];
						$tabLinesDatas[$kButton][$ValCustom['num_ligne']][$ValCustom['num_column']][$m]		= $ValCustom;
						$tabLinesType[$kButton][$ValCustom['num_ligne']][$ValCustom['num_column']][$m]		= 'custom';
						$tabColumn[$kButton][$ValCustom['num_ligne']] 										= $ValCustom['num_column'];
						$tabColumnOrder[$kButton][$ValCustom['num_column']][$ValCustom['num_ligne']] 		= $ValCustom['num_ligne'];
						$tabColumnDatas[$kButton][$ValCustom['num_column']][$ValCustom['num_ligne']][$m]	= $ValCustom;
						$tabColumnType[$kButton][$ValCustom['num_column']][$ValCustom['num_ligne']][$m]		= 'custom';
						$m++;
						$MaxCols <($ValCustom['num_column']*1) ? $MaxCols = $ValCustom['num_column'] : false;
						$MaxLines <($ValCustom['num_ligne']*1) ? $MaxLines = $ValCustom['num_ligne'] : false;
					}
				}
				if(array_key_exists( $kButton, $tabLines))
				if(sizeof($tabLines[$kButton])) 
				{
					$this->_menu .= '<div class="sub" style="width: '.($MDParameters[0]['MenuWidth'] - 2).'px;  background-color: #'.$ValButton['buttonColor'].'; '.($ValButton['img_name_background']!="" ? 'background-image: url('.$this->_path.'views/img/menu/'.$ValButton['img_name_background'].'); background-repeat:no-repeat; background-position:top left; ' : false).' ">'.$this->eol;
					$this->_menu .= '<table class="megaDrownTable" cellpadding="0" cellspacing="0" width="100%">';
					if($MDParameters[0]['stateTR1']=="on") 
					{
						$this->_menu .= '<tr style="height:'.$MDParameters[0]['heightTR1'].'px">';
							$MDParameters[0]['stateTD1']=="on" ? $nbColspan = 2 : $nbColspan = 1;
							$this->_menu .= '<td class="megaDrownTR1" valign="top" colspan="'.$nbColspan.'">'.$this->eol;
							$this->_menu .= $ValButton['detailSubTR']=="" ? "&nbsp;" : html_entity_decode($ValButton['detailSubTR']);
							$this->_menu .= '</td>';
							$this->_menu .= '<td rowspan="2" class="megaDrownTD3" valign="top" style="width:'.$MDParameters[0]['widthTD3'].'px">'.($ValButton['detailSub']=="" ? "&nbsp;" : html_entity_decode($ValButton['detailSub'])).'</td>'.$this->eol;
						$this->_menu .= '</tr>';
					}
					$this->_menu .= '<tr>';
					if($MDParameters[0]['stateTD1']=="on") {
						$this->_menu .= '<td class="megaDrownTD1" valign="top" style="width:'.$MDParameters[0]['widthTD1'].'px">'.$this->eol;
						if($ValButton['img_name'] != '') {
							if($ValButton['img_link'] != '')
								$this->_menu .= '<a href="'.urldecode($ValButton['img_link']).'" style="float:none; margin:0; padding:0">';
							$this->_menu .= '<img src="'.$this->_path.'views/img/menu/'.$ValButton['img_name'].'" style="border:0px" alt="'.$ValButton['img_name'].'"/>'.$this->eol;
							if($ValButton['img_link'] != '')
								$this->_menu .= '</a>';
						}
						$this->_menu .= '<br />'.html_entity_decode($ValButton['detailSubLeft']).'</td>';
					}
					$this->_menu .= '<td class="megaDrownTD2" valign="top">'.$this->eol;
					$this->_menu .= '<table class="MegaEvoLinks" style="border:0px">'.$this->eol;
					$this->_menu .= '<tr>'.$this->eol;
					for($c=1; $c<=$MaxCols; $c++) {						
          	$this->_menu .= '<td valign="top" class="col'.$c.'">'.$this->eol;

						for($l=1; $l<=$MaxLines; $l++) 
						{
							if(array_key_exists($c, $tabColumnDatas[$kButton]))
							if(array_key_exists($l, $tabColumnDatas[$kButton][$c]))
							if(sizeof(@$tabColumnDatas[$kButton][$c][$l])) 
							{
								$this->_menu .= '<table border="0" style="width:'.$MDParameters[0]['columnSize'].'px">'.$this->eol;
								foreach($tabColumnDatas[$kButton][$c][$l] as $keyMenu=>$ValMenu) 
								{
									$this->_menu .= '<tr>'.$this->eol;
									$this->_menu .= '<td style="width:'.$MDParameters[0]['columnSize'].'px" class="ligne'.$l.'">'.$this->eol;
									switch($tabColumnType[$kButton][$c][$l][$keyMenu]) 
									{
										case 'category':
											$category = new Category((int)$ValMenu['id_link_cat']);
										
											if(!$category->checkAccess($this->context->customer->id))
												break;
											else
											{
												$this->_menu .= '<ul>'.$this->eol;
												$NameCategory = $this->getNameCategory($ValMenu['id_link_cat'], $this->context->cookie->id_lang, $ValButton['id_button']);
												$NameSubstitute = $this->getNameSubstitute($ValMenu['id_link_cat'], $this->context->cookie->id_lang, $ValButton['id_button']);
												$Category = new Category(intval($ValMenu['id_link_cat']), intval($this->context->cookie->id_lang));
												$rewrited_url = $this->link->getCategoryLink($ValMenu['id_link_cat'], $Category->link_rewrite);
												$this->_menu .= '	<li class="stitle">
																						<a href="'.$rewrited_url.'" style="text-align:left">'.(trim($NameSubstitute[0]['name_substitute']) != '' ? $NameSubstitute[0]['name_substitute'] : $NameCategory[0]['name']).'</a>
																					</li>'.$this->eol;

												if($ValMenu['view_products'] != 'on') 
												{
													$NameCategoryUnder = array();
													$NameCategoryUnder = $this->getNameCategoryUnder($ValMenu['id_link_cat'], $ValButton['id_button']);
													if(sizeof($NameCategoryUnder)) 
													{
														foreach($NameCategoryUnder as $KUnderCat=>$ValUnderCat) 
														{
															$Category = new Category(intval($ValUnderCat['id_category']), intval($this->context->cookie->id_lang));
															if($Category->checkAccess($this->context->customer->id))
															{
																$rewrited_url = $this->link->getCategoryLink($ValUnderCat['id_category'], $Category->link_rewrite);
																$NameCategoryUnder = $this->getNameCategory($ValUnderCat['id_category'], $this->context->cookie->id_lang, $ValButton['id_button']);
																$NameSubstitute = $this->getNameSubstitute($ValUnderCat['id_category'], $this->context->cookie->id_lang, $ValButton['id_button']);
																$this->_menu .= '	<li>
																										<a href="'.$rewrited_url.'" style="text-align:left">'.(trim($NameSubstitute[0]['name_substitute']) != '' ? $NameSubstitute[0]['name_substitute'] : $NameCategoryUnder[0]['name']).'
																										</a>
																									</li>'.$this->eol;
															}
														}
													}
												}
												else 
												{
													$NameProductsUnder = array();
													$NameProductsUnder = $this->getProductsUnder($ValMenu['id_link_cat'], $this->context->cookie->id_lang, $this->context->shop->id);
													if(sizeof($NameProductsUnder)) 
													{
														foreach($NameProductsUnder as $KUnderProd=>$ValUnderProd) 
														{
															$Products = new Product(intval($ValUnderProd['id_product']), true, intval($this->context->cookie->id_lang));
															$rewrited_url = $Products->getLink();
															$NameProduct = $Products->name;
															$this->_menu .= '<li><a href="'.$rewrited_url.'" style="text-align:left">'.(strlen($NameProduct)>20 ? substr(($NameProduct), 0, 40)."..." : ($NameProduct)).'</a></li>'.$this->eol;
														}
													}
												}
												$this->_menu .= '</ul>'.$this->eol;
											}
											
											break;
										
										case 'custom':
											$this->_menu .= '<ul>'.$this->eol;
											$this->_menu .= '<li class="stitle"><a href="'.$ValMenu['link'].'" '.($ValMenu['link']=="#" || $ValMenu['link']=="" ? "onclick='return false'" : false).' style="text-align:left">'.$ValMenu['name_menu'].'</a></li>'.$this->eol;
											$NameLinkUnder = array();
											$NameLinkUnder = $this->getButtonLinksCustomUnder($ValButton['id_button'], $ValMenu['id_custom'], $this->context->cookie->id_lang);
											if(sizeof($NameLinkUnder)) 
											{
												foreach($NameLinkUnder as $KUnderLink=>$ValUnderLink)
													$this->_menu .= '<li><a href="'.$ValUnderLink['link'].'" '.($ValUnderLink['link']=="#" || $ValUnderLink['link']=="" ? "onclick='return false'" : false).' style="text-align:left">'.$ValUnderLink['name_menu'].'</a></li>'.$this->eol;
											}
											$this->_menu .= '</ul>'.$this->eol;
										break;
									}
									$this->_menu .= '</td>'.$this->eol;
									$this->_menu .= '</tr>'.$this->eol;
								}
								$this->_menu .= '</table>'.$this->eol;
							}
						}
						$this->_menu .= '</td>'.$this->eol;
					}
					$this->_menu .= '</tr>'.$this->eol;
					$this->_menu .= '</table>'.$this->eol;
					$this->_menu .= '</td>'.$this->eol;
					//Colonne droite;
					if($MDParameters[0]['stateTD3']=="on" && $MDParameters[0]['stateTR1']!="on") {
						$this->_menu .= '<td class="megaDrownTD3" valign="top" style="width:'.$MDParameters[0]['widthTD3'].'px">'.($ValButton['detailSub']=="" ? "&nbsp;" : html_entity_decode($ValButton['detailSub'])).'</td>'.$this->eol;
					}
					$this->_menu .= '</tr></table></div>'.$this->eol;
				}
				$this->_menu .= '</li>'.$this->eol;
				$b++;
			}
		}
	}	
	
	private function checkIfImageExist($file_name = '', $ext = 'png')
	{
		$image = array();
		$image['exist'] = 0;
		$image['path'] = '';
		
		if(is_file(dirname(__FILE__).'/views/img/menu/'.$file_name.'-'.$this->context->shop->id.$ext)) 	
		{				
			$image['exist'] = 1;
			$image['path'] = $this->_path.'views/img/menu/'.$file_name.'-'.$this->context->shop->id.$ext;
		}
		
		return $image;	
	}
	
	public function hookDisplayBackOfficeHeader()
	{
		if (method_exists($this->context->controller, 'addJquery'))
		{
			$this->context->controller->addJquery();
			$this->context->controller->addJqueryUI('ui.sortable');
		}
	}
	
	public function hookDisplayTop($param) 
	{	
		$this->user_groups =  ($this->context->customer->isLogged() ? $this->context->customer->getGroups() : array(Configuration::get('PS_UNIDENTIFIED_GROUP')));
		$this->page_name = Dispatcher::getInstance()->getController();
		$smarty_cache_id = 'navmegadrownevo-'.$this->page_name.'-'.(int)$this->context->shop->id.'-'.implode(', ',$this->user_groups).'-'.(int)$this->context->language->id.'-'.(int)Tools::getValue('id_category').'-'.(int)Tools::getValue('id_manufacturer').'-'.(int)Tools::getValue('id_supplier').'-'.(int)Tools::getValue('id_cms').'-'.(int)Tools::getValue('id_product');
		$this->context->smarty->cache_lifetime = 31536000;
		Tools::enableCache();
		if (!$this->isCached('views/templates/front/navmegadrownevo.tpl', $smarty_cache_id))
		{
			$this->makeMegaDrown();
			
			$MDParameters = array();
			$MDParameters = $this->getParameters();
					
			$MDParameters[0]['bg_menu'] 			= $this->checkIfImageExist('bg_menu', $MDParameters[0]['extensionMenu']);
			$MDParameters[0]['bg_bout'] 			= $this->checkIfImageExist('bg_bout', $MDParameters[0]['extensionBout']);;
			$MDParameters[0]['navlist_arrow'] = $this->checkIfImageExist('navlist_arrow', $MDParameters[0]['extensionArro']);;
			$MDParameters[0]['sub_bg'] 				= $this->checkIfImageExist('sub_bg', $MDParameters[0]['extensionBack']);
						
			if($MDParameters[0]['FontSizeSubMenu'] != 0) 
			{
				$HeightCalculate = round( ($MDParameters[0]['MenuHeight']/2 + $MDParameters[0]['FontSizeSubMenu']/2 + ($MDParameters[0]['MenuHeight']/$MDParameters[0]['FontSizeSubMenu'])) , 0);
				$PaddingTopCalculate = round( $MDParameters[0]['MenuHeight'] - ($MDParameters[0]['MenuHeight']/2 + $MDParameters[0]['FontSizeSubMenu']/2 + ($MDParameters[0]['MenuHeight']/$MDParameters[0]['FontSizeSubMenu'])), 0 );
			}
			else 
			{
				$HeightCalculate = round( ($MDParameters[0]['MenuHeight']/2 + $MDParameters[0]['FontSizeSubMenu']/2) , 0);
				$PaddingTopCalculate = round( $MDParameters[0]['MenuHeight'] - ($MDParameters[0]['MenuHeight']/2), 0 );
			}
		
			$this->context->smarty->assign(array(
				'MenuWidthEvo' => ($MDParameters[0]['MenuWidth'] - $MDParameters[0]['paddingLeft']),
				'MenuHeightEvo' => $MDParameters[0]['MenuHeight'],
				'MinButtonWidthEvo' => $MDParameters[0]['MinButtonWidth'],
				'MaxButtonWidthEvo' => $MDParameters[0]['MaxButtonWidth'],
				'GeneralColorEvo' => $MDParameters[0]['GeneralColor'],
				'FontSizeMenuEvo' => $MDParameters[0]['FontSizeMenu'],
				'FontSizeSubMenuEvo' => $MDParameters[0]['FontSizeSubMenu'],
				'FontSizeSubSubMenuEvo' => $MDParameters[0]['FontSizeSubSubMenu'],
				'ColorFontMenuEvo' => $MDParameters[0]['ColorFontMenu'],
				'ColorFontSubMenuEvo' => $MDParameters[0]['ColorFontSubMenu'],
				'ColorFontSubSubMenuEvo' => $MDParameters[0]['ColorFontSubSubMenu'],
				'ColorFontMenuHoverEvo' => $MDParameters[0]['ColorFontMenuHover'],
				'ColorFontSubMenuHoverEvo' => $MDParameters[0]['ColorFontSubMenuHover'],
				'ColorFontSubSubMenuHoverEvo' => $MDParameters[0]['ColorFontSubSubMenuHover'],
				'widthTD1Evo' => $MDParameters[0]['widthTD1'],
				'widthTD3Evo' => $MDParameters[0]['widthTD3'],
				'bgColorTR1Evo' => $MDParameters[0]['backgroundTR1'],
				'bgColorTD1Evo' => $MDParameters[0]['backgroundTD1'],
				'bgColorTD2Evo' => $MDParameters[0]['backgroundTD2'],
				'bgColorTD3Evo' => $MDParameters[0]['backgroundTD3'],
				'VerticalPaddingEvo' => $MDParameters[0]['VerticalPadding'],
				'HeightCalculateEvo' => $HeightCalculate, 
				'ColumnWidthEvo' => $MDParameters[0]['columnSize'],
				'PaddingTopCalculateEvo' => $PaddingTopCalculate, 
				'PaddingLeftEvo' => $MDParameters[0]['paddingLeft'], 
				'MarginTopEvo' => $MDParameters[0]['marginTop'], 
				'MarginBottomEvo' => $MDParameters[0]['marginBottom'], 
				'bg_menuEvo' => $MDParameters[0]['bg_menu'],
				'bg_boutEvo' => $MDParameters[0]['bg_bout'],
				'navlist_arrowEvo' => $MDParameters[0]['navlist_arrow'],
				'sub_bgEvo' => $MDParameters[0]['sub_bg'], 
				'MENUEVO_SEARCH' => $MDParameters[0]['SearchBar']
				)
			);				
		
			$this->context->smarty->assign('pathMDEvo', $this->_path);
			$this->context->smarty->assign('menuMDEvo', $this->_menu);
		}
		
		$html = $this->display(__FILE__, 'views/templates/front/navmegadrownevo.tpl', $smarty_cache_id);
		Tools::restoreCacheSettings();
		return $html;
	}	
  function hookDisplayHeader($params)
  {
  	$this->user_groups =  ($this->context->customer->isLogged() ? $this->context->customer->getGroups() : array(Configuration::get('PS_UNIDENTIFIED_GROUP')));
		$this->page_name = Dispatcher::getInstance()->getController();
		$smarty_cache_id = 'navmegadrownevo-'.$this->page_name.'-'.(int)$this->context->shop->id.'-'.implode(', ',$this->user_groups).'-'.(int)$this->context->language->id.'-'.(int)Tools::getValue('id_category').'-'.(int)Tools::getValue('id_manufacturer').'-'.(int)Tools::getValue('id_supplier').'-'.(int)Tools::getValue('id_cms').'-'.(int)Tools::getValue('id_product');
		$this->context->smarty->cache_lifetime = 31536000;
		Tools::enableCache();
		if (!$this->isCached('views/templates/front/cssnavmegadrownevo.tpl', $smarty_cache_id))
		{
			$MDParameters = array();
			$MDParameters = $this->getParameters();
			
			$MDParameters[0]['bg_menu'] 			= $this->checkIfImageExist('bg_menu', $MDParameters[0]['extensionMenu']);
			$MDParameters[0]['bg_bout'] 			= $this->checkIfImageExist('bg_bout', $MDParameters[0]['extensionBout']);;
			$MDParameters[0]['navlist_arrow'] = $this->checkIfImageExist('navlist_arrow', $MDParameters[0]['extensionArro']);;
			$MDParameters[0]['sub_bg'] 				= $this->checkIfImageExist('sub_bg', $MDParameters[0]['extensionBack']);
			
			if($MDParameters[0]['FontSizeSubMenu'] != 0) 
			{
				$HeightCalculate = round( ($MDParameters[0]['MenuHeight']/2 + $MDParameters[0]['FontSizeSubMenu']/2 + ($MDParameters[0]['MenuHeight']/$MDParameters[0]['FontSizeSubMenu'])) , 0);
				$PaddingTopCalculate = round( $MDParameters[0]['MenuHeight'] - ($MDParameters[0]['MenuHeight']/2 + $MDParameters[0]['FontSizeSubMenu']/2 + ($MDParameters[0]['MenuHeight']/$MDParameters[0]['FontSizeSubMenu'])), 0 );
			}
			else 
			{
				$HeightCalculate = round( ($MDParameters[0]['MenuHeight']/2 + $MDParameters[0]['FontSizeSubMenu']/2) , 0);
				$PaddingTopCalculate = round( $MDParameters[0]['MenuHeight'] - ($MDParameters[0]['MenuHeight']/2), 0 );
			}
			
			$this->context->smarty->assign(array(
				'MenuWidthEvo' => ($MDParameters[0]['MenuWidth'] - $MDParameters[0]['paddingLeft']),
				'MenuHeightEvo' => $MDParameters[0]['MenuHeight'],
				'MinButtonWidthEvo' => $MDParameters[0]['MinButtonWidth'],
				'MaxButtonWidthEvo' => $MDParameters[0]['MaxButtonWidth'],
				'GeneralColorEvo' => $MDParameters[0]['GeneralColor'],
				'FontSizeMenuEvo' => $MDParameters[0]['FontSizeMenu'],
				'FontSizeSubMenuEvo' => $MDParameters[0]['FontSizeSubMenu'],
				'FontSizeSubSubMenuEvo' => $MDParameters[0]['FontSizeSubSubMenu'],
				'ColorFontMenuEvo' => $MDParameters[0]['ColorFontMenu'],
				'ColorFontSubMenuEvo' => $MDParameters[0]['ColorFontSubMenu'],
				'ColorFontSubSubMenuEvo' => $MDParameters[0]['ColorFontSubSubMenu'],
				'ColorFontMenuHoverEvo' => $MDParameters[0]['ColorFontMenuHover'],
				'ColorFontSubMenuHoverEvo' => $MDParameters[0]['ColorFontSubMenuHover'],
				'ColorFontSubSubMenuHoverEvo' => $MDParameters[0]['ColorFontSubSubMenuHover'],
				'widthTD1Evo' => $MDParameters[0]['widthTD1'],
				'widthTD3Evo' => $MDParameters[0]['widthTD3'],
				'bgColorTR1Evo' => $MDParameters[0]['backgroundTR1'],
				'bgColorTD1Evo' => $MDParameters[0]['backgroundTD1'],
				'bgColorTD2Evo' => $MDParameters[0]['backgroundTD2'],
				'bgColorTD3Evo' => $MDParameters[0]['backgroundTD3'],
				'VerticalPaddingEvo' => $MDParameters[0]['VerticalPadding'],
				'HeightCalculateEvo' => $HeightCalculate, 
				'ColumnWidthEvo' => $MDParameters[0]['columnSize'],
				'PaddingTopCalculateEvo' => $PaddingTopCalculate, 
				'PaddingLeftEvo' => $MDParameters[0]['paddingLeft'], 
				'MarginTopEvo' => $MDParameters[0]['marginTop'], 
				'MarginBottomEvo' => $MDParameters[0]['marginBottom'], 
				'bg_menuEvo' => $MDParameters[0]['bg_menu'],
				'bg_boutEvo' => $MDParameters[0]['bg_bout'],
				'navlist_arrowEvo' => $MDParameters[0]['navlist_arrow'],
				'sub_bgEvo' => $MDParameters[0]['sub_bg'] )
			);				
			
			$this->context->smarty->assign('pathMDEvo', $this->_path);
			$this->context->smarty->assign('menuMDEvo', $this->_menu);
		}
		
		$this->context->controller->addJS(($this->_path).'/views/js/jquery.hoverIntent.minified.js', 'all');
		$this->context->controller->addCSS(($this->_path).'/views/css/navmegadrownEvo.css', 'all');
		$this->context->controller->addJS(($this->_path).'/views/js/navmegadrownEvo.js');
		
		$html = $this->display(__FILE__, 'views/templates/front/cssnavmegadrownevo.tpl', $smarty_cache_id);
		Tools::restoreCacheSettings();
		return $html;
  }
}
