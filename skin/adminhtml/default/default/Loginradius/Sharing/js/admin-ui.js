// get trim() worked in IE 
if (typeof String.prototype.trim !== 'function') {
    String.prototype.trim = function () {
        return this.replace(/^\s+|\s+$/g, '');
    }
}
var $loginRadiusSharingJquery = jQuery.noConflict();
// prepare admin UI on window load
function loginRadiusSharingPrepareAdminUI() {

    var horizontalSharingTheme, verticalSharingTheme;
    // fetch horizontal and vertical sharing providers dynamically from LoginRadius on window load
    var sharingType = ['horizontal', 'vertical'];
    var sharingModes = ['Sharing', 'Counter'];
    // show the sharing/counter providers according to the selected sharing theme
    for (var j = 0; j < sharingType.length; j++) {
        var loginRadiusHorizontalSharingThemes = document.getElementById('row_sharing_options_' + sharingType[j] + 'Sharing_' + sharingType[j] + 'SharingTheme').getElementsByTagName('input');
        for (var i = 0; i < loginRadiusHorizontalSharingThemes.length; i++) {
            if (sharingType[j] == 'horizontal') {
                loginRadiusHorizontalSharingThemes[i].onclick = function () {
                    loginRadiusToggleSharingProviders(this, 'horizontal');
                }
            } else if (sharingType[j] == 'vertical') {
                loginRadiusHorizontalSharingThemes[i].onclick = function () {
                    loginRadiusToggleSharingProviders(this, 'vertical');
                }
            }
            if (loginRadiusHorizontalSharingThemes[i].checked == true) {
                if (sharingType[j] == 'horizontal') {
                    horizontalSharingTheme = loginRadiusHorizontalSharingThemes[i].value;
                } else if (sharingType[j] == 'vertical') {
                    verticalSharingTheme = loginRadiusHorizontalSharingThemes[i].value;
                }
                loginRadiusToggleSharingProviders(loginRadiusHorizontalSharingThemes[i], sharingType[j]);
            }
        }
    }
    // set left margin for first radio button in Horizontal counter
    document.getElementById('sharing_options_horizontalSharing_horizontalSharingTheme32').style.marginLeft = '6px';
    // if selected sharing theme is worth showing rearrange icons, then show rearrange icons and manage sharing providers in hidden field
    for (var j = 0; j < sharingType.length; j++) {
        for (var jj = 0; jj < sharingModes.length; jj++) {
            // get sharing providers table-row reference
            var loginRadiusHorizontalSharingProvidersRow = document.getElementById('row_sharing_options_' + sharingType[j] + 'Sharing_' + sharingType[j] + sharingModes[jj] + 'Providers');
            // get sharing providers checkboxes reference
            var loginRadiusHorizontalSharingProviders = loginRadiusHorizontalSharingProvidersRow.getElementsByTagName('input');
            for (var i = 0; i < loginRadiusHorizontalSharingProviders.length; i++) {
                if (sharingType[j] == 'horizontal') {
                    if (sharingModes[jj] == 'Sharing') {
                        loginRadiusHorizontalSharingProviders[i].onclick = function () {
                            loginRadiusSharingShowIcon(false, this, 'horizontal');
                        }
                    } else {
                        loginRadiusHorizontalSharingProviders[i].onclick = function () {
                            loginRadiusSharingPopulateCounter(this, 'horizontal');
                        }
                    }
                } else if (sharingType[j] == 'vertical') {
                    if (sharingModes[jj] == 'Sharing') {
                        loginRadiusHorizontalSharingProviders[i].onclick = function () {
                            loginRadiusSharingShowIcon(false, this, 'vertical');
                        }
                    } else {
                        loginRadiusHorizontalSharingProviders[i].onclick = function () {
                            loginRadiusSharingPopulateCounter(this, 'vertical');
                        }
                    }
                }
            }

            // check the sharing providers that were saved previously in the hidden field
            var loginRadiusSharingProvidersHidden = document.getElementById('sharing_options_' + sharingType[j] + 'Sharing_' + sharingType[j] + sharingModes[jj] + 'ProvidersHidden').value.trim();
            if (loginRadiusSharingProvidersHidden != "") {
                var loginRadiusSharingProviderArray = loginRadiusSharingProvidersHidden.split(',');
                if (sharingModes[jj] == 'Sharing') {
                    for (var i = 0; i < loginRadiusSharingProviderArray.length; i++) {
                        if (document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i])) {
                            document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i]).checked = true;
                            loginRadiusSharingShowIcon(true, document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i]), sharingType[j]);
                        }
                    }
                } else {
                    for (var i = 0; i < loginRadiusSharingProviderArray.length; i++) {
                        if (document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i])) {
                            document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i]).checked = true;
                        }
                    }
                }
            } else {
                if (sharingModes[jj] == 'Sharing') {
                    var loginRadiusSharingProviderArray = ["Facebook", "GooglePlus", "Twitter", "Pinterest", "Email", "Print"];
                    for (var i = 0; i < loginRadiusSharingProviderArray.length; i++) {
                        if (document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i])) {
                            document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i]).checked = true;
                            loginRadiusSharingShowIcon(true, document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i]), sharingType[j], true);
                        }
                    }
                } else {
                    var loginRadiusSharingProviderArray = ["Facebook Like", "Google+ +1", "Twitter Tweet", "Pinterest Pin it", "Hybridshare"];
                    for (var i = 0; i < loginRadiusSharingProviderArray.length; i++) {
                        if (document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i])) {
                            document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i]).checked = true;
                            loginRadiusSharingPopulateCounter(document.getElementById(sharingType[j] + "_" + sharingModes[jj] + "_" + loginRadiusSharingProviderArray[i]), sharingType[j]);
                        }
                    }
                }
            }
        }
    }
}
// limit maximum number of providers selected in sharing
function loginRadiusSharingSharingLimit(elem, sharingType) {
    var checkCount = 0;
    // get providers table-row reference
    var loginRadiusSharingProvidersRow = document.getElementById('row_sharing_options_' + sharingType + 'Sharing_' + sharingType + 'SharingProviders');
    // get sharing providers checkboxes reference
    var loginRadiusSharingProviders = loginRadiusSharingProvidersRow.getElementsByTagName('input');
    for (var i = 0; i < loginRadiusSharingProviders.length; i++) {
        if (loginRadiusSharingProviders[i].checked) {
            // count checked providers
            checkCount++;
            if (checkCount >= 10) {
                elem.checked = false;
                if (document.getElementById('loginRadius' + sharingType + 'ErrorDiv') == null) {
                    // create and show div having error message
                    var errorDiv = document.createElement('div');
                    errorDiv.setAttribute('id', 'loginRadius' + sharingType + 'ErrorDiv');
                    errorDiv.innerHTML = "You can select only 9 providers.";
                    errorDiv.style.color = 'red';
                    errorDiv.style.marginBottom = '10px';
                    // append div to the <td> containing sharing provider checkboxes
                    var rearrangeTd = loginRadiusSharingProvidersRow.getElementsByTagName('td');
                    $loginRadiusSharingJquery(rearrangeTd[1]).find('ul').before(errorDiv);
                }
                return;
            }
        }
    }
}
// add/remove icons from counter hidden field
function loginRadiusSharingPopulateCounter(elem, sharingType, lrDefault) {
    // get providers hidden field value
    var providers = document.getElementById('sharing_options_' + sharingType + 'Sharing_' + sharingType + 'CounterProvidersHidden');
    if (elem.value != 1) {
        if (elem.checked) {
            // add selected providers in the hiddem field value
            if (typeof elem.checked != "undefined" || lrDefault == true) {
                if (providers.value == "") {
                    providers.value = elem.value;
                } else {
                    providers.value += "," + elem.value;
                }
            }
        } else {
            if (providers.value.indexOf(',') == -1) {
                providers.value = providers.value.replace(elem.value, "");
            } else {
                if (providers.value.indexOf("," + elem.value) == -1) {
                    providers.value = providers.value.replace(elem.value + ",", "");
                } else {
                    providers.value = providers.value.replace("," + elem.value, "");
                }
            }
        }
    }
}
// show selected providers in rearrange option
function loginRadiusSharingShowIcon(pageRefresh, elem, sharingType, lrDefault) {
    loginRadiusSharingSharingLimit(elem, sharingType);
    // get providers hidden field value
    var providers = document.getElementById('sharing_options_' + sharingType + 'Sharing_' + sharingType + 'SharingProvidersHidden');
    if (elem.value != 1) {
        if (elem.checked) {
            // get reference to "rearrange providers" <ul> element
            var ul = document.getElementById('loginRadius' + sharingType + 'RearrangeSharing');
            // if <ul> is not already created
            if (ul == null) {
                // create <ul> element
                var ul = document.createElement('ul');
                ul.setAttribute('id', 'loginRadius' + sharingType + 'RearrangeSharing');
                $loginRadiusSharingJquery(ul).sortable({
                    update: function (e, ui) {
                        var val = $loginRadiusSharingJquery(this).children().map(function () {
                            return $loginRadiusSharingJquery(this).attr('title');
                        }).get().join();
                        $loginRadiusSharingJquery(providers).val(val);
                    },
                    revert: true});
            }
            // create list items
            var listItem = document.createElement('li');
            listItem.setAttribute('id', 'loginRadius' + sharingType + 'LI' + elem.value);
            listItem.setAttribute('title', elem.value);
            listItem.setAttribute('class', 'lrshare_iconsprite32 lrshare_' + elem.value.toLowerCase());
            ul.appendChild(listItem);
            // add selected providers in the hiddem field value
            if (!pageRefresh || lrDefault == true) {
                if (providers.value == "") {
                    providers.value = elem.value;
                } else {
                    providers.value += "," + elem.value;
                }
            }
            // append <ul> to the <td>
            var rearrangeRow = document.getElementById('row_sharing_options_' + sharingType + 'Sharing_' + sharingType + 'SharingProvidersHidden');
            var rearrangeTd = rearrangeRow.getElementsByTagName('td');
            rearrangeTd[1].appendChild(ul);
        } else {
            var remove = document.getElementById('loginRadius' + sharingType + 'LI' + elem.value);
            if (remove) {
                remove.parentNode.removeChild(remove);
            }
            if (providers.value.indexOf(',') == -1) {
                providers.value = providers.value.replace(elem.value, "");
            } else {
                if (providers.value.indexOf("," + elem.value) == -1) {
                    providers.value = providers.value.replace(elem.value + ",", "");
                } else {
                    providers.value = providers.value.replace("," + elem.value, "");
                }
            }
        }
    }
}

function loginradiusChangeInheritCheckbox(shareId1, shareId2) {
    if ($loginRadiusSharingJquery("#sharing_options_" + shareId1 + "_" + shareId2 + "Providers_inherit").is(':checked')) {
        $loginRadiusSharingJquery("#sharing_options_" + shareId1 + "_" + shareId2 + "ProvidersHidden_inherit").attr('checked', true);
        $loginRadiusSharingJquery("#sharing_options_" + shareId1 + "_" + shareId2 + "ProvidersHidden").attr("disabled", true);
    } else {
        $loginRadiusSharingJquery("#sharing_options_" + shareId1 + "_" + shareId2 + "ProvidersHidden_inherit").attr('checked', false);
        $loginRadiusSharingJquery("#sharing_options_" + shareId1 + "_" + shareId2 + "ProvidersHidden").attr("disabled", false);
    }
}

function loginradiusChangeInheritCheckboxHidden(shareId1, shareId2) {
    if ($loginRadiusSharingJquery("#sharing_options_" + shareId1 + "_" + shareId2 + "ProvidersHidden_inherit").is(':checked')) {
        $loginRadiusSharingJquery("#sharing_options_" + shareId1 + "_" + shareId2 + "Providers_inherit").attr('checked', true);
        $loginRadiusSharingJquery("#sharing_options_" + shareId1 + "_" + shareId2 + "ProvidersHidden").attr("disabled", true);
    } else {
        $loginRadiusSharingJquery("#sharing_options_" + shareId1 + "_" + shareId2 + "Providers_inherit").attr('checked', false);
        $loginRadiusSharingJquery("#sharing_options_" + shareId1 + "_" + shareId2 + "ProvidersHidden").attr("disabled", false);
    }
}

$loginRadiusSharingJquery(document).ready(function () {
    loginradiusChangeInheritCheckboxHidden('horizontalSharing', 'horizontalCounter');
    loginradiusChangeInheritCheckboxHidden('verticalSharing', 'verticalCounter');
    loginradiusChangeInheritCheckboxHidden('horizontalSharing', 'horizontalSharing');
    loginradiusChangeInheritCheckboxHidden('verticalSharing', 'verticalSharing');
    $loginRadiusSharingJquery("#sharing_options_horizontalSharing_horizontalCounterProviders_inherit").click(function () {
        loginradiusChangeInheritCheckbox('horizontalSharing', 'horizontalCounter');
    });
    $loginRadiusSharingJquery("#sharing_options_verticalSharing_verticalCounterProviders_inherit").click(function () {
        loginradiusChangeInheritCheckbox('verticalSharing', 'verticalCounter');
    });
    $loginRadiusSharingJquery("#sharing_options_horizontalSharing_horizontalSharingProviders_inherit").click(function () {
        loginradiusChangeInheritCheckbox('horizontalSharing', 'horizontalSharing');
    });
    $loginRadiusSharingJquery("#sharing_options_verticalSharing_verticalSharingProviders_inherit").click(function () {
        loginradiusChangeInheritCheckbox('verticalSharing', 'verticalSharing');
    });
    $loginRadiusSharingJquery("#sharing_options_horizontalSharing_horizontalSharingProvidersHidden_inherit").click(function () {
        loginradiusChangeInheritCheckboxHidden('horizontalSharing', 'horizontalSharing');
    });
    $loginRadiusSharingJquery("#sharing_options_verticalSharing_verticalSharingProvidersHidden_inherit").click(function () {
        loginradiusChangeInheritCheckboxHidden('verticalSharing', 'verticalSharing');
    });
});