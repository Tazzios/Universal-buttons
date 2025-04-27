<?php
/**
 * @version   	1.4
 * @package     Joomla
 * @subpackage  System
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters. All rights reserved.
 * @license     GNU GPL v2.0
 */
 
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Object\CMSObject;

use Joomla\CMS\Uri\Uri;


class plgButtonUniversalButtons extends CMSPlugin
{
    public function onDisplay($name, &$params)
    {
		

		
		
		//retrieve Buttons
		$buttons = $this->params->get('buttons');
		$arr = (array) $buttons;
		
		$buttons = array();
		$i=0;
		$additional_names = '';
		foreach ($arr as $value) {
					
			// check autorisation
			$autorised = 'false';
			$usergroups = Access::getGroupsByUser(Factory::getUser()->get('id'), $recursive = true);
			

			if (empty($value->usergroups)) { // if not set show button
				$autorised = 'true';
			}
			elseif (array_sum(array_count_values(array_intersect($usergroups, $value->usergroups)))>0) {
				// compare user and usergroup values for matches. by summing the matching array ID`s. 
				$autorised = 'true';	 		
			}

			
			if ($autorised == 'true') {
		
									
				switch ($value->style) {

					case "0":
						
						$app = Factory::getApplication();
							// ...
						$root = '';
						if ($app->isClient('administrator')) {
							$root = '../';  // Joomla expects a relative path, leave site folder "administrator"
						} else {
							$root = '';
						}
										

						
						$url = $root . $value->url;
						
						
						
						if ($this->params->get('componentonly')==1 ) {
							if( strpos( $url , '?' ) !== false) {
								$url .= $url . '&tmpl=component' ;
							} else {
								$url .= $url . '?tmpl=component' ;
							}	
						}
							
					//$button = new CMSObject();
					$button          = new JObject;
					$button->modal   = true;
					$button->class   = 'btn';
					$button->set('link', $url);
					$button->options = "{handler: 'iframe', size: {x: ".$value->popupwidth.", y: ".$value->popupheight."}}";						
					$button->set('text', $value->Buttonlabel);
					//$button->set('onclick', 'insertText(\''.$value->name .'\')');
					$button->set('icon', $value->Buttonicon);

							
						
						$buttons[$i]= $button ;
						break;

					case "1":
					
					// remove enters
					$value->code= str_replace(array("\n", "\r"), ' ', $value->code); 

					$js =  " function buttonClick".$i."(editor) { let str = '". $value->code . "';
						";

						$subforminputs = $value->variables;
						$arr2 = (array) $subforminputs;
						
						$iVariable=1;
						foreach ($arr2 as $value2) {
							
						$js .= " txt".$iVariable." = prompt('". $value2->Variablelabel. "','".$value2->variabledefault."');
							if (txt".$iVariable." !== null) {						
								str = str.replace(/%".$iVariable."/g,txt".$iVariable.");
							}
							";
							 
							$iVariable++;
						}

						$js .= " if (str.includes('%') == false) { Joomla.editors.instances[editor].replaceSelection(str);} }";

							
						$css = "";
						
						$doc = Factory::getDocument();
						$doc->addScriptDeclaration($js);
						$doc->addStyleDeclaration($css);
						$button = new CMSObject();
						$button->set('modal', false);
						$button->set('onclick', 'buttonClick'.$i.'(\''.$name.'\');return false;');
						$button->set('text', $value->Buttonlabel);
						$button->set('name', $value->Buttonicon);
						$button->set('link', '#');
						
						$buttons[$i]= $button ;
							
						break;
						case "2":


						break;
				} // end case
				$i++;
			}// end autorised
		} // end foreach
				
		return $buttons;
	} // end ondisplay
		
		
		
		

		
		
	
		
		


    
}
?>
