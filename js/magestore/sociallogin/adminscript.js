    document.observe("dom:loaded", function() {
        if($('sociallogin_general-head')){
            document.observe('click', function(event) {
                var element = Event.element(event);
                if(element.className == 'link-store-scope'){
                    $$('.login_api_demo_img').each(function(el){
                            el.hide();
                    });
                    element.nextSibling.show();
                }else if(element.className != 'login_api_demo_img'){
                    $$('.login_api_demo_img').each(function(el){
                            el.hide();
                    });
                }
            });
        }
    });
    function addNewImageElement(id,src){
            if($(id))
        $(id).outerHTML += '<div class="link-store-scope" style="margin-top: 3px;margin-left: 3px;cursor: pointer;"></div><img class="login_api_demo_img" src="'+src+'" onclick="return showPopupImg(this.src);" style="cursor: pointer; position: absolute;width: 500px;display: none;height: 200px;border: 2px solid #6f8992;">';
    }
	function showPopupImg(src){
		$('white_content').src = src;	
		$('white_content').style.display = "block";
		$('black_overlay').style.display = "block";
	}
	function hidePopupImg(){
		$('white_content').style.display = "none";
		$('black_overlay').style.display = "none";
	}



