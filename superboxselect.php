<?php
/**
 * @package modx
 * @subpackage processors.element.tv.renders.mgr.input
 * @author Benjamin Vauchel (benjamin.vauchel@omycode.fr)
 * @version 1
 */

$modx->lexicon->load('tv_widget');
$parents = $this->get('elements');

$bindingsResult = $this->processBindings($this->get('elements'),$modx->resource->get('id'));
$parents = $this->parseInputOptions($bindingsResult);
if (empty($parents)) { $parents = array($modx->getOption('site_start',null,1)); }

$parents = implode(',', $parents);
$values = str_replace('||', ',', $value);
$remote_url = $modx->getOption('assets_url',null,'/assets/').'components/superboxselect/fetch_resource.php';

$this->xpdo->smarty->assign('assets_url',$modx->getOption('assets_url',null,'/assets/'));
$this->xpdo->smarty->assign('parents',$parents);
$this->xpdo->smarty->assign('values',$values);
$this->xpdo->smarty->assign('remote_url',$remote_url);
return $this->xpdo->smarty->fetch('element/tv/renders/input/superboxselect.tpl');
