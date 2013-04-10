<?php
if (!defined('_PS_VERSION_'))
  exit;
 
function upgrade_module_2_3_7($object)
{
	$sql[] = 'ALTER TABLE `'._DB_PREFIX_.'admevo_parameters` ADD `MinButtonWidth` INT( 5 ) NOT NULL';
	
  foreach ($sql as $s)
		if (!Db::getInstance()->execute($s))
			return false;		
}
?>