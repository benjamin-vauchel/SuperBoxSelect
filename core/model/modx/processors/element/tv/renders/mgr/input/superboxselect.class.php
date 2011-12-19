<?php
/**
 * @package modx
 * @subpackage processors.element.tv.renders.mgr.input
 * @author Benjamin Vauchel (benjamin.vauchel@omycode.fr)
 * @version 1
 */

class modTemplateVarInputRenderSuperBoxSelect extends modTemplateVarInputRender {

    public function process($value,array $params = array()) {
    
        $options = $this->getInputOptions();

        $parents = !empty($options) ? $options : $this->modx->getOption('site_start',null,1);
		$parents = implode(',', $parents);
		$values = str_replace('||', ',', $value);
		$remote_url = $this->modx->getOption('assets_url',null,'/assets/').'components/superboxselect/fetch_resources.php';
		
        $this->setPlaceholder('base_url', $this->modx->getOption('base_url'));
        $this->setPlaceholder('assets_url', $this->modx->getOption('assets_url',null,'/assets/'));
        $this->setPlaceholder('parents', $parents);
        $this->setPlaceholder('values', $values);
        $this->setPlaceholder('remote_url', $remote_url);
    }
    
    public function getTemplate() {
        return 'element/tv/renders/input/superboxselect.tpl';
    }
}
return 'modTemplateVarInputRenderSuperBoxSelect';