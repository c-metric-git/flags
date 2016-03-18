var SolrBridgeIndex = Class.create();
SolrBridgeIndex.prototype = {
		itemId: null,
		editUrl: null,
		deleteUrl: null,
		messageDelete: null,
		okLabel: 'Yes',
		cancelLabel: 'No',
		eleUpdate: null,
		confirmDialog: null,
	initialize: function() {
			var me = this;
			$$('a[class="action-button"]').each(function(element){
				Event.observe(element, 'click', me.onClick.bind(me, element));
			});
			Event.observe('reindex-selected-items', 'click', me.onReindexSelectedItems.bind(me));
			Event.observe('update-selected-items', 'click', me.onUpdateSelectedItems.bind(me));
			Event.observe('delete-selected-items', 'click', me.onDeleteSelectedItems.bind(me));
			var checkAllLink = $('checkall');
			Event.observe(checkAllLink, 'click', me.onCheckAll.bind(me, checkAllLink));
	},
	onCheckAll: function(el, e) {
		$$('input[class="massaction-checkbox"]').each(function(element){
			element.checked = el.checked;
		});
	},
	onReindexSelectedItems: function() {
		var me = this;
		$$('input[class="massaction-checkbox"]').each(function(element){
			if(element.checked) {
				$(element).up(1).getElementsBySelector('a[class="action-button"]').each(function(el){
					if(el.title == 're-index') {
						me.doRequest(el);
					}
				})
			}
		});
	},
	onUpdateSelectedItems: function() {
		var me = this;
		$$('input[class="massaction-checkbox"]').each(function(element){
			if(element.checked) {
				$(element).up(1).getElementsBySelector('a[class="action-button"]').each(function(el){
					if(el.title == 'update-index') {
						me.doRequest(el);
					}
				})
			}
		});
	},
	onDeleteSelectedItems: function() {
		var me = this;
		$$('input[class="massaction-checkbox"]').each(function(element){
			if(element.checked){
				$(element).up(1).getElementsBySelector('a[class="action-button"]').each(function(el){
					if(el.title == 'delete-index') {
						me.doRequest(el);
					}
				})
			}
		});
	},
	onClick: function(element, e) {
		Event.stop(e);
		this.showConfirmDialog(element);
		return;
	},
	showConfirmDialog: function(element) {
		var me = this;
		this.confirmDialog = Dialog.confirm(
				'Are you sure to do this action?',
				{
					className:'alphacube', 
					width: 250, 
					height: 100, 
					closable:true,
					okLabel: 'Yes',
					cancelLabel: 'No',
					onOk: function()
					{
						me.doRequest(element);
						this.close();
					}
				}
		);
	},
	doRequest: function(element) {
		var me = this;
		var button = element;
		var reindexUrl = button.href;
		
		var tr = $(button).up(1).addClassName('on-mouse');
		var processing = $(button).up().down('img[class="icon-proccesing"]').show();
		
		var params = {};
		me.request(button, params);
	},
	processResponse: function(button, response) {
		var me = this;
		if(undefined !== response.documents) {
			$(button).up(1).down('td[class="solr-documents"]').update(response.documents);
		}
		if(response.percent) {
			if(response.percent > 100) response.percent = 100;
			$(button).up(1).down('div[class="bar"]').setStyle({width:(response.percent) + '%'});
			$(button).up(1).down('div[class="percent"]').update(response.percent + '%');
		}
		
		if(button.title == 'delete-index') {
			if(response.percent < 1) {
				$(button).up().down('img[class="icon-proccesing"]').hide();
			}
			window.location.reload();
			return;
		}else if(button.title == 're-index') {
			if(response.percent >= 100) {
				$(button).up().down('img[class="icon-proccesing"]').hide();
			} else {
				me.request(button, response);
			}
		}else if(button.title == 'update-index') {
			if(response.percent >= 100) {
				$(button).up().down('img[class="icon-proccesing"]').hide();
			} else {
				me.request(button, response);
			}
		}
		return false;
	},
	request: function(button, params){
		var url = button.href;
		var me = this;
		var ajaxRequest = new Ajax.Request(url, {
			  method:'post',
			  evalJSON:'force',
			  parameters: params,
			  loaderArea: false,
			  onSuccess: function(transport) {
				  //Error occur
				  if(transport.responseJSON === undefined) {
					  alert(transport.responseText);
					  return;
				  }
				  me.processResponse(button, transport.responseJSON);
			   }
			});
	},
	deleteOnComplete: function(){
		this.eleUpdate.remove();
	},
}
//BOOST MANAGER
var SolrBridgeBoost = Class.create();
SolrBridgeBoost.prototype = {
	initialize: function(){
		//Do some thing here
	},
	//Do adding more row (TR) of District
	addMoreBoostItem: function(button) {
		var tbody = button.up(2);
		var items = $(tbody).select('tr[class="item"]');
		var lastTR = items.last();
		var newTR = lastTR.clone(true);
			
		var inputs = newTR.select('input');
		var selects = newTR.select('select');
		var hiddenInputs = newTR.select('input[type="hidden"]');
		//Get Id column
		var lastInput = lastTR.select('input[type="hidden"]').first();
		var index = (parseInt(lastInput.value) + 1);
		hiddenInputs.first().value = index;
			
		inputs.each( function(item) {
			if(item.name.search('id') != -1) {
				item.name = 'boost['+index+'][id]';
			} else if ( item.name.search('weight') != -1 ) {
				item.name = 'boost['+index+'][weight]';
			}
		} );
			
		selects.each( function(item) {
			if(item.name.search('field') != -1) {
				item.name = 'boost['+index+'][field]';
			}
		} );
			
		newTR.writeAttribute('state', 'new');
		$(lastTR).insert({after:newTR});
	},
	deleteBoostItem: function (button, deleteDataUrl) {
		var boostItemId = $(button).up(1).select('input[type="hidden"][class="input-text"]').first().value;
		this.deleteBoostItemById(boostItemId, $(button).up(1), deleteDataUrl);
	},
	//Perform deleting district 1 by 1 by district id
	deleteBoostItemById: function (boostItemId, TR, deleteDataUrl) {
		if($(TR).readAttribute('state') == 'new') {
			TR.remove();
		} else {
			new Ajax.Request(deleteDataUrl, {evalJSON:'force',parameters: {'itemid':boostItemId},onSuccess: function(transport){
				var json = transport.responseJSON;
				 if(json.status == 'SUCCESS') {
					 TR.remove();
				 }
			}});
		}
	},
	//Collect data and ajax save district
	saveBoostData: function(formId, saveDataUrl) {
		var formData = $(formId).serialize(true);
		this.doRequest(saveDataUrl,formData);
	},
	//Do ajax request
	doRequest: function(url, params) {
		new Ajax.Request(url, {method:'post',evalJSON:'force',parameters: params,onSuccess: function(transport) {
				var json = transport.responseJSON;
				$('message-text-wrapper').update(json.message);
				$('boost-fields-messages').show();
		}});
	}
}

//************DOCUMENT MANAGER****************/
var SolrBridgeDocument = Class.create();
SolrBridgeDocument.prototype = {
		solrcoreSelector: null,
		storeSelector: null,
		loadDocumentUrl: null,
		truncateUrl: null,
		confirmMessage: 'Are you sure?',
		initialize: function(loadDocumentUrl, truncateUrl) {
			this.loadDocumentUrl = loadDocumentUrl;
			this.truncateUrl = truncateUrl;
			this.solrcoreSelector = $('solrcore-selector');
			this.storeSelector = $('store-selector');
			var me = this;
			Event.observe(me.solrcoreSelector, 'change', me.onChangeSolrCore.bind(me, me.solrcoreSelector));
			Event.observe(me.storeSelector, 'change', me.onChangeStore.bind(me, me.storeSelector));
			var truncateButton = $('truncate-core-button');
			Event.observe(truncateButton, 'click', me.onTruncateCore.bind(me, truncateButton));
			var loadDocumentsButton = $('load-document-button');
			Event.observe( loadDocumentsButton, 'click', me.onLoadDocuments.bind(me, loadDocumentsButton) );
		},
		applyCheckAll: function() {
			var me = this;
			var checkAllLink = $('checkall');
			Event.observe(checkAllLink, 'click', me.onCheckAll.bind(me, checkAllLink));
		},
		applyDeleteButton: function() {
			var me = this;
			$('document-grid-table').select('a[class="action-delete-button"]').each( function (button) {
				var deleteUrl = $(button).readAttribute('href');
				$(button).writeAttribute('href', 'javascript:;');
				Event.observe(button, 'click', me.onDeleteDocument.bind(me, button, deleteUrl));
			} );
		},
		onDeleteDocument: function(button, deleteUrl) {
			var me = this;
			var tr = $(button).up(1);
			var table = $(tr).up(1);
			
			Dialog.confirm(me.confirmMessage,{className:'alphacube', width: 250, height: 100, closable:true,okLabel: 'Yes',cancelLabel: 'No',onOk: function() {
				var dialog = this;
				dialog.close();
				new Ajax.Request(deleteUrl, {method:'get',evalJSON:'force',parameters: {},onSuccess: function(transport) {
					var json = transport.responseJSON;
					if( json.status == 'SUCCESS' ) {
						$(tr).remove();
						if( $(table).select('tr[class="document-item"]').length < 1 ) {
							me.refresh();
						}
					} else {
						var lis = $('document-manage-messages').select('li.success-msg').first().removeClassName('success-msg').addClassName('error-msg');
					}
					$('message-text-wrapper').update(json.message);
					$('document-manage-messages').show();
				}});
			}});
		},
		showMessage: function (response) {
			var json = transport.responseJSON;
			if( json.status == 'ERROR' ) {
				$(tr).remove();
			} else {
				var lis = $('document-manage-messages').select('li.success-msg').first().removeClassName('success-msg').addClassName('error-msg');
			}
			$('message-text-wrapper').update(json.message);
			$('document-manage-messages').show();
		},
		onCheckAll: function(el, e) {
			$$('input[class="massaction-checkbox"]').each(function(element){
				element.checked = el.checked;
			});
			if(el.checked === true) {
				$('delete-selected-items').show();
			} else {
				$('delete-selected-items').hide();
			}
		},
		onLoadDocuments: function() {
			var me = this;
			var solrcore = me.solrcoreSelector.getValue();
			var store = me.storeSelector.getValue();
			
			this.reloadDocuments(solrcore, store);
		},
		onChangeSolrCore: function() {
			var me = this;
			var solrcore = me.solrcoreSelector.getValue();
			//alert(solrcore);
			var store = me.storeSelector.getValue();
			this.reloadDocuments(solrcore, store);
		},
		onTruncateCore: function() {
			var me = this;
			var solrcore = me.solrcoreSelector.getValue();
			var store = me.storeSelector.getValue();
			
			Dialog.confirm(me.confirmMessage,{className:'alphacube', width: 250, height: 100, closable:true,okLabel: 'Yes',cancelLabel: 'No',onOk: function() {
				var dialog = this;
				dialog.close();
				new Ajax.Request(me.truncateUrl, {method:'get',evalJSON:'force',parameters: {'solrcore':solrcore, 'store':store},onSuccess: function(transport) {
					me.refresh();
				}});
			}});
		},
		onChangeStore: function() {
			var me = this;
			var solrcore = me.solrcoreSelector.getValue();
			var store = me.storeSelector.getValue();
			//alert(solrcore + '-' + store);
			this.reloadDocuments(solrcore, store);
		},
		reloadDocuments: function (solrcore, store) {
			var me = this;
			var params = {'solrcore':solrcore, 'store':store};
			new Ajax.Request(me.loadDocumentUrl, {method:'get',evalJSON:'force',parameters: params,onSuccess: function(transport) {
				$('document-list-wrapper').update(transport.responseText);
			}});
		},
		refresh: function() {
			window.location.reload(true);
		}
}