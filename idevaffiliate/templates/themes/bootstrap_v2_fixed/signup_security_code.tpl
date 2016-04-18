 <legend style="color:{$legend};">{$signup_security_title}</legend>   
 <span class="help-block">{$signup_security_info}</span>  
 <div class="control-group">
    <label for="{$signup_security_code}" class="control-label">{$signup_security_code}</label>
    <div class="controls">      
      <input class="input-xlarge span4" id="security_code" name="security_code" type="text" />
    </div>
    <div class="controls" style="margin-top:20px;">      
      {$captcha_image}
    </div>
  </div>