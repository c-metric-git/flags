var awQuickCouponGeneration = {

    defaultErrorMsg: 'Ooops, something went wrong',

    runFromTicket: function(url, noteUrl) {
        var params = {
            coupon_generation_rule : $('coupon_generation_rule').getValue(),
            coupon_generation_email : $$('input[name="coupon_generation_email"]')[0].getValue(),
            customer_id : $$('input[name="coupon_generation_customer_id"]')[0].getValue(),
            coupon_generation_notification : $('coupon_generation_notification').checked,
            coupon_generation_expiration: true
        };
        var me = this;
        new Ajax.Request(url, {
            parameters: params,
            method: 'POST',
            onComplete: function (transport) {
                if (!transport.responseText.isJSON()) {
                    me.showError(me.defaultErrorMsg);
                    return;
                }
                try {
                    var response = transport.responseText.evalJSON();
                    if (response.success) {
                        me.showSuccess(response.msg);
                        me.addNote(response.msg, noteUrl);
                    } else {
                        me.showError(response.msg);
                    }
                } catch (e) {
                    me.showError(me.defaultErrorMsg);
                }
            }
        });
    },

    runFromCouponsGrid: function(url) {
        var params = {
            coupon_generation_rule : $('coupon_generation_rule').getValue(),
            coupon_generation_email : $('coupon_generation_email').getValue().trim()
        };
        if ( ! params.coupon_generation_email.blank()) {
            params.coupon_generation_notification = true;
        }
        setLocation(url + '?' + Object.toQueryString(params));
    },

    runFromCustomer: function(url) {
        var button = $('coupon_generation');
        var form = button.up('form');
        var fields = Form.serialize(form, true);
        var params = {};
        var availableParamKeys = ['customer_id'];
        Object.keys(fields).each(function(key){
            if (key.indexOf('coupon_generation') === 0
             || availableParamKeys.indexOf(key) !== -1
            ) {
                params[key] = fields[key];
            } else if (key == 'account[email]') {
                params.coupon_generation_email = fields[key];
            }
        });
        var notificationField = $('_aw_coupongenerator_coupon_generation_notification');
        params[notificationField.readAttribute('name')] = notificationField.checked;
        var me = this;
        new Ajax.Request(url, {
            parameters: params,
            method: 'POST',
            onComplete: function (transport) {
                if (!transport.responseText.isJSON()) {
                    me.showError(me.defaultErrorMsg);
                    return;
                }
                try {
                    var response = transport.responseText.evalJSON();
                    if (response.success) {
                        me.showSuccess(response.msg);
                    } else {
                        me.showError(response.msg);
                    }
                } catch (e) {
                    me.showError(me.defaultErrorMsg);
                }
            }
        });
    },

    addNote: function(note, noteUrl) {
        var me = this;
        new Ajax.Request(noteUrl, {
            parameters: {note : note},
            method: 'POST',
            onComplete: function (transport) {
                if ( ! transport.responseText.isJSON()) {
                    me.showError(me.defaultErrorMsg);
                    return;
                }
                try {
                    var response = transport.responseText.evalJSON();
                    if (response.success) {
                        awHDU3TicketThread.ajaxUpdate();
                    } else {
                        me.showError(response.msg);
                    }
                } catch (e) {
                    me.showError(me.defaultErrorMsg);
                }
            }
        });
    },

    showError: function(msg) {
        this.showMsg(msg, {
            'color': 'red',
            'fontWeight': 'bold'
        });
    },
    showSuccess: function(msg) {
        this.showMsg(msg, {
            'color': 'green',
            'fontWeight': 'bold'
        });
    },
    showMsg: function(msg, style) {
        var currentMsgEl = $('coupon_generation_msg');
        if (currentMsgEl) {
            currentMsgEl.remove();
        }
        var button = $('coupon_generation');
        var msgEl = new Element('div');
        msgEl.setAttribute('id', 'coupon_generation_msg');
        msgEl.update(msg).setStyle(style);
        button.insert({'after': msgEl});
    }
};

AWCouponAutocompleter = Class.create(Ajax.Autocompleter, {
    updateElement: function(li) {
        var value = li.getAttribute('data-email');
        var bounds = this.getTokenBounds();
        if (bounds[0] != -1) {
            var newValue = this.element.value.substr(0, bounds[0]);
            var whitespace = this.element.value.substr(bounds[0]).match(/^\s+/);
            if (whitespace)
                newValue += whitespace[0];
            this.element.value = newValue + value + this.element.value.substr(bounds[1]);
        } else {
            this.element.value = value;
        }
        this.oldElementValue = this.element.value;
        this.element.focus();
    }
});