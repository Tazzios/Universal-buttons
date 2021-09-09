<?php
/**
 * @version   	1.4
 * @package     Joomla
 * @subpackage  System
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters. All rights reserved.
 * @license     GNU GPL v2.0
 */
 
defined('_JEXEC') or die;

class plgButtonUniversalButtons extends JPlugin
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
			$usergroups = JAccess::getGroupsByUser(JFactory::getUser()->get('id'), $recursive = true);
			

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
						
						$app = JFactory::getApplication();
							// ...
						$root = '';
						if ($app->isAdmin()) {
							$root = '../';  // Joomla expects a relative path, leave site folder "administrator"
						} else {
							$root = '';
						}
										
						$button = new JObject();
						
						$url = $root . $value->url;
						
						
						
						if ($this->params->get('componentonly')==1 ) {
							if( strpos( $url , '?' ) !== false) {
								$url .= $url . '&tmpl=component' ;
							} else {
								$url .= $url . '?tmpl=component' ;
							}	
						}
							

						$button->set('link', $url);
						$button->set('options', "{handler: 'iframe', size: {x: ".$value->popupwidth.", y: ".$value->popupheight."}}");
						
						$button->set('text', $value->Buttonlabel);
						//$button->set('onclick', 'insertText(\''.$value->name .'\')');
						$button->set('name', $value->Buttonicon);
							
						
						$buttons[$i]= $button ;
						break;

					case "1":
					
					
					$js =  " function buttonClick".$i."(editor) { let str = '". $value->code. "';";
					
						$subforminputs = $value->variables;
						$arr2 = (array) $subforminputs;
						
						$iVariable=1;
						foreach ($arr2 as $value2) {
							
						$js .= " txt".$iVariable." = prompt('". $value2->Variablelabel. "','".$value2->variabledefault."');
							str = str.replace(/%".$iVariable."/g,txt".$iVariable."); ";
							 
							$iVariable++;
						}
					
						$js .= 	" jInsertEditorText( str, editor); }";

							
						$css = "";
						
						$doc = JFactory::getDocument();
						$doc->addScriptDeclaration($js);
						$doc->addStyleDeclaration($css);
						$button = new JObject();
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