<?php
if (!defined('_PS_VERSION_'))
  exit;
 
function upgrade_module_2_2_4($object)
{
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'admevo_button_shop` (
  `id_admevo_button` int(11) NOT NULL,
  `id_shop` int(11) NOT NULL
	) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';
	
  foreach ($sql as $s)
		if (!Db::getInstance()->execute($s))
			return false;		
}
?>