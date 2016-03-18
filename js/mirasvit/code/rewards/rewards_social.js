RewardsGoogleplusOne = Class.create();
RewardsGoogleplusOne.prototype = {
  initialize : function(url) {
    this.url = url;
    return this;
  },
  onPlus : function() {
    new Ajax.Request(this.url, {
      method : "get",
      parameters : {},
      onSuccess : this.onSuccess.bind(this),
    });
    return this;
  },
  onUnPlus : function() {
    return this;
  },
  onSuccess : function(response) {
    $('status-message').update(response.responseText);
    $('googleplus-message').update('');
    return this;
  }
};


