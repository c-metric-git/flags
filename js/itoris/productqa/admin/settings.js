Event.observe(window, 'load', function() {
		Event.observe($('visible'), 'change', function(){
			var ids = ['captcha', 'visitor_post', 'visitor_can_rate'];
			if($F(this) == 4){
				for (var i = 0; i < ids.length; i++) {
					$(ids[i]).disabled = true;
				}
			}else{
				for (var i = 0; i < ids.length; i++) {
					if ($('check_' + ids[i])) {
						if (!$('check_' + ids[i]).checked) {
							$(ids[i]).disabled = false;
						}
					} else {
						$(ids[i]).disabled = false;
					}
				}
			}
		});
	if(!oldMagento){
		tinyMCE.init({
			// General options
			mode : "exact",
			elements : 'template_admin_notification',
			theme : "advanced",
			plugins : "spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

			// Theme options
			theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,",
			theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,",
			theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage,|,forecolor,backcolor,|,fullscreen,|,ltr,rtl,",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,

			// Skin options
			skin : "default",
			//skin_variant : "silver",
			setup : function(ed) {
						ed.makeReadOnly = function(ro) {
							var t = this, s = t.settings, DOM = tinymce.DOM, d = t.getDoc();

							if(!s.readonly && ro) {
								if (!tinymce.isIE) {
									try {
										d.designMode = 'Off';
									} catch (ex) {

									}
								} else {
									// It will not steal focus if we hide it while setting contentEditable
									b = t.getBody();
									DOM.hide(b);
									b.contentEditable = false;
									DOM.show(b);
								}
								s.readonly = true;
							} else if(s.readonly && !ro) {
								if (!tinymce.isIE) {
									try {
										d.designMode = 'On';
									} catch (ex) {

									}
								} else {
									// It will not steal focus if we hide it while setting contentEditable
									b = t.getBody();
									DOM.hide(b);
									b.contentEditable = true;
									DOM.show(b);
								}
								s.readonly = false;
							}
							return s.readonly;
						};

						if(ed.settings.readonly) {
							ed.settings.readonly = false;
							ed.onInit.add(function(ed) {
								toggleReadOnly(ed);
							});
						}
					}

		});

		tinyMCE.init({
			// General options
			mode : "exact",
			elements : 'template_user_notification',
			theme : "advanced",
			plugins : "spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

			// Theme options
			theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,",
			theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,",
			theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage,|,forecolor,backcolor,|,fullscreen,|,ltr,rtl,",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,

			// Skin options
			skin : "default",
			//skin_variant : "silver",
			setup : function(ed) {
						ed.makeReadOnly = function(ro) {
							var t = this, s = t.settings, DOM = tinymce.DOM, d = t.getDoc();

							if(!s.readonly && ro) {
								if (!tinymce.isIE) {
									try {
										d.designMode = 'Off';
									} catch (ex) {

									}
								} else {
									// It will not steal focus if we hide it while setting contentEditable
									b = t.getBody();
									DOM.hide(b);
									b.contentEditable = false;
									DOM.show(b);
								}
								s.readonly = true;
							} else if(s.readonly && !ro) {
								if (!tinymce.isIE) {
									try {
										d.designMode = 'On';
									} catch (ex) {

									}
								} else {
									// It will not steal focus if we hide it while setting contentEditable
									b = t.getBody();
									DOM.hide(b);
									b.contentEditable = true;
									DOM.show(b);
								}
								s.readonly = false;
							}
							return s.readonly;
						};

						if(ed.settings.readonly) {
							ed.settings.readonly = false;
							ed.onInit.add(function(ed) {
								toggleReadOnlyEditor(ed);
							});
						}
					}

		});

		tinyMCE.init({
			// General options
			mode : "exact",
			elements : 'template_guest_notification',
			theme : "advanced",
			plugins : "spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

			// Theme options
			theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,",
			theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,",
			theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage,|,forecolor,backcolor,|,fullscreen,|,ltr,rtl,",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,

			// Skin options
			skin : "default",
			//skin_variant : "silver",
			setup : function(ed) {
						ed.makeReadOnly = function(ro) {
							var t = this, s = t.settings, DOM = tinymce.DOM, d = t.getDoc();

							if(!s.readonly && ro) {
								if (!tinymce.isIE) {
									try {
										d.designMode = 'Off';
									} catch (ex) {

									}
								} else {
									// It will not steal focus if we hide it while setting contentEditable
									b = t.getBody();
									DOM.hide(b);
									b.contentEditable = false;
									DOM.show(b);
								}
								s.readonly = true;
							} else if(s.readonly && !ro) {
								if (!tinymce.isIE) {
									try {
										d.designMode = 'On';
									} catch (ex) {

									}
								} else {
									// It will not steal focus if we hide it while setting contentEditable
									b = t.getBody();
									DOM.hide(b);
									b.contentEditable = true;
									DOM.show(b);
								}
								s.readonly = false;
							}
							return s.readonly;
						};

						if(ed.settings.readonly) {
							ed.settings.readonly = false;
							ed.onInit.add(function(ed) {
								toggleReadOnlyEditor(ed);
							});
						}
					}

		});

		setTimeout(disableEditors, 1000);
	}
});

function toggleReadOnlyEditor(ed) {
               ed.makeReadOnly(!ed.settings.readonly) ? "Disable ReadOnly" : "Enable ReadOnly";
}

function disableEditors(){
	//vars defined in element.phtml
	if(template_user_notification){
		toggleReadOnlyEditor(tinyMCE.get('template_user_notification'));
	}
	if(template_admin_notification){
		toggleReadOnlyEditor(tinyMCE.get('template_admin_notification'));
	}
	if(template_guest_notification){
		toggleReadOnlyEditor(tinyMCE.get('template_guest_notification'));
	}
}

function itoris_toogleFieldEditMode(toogleIdentifier, fieldContainer) {

	if ($(toogleIdentifier).checked) {
		if(fieldContainer == 'template_user_notification'
				|| fieldContainer == 'template_admin_notification'
				|| fieldContainer == 'template_guest_notification'
		){
			toggleReadOnlyEditor(tinyMCE.get(fieldContainer));
		}
		$(fieldContainer).disabled = true;
		if(fieldContainer == 'notify_administrator'){
			$('admin_email').disabled = true;
		}
    } else {
		if(fieldContainer == 'template_user_notification'
				|| fieldContainer == 'template_admin_notification'
				|| fieldContainer == 'template_guest_notification'
		){
			toggleReadOnlyEditor(tinyMCE.get(fieldContainer));
		}
        $(fieldContainer).disabled = false;
		if(fieldContainer == 'notify_administrator'){
			if($(fieldContainer).checked){
				$('admin_email').disabled = false;
			}else{
				$('admin_email').disabled = true;
			}
		}
    }
}