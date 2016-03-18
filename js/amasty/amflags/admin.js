/**
* @author Amasty Team
* @copyright Copyright (c) 2010-2012 Amasty (http://www.amasty.com)
* @package Amasty_Flags
*/

// overriding this function to handle row clicks on flag element
varienGridMassaction.prototype.onGridRowClick = function(grid, evt) 
{
    var tdElement = Event.findElement(evt, 'td');
    var trElement = Event.findElement(evt, 'tr');

    if(!$(tdElement).down('input')) {
        /* amasty: add div exclude */
        if($(tdElement).down('a') || $(tdElement).down('select') || $(tdElement).down('div')) {
            return;
        }
        if (trElement.title) {
            setLocation(trElement.title);
        }
        else{
            var checkbox = Element.select(trElement, 'input');
            var isInput  = Event.element(evt).tagName == 'input';
            var checked = isInput ? checkbox[0].checked : !checkbox[0].checked;

            if(checked) {
                this.checkedString = varienStringArray.add(checkbox[0].value, this.checkedString);
            } else {
                this.checkedString = varienStringArray.remove(checkbox[0].value, this.checkedString);
            }
            this.grid.setCheckboxChecked(checkbox[0], checked);
            this.updateCount();
        }
        return;
    }

    if(Event.element(evt).isMassactionCheckbox) {
       if (!$(tdElement).down('div')) // condition added
       {
           this.setCheckbox(Event.element(evt));
       }
    } else if (checkbox = this.findCheckbox(evt)) {
       if (!$(tdElement).down('div')) // condition added
       {
           checkbox.checked = !checkbox.checked;
           this.setCheckbox(checkbox);
       }
    }
};

function openFlagDialog(orderId, incrementId, columnId, columnAlias)
{
    
    flagDialog = Dialog.info($('flaglist-' + orderId + '-column-' + columnId).innerHTML, {
        draggable:true,
        resizable:false,
        closable:true,
        className:"magento",
        windowClassName:"popup-window",
        title:'Flag For Order #' + incrementId + ' (Column: ' + columnAlias + ')',
        width:350,
        //height:270,
        zIndex:1000,
        recenterAuto:false,
        hideEffect:Element.hide,
        showEffect:Element.show,
        id:'dialog-' + orderId + '-' + columnId,
    //    onClose: this.closeDialogWindow.bind(this)
    });
}

function setOrderFlag(setFlagUrl, orderId, flagId, columnId, emptyUrl)
{
    if (flagId)
    {
        // setting order flag
        comment = $('flagselect-comment-' + orderId + '-column-' + columnId + '-' + flagId).value;
        $('flagimg-' + orderId + '-column-' + columnId).src   = $('flagselect-img-' + orderId + '-column-' + columnId + '-' + flagId).src;
        $('flagimg-' + orderId + '-column-' + columnId).alt   = comment;
        $('flagimg-' + orderId + '-column-' + columnId).title = comment;
        $('orderflag-' + orderId + '-column-' + columnId).value = flagId;
        flagDialog.close();
        
        // saving flag id to server
        postData = 'form_key=' + FORM_KEY + '&orderId=' + orderId + '&flagId=' + flagId + '&columnId='+ columnId + '&comment=' + comment;
        new Ajax.Request(setFlagUrl, 
        {
            method: 'post',
            postBody : postData,
            onSuccess: function(transport) 
            {
                
            }
        });
    } else 
    {
        $('flagimg-' + orderId + '-column-' + columnId).src = emptyUrl;
        $('flagimg-' + orderId + '-column-' + columnId).alt   = 'No Flag';
        $('flagimg-' + orderId + '-column-' + columnId).title = 'No Flag';
        // removing flag for the order
        flagDialog.close();
        
        postData = 'form_key=' + FORM_KEY + '&orderId=' + orderId + '&flagId=0' + '&columnId=' + columnId;
        new Ajax.Request(setFlagUrl, 
        {
            method: 'post',
            postBody : postData,
            onSuccess: function(transport) 
            {
                
            }
        });
    }
}