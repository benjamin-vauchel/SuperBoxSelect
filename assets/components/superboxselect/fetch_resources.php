<?php
// Load MODx
require_once '../../../config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CORE_PATH.'model/modx/modx.class.php';
$modx = new modX();
$modx->initialize('mgr');

// User Authentification
if (!$modx->user->isAuthenticated('mgr')) {
   $modx->sendUnauthorizedPage();
}

//queryValuesIndicator and queryValuesDelimiter are used when querying (multiple) values and
//should be matched by the components config options of the same name.
$queryValuesIndicator = 'valuesqry';
$queryValuesDelimiter = "|";
    
// Select resources
$parents = !empty($_REQUEST['parents']) ? explode(',',$_REQUEST['parents']) : array(0);
$depth = 10;

$c = $modx->newQuery('modResource');
  
$c->where(array(
	'deleted' => false,
	'published' => true,
	'isfolder' => 0,
));

if(!empty($_REQUEST['parents']))
{
	$children = array();
	foreach ($parents as $parent) {
	    $pchildren = $modx->getChildIds($parent, $depth);
	    if (!empty($pchildren)) $children = array_merge($children, $pchildren);
	}
	if (!empty($children)) $parents = array_merge($parents, $children);
	
	$c->andCondition(array(
	    'parent:IN' => $parents,
	));
}
  
 
if(!empty($_REQUEST['query']))
{
	if(!empty($_REQUEST[$queryValuesIndicator]))
	{
		$c->andCondition(array(
			'id:IN' => explode($queryValuesDelimiter,$_REQUEST['query'])
		));
	}
	else
	{
		$c->andCondition(array(
			'pagetitle:LIKE' => $_REQUEST['query'].'%'
		));
	}
}
  
$c->select(array('id','pagetitle'));
  
$c->sortby('pagetitle','ASC');
  
$resources = $modx->getCollection('modResource',$c);
  
// Prepare results to ExtJS JSON pattern
$results = array(
  "success"=>true,
  "rows"=>array(),
);

foreach($resources as $resource)
{
	$results['rows'][] = array(
	'id' => $resource->get('id'),
	'pagetitle' => $resource->get('pagetitle'),
	);
}
  
$output = $modx->toJSON($results);;

// Send results
header("Content-type: text/html; charset=UTF-8");
header("Content-Size: " . strlen($outputd));
echo $output;