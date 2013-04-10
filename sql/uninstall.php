<?php

	// Init
	$sql = array();
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'admevo_button`;';			
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'admevo_button_organization`';
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'admevo_button_lang`';
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'admevo_button_langcat`';
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'admevo_button_link_cat`';
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'admevo_button_link`';
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'admevo_parameters`';
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'admevo_custom_menu`';
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'admevo_custom_menu_lang`';