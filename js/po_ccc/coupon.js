/* COUPON CODE */
PoCheckoutCoupon = Class.create();
PoCheckoutCoupon.prototype = {
    initialize: function(config) {
        // Init dom elements
        this.couponCodeInput    = $$(config.couponCodeInputSelector).first();
        this.applyCouponButton  = $$(config.applyCouponButtonSelector).first();
        this.cancelCouponButton = $$(config.cancelCouponButtonSelector).first();
        this.couponCodeContainer = $$(config.couponCodeContainerSelector).first();
        this.unobtrusiveCheckbox = $$(config.unobtrusiveCheckboxSelector).first();
        this.couponCodeList = $$(config.couponCodeListSelector).first();
        this.paymentMethodContainer = $$(config.paymentMethodContainerSelector).first();

        this.loadingIndicator   = $$(config.loadingIndicatorSelector).first();
        this.msgContainer       = $$(config.msgContainerSelector).first();

        // Init config
        this.applyCouponUrl  = config.applyCouponUrl;
        this.cancelCouponUrl = config.cancelCouponUrl;
        this.cartUrl         = config.cartUrl;
        this.isCouponApplied = config.isCouponApplied;
        this.isEnabledUnobtrusiveInterface = config.isEnabledUnobtrusiveInterface;

        // API
        this.canShowCancelButton          = config.canShowCancelButton;
        this.isCleanCouponCodeAfterApply  = config.isCleanCouponCodeAfterApply;
        this.isCleanCouponCodeAfterCancel = config.isCleanCouponCodeAfterCancel;

        // Init messages
        this.successMessageBoxCssClass = config.successMessageBoxCssClass;
        this.errorMessageBoxCssClass   = config.errorMessageBoxCssClass;
        this.defaultSuccessMsg = config.defaultSuccessMsg;
        this.defaultErrorMsg   = config.defaultErrorMsg;

        // init behaviour
        this.init();
    },
    init: function() {
        if (this.applyCouponButton) {
            this.applyCouponButton.observe('click', this.applyCoupon.bind(this));
        }
        if (this.cancelCouponButton) {
            this.cancelCouponButton.observe('click', this.cancelCoupon.bind(this));
        }
        if (this.isEnabledUnobtrusiveInterface && this.unobtrusiveCheckbox) {
            this.unobtrusiveCheckbox.observe('change', this.processUnobtrusive.bind(this));
        }
        if (this.couponCodeList) {
            this._initCancelCouponLink();
        }
    },
    applyCoupon: function() {
        this.removeMsg();

        this.couponCodeInput.addClassName('required-entry');
        var validationResult = Validation.validate(this.couponCodeInput)
        this.couponCodeInput.removeClassName('required-entry');
        if (!validationResult) {
            return;
        }

        var me = this;

        new Ajax.Request(
            me.applyCouponUrl,
            {
                method: 'post',
                parameters : {
                    'coupon_code' : me.couponCodeInput.getValue()
                },
                onCreate : function() {
                    me.showLoadingIndicator();
                    me._onAjaxApplyCouponActionCreateFn();
                },
                onComplete: function(transport) {
                    me.hideLoadingIndicator();
                    me._onAjaxApplyCouponActionCompleteFn(transport);
                },
                onFailure: function(transport) {
                    me.hideLoadingIndicator();
                    me._onAjaxApplyCouponActionFailureFn(transport);
                }
            }
        );
    },

    cancelCoupon: function() {
        this.removeMsg();
        this._cancelCouponHandler(this.cancelCouponUrl);
    },

    _cancelCouponHandler: function(cancelCouponUrl) {
        this.removeMsg();
        var me = this;

        new Ajax.Request(
            cancelCouponUrl,
            {
                method: 'post',
                onCreate : function() {
                    me.showLoadingIndicator();
                    me._onAjaxCancelCouponActionCreateFn();
                },
                onComplete: function(transport) {
                    me.hideLoadingIndicator();
                    me._onAjaxCancelCouponActionCompleteFn(transport);
                },
                onFailure: function(transport) {
                    me.hideLoadingIndicator();
                    me._onAjaxCancelCouponActionFailureFn(transport);
                }
            }
        );
    },

    _initCancelCouponLink: function() {
        var removeCouponCodeList = this.couponCodeList.select('a.btn-remove');
        var me = this;
        removeCouponCodeList.each(function(removeLink) {
            Event.observe(removeLink, 'click', function(e){
                e.stop();
                me._cancelCouponHandler(removeLink.readAttribute('href'));
            });
        })
    },

    processUnobtrusive: function() {
        if (this.unobtrusiveCheckbox.checked) {
            this.removeMsg();
            this.couponCodeContainer.show();
        } else {
            this.couponCodeContainer.hide();
            if (this.isCouponApplied) {
                this.applyCoupon();
            }
        }
    },
    _onAjaxApplyCouponActionCreateFn: function() {
        if (this.canShowCancelButton) {
            if (this.isCouponApplied) {
                this.applyCouponButton.hide();
            } else {
                this.cancelCouponButton.hide();
            }
        }
    },
    _onAjaxApplyCouponActionCompleteFn: function(transport) {
        try {
            eval("var json = " + transport.responseText + " || {}");
        } catch(e) {
            this.showError(this.defaultErrorMsg);
            return;
        }
        this.isCouponApplied = json.coupon_applied;
        if (json.success) {
            var successMsg = this.defaultSuccessMsg;
            if (("messages" in json) && ("length" in json.messages) && json.messages.length > 0) {
                successMsg = json.messages;
            }
            this.showSuccess(successMsg);
            if (this.canShowCancelButton && this.isCouponApplied) {
                this.applyCouponButton.hide();
                this.cancelCouponButton.show();
            }
            if (this.isCleanCouponCodeAfterApply) {
                this.couponCodeInput.setValue('');
            }
            this.couponCodeList.update(json.coupon_code_list);
            this.updatePaymentMethods(json.payment_html);
            this._initCancelCouponLink();
        } else {
            var errorMsg = this.defaultErrorMsg;
            if (("messages" in json) && ("length" in json.messages) && json.messages.length > 0) {
                errorMsg = json.messages;
            }
            this.showError(errorMsg);
        }
    },
    _onAjaxApplyCouponActionFailureFn: function() {
        location.href = this.cartUrl;
    },
    _onAjaxCancelCouponActionCreateFn: function() {
        if (this.canShowCancelButton) {
            if (this.isCouponApplied) {
                this.applyCouponButton.hide();
            } else {
                this.cancelCouponButton.hide();
            }
        }
    },
    _onAjaxCancelCouponActionCompleteFn: function(transport) {
        try {
            eval("var json = " + transport.responseText + " || {}");
        } catch(e) {
            this.showError(this.defaultErrorMsg);
            return;
        }
        this.isCouponApplied = json.coupon_applied;
        if (json.success) {
            var successMsg = this.defaultSuccessMsg;
            if (("messages" in json) && ("length" in json.messages) && json.messages.length > 0) {
                successMsg = json.messages;
            }
            this.showSuccess(successMsg);
            if (this.canShowCancelButton && !this.isCouponApplied) {
                this.applyCouponButton.show();
                this.cancelCouponButton.hide();
            }
            if (this.isCleanCouponCodeAfterCancel) {
                this.couponCodeInput.setValue('');
            }
            this.couponCodeList.update(json.coupon_code_list);
            this.updatePaymentMethods(json.payment_html);
            this._initCancelCouponLink();
        } else {
            var errorMsg = this.defaultErrorMsg;
            if (("messages" in json) && ("length" in json.messages) && json.messages.length > 0) {
                errorMsg = json.messages;
            }
            this.showError(errorMsg);
        }
    },
    _onAjaxCancelCouponActionFailureFn: function() {
        location.href = this.cartUrl;
    },
    updatePaymentMethods: function(paymentMethods) {
        if (this.paymentMethodContainer) {
            this.paymentMethodContainer.update(paymentMethods);
            if ('checkout' in window) {
                checkout.reloadStep('shipping_method')
            }
        }
    },
    showLoadingIndicator: function() {
        this.loadingIndicator.show();
    },
    hideLoadingIndicator: function() {
        this.loadingIndicator.hide();
    },
    showError: function(msg){
        this.renderMessageList(msg, this.errorMessageBoxCssClass);
    },
    showSuccess: function(msg){
        this.renderMessageList(msg, this.successMessageBoxCssClass);
    },
    removeMsg: function() {
        this.msgContainer.update();
        this.msgContainer.hide();
    },
    renderMessageList: function(msg, cssClass){
        var me = this;
        if ((typeof(msg) === "object") && ("length" in msg)) {
            msg.each(function(msgItem){
                me._addMessageToContainer(msgItem, cssClass);
            });
        } else if(typeof(msg) === "string") {
            this._addMessageToContainer(msg, cssClass);
        }
        this.msgContainer.show();
    },
    _addMessageToContainer: function(msg, cssClass) {
        var targetBlock = null;
        var existsErrorBlocks = this.msgContainer.select("." + cssClass + " ul");
        if (existsErrorBlocks.length === 0) {
            var errorMsgBlock = new Element('li');
            errorMsgBlock.addClassName(cssClass);
            errorMsgBlock.appendChild(new Element('ul'));
            this.msgContainer.insertBefore(errorMsgBlock, this.msgContainer.down());
            targetBlock = errorMsgBlock.down();
        } else {
            targetBlock = existsErrorBlocks.first();
        }
        var newMessage = new Element('li');
        newMessage.update(msg);
        targetBlock.appendChild(newMessage);
    }
};