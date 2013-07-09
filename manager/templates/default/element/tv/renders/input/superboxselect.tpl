<link rel="stylesheet" href="{$assets_url}/components/superboxselect/css/superboxselect.css" />

<div id="tv{$tv->id}"></div>

<script type="text/javascript">
// <![CDATA[
{literal}
Ext.onReady(function() {
	var resources = new Ext.data.JsonStore({
		id:'id',
		root:'rows',
		fields:[
		 	{name:'id', type:'int'},
		    {name:'pagetitle', type:'string'},
		],
		{/literal}
    	url: '{$remote_url}',
		{literal}
		baseParams:{
		    {/literal}
		    parents: '{$parents}',
		    resource_id: '{$resource_id}',
			{literal}
		}
	});

     new Ext.ux.form.SuperBoxSelect({
            {/literal}
			transform: 'tv{$tv->id}', 
			name: 'tv{$tv->id}[]',
			value:'{$values}',
            {literal}
			allowBlank:true,
			msgTarget: 'title',
			fieldLabel: 'Resources',
			width:500,
			displayField: 'pagetitle',
			displayFieldTpl: '{pagetitle} ({id})',
			valueField: 'id',
			addNewDataOnBlur : false, 
			anchor:'100%',				
			minChars: 2,
			classField: 'cls',
			styleField: 'style',
			store: resources,
			mode: 'remote',
			queryDelay: 0,
			triggerAction: 'all',
			listeners: {
			    
			    additem: function(bs,v){
			    MODx.fireResourceFormChange();
			    },
			    
			    removeitem: function(bs,v){
			    MODx.fireResourceFormChange();
			    },
			    
			}
		});
});
{/literal}
// ]]>
</script>