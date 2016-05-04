Payment.prototype.init = function () {
    this.beforeInit();
    try{
        var elements = Form.getElements(this.form);
        if ($(this.form)) {
            $(this.form).observe('submit', function(event){this.save();Event.stop(event);}.bind(this));
        }
        var method = null;
        /*
         for (var i=0; i<elements.length; i++) {
         if (elements[i].name=='payment[method]') {
         if (elements[i].checked) {
         method = elements[i].value;
         }
         } else {
         elements[i].disabled = true;
         }
         elements[i].setAttribute('autocomplete','off');
         }
         */
        if (method) this.switchMethod(method);
    }catch (e){

    }

    this.afterInit();
}