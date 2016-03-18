document.observe("dom:loaded", function() {

    configureSelectOptions();
    configureInputOptions();
});

function configureSelectOptions()
{
    // initially hide all containers for tab content
    $$('select.bundle-option-input').each(function(option) {
        var elementId = $(option).getAttribute('id');
        var idData = elementId.split('-');

        if(optionHasConfigurableSelected(idData[2])) {
            fillFirstAttribute(idData[2]);
        } else {
            removeConfigurableInputs(idData[2]);
        }

        $(option).observe('change', function(event) {
            removeConfigurableInputs(idData[2]);
            fillFirstAttribute(idData[2]);
        });
    });
}

function configureInputOptions()
{
    // initially hide all containers for tab content
    $$('input.bundle-option-input').each(function(option) {
        var elementId = $(option).getAttribute('id');
        var idData = elementId.split('-');

        $(option).observe('change', function(event) {
            removeConfigurableInputs(idData[2]);
            fillFirstAttribute(idData[2]);
        });
    });
}

/**
 * Get the selection Id based on the option
 * @param optionId
 */
function getSelectionId(optionId)
{
    if($$('.bundle-option-' + optionId) == undefined) {
        return false;
    }

    return $$('.bundle-option-' + optionId)[0].value;
}

/**
 * Test if the option has a configurable selected
 *
 * @param optionId
 */
function optionHasConfigurableSelected(optionId)
{
    // If nothing is selected, its most definitely not a configurable
    if(getSelectionId(optionId) == '') {
        return false;
    }

    return getBundleSelection(optionId).confattributes != undefined;
}

function getBundleOption(optionId)
{
    return bundle.config.options[optionId];
}

function getBundleSelection(optionId)
{
    return bundle.config.options[optionId].selections[getSelectionId(optionId)]
}

/**
 * Remove any configurable inputs at the option if the selection is a simple product
 */
function removeConfigurableInputs(optionId)
{
    document.getElementById('bundle-options-' + optionId).innerHTML = "";
}

function setProductInfo(option_id, productId, position)
{
    new Ajax.Request($('baseurl').readAttribute('data-baseurl') + '/index.php/wizbundle/ajax/realproductinfo/product_id/' + productId, {
        method:'get',
        onSuccess: function(transport) {
            if(transport.responseText != '') {
                var data  = JSON.parse(transport.responseText);
            }

            console.log(data);

            $('bundle-option-description-' + option_id).update(data.description);
            $('bundle-option-image-' + option_id).setAttribute('src', data.image);
        },
        onFailure: function() { }
    });
}


/**
 * After we changed the configurable, fill the first attribute
 */
function fillFirstAttribute(option_id)
{
    if(optionHasConfigurableSelected(option_id) == false) {
        return;
    }

    if(getSelectionId(option_id) == false) {
        return;
    }

    setCheckoutDisabled();

    setConfigurableAttributes(option_id);

    setFirstValues(option_id);

    updateConfigurableData(option_id);
}

function updateInformation(option_id, position)
{
    var selection = getBundleSelection(option_id);

    $('bundle-option-name-' + option_id).update(selection.name);

    setProductInfo(option_id, selection.confProductId, position);
}

/**
 * Fill the next attribute of the current option and remove anything left over that should not be there
 * @param element
 * @param current_option_id
 * @param current_attribute_id
 */
function fillNextAttribute(option_id, attribute_id)
{
    if(optionHasConfigurableSelected(option_id) == false) {
        return;
    }

    setCheckoutDisabled();

    new Ajax.Request($('baseurl').readAttribute('data-baseurl') + '/index.php/wizbundle/ajax/nextattributeid/id/' + getSelectionId(option_id) + '/attribute_id/' + attribute_id + '/bundle_id/' + bundle.config.bundleId, {
        method:'get',
        onSuccess: function(transport) {            
            if(transport.responseText != '') {
                var next_attribute_id = transport.responseText;

                new Ajax.Request($('baseurl').readAttribute('data-baseurl') + '/index.php/wizbundle/ajax/singleattribute/id/' + getSelectionId(option_id) + '/option_id/' + option_id + '/attribute_id/' + next_attribute_id + '/bundle_id/' + bundle.config.bundleId, {
                    method: 'POST',
                    postBody: Object.toQueryString({attributes: Object.toJSON(getOptionValues(option_id, attribute_id))}),
                    onSuccess: function(transport) {
                        var items = transport.responseText.evalJSON();

                        $('super-option-' + option_id + '-' + next_attribute_id).descendants().each(Element.remove);
                        $('super-option-' + option_id + '-' + next_attribute_id).insert({
                            bottom: '<option value="0">Select a value...</option>'
                        });

                        $H(items).each(function(item){
                            $('super-option-' + option_id + '-' + next_attribute_id).insert({
                                bottom: '<option value="' + item[1].value_index + '">' + item[1].store_label + '</option>'
                            });
                        })

                        $('super-option-' + option_id + '-' + next_attribute_id).selectedIndex = 0;
                    }
                });
            }else{

                setCheckoutDisabled();
                var custom_options_id = 'custom-option-'+ option_id +'-'+ attribute_id;
                if($(custom_options_id)){
                    $(custom_options_id).replace('');
                }
                new Ajax.Request($('baseurl').readAttribute('data-baseurl') + '/index.php/wizbundle/ajax/productoptions/id/' + getConfigurableId(option_id), {
                method: 'POST',
                postBody: Object.toQueryString({bundle_id: bundle.config.bundleId, option_id: option_id, attributes: Object.toJSON(getOptionValues(option_id, attribute_id))}),
                onSuccess: function(transport) {
                    if(transport.responseText != ''){
                        var selector = '.option-' + option_id + '-attribute-'+ attribute_id;  
                        if(!$(custom_options_id)){
                            $$('dd' + selector)[0].insert({after: '<div id="' + custom_options_id + '">'+transport.responseText + '</div>'});                                               
                        }else{
                            $(custom_options_id).replace(transport.responseText);
                        }
                    }
                    unsetCheckoutDisabled();
                }
            });
            }            
        },
        onFailure: function() { }
    });
}

/*
 * After changing the configurable, update its image
 */
function updateConfigurableData(option_id)
{
    if(getConfigurableId(option_id) == 0) {
        return;
    }

    new Ajax.Request($('baseurl').readAttribute('data-baseurl') + '/index.php/wizbundle/ajax/realproductinfo/product_id/' + getConfigurableId(option_id), {
         method:'get',
         onSuccess: function(transport) {
             if(transport.responseText != '') {
                 var data = JSON.parse(transport.responseText);
             }
        
            if(updateStatus.configurable_image !=  0) {
                $('bundle-option-image-' + option_id).setAttribute('src', data.image);
            }
	    
            unsetCheckoutDisabled();
         },
         onFailure: function() { }
     });
}

/**
 * After a unique simple product has been selected, update its information on the page
 */
function updateProductInfo(product_id, option_id)
{
    if(getConfigurableId(option_id) == 0) {
        return;
    }

    new Ajax.Request($('baseurl').readAttribute('data-baseurl') + '/index.php/wizbundle/ajax/productinfo/selection_id/' + product_id + '/bundle_id/' + bundle.config.bundleId, {
        method:'get',
        onSuccess: function(transport) {
            if(transport.responseText != '') {
                var data = JSON.parse(transport.responseText);
            }
       

            if(updateStatus.image !=  0) {
                $('bundle-option-image-' + option_id).setAttribute('src', data.image);
            }
 

            if(updateStatus.name !=  0) {
                $('bundle-option-name-' + option_id).update(data.name);
            }
 
            if(updateStatus.description !=  0) {
                $('bundle-option-description-' + option_id).update(data.description);
            }

            if(updateStatus.stock !=  0) {
                var stockElement = new Element('p', {'class': 'availability'});
                if(data.stock > 0) {
                    $('oos-warning-' + option_id).addClassName('hidden');
                    stockElement.addClassName('in-stock');
                    stockElement.update('Availability: <span>In Stock</span>');
                } else {
                    $('oos-warning-' + option_id).removeClassName('hidden');
                    stockElement.addClassName('out-of-stock');
                    stockElement.update('Availability: <span>Out of Stock</span>');
                }
                $('bundle-option-stock-' + option_id).update(stockElement);
            }
            
            unsetCheckoutDisabled();
          },
          onFailure: function() { }
    });
}

/**
 * Get the configurable ID if that exists
 */
function getConfigurableId(option_id)
{
    if($('configurables-' + option_id + '-select')) {
        return $('configurables-' + option_id + '-select').getValue();
    }

    return 0;
}

/**
 * Disable the checkout button
 */
function setCheckoutDisabled()
{
    $$('.btn-cart').each(function(item,index){
        $(item).setAttribute('disabled', 'disabled');
	    $(item).addClassName('loading');
    }); 
}

/**
 * Enable the checkout button
 */
function unsetCheckoutDisabled()
{
    $$('.btn-cart').each(function(item,index){
        if($(item).hasAttribute('disabled')) {
            $(item).removeAttribute('disabled');
        }

        if($(item).hasClassName('loading')) {
            $(item).removeClassName('loading');
        }
    }); 
}

/**
 * Load all the attributes that the new configurable we selected has
 */
function setFirstValues(current_option_id)
{
    var selection = getBundleSelection(current_option_id);

    if(selection.confProductId != undefined) {
        var attributeSelection = selection.confProductId + '_' + getSelectionId(current_option_id);
    }

    var attributes = selection.confattributes[attributeSelection].items[0];

    $(attributes.prices).each(function(item) {
        var parentItem = selection.confattributes[attributeSelection].items[0];

        $('super-option-' + current_option_id + '-' + parentItem.attribute_id).insert({
            bottom: '<option value="' + item.value_index + '">' + item.store_label + '</option>'
        });

        $('super-option-' + current_option_id + '-' + parentItem.attribute_id).selectedIndex = 0;

    });
}

/**
 * Load all the attributes that the new configurable we selected has
 */
function setConfigurableAttributes(current_option_id)
{
    var selection = getBundleSelection(current_option_id);

    if(selection.confProductId != undefined) {
        var attributeSelection = selection.confProductId + '_' + getSelectionId(current_option_id);
    }

    $('bundle-options-' + current_option_id).update();

    selection.confattributes[attributeSelection].items.each(function(item) {
        $('bundle-options-' + current_option_id).insert({
            bottom:  '<div class="option-data">' +
                        '<div class="option-label">' +
                            item.label +
                         '</div>'+
                        '<div class="option-select">' +
                            '<select name="super_attribute[' + current_option_id + '][' + item.attribute_id + ']" id="super-option-' + current_option_id + '-' + item.attribute_id + '" onchange="fillNextAttribute(' + current_option_id + ',' + item.attribute_id + ')" class="change-container-classname required-entry">' +
                                '<option value="0">Select a value...</option>' +
                            '</select>' +
                        '</div>' +
                    '</div>'
        });
    });
}

/**
 * Get all the option values available in the selector
 *
 * @param option_id
 * @returns {{}}
 */
function getOptionValues(option_id, attribute_id)
{
    var selector = $('bundle-options-' + option_id).select('select');

    var values = {};

    $H(selector).each(function(item, index) {
        // If the first key is a numeric value
        if($(item)[0].match(/^\d+$/) != null) {
            var select = $(item)[1];

            var current_attribute_id = resolveAttributeId($(select).getAttribute('id'), option_id);

            if(typeof values[current_attribute_id] == 'undefined') {
                values[current_attribute_id] = [];
            }

            values[current_attribute_id].push($(select).getValue());

            // Dont continue, we break since we just changed this one
            if(current_attribute_id == attribute_id) {
                throw $break;
            }
        }

    });

    return values;
}

/**
 * Resolve an attribute ID from a select element
 */
function resolveAttributeId(name, option_id)
{
    // Strip all in front of the attribute_id
    var attribute_id = name.replace("super-option-" + option_id + "-", "");

    // Strip all after, so we only keep the attribute itd itself
    return attribute_id;

}

/**
 * this supports trigger native events such as 'onchange' 
 * whereas prototype.js Event.fire only supports custom events
 */
function triggerEvent(element, eventName) {
    // safari, webkit, gecko
    if (document.createEvent)
    {
        var evt = document.createEvent('HTMLEvents');
        evt.initEvent(eventName, true, true);
 
        return element.dispatchEvent(evt);
    }
 
    // Internet Explorer
    if (element.fireEvent) {
        return element.fireEvent('on' + eventName);
    }
}
