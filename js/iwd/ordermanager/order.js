;window.hasOwnProperty = function (obj) {return (this[obj]) ? true : false;};
if (!window.hasOwnProperty('IWD')) {IWD = {};}
if(typeof(jQueryIWD) == "undefined"){if(typeof(jQuery) != "undefined") {jQueryIWD = jQuery;}} $ji = jQueryIWD;

IWD.OrderManager = {
    orderId: null,
    lang: 'en',
    is_archive: 0,

    init: function (orderId) {
        IWD.OrderManager.orderId = orderId;
        IWD.OrderManager.markAsArchived();
    },

    ShowLoadingMask: function () {
        $ji('#loading-mask').width($ji("html").width()).height($ji("html").height()).css('top', 0).css('left', -2).show();
    },

    HideLoadingMask: function () {
        $ji('#loading-mask').hide();
    },

    markAsArchived: function(){
        if(IWD.OrderManager.is_archive == 1){
            var text = $ji('h3.head-sales-order').html();
            $ji('h3.head-sales-order').html(text + " | ARCHIVED");
        }
    }
};

IWD.OrderManager.Popup = {
    currentPopup: null,
    popupValues: {},
    popupTitles: {},

    showModal: function(block){
        var options = {"backdrop":"static", "show":true};
        $ji('.om-iwd-modal').modaliwd(options);
        IWD.OrderManager.Popup.currentPopup = block;

        var block_id = '#control_form_' + IWD.OrderManager.Popup.currentPopup;
        var form = $ji(block_id).html();

        if(form.length){
            $ji('#iwd_om_popup_form').html("<form>"+form+"</form>");
        }

        if(typeof(IWD.OrderManager.Popup.popupValues[IWD.OrderManager.Popup.currentPopup]) != "undefined"){
            $ji.each(IWD.OrderManager.Popup.popupValues[IWD.OrderManager.Popup.currentPopup], function(){
                $ji('#iwd_om_popup_form [name="'+this.name+'"]').val(this.value);
            });
        } else {
            $ji.each($ji('#iwd_om_popup_form form').find('input, textarea'), function(){
                $ji(this).val($ji(this).attr("value"));
            });
        }

        $ji(".om-iwd-modal-title").html(IWD.OrderManager.Popup.popupTitles[IWD.OrderManager.Popup.currentPopup]);
    },

    hideModal: function(){
        $ji('.om-iwd-modal').modaliwd('hide');
    },

    cancelModal:function(){
        IWD.OrderManager.Popup.hideModal();
    },

    updateModal:function(){
        if(!IWD.OrderManager.Popup.validatePopupForm()){
            return;
        }

        var block_id = '#control_form_' + IWD.OrderManager.Popup.currentPopup;

        var form = $ji('#iwd_om_popup_form').html();
        $ji(block_id).html(form);

        IWD.OrderManager.Popup.popupValues[IWD.OrderManager.Popup.currentPopup] = $ji('#iwd_om_popup_form form').serializeArray();
        $ji.each(IWD.OrderManager.Popup.popupValues[IWD.OrderManager.Popup.currentPopup], function(){
            $ji(block_id + ' [name="'+this.name+'"]').val(this.value);
        });

        IWD.OrderManager.Popup.hideModal();
    },

    validatePopupForm:function(){
        var result = true;
        $ji.each($ji('#iwd_om_popup_form input, #iwd_om_popup_form textarea'), function(){
            $ji(this).removeClass('validation-failed');

            if($ji(this).attr('required') == 'required' && $ji(this).val() == ""){
                $ji(this).addClass('validation-failed');
                result = false;
                return true;
            }

            if($ji(this).attr('name') == "comment_email"){
                if(IWD.OrderManager.Popup.isEmail($ji(this).val()) == false){
                    $ji(this).addClass('validation-failed');
                    result = false;
                    return true;
                }
            }
        });
        return result;
    },


    isEmail:function(email){
        var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        var result = true;

        email.split(',').each(function(mail){
            mail = mail.trim();
            if(mail.length > 0){
                result = regex.test(mail) == false ? false : result;
            }
        });

        return result;
    }
};

IWD.OrderManager.OrderedItems = {
    urlEditOrderedItemsForm: '',
    urlEditOrderedItems: '',
    urlAddOrderedItemsForm: '',
    urlAddOrderedItems: '',
    discountTax: 0,
    applyTaxAfterDiscount: 0,
    order: null,
    orderedItems: null,
    configureItems: {},
    newItemOptions: {},

    initProductConfigure: function () {
    },

    init: function () {
        $ji("#ordered_items_edit").on("click", function (event) {
            IWD.OrderManager.OrderedItems.editOrderedItemsForm(event);
        });
    },

    /**** edit ordered items ****/
    editOrderedItemsForm: function (event) {
        event.preventDefault();

        IWD.OrderManager.ShowLoadingMask();

        $ji.ajax({url: IWD.OrderManager.OrderedItems.urlEditOrderedItemsForm,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&order_id=" + IWD.OrderManager.orderId,
            success: function (result) {
                if (result.ajaxExpired) {
                    location.reload();
                }
                else if (result.status) {
                    $ji('#ordered_items_table').hide();
                    $ji('#ordered_items_edit_form').remove();
                    $ji('#add_ordered_items_form').remove();
                    $ji("#ordered_items_box").append(result.form.toString());
                }

                IWD.OrderManager.HideLoadingMask();
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editOrderedItemsSubmit: function () {
        var orderedItemsFormValidation = new varienForm('ordered_items_form');
        if (!orderedItemsFormValidation.validator.validate())
            return;

        /* if all items checked */
        if ($ji('.ordered_item_remove').size() == $ji('.ordered_item_remove input:checkbox:checked').size()) {
            alert("Sorry, but you can not delete all items in order. Maybe, better remove this order?");
            return;
        }

        IWD.OrderManager.ShowLoadingMask();

        var formData = $ji('#ordered_items_form').serialize();
        $ji.ajax({
            url: IWD.OrderManager.OrderedItems.urlEditOrderedItems,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&" + formData,
            success: function () {
                location.reload();
            },
            error: function () {
                location.reload();
            }
        });
    },

    editOrderedItemsCancel: function () {
        $ji('#ordered_items_table').show();
        $ji('#ordered_items_edit_form').remove();
        $ji('#add_ordered_items_form').remove();
    },

    /**** add new items ****/
    addOrderedItemsForm: function () {
        IWD.OrderManager.ShowLoadingMask();

        $ji('#button_add_selected_items').show();
        $ji('#button_search_items_form').hide();

        IWD.OrderManager.OrderedItems.order.gridProducts = $H({});

        if ($ji("#add_ordered_items_form").length > 0) {
            $ji("#add_ordered_items_form").show();
            IWD.OrderManager.HideLoadingMask();
            return;
        }

        $ji.ajax({url: IWD.OrderManager.OrderedItems.urlAddOrderedItemsForm,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&order_id=" + IWD.OrderManager.orderId,
            success: function (result) {
                if (result.ajaxExpired) {
                    location.reload();
                }
                else if (result.status == 1) {
                    $ji("#add_ordered_items_form").remove();

                    $ji.getScript(result.url_configure_js, function () {
                        $ji('form#product_composite_configure_form').remove();
                        $ji('#popup-window-mask').remove();
                        $ji('#product_composite_configure').remove();

                        $ji("#anchor-content").append(result.configure_form.toString());
                        IWD.OrderManager.OrderedItems.initProductConfigure();

                        $ji("#ordered_items_box").append('<div id="add_ordered_items_form">' + result.form.toString() + '</div>');

                        $ji('form#product_composite_configure_form button[type="submit"]').on('click', function () {
                            var formData = $ji('form#product_composite_configure_form').serializeArray();
                            var productId = productConfigure.current.itemId;
                            IWD.OrderManager.OrderedItems.configureItems[productId] = formData;
                        });

                        IWD.OrderManager.HideLoadingMask();
                    });
                }
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    addOrderedItems: function () {
        IWD.OrderManager.ShowLoadingMask();

        var selected_items = IWD.OrderManager.OrderedItems.order.gridProducts.toObject();

        if (Object.keys(selected_items).length <= 0) {
            $ji("#add_ordered_items_form").hide();
            $ji('#button_add_selected_items').hide();
            $ji('#button_search_items_form').show();
            IWD.OrderManager.HideLoadingMask();
            return;
        }
        //console.log(IWD.OrderManager.OrderedItems.urlAddOrderedItems);
        $ji.ajax({
            url: IWD.OrderManager.OrderedItems.urlAddOrderedItems,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY +
            "&order_id=" + IWD.OrderManager.orderId +
            "&items=" + JSON.stringify(selected_items, null, 2) +
            "&options=" + JSON.stringify(IWD.OrderManager.OrderedItems.configureItems, null, 2),
            success: function (result) {
                if (result.status == 1) {
                    $ji("#add_ordered_items_form").hide();
                    $ji('#button_add_selected_items').hide();
                    $ji('#button_search_items_form').show();

                    IWD.OrderManager.OrderedItems.order.gridProducts = $H({});
                    IWD.OrderManager.OrderedItems.enabledSubmitButton();
                    $ji("#ordered_items_edit_table").append(result.form);
                }
                else if(result.status == 0){
                    alert(result.error_message);
                }

                IWD.OrderManager.HideLoadingMask();
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editOrderedItemsOptions: function (item_id) {
        IWD.OrderManager.ShowLoadingMask();

        var form = new FormData($('iwd_om_product_composite_configure_form'));
        var url = IWD.OrderManager.OrderedItems.urlEditOrderedItemsOptions + "&form_key=" + FORM_KEY
            + "&order_id=" + IWD.OrderManager.orderId + "&item_id=" + item_id;

        $ji.ajax({
            url: url,
            type: "POST",
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            data: form,
            success: function (result) {
                if (result.status == 1) {
                    $ji('.item_name_' + item_id).html(result['name']);
                    $ji('.item_sku_' + item_id).html(result['sku']);
                    $ji('.item_options_' + item_id).html(result['options_html']);
                    $ji('input[name="items[' + item_id + '][price]"]').val(result['price']).change();
                    $ji('input[name="items[' + item_id + '][product_options]"]').val(result['product_options']);
                }
                else if(result.status == 0){
                    alert(result.error_message);
                }

                IWD.OrderManager.HideLoadingMask();
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    /**** enabled submit button after edit ****/
    enabledSubmitButton: function () {
        $ji('#edit_ordered_items_submit').removeAttr('disabled').removeClass('disabled');
    },

    showItemConfiguration: function(id){

        var listType = 'iwd_quote_items';
        var qtyElement = $('ordered_items_edit_table').select('input[name="items\['+id+'\]\[fact_qty\]"]')[0];

        productConfigureIWDOMEdit.setConfirmCallback(listType, function() {
            var confirmedCurrentQty = productConfigureIWDOMEdit.getCurrentFormQtyElement();
            if (qtyElement && confirmedCurrentQty && !isNaN(confirmedCurrentQty.value)) {
                qtyElement.value = confirmedCurrentQty.value;
            }
            fireEvent(qtyElement, 'change');
        }.bind(this));

        productConfigureIWDOMEdit.setShowWindowCallback(listType, function() {
            var formCurrentQty = productConfigureIWDOMEdit.getCurrentFormQtyElement();
            if (formCurrentQty && qtyElement && !isNaN(qtyElement.value)) {
                formCurrentQty.value = qtyElement.value;
            }
        }.bind(this));

        productConfigureIWDOMEdit.showItemConfiguration(listType, id);
    }
};

IWD.OrderManager.Address = {
    urlEditAddressForm: '',
    urlEditAddressSubmit: '',

    init: function () {
        $ji(".order_address_edit").on("click", function (event) {
            event.preventDefault();
            var address_id = this.id.split('_').last();
            IWD.OrderManager.Address.editAddressForm(address_id);
        });
    },

    editAddressForm: function (address_id) {
        IWD.OrderManager.ShowLoadingMask();

        $ji.ajax({url: IWD.OrderManager.Address.urlEditAddressForm,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&address_id=" + address_id,
            success: function (result) {
                if (result.ajaxExpired) {
                    location.reload();
                }
                else if (result.status) {
                    var order_address_block = $ji("#order_address_" + address_id);
                    order_address_block.hide();
                    $ji('#address_edit_form_' + address_id).remove();

                    var html = result.form.toString();
                    var VRegExp = new RegExp(/"region_id"/g);
                    html = html.replace(VRegExp, '"region_id_' + address_id + '"');
                    VRegExp = new RegExp(/"region"/g);
                    html = html.replace(VRegExp, '"region_' + address_id + '"');
                    VRegExp = new RegExp(/"country_id"/g);
                    html = html.replace(VRegExp, '"country_id_' + address_id + '"');
                    VRegExp = new RegExp(/"vat_id"/g);
                    html = html.replace(VRegExp, '"vat_id_' + address_id + '"');

                    order_address_block.parent().append(html);
                }

                IWD.OrderManager.HideLoadingMask();
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editAddressSubmit: function (address_id) {
        var addressFormValidation = eval('addressFormValidation_' + address_id);
        if (!addressFormValidation.validator.validate()){
            return;
        }

        IWD.OrderManager.ShowLoadingMask();

        var form = $ji('#address_edit_form_' + address_id);
        var formData = form.serialize();

        $ji.ajax({
            url: IWD.OrderManager.Address.urlEditAddressSubmit,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&" + formData,
            success: function (result) {
                if (result.ajaxExpired || result.status != 1  || result.address == false) {
                    location.reload();
                } else {
                    form.remove();
                    $ji("#order_address_" + address_id).html(result.address).show();
                    IWD.OrderManager.HideLoadingMask();
                }
            },
            error: function (result) {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editAddressCancel: function (address_id) {
        $ji('#address_edit_form_' + address_id).remove();
        $ji("#order_address_" + address_id).show();
    }
};

IWD.OrderManager.AccountInfo = {
    urlEditAccountForm: '',
    urlEditAccountSubmit: '',

    init: function () {
        $ji(".account_information_edit").click(function (event) {
            event.preventDefault();
            IWD.OrderManager.AccountInfo.editCustomerInfoForm();
        });
    },

    editCustomerInfoForm: function () {
        IWD.OrderManager.ShowLoadingMask();

        $ji.ajax({url: IWD.OrderManager.AccountInfo.urlEditAccountForm,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&order_id=" + IWD.OrderManager.orderId,
            success: function (result) {
                if (result.ajaxExpired) {
                    location.reload();
                }
                else if (result.status) {
                    $ji("#account_information_" + IWD.OrderManager.orderId).hide();
                    $ji('#account_information_form_' + IWD.OrderManager.orderId).remove();
                    $ji("#account_information_" + IWD.OrderManager.orderId).parent().append(result.form.toString());
                }

                IWD.OrderManager.HideLoadingMask();
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editCustomerInfoSubmit: function () {
        var accountInfoFormValidation = new varienForm('account_information_form_' + IWD.OrderManager.orderId);
        if (!accountInfoFormValidation.validator.validate())
            return;

        IWD.OrderManager.ShowLoadingMask();

        var form = $ji('#account_information_form_' + IWD.OrderManager.orderId);
        var formData = form.serialize();

        $ji.ajax({
            url: IWD.OrderManager.AccountInfo.urlEditAccountSubmit,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&" + formData,
            success: function (result) {
                location.reload();
                /*if (result.ajaxExpired) {
                 location.reload();
                 }
                 else if (result.status) {
                 form.remove();
                 $ji("#account_information_" + IWD.OrderManager.orderId).html(result.text).show();
                 }
                 IWD.OrderManager.HideLoadingMask();*/
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editCustomerInfoCancel: function () {
        $ji('#account_information_form_' + IWD.OrderManager.orderId).remove();
        $ji("#account_information_" + IWD.OrderManager.orderId).show();
    }
};

IWD.OrderManager.OrderInfo = {
    urlEditOrderInfoForm: '',
    urlEditOrderInfoSubmit: '',

    init: function () {
        $ji(".order_information_edit").on("click", function (event) {
            event.preventDefault();
            IWD.OrderManager.OrderInfo.editOrderInformationForm();
        });
    },

    getParams: function(){
        var params = "&order_id=" + IWD.OrderManager.orderId;

        var invoice_id = $ji(".order_information_edit").attr('data-invoice-id');
        if(typeof(invoice_id) != 'undefined'){
            params += "&invoice_id=" + invoice_id;
        }

        var shipping_id = $ji(".order_information_edit").attr('data-shipping-id');
        if(typeof(shipping_id) != 'undefined'){
            params += "&shipping_id=" + shipping_id;
        }

        var creditmemo_id = $ji(".order_information_edit").attr('data-creditmemo-id');
        if(typeof(creditmemo_id) != 'undefined'){
            params += "&creditmemo_id=" + creditmemo_id;
        }

        return params;
    },

    editOrderInformationForm: function () {
        IWD.OrderManager.ShowLoadingMask();

        $ji.ajax({url: IWD.OrderManager.OrderInfo.urlEditOrderInfoForm,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + IWD.OrderManager.OrderInfo.getParams(),
            success: function (result) {
                if (result.ajaxExpired) {
                    location.reload();
                }
                else if (result.status) {
                    $ji("#order_information").hide();
                    $ji('#order_information_form').remove();
                    $ji("#order_information").parent().append(result.form.toString());
                    $ji(".jquery-ui-datepicker").datetimepicker(
                        {
                            lang: IWD.OrderManager.lang,
                            format: IWD.OrderManager.date_format
                        }
                    );
                }

                IWD.OrderManager.HideLoadingMask();
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editOrderInformationSubmit: function () {
        var orderInfoFormValidation = new varienForm('order_information_form');
        if (!orderInfoFormValidation.validator.validate())
            return;

        IWD.OrderManager.ShowLoadingMask();

        var form = $ji('#order_information_form');
        var formData = form.serialize();

        $ji.ajax({
            url: IWD.OrderManager.OrderInfo.urlEditOrderInfoSubmit,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&" + formData,
            success: function (result) {
                location.reload();
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editOrderInformationCancel: function () {
        $ji('#order_information_form').remove();
        $ji("#order_information").show();
    }
};

IWD.OrderManager.Comments = {
    urlEditCommentForm: '',
    urlEditCommentSubmit: '',
    urlDeleteCommentSubmit: '',
    type: 'order',
    confirmText: "Are you sure?",

    init: function (type) {
        IWD.OrderManager.Comments.type = type;

        $ji(".delete_history_icon").on('click', function () {
            IWD.OrderManager.Comments.deleteComment(this);
        });

        $ji(".update_history_icon").on('click', function () {
            IWD.OrderManager.Comments.editCommentForm(this);
        });
    },

    deleteComment: function (item) {
        if (confirm(IWD.OrderManager.Comments.confirmText)) {
            IWD.OrderManager.ShowLoadingMask();

            var comment_id = item.id.split('_').last();

            $ji.ajax({url: IWD.OrderManager.Comments.urlDeleteCommentSubmit,
                type: "POST",
                dataType: 'json',
                data: "form_key=" + FORM_KEY +
                "&type=" + IWD.OrderManager.Comments.type +
                "&comment_id=" + comment_id,
                success: function (result) {
                    if (result.ajaxExpired) {
                        location.reload();
                    }
                    else if (result.status) {
                        $ji(item).hide();
                        $ji(item).parent().delay(500).hide(1000);
                    }
                    IWD.OrderManager.HideLoadingMask();
                },
                error: function () {
                    IWD.OrderManager.HideLoadingMask();
                }
            });
        }
    },

    editCommentForm: function (item) {
        IWD.OrderManager.ShowLoadingMask();

        var comment_id = item.id.split('_').last();

        $ji.ajax({url: IWD.OrderManager.Comments.urlEditCommentForm,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY +
            "&type=" + IWD.OrderManager.Comments.type +
            "&comment_id=" + comment_id,
            success: function (result) {
                $ji("#comment_text_" + comment_id).hide();
                $ji("#updated_comment_form_" + comment_id).remove();
                $ji(item).parent().append(result.comment);
                IWD.OrderManager.HideLoadingMask();
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editCommentSubmit: function (comment_id) {
        IWD.OrderManager.ShowLoadingMask();

        var comment_text = $ji("textarea#updated_comment_text_" + comment_id).val();

        $ji.ajax({url: IWD.OrderManager.Comments.urlEditCommentSubmit,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY +
            "&comment_id=" + comment_id +
            "&type=" + IWD.OrderManager.Comments.type +
            "&comment_text=" + comment_text,
            success: function (result) {
                if (result.ajaxExpired) {
                    location.reload();
                }
                else {
                    var comment = (result.comment != null) ? result.comment : '';
                    $ji("#updated_comment_form_" + comment_id).remove();
                    $ji("#comment_text_" + comment_id).html(comment).show();
                    IWD.OrderManager.HideLoadingMask();
                }
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editCommentCancel: function (comment_id) {
        $ji("#updated_comment_form_" + comment_id).remove();
        $ji("#comment_text_" + comment_id).show();
    }
};

IWD.OrderManager.Shipping = {
    urlEditShippingForm: '',
    urlEditShippingSubmit: '',

    init: function () {
        $ji(".order_shipping_edit").on("click", function (event) {
            event.preventDefault();
            IWD.OrderManager.Shipping.editShippingForm();
        });

        IWD.OrderManager.Shipping.radioInit();
        IWD.OrderManager.Shipping.interactiveForm();
    },

    radioInit: function(){
        $ji(document).on("change", "#order-shipping-method-choose input[type=radio]", function () {
            IWD.OrderManager.Shipping.showEditTable();
        });

        $ji(document).on('keypress', "#order-shipping-method-choose input.validate-number", function (e) {
            if (e.which == 13) return 1;
            var letters = '1234567890.,';
            return (letters.indexOf(String.fromCharCode(e.which)) != -1);
        });
    },

    showEditTable: function () {
        $ji("#order-shipping-method-choose input[type=text]").attr('disabled', 'disabled').removeClass('required-entry');

        $ji("#order-shipping-method-choose input[name=shipping_method_radio]").each(function() {
            var code = $ji(this).attr('id');
            if($ji("#" + code).prop('checked')){
                $ji("#" + code + "_edit_table").show();
                $ji('#order-shipping-method-choose input[name="s_amount_excl_tax[' + $ji("#" + code).val() + ']"]').removeAttr('disabled').addClass('required-entry');
                $ji('#order-shipping-method-choose input[name="s_amount_incl_tax[' + $ji("#" + code).val() + ']"]').removeAttr('disabled').addClass('required-entry');
                $ji('#order-shipping-method-choose input[name="s_tax_percent[' + $ji("#" + code).val() + ']"]').removeAttr('disabled').addClass('required-entry');
                $ji('#order-shipping-method-choose input[name="s_description[' + $ji("#" + code).val() + ']"]').removeAttr('disabled').addClass('required-entry');
            }
            else{
                $ji("#" + code + "_edit_table").hide();
            }
        });
    },

    getInputId: function (item) {
        var reg = /items\[(\w+)\]\[(\w+)\]/i;
        var attr_name = reg.exec($ji(item).attr('name'));
        return attr_name[1];
    },

    interactiveForm: function() {
        $ji(document).on('change', "#order-shipping-method-choose input.input-text", function () {
            var code = $ji(this).attr('data-method');

            var amount_excl_tax = $ji('#order-shipping-method-choose input[name="s_amount_excl_tax[' + code + ']"]');
            var amount_incl_tax = $ji('#order-shipping-method-choose input[name="s_amount_incl_tax[' + code + ']"]');
            var tax_percent = $ji('#order-shipping-method-choose input[name="s_tax_percent[' + code + ']"]');

            if($ji(this).hasClass('amount_excl_tax') || $ji(this).hasClass('tax_percent')){
                var incl_tax = parseFloat(amount_excl_tax.val()) + (parseFloat(amount_excl_tax.val()) * parseFloat(tax_percent.val()) / 100);
                amount_incl_tax.val(incl_tax.toFixed(2));
            } else if($ji(this).hasClass('amount_incl_tax')){
                var excl_tax = parseFloat(amount_incl_tax.val()) / (1 + parseFloat(tax_percent.val()) / 100);
                amount_excl_tax.val(excl_tax.toFixed(2));
            }
        });
    },

    editShippingForm: function () {
        IWD.OrderManager.ShowLoadingMask();

        $ji.ajax({url: IWD.OrderManager.Shipping.urlEditShippingForm,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&order_id=" + IWD.OrderManager.orderId,
            success: function (result) {
                if (result.ajaxExpired) {
                    location.reload();
                }
                else if (result.status) {
                    $ji('#iwd_shipping_edit_form').remove();
                    $ji("#order_shipping").hide();
                    $ji("#order_shipping").parent().append(result.form.toString());
                }

                IWD.OrderManager.HideLoadingMask();
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editShippingSubmit: function () {
        var shippingFormValidation = new varienForm('iwd_shipping_edit_form');
        if (!shippingFormValidation.validator.validate())
            return;

        IWD.OrderManager.ShowLoadingMask();

        var formData = $ji('#iwd_shipping_edit_form').serialize();

        $ji.ajax({
            url: IWD.OrderManager.Shipping.urlEditShippingSubmit,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&order_id=" + IWD.OrderManager.orderId + "&" + formData,
            success: function (result) {
                location.reload();
            },
            error: function (result) {
                location.reload();
            }
        });
    },

    editShippingCancel: function () {
        $ji('#iwd_shipping_edit_form').remove();
        $ji("#order_shipping").show();
    }

};

IWD.OrderManager.Payment = {
    urlEditPaymentForm: '',
    urlEditPaymentSubmit: '',

    init: function () {
        $ji(".order_payment_edit").on("click", function (event) {
            event.preventDefault();
            IWD.OrderManager.Payment.editPaymentForm();

        });

        IWD.OrderManager.Payment.radioInit();
    },

    radioInit: function(){
        $ji(document).on("change", "#iwd_payment_edit_form [name='payment[method]']", function () {
            $ji("#iwd_edit_payment_form_submit").removeAttr("disabled").removeClass("disabled");
        });
    },

    editPaymentForm: function(){
        IWD.OrderManager.ShowLoadingMask();

        $ji.ajax({url: IWD.OrderManager.Payment.urlEditPaymentForm,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&order_id=" + IWD.OrderManager.orderId,
            success: function (result) {
                if (result.ajaxExpired) {
                    location.reload();
                }
                else if (result.status) {
                    $ji('#iwd_payment_edit_form').remove();
                    $ji("#order_payment").hide();
                    $ji("#order_payment").parent().append(result.form.toString());

                    if($ji('#order-billing_method_form dd').length == 1){
                        $ji("#iwd_edit_payment_form_submit").removeAttr("disabled").removeClass("disabled");
                    }

                    $ji('#iwd_payment_edit_form input[type=text]').val("");
                }

                IWD.OrderManager.HideLoadingMask();
            },
            error: function () {
                IWD.OrderManager.HideLoadingMask();
            }
        });
    },

    editPaymentSubmit: function(){
        var paymentFormValidation = new varienForm('iwd_payment_edit_form');
        if (!paymentFormValidation.validator.validate())
            return;

        IWD.OrderManager.ShowLoadingMask();

        var formData = $ji('#iwd_payment_edit_form').serialize();

        $ji.ajax({
            url: IWD.OrderManager.Payment.urlEditPaymentSubmit,
            type: "POST",
            dataType: 'json',
            data: "form_key=" + FORM_KEY + "&order_id=" + IWD.OrderManager.orderId + "&" + formData,
            success: function (result) {
                location.reload();
            },
            error: function (result) {
                location.reload();
            }
        });
    },

    editPaymentCancel: function () {
        $ji('#iwd_payment_edit_form').remove();
        $ji("#order_payment").show();
    }
};

/**** interactive edit grid ****/
IWD.OrderManager.TaxCalculation = {
    taxCalculationMethodBasedOn: 0, /* algorithm */
    taxCalculationBasedOn: 0,
    catalogPrices: 0, /* 1-include; 0-exclude tax */
    shippingPrices: 0,
    applyTaxAfterDiscount: 0, /* applyCustomerTax */
    discountTax: 0, /* applyDiscountOnPrices */
    validateStockQty: 1,

    CALC_TAX_BEFORE_DISCOUNT_ON_EXCL: '0_0',
    CALC_TAX_BEFORE_DISCOUNT_ON_INCL: '0_1',
    CALC_TAX_AFTER_DISCOUNT_ON_EXCL: '1_0',
    CALC_TAX_AFTER_DISCOUNT_ON_INCL: '1_1',

    init: function () {
        $ji(document).on('keypress', "input.validate-number", function (e) {
            if (e.which == 13 || e.which == 8) return 1;
            var letters = '1234567890.,';
            return (letters.indexOf(String.fromCharCode(e.which)) != -1);
        });

        $ji(document).on('change', "input.edit_order_item", function () {
            IWD.OrderManager.TaxCalculation.updateOrderItemInput(this);
            IWD.OrderManager.TaxCalculation.enabledSubmitButton();
        });

        $ji(document).on('change', "input[type=checkbox].remove_ordered_item", function () {
            IWD.OrderManager.TaxCalculation.removeItemRow(this);
            IWD.OrderManager.TaxCalculation.enabledSubmitButton();
        });
    },

    removeItemRow: function (item) {
        var parent_id = $ji(item).attr('data-parent-id') || null;
        var id = $ji(item).attr('data-item-id') || null;
        var result = true;

        if ($ji(item).prop("checked")) {
            result = IWD.OrderManager.TaxCalculation.disabledRow(id, parent_id);
        } else {
            result = IWD.OrderManager.TaxCalculation.enabledRow(id, parent_id);
        }

        if (parent_id && result) {
            var bundle_items = IWD.OrderManager.TaxCalculation.getBundleItems(parent_id);
            if (!IWD.OrderManager.TaxCalculation.isRemoveAllBundleItems(bundle_items, parent_id)){
                IWD.OrderManager.TaxCalculation.calculateBundleTotals(bundle_items, parent_id);
            }
        }
    },

    disabledRow: function (row_id, parent_id) {
        var row_item = $ji('#ordered_items_edit_table tr[data-item-id="' + row_id + '"]');
        row_item.addClass('removed_item');
        row_item.find('input[type=text], button').attr('disabled', 'disabled');
        row_item.find('button').addClass('disabled');

        /* for bundle product */
        $ji('input.remove_ordered_item.has_parent_' + row_id).prop("checked", true).click(IWD.OrderManager.TaxCalculation.deactivator);
        $ji('tr.has_parent_' + row_id).addClass('removed_item');
        $ji('tr.has_parent_' + row_id + ' input[type=text]').attr('disabled', 'disabled');

        return true;
    },

    enabledRow: function (row_id, parent_id) {
        if (parent_id && $ji('#remove_' + parent_id).prop("checked"))
            return false;

        var row_item = $ji('#ordered_items_edit_table tr[data-item-id="' + row_id + '"]');
        row_item.removeClass('removed_item');
        row_item.find('input[type=text], button').removeAttr('disabled');
        row_item.find('button').removeClass('disabled');

        /* for bundle product */
        $ji('input.remove_ordered_item.has_parent_' + row_id).prop("checked", false).unbind('click', IWD.OrderManager.TaxCalculation.deactivator);
        $ji('tr.has_parent_' + row_id).removeClass('removed_item');
        $ji('tr.has_parent_' + row_id + ' input[type=text]').removeAttr('disabled');

        return true;
    },

    calculateBundleTotals: function (bundle_items, bundle_id) {
        /* !canShowPriceInfo */
        if (!bundle_items[Object.keys(bundle_items)[0]].price.val()){
            return false;
        }

        var total_price_tax_incl = 0;
        var total_price_tax_excl = 0;
        var total_subtotal_tax_incl = 0;
        var total_subtotal_tax_excl = 0;
        var total_tax_amount = 0;
        var bundle = IWD.OrderManager.TaxCalculation.getInputs(bundle_id);

        var bundle_qty = parseFloat(bundle.fact_qty.val());
        $ji.each(bundle_items, function (i, input) {
            /* item was removed */
            if (input.remove.prop("checked")) {
                return true;
            }
            var qty = parseFloat(input.fact_qty.val()) / bundle_qty;
            total_price_tax_incl += parseFloat(input.price_incl_tax.val()) * qty;
            total_price_tax_excl += parseFloat(input.price.val()) * qty;
            total_subtotal_tax_incl += parseFloat(input.subtotal_incl_tax.val());
            total_subtotal_tax_excl += parseFloat(input.subtotal.val());
            total_tax_amount += parseFloat(input.tax_amount.val());

            IWD.OrderManager.TaxCalculation.updateQtyInBundle(input, bundle);
        });

        bundle.price_incl_tax.val(total_price_tax_incl.toFixed(2));
        bundle.price.val(total_price_tax_excl.toFixed(2));
        bundle.subtotal_incl_tax.val(total_subtotal_tax_incl.toFixed(2));
        bundle.subtotal.val(total_subtotal_tax_excl.toFixed(2));
        bundle.tax_amount.val(total_tax_amount.toFixed(2));

        return true;
    },

    isRemoveAllBundleItems: function (bundle_items, bundle_id) {
        var count_removed_items = 0;
        $ji.each(bundle_items, function (i, input) {
            if (input.remove.prop("checked")) count_removed_items++;
        });

        /* checked all bundle items */
        if (count_removed_items == Object.keys(bundle_items).length) {
            $ji('input.remove_ordered_item.has_parent_' + bundle_id).prop("checked", false);
            IWD.OrderManager.TaxCalculation.calculateBundleTotals(bundle_items, bundle_id);
            $ji('input[name="items[' + bundle_id + '][remove]"').prop("checked", true);
            IWD.OrderManager.TaxCalculation.disabledRow(bundle_id, null);
            return true;
        }

        return false;
    },

    updateBundleItems: function (name, id) {
        var bundle_items = IWD.OrderManager.TaxCalculation.getBundleItems(id);
        if (Object.keys(bundle_items).length == 0){
            return;
        }

        switch (name) {
            case "qty":
                var bundle = IWD.OrderManager.TaxCalculation.getInputs(id);
                var bundle_qty = parseFloat(bundle.fact_qty.val());

                $ji.each(bundle_items, function (i, input) {
                    var qty_item_in_bundle = parseFloat(input.qty_item_in_bundle.val());
                    input.fact_qty.val(bundle_qty * qty_item_in_bundle).change();
                    IWD.OrderManager.TaxCalculation.updateQtyInBundle(input, bundle);
                });

                break;

            case "fact_qty":
                var bundle = IWD.OrderManager.TaxCalculation.getInputs(id);
                var bundle_qty = parseFloat(bundle.fact_qty.val());

                $ji.each(bundle_items, function (i, input) {
                    var qty_item_in_bundle = parseFloat(input.qty_item_in_bundle.val());
                    input.fact_qty.val(bundle_qty * qty_item_in_bundle).change();
                    IWD.OrderManager.TaxCalculation.updateQtyInBundleFact(input, bundle);
                });

                break;
        }
    },

    /** helpers methods **/
    deactivator: function (event) {
        event.preventDefault();
    },
    getInputId: function (item) {
        var reg = /items\[(\w+)\]\[(\w+)\]/i;
        var attr_name = reg.exec($ji(item).attr('name'));
        return attr_name[1];
    },
    getInputName: function (item) {
        var reg = /items\[(\w+)\]\[(\w+)\]/i;
        var attr_name = reg.exec($ji(item).attr('name'));
        return attr_name[2];
    },
    getInputs: function (id) {
        return {
            original_price: $ji("input[name='items[" + id + "][original_price]']"),
            price: $ji("input[name='items[" + id + "][price]']"),
            price_incl_tax: $ji("input[name='items[" + id + "][price_incl_tax]']"),
            subtotal: $ji("input[name='items[" + id + "][subtotal]']"),
            subtotal_incl_tax: $ji("input[name='items[" + id + "][subtotal_incl_tax]']"),
            tax_amount: $ji("input[name='items[" + id + "][tax_amount]']"),
            hidden_tax_amount: $ji("input[name='items[" + id + "][hidden_tax_amount]']"),
            weee_tax_applied_row_amount: $ji("input[name='items[" + id + "][weee_tax_applied_row_amount]']"),
            tax_percent: $ji("input[name='items[" + id + "][tax_percent]']"),
            discount_amount: $ji("input[name='items[" + id + "][discount_amount]']"),
            discount_percent: $ji("input[name='items[" + id + "][discount_percent]']"),
            row_total: $ji("input[name='items[" + id + "][row_total]']"),

            qty_item_in_bundle: $ji("input[name='items[" + id + "][qty_item_in_bundle]']"),

            fact_qty: $ji("input[name='items[" + id + "][fact_qty]']"),

            item_id: id,

            remove: $ji("input[name='items[" + id + "][remove]']"),
            parent: $ji("input[name='items[" + id + "][parent]']")
        };
    },
    getBundleItems: function (bundle_id) {
        var children = {};
        $ji(".has_parent_" + bundle_id).each(function () {
            var item_id = $ji(this).attr('data-item-id');
            if (item_id != bundle_id){
                children[item_id] = IWD.OrderManager.TaxCalculation.getInputs(item_id);
            }
        });
        return children;
    },
    getCalculationSequence: function () {
        if (IWD.OrderManager.TaxCalculation.applyTaxAfterDiscount) {
            if (IWD.OrderManager.TaxCalculation.discountTax)
                return IWD.OrderManager.TaxCalculation.CALC_TAX_AFTER_DISCOUNT_ON_INCL;
            return IWD.OrderManager.TaxCalculation.CALC_TAX_AFTER_DISCOUNT_ON_EXCL;
        } else {
            if (IWD.OrderManager.TaxCalculation.discountTax)
                return IWD.OrderManager.TaxCalculation.CALC_TAX_BEFORE_DISCOUNT_ON_INCL;
            return IWD.OrderManager.TaxCalculation.CALC_TAX_BEFORE_DISCOUNT_ON_EXCL;
        }
    },
    enabledSubmitButton: function () {
        $ji('#edit_ordered_items_submit').removeAttr('disabled').removeClass('disabled');
    },
    /*********************/

    _checkFactQty: function(item){
        var data_stock_qty_increment = parseFloat($ji(item.fact_qty).attr("data-stock-qty-increment"));
        var data_stock_qty = parseFloat($ji(item.fact_qty).attr("data-stock-qty"));
        var data_stock_min_sales_qty = parseFloat($ji(item.fact_qty).attr("data-stock-min-sales-qty"));
        var data_stock_max_sales_qty = parseFloat($ji(item.fact_qty).attr("data-stock-max-sales-qty"));

        var qty_value = parseFloat($ji(item.fact_qty).val());

        if(qty_value <= 0){
            qty_value = 1;
        }

        if(IWD.OrderManager.TaxCalculation.validateStockQty == 1) {
            /* check max sales qty */
            if (qty_value > data_stock_max_sales_qty) {
                qty_value = data_stock_max_sales_qty;
            }

            /* check min sales qty */
            if (qty_value < data_stock_min_sales_qty) {
                qty_value = data_stock_min_sales_qty;
            }
        }

        /* check stock qty */
        if(data_stock_qty < qty_value){
            if(IWD.OrderManager.TaxCalculation.validateStockQty == 1){
                qty_value = data_stock_qty;
            }
            $ji('.notice_' + item.item_id).show();
            $ji('.notice_' + item.item_id + ' .notice_qty').show();
        }else {
            $ji('.notice_' + item.item_id).hide();
            $ji('.notice_' + item.item_id + ' .notice_qty').hide();
        }

        /* check qty increment */
        if(qty_value % data_stock_qty_increment != 0){
            qty_value = Math.round((qty_value / data_stock_qty_increment)) * data_stock_qty_increment;
        }

        $ji(item.fact_qty).val(qty_value);
    },

    updateQtyInBundle: function(item, parent){
        var item_qty = $ji(item.fact_qty).val();
        var parent_qty = $ji(parent.fact_qty).val();
        var qty_in_bundle = item_qty / parent_qty;
        qty_in_bundle = qty_in_bundle != qty_in_bundle.toFixed(2) ? qty_in_bundle.toFixed(2) : qty_in_bundle;
        $ji("#qty_in_bundle_" + item.item_id).text(qty_in_bundle);
    },

    updateQtyInBundleFact: function(item, parent){
        var item_qty = $ji(item.fact_qty).val();
        var parent_qty = $ji(parent.fact_qty).val();
        var qty_in_bundle = item_qty / parent_qty;
        qty_in_bundle = qty_in_bundle != qty_in_bundle.toFixed(2) ? qty_in_bundle.toFixed(2) : qty_in_bundle;
        $ji("#qty_in_bundle_" + item.item_id).text(qty_in_bundle);
    },

    /* 1. After every change */
    updateOrderItemInput: function (item) {
        var id = IWD.OrderManager.TaxCalculation.getInputId(item);
        var name = IWD.OrderManager.TaxCalculation.getInputName(item);
        var input = IWD.OrderManager.TaxCalculation.getInputs(id);

        /* !canShowPriceInfo */
        if (!input.price.val())
            return;

        switch (name) {
            case "original_price":
                break;
            case "price":
                IWD.OrderManager.TaxCalculation._calculatePriceExclTax(input);
                IWD.OrderManager.TaxCalculation._calculateSubtotal(input);
                break;
            case "price_incl_tax":
                IWD.OrderManager.TaxCalculation._calculatePriceInclTax(input);
                IWD.OrderManager.TaxCalculation._calculateSubtotal(input);
                break;
            case "fact_qty":
                IWD.OrderManager.TaxCalculation._checkFactQty(input);
                IWD.OrderManager.TaxCalculation._calculateSubtotal(input);
                break;
            case "tax_amount":
                break;
            case "tax_percent":
                IWD.OrderManager.TaxCalculation._changePrice(input);
                IWD.OrderManager.TaxCalculation._calculateSubtotal(input);
                break;
            case "discount_amount":
                break;
            case "discount_percent":
                break;
        }

        IWD.OrderManager.TaxCalculation.baseCalculation(input);
        IWD.OrderManager.TaxCalculation._calculateRowTotal(input);

        /* update related items */
        IWD.OrderManager.TaxCalculation.updateBundleItems(name, id);

        /* item is a part of bundle product (has parent) */
        if (input.parent.val()) {
            var parent_id = input.parent.val();
            var bundle_items = IWD.OrderManager.TaxCalculation.getBundleItems(parent_id);
            IWD.OrderManager.TaxCalculation.calculateBundleTotals(bundle_items, parent_id);
        }
    },

    /* 2. Select a tax calculation method */
    baseCalculation: function (input) {
        switch (IWD.OrderManager.TaxCalculation.taxCalculationMethodBasedOn) {
            case 'UNIT_BASE_CALCULATION':
                IWD.OrderManager.TaxCalculation._unitBaseCalculation(input);
                break;
            case 'ROW_BASE_CALCULATION':
                IWD.OrderManager.TaxCalculation._rowBaseCalculation(input);
                break;
            case 'TOTAL_BASE_CALCULATION':
                IWD.OrderManager.TaxCalculation._totalBaseCalculation(input);
                break;
        }
    },

    /* 2.1. Method: Unit price */
    _unitBaseCalculation: function (input) {
        var tax_amount = 0;
        var hidden_tax_amount = 0;

        switch (IWD.OrderManager.TaxCalculation.getCalculationSequence()) {
            case IWD.OrderManager.TaxCalculation.CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
                tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.subtotal.val(), input.tax_percent.val(), 0);
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal.val());
                break;
            case IWD.OrderManager.TaxCalculation.CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.subtotal_incl_tax.val(), input.tax_percent.val(), 1);
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal_incl_tax.val());
                break;

            case IWD.OrderManager.TaxCalculation.CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal.val());

                var qty = parseFloat(input.fact_qty.val());
                var discountAmount = parseFloat(input.discount_amount.val()) / qty;
                var price = parseFloat(input.price_incl_tax.val());
                var unitTaxDiscount = 0;
                var unitTax = 0;

                if (IWD.OrderManager.TaxCalculation.catalogPrices) {
                    unitTax = IWD.OrderManager.TaxCalculation._calcTaxAmount(price, input.tax_percent.val(), 1);
                    var discountRate = (unitTax / price) * 100;
                    unitTaxDiscount = IWD.OrderManager.TaxCalculation._calcTaxAmount(discountAmount, discountRate, 0);  /*1*/
                    hidden_tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(discountAmount, input.tax_percent.val(), 1);
                } else {
                    price = parseFloat(input.price.val());
                    unitTax = IWD.OrderManager.TaxCalculation._calcTaxAmount(price, input.tax_percent.val(), 0);
                    unitTaxDiscount = IWD.OrderManager.TaxCalculation._calcTaxAmount(discountAmount, input.tax_percent.val(), 0);
                }

                unitTax = Math.max(unitTax - unitTaxDiscount, 0);
                tax_amount = Math.max(qty * unitTax, 0);
                hidden_tax_amount = Math.max(qty * hidden_tax_amount, 0);
                break;

            case IWD.OrderManager.TaxCalculation.CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal_incl_tax.val());

                var qty = parseFloat(input.fact_qty.val());
                var discountAmount = parseFloat(input.discount_amount.val()) / qty;
                var price = parseFloat(input.price_incl_tax.val());
                var unitTax = 0;
                var unitTaxDiscount = 0;

                if (IWD.OrderManager.TaxCalculation.catalogPrices) {
                    unitTax = IWD.OrderManager.TaxCalculation._calcTaxAmount(price, input.tax_percent.val(), 1);
                    var discountRate = (unitTax / price) * 100;
                    unitTaxDiscount = IWD.OrderManager.TaxCalculation._calcTaxAmount(discountAmount, discountRate, 0); /*1*/
                    hidden_tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(discountAmount, input.tax_percent.val(), 1);
                } else {
                    price = parseFloat(input.price.val());
                    unitTax = IWD.OrderManager.TaxCalculation._calcTaxAmount(price, input.tax_percent.val(), 0);
                    unitTaxDiscount = IWD.OrderManager.TaxCalculation._calcTaxAmount(discountAmount, input.tax_percent.val(), 0);
                }

                unitTax = Math.max(unitTax - unitTaxDiscount, 0);
                tax_amount = Math.max(qty * unitTax, 0);
                hidden_tax_amount = Math.max(qty * hidden_tax_amount, 0);
                break;
        }

        input.tax_amount.val(tax_amount.toFixed(2));
        input.hidden_tax_amount.val(hidden_tax_amount.toFixed(2));
    },

    /* 2.2. Method: Row total */
    _rowBaseCalculation: function (input) {
        var tax_amount = 0;
        var hidden_tax_amount = 0;

        switch (IWD.OrderManager.TaxCalculation.getCalculationSequence()) {
            case IWD.OrderManager.TaxCalculation.CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
                tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.subtotal.val(), input.tax_percent.val(), 0);
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal.val());
                break;

            case IWD.OrderManager.TaxCalculation.CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.subtotal_incl_tax.val(), input.tax_percent.val(), 1);
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal_incl_tax.val());
                break;

            case IWD.OrderManager.TaxCalculation.CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal.val());
                if (IWD.OrderManager.TaxCalculation.catalogPrices) {
                    hidden_tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.discount_amount.val(), input.tax_percent.val(), 1);
                    tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.subtotal.val(), input.tax_percent.val(), 0);
                    tax_amount -= hidden_tax_amount;
                } else {
                    tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.subtotal.val() - input.discount_amount.val(), input.tax_percent.val(), 0);
                }
                break;

            case IWD.OrderManager.TaxCalculation.CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal_incl_tax.val());
                if (IWD.OrderManager.TaxCalculation.catalogPrices) {
                    hidden_tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.discount_amount.val(), input.tax_percent.val(), 1);
                    tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.subtotal.val(), input.tax_percent.val(), 0);
                    tax_amount -= hidden_tax_amount;
                } else {
                    tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.subtotal.val() - input.discount_amount.val(), input.tax_percent.val(), 0);
                }
                break;
        }

        input.tax_amount.val(tax_amount.toFixed(2));
        input.hidden_tax_amount.val(hidden_tax_amount.toFixed(2));
    },

    /* 2.3. Method: Total */
    _totalBaseCalculation: function (input) {
        var tax_amount = 0;
        var price = 0;
        var hidden_tax_amount = 0;

        switch (IWD.OrderManager.TaxCalculation.getCalculationSequence()) {
            case IWD.OrderManager.TaxCalculation.CALC_TAX_BEFORE_DISCOUNT_ON_EXCL:
                tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.subtotal.val(), input.tax_percent.val(), 0);
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal.val());
                break;

            case IWD.OrderManager.TaxCalculation.CALC_TAX_BEFORE_DISCOUNT_ON_INCL:
                tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.subtotal_incl_tax.val(), input.tax_percent.val(), 1);
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal_incl_tax.val());
                break;

            case IWD.OrderManager.TaxCalculation.CALC_TAX_AFTER_DISCOUNT_ON_EXCL:
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal.val());
                hidden_tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.discount_amount.val(), input.tax_percent.val(), 0);
                if(IWD.OrderManager.TaxCalculation.catalogPrices) {
                    price = input.subtotal.val() - input.discount_amount.val();
                } else {
                    price = input.subtotal.val() - input.discount_amount.val() - hidden_tax_amount;
                    hidden_tax_amount = 0;
                }
                tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(price, input.tax_percent.val(), 0);
                break;

            case IWD.OrderManager.TaxCalculation.CALC_TAX_AFTER_DISCOUNT_ON_INCL:
                IWD.OrderManager.TaxCalculation._calculateDiscountAmount(input, input.subtotal_incl_tax.val());
                if(IWD.OrderManager.TaxCalculation.catalogPrices) {
                    hidden_tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(input.discount_amount.val(), input.tax_percent.val(), 1);
                    price = input.subtotal.val() - input.discount_amount.val() + hidden_tax_amount;
                } else {
                    hidden_tax_amount = 0;
                    price = input.subtotal.val() - input.discount_amount.val();
                }
                tax_amount = IWD.OrderManager.TaxCalculation._calcTaxAmount(price, input.tax_percent.val(), 0);
                break;
        }

        input.tax_amount.val(tax_amount.toFixed(2));
        input.hidden_tax_amount.val(hidden_tax_amount.toFixed(2));
    },

    _calculateDiscountAmount: function (input, subtotal) {
        var discount_percent = parseFloat(input.discount_percent.val());
        var discount_amount = subtotal * discount_percent / 100;

        input.discount_amount.val(discount_amount.toFixed(2));
        input.discount_percent.val(discount_percent.toFixed(2));
    },
    _calcTaxAmount: function (price, tax_percent, priceIncludeTax) {
        var tax_rate = parseFloat(tax_percent) / 100;
        price = parseFloat(price);

        if (priceIncludeTax) {
            return price * (1 - 1 / (1 + tax_rate));
        } else {
            return price * tax_rate;
        }
    },
    _calculateSubtotal: function (input) {
        var subtotal = parseFloat(input.price.val()) * parseFloat(input.fact_qty.val());
        var subtotal_incl_tax = parseFloat(input.price_incl_tax.val()) * parseFloat(input.fact_qty.val());
        input.subtotal.val(subtotal.toFixed(2));
        input.subtotal_incl_tax.val(subtotal_incl_tax.toFixed(2));
    },
    _calculateRowTotal: function (input) {
        var subtotal = parseFloat(input.subtotal.val());
        var discount_amount = parseFloat(input.discount_amount.val());
        var tax_amount = parseFloat(input.tax_amount.val());
        var hidden_tax_amount = parseFloat(input.hidden_tax_amount.val());
        var weee_tax_applied_row_amount = parseFloat(input.weee_tax_applied_row_amount.val());

        var row_total = subtotal + tax_amount + hidden_tax_amount + weee_tax_applied_row_amount - discount_amount;

        input.row_total.val(row_total.toFixed(2));
        return row_total;
    },
    _calculatePriceExclTax: function (input) {
        var price_excl_tax = parseFloat(input.price.val());
        var tax_percent = parseFloat(input.tax_percent.val());
        var price = price_excl_tax * (1 + tax_percent / 100);

        input.price.val(price_excl_tax.toFixed(2));
        input.price_incl_tax.val(price.toFixed(2));
        input.tax_percent.val(tax_percent.toFixed(2));
    },
    _calculatePriceInclTax: function (input) {
        var price_incl_tax = parseFloat(input.price_incl_tax.val());
        var tax_percent = parseFloat(input.tax_percent.val());

        var price = price_incl_tax / (1 + tax_percent / 100);

        input.price.val(price.toFixed(2));
        input.price_incl_tax.val(price_incl_tax.toFixed(2));
        input.tax_percent.val(tax_percent.toFixed(2));
    },
    _changePrice: function (input) {
        if (IWD.OrderManager.TaxCalculation.catalogPrices) {
            IWD.OrderManager.TaxCalculation._calculatePriceInclTax(input); /* incl tax fixed */
        } else {
            IWD.OrderManager.TaxCalculation._calculatePriceExclTax(input); /* excl tax fixed */
        }
    }
};
