var keyStr = "ABCDEFGHIJKLMNOP" +
	               "QRSTUVWXYZabcdef" +
	               "ghijklmnopqrstuv" +
	               "wxyz0123456789+/" +
	               "=";
/*
    @Description: Function to get confirmation for deleting a record
    @Author: Hima
    @Input: - divid, module name, url
    @Output: - confirmation in popup box
    @Date: 10-05-2013
*/
function deletepopup(id,name,url)
{      

     	  $.confirm({
'title': 'CONFIRM','message': " <strong> Are you sure you want to delete "+"'"+id+"'<strong>?</strong>",'buttons': {'Yes': {'class': 'special',
'action': function(){
                                 window.location= url;
								 }},'No'	: {'class'	: ''}}});

     
}

/*
    @Description: Function equivalent to base64_encode in PHP
    @Author: Kashyap Padh
    @Input: string which needs to be encoded
    @Output: encoded value
    @Date: 17-05-2013
*/
function encode64(input) 
{
    input = escape(input);
    var output = "";
    var chr1, chr2, chr3 = "";
    var enc1, enc2, enc3, enc4 = "";
    var i = 0;

    do {
       chr1 = input.charCodeAt(i++);
       chr2 = input.charCodeAt(i++);
       chr3 = input.charCodeAt(i++);

       enc1 = chr1 >> 2;
       enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
       enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
       enc4 = chr3 & 63;

       if (isNaN(chr2)) {
          enc3 = enc4 = 64;
       } else if (isNaN(chr3)) {
          enc4 = 64;
       }

       output = output +
          keyStr.charAt(enc1) +
          keyStr.charAt(enc2) +
          keyStr.charAt(enc3) +
          keyStr.charAt(enc4);
       chr1 = chr2 = chr3 = "";
       enc1 = enc2 = enc3 = enc4 = "";
    } while (i < input.length);

    return output;
}

/*
    @Description: Function equivalent to base64_decode in PHP
    @Author: Kashyap Padh
    @Input: base64 encoded value
    @Output: decoded value
    @Date: 17-05-2013
*/
    
function decode64(input) 
{
    var output = "";
    var chr1, chr2, chr3 = "";
    var enc1, enc2, enc3, enc4 = "";
    var i = 0;

    // remove all characters that are not A-Z, a-z, 0-9, +, /, or =
    var base64test = /[^A-Za-z0-9\+\/\=]/g;
    if (base64test.exec(input)) {
       alert("There were invalid base64 characters in the input text.\n" +
             "Valid base64 characters are A-Z, a-z, 0-9, '+', '/',and '='\n" +
             "Expect errors in decoding.");
    }
    input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

    do {
       enc1 = keyStr.indexOf(input.charAt(i++));
       enc2 = keyStr.indexOf(input.charAt(i++));
       enc3 = keyStr.indexOf(input.charAt(i++));
       enc4 = keyStr.indexOf(input.charAt(i++));

       chr1 = (enc1 << 2) | (enc2 >> 4);
       chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
       chr3 = ((enc3 & 3) << 6) | enc4;

       output = output + String.fromCharCode(chr1);

       if (enc3 != 64) {
          output = output + String.fromCharCode(chr2);
       }
    if (enc4 != 64) {
          output = output + String.fromCharCode(chr3);
       }

       chr1 = chr2 = chr3 = "";
       enc1 = enc2 = enc3 = enc4 = "";

    } while (i < input.length);

    return unescape(output);
}