var PotatoCoreContactForm = Class.create();
PotatoCoreContactForm.prototype = {
    initialize: function() {
        this._validator = new Validation($('config_edit_form'));
},
    submit: function() {
        this._addRequiredClass();
        if (this._validator.validate()) {
            $('po_core_contactus').appendChild();
            $('config_edit_form').submit();
            return true;
        }
        this._removeRequiredClass();
        return false;
    },
    _addRequiredClass: function() {
        $$('#po_core_contactus input, #po_core_contactus textarea').each(function(el) {
            el.addClassName('required-entry');
        });
        return true;
    },
    _removeRequiredClass: function() {
        $$('#po_core_contactus input, #po_core_contactus textarea').each(function(el) {
            el.removeClassName('required-entry');
        });
        return true;
    }
};