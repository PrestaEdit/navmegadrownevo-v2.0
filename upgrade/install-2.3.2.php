<?php
if (!defined('_PS_VERSION_'))
  exit;
 
function upgrade_module_2_3_2($object)
{
	$sql[] = 'ALTER TABLE `'._DB_PREFIX_.'admevo_parameters` ADD `SearchBar` VARCHAR( 3 ) NOT NULL';
	
  foreach ($sql as $s)
		if (!Db::getInstance()->execute($s))
			return false;		
}
?>