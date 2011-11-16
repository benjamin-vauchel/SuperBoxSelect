<?php
/**
* Template variable objects for the SuperBoxSelects package
* @author Benjamin Vauchel <contact@omycode.fr>
* 13/11/11
*
* @package superboxselects
* @subpackage build
*/

$templateVariables = array();

$templateVariables[1]= $modx->newObject('modTemplateVar');
$templateVariables[1]->fromArray(array(
    'id' => 1,
    'type' => 'superboxselect',
    'name' => 'superboxselect',
    'caption' => 'SuperBoxSelect',
    'description' => 'Example of SuperBoxSelect TV for MODx Revolution',
    'display' => 'default',
    'elements' => '',  /* input option values */
    'locked' => 0,
    'rank' => 0,
    'display_params' => '',
    'default_text' => '',
    'properties' => array(),
),'',true,true);

return $templateVariables;
