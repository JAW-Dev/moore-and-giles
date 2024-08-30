jQuery(document).ready(function() {
	jQuery(".modaal-iframe").modaal({
		type: 'iframe',
		width: '70%',
		height: Math.max(document.documentElement.clientHeight, window.innerHeight || 0) * 0.9
	});

	if ( jQuery("#bulk-print").length ) {
		var iframe = jQuery('#bulk-print')[0];
		iframe.contentWindow.focus();
		iframe.contentWindow.print();

		var redirect = removeURLParameter(window.location.href, 'bulk-print');
        redirect = removeURLParameter(redirect, 'bulk-pick-list-print');
		redirect = removeURLParameter(redirect, 'selected');

		window.location = redirect;
	}
});

function removeURLParameter(url, parameter) {
    //prefer to use l.search if you have a location/link object
    var urlparts= url.split('?');
    if (urlparts.length>=2) {

        var prefix= encodeURIComponent(parameter)+'=';
        var pars= urlparts[1].split(/[&;]/g);

        //reverse iteration as may be destructive
        for (var i= pars.length; i-- > 0;) {
            //idiom for string.startsWith
            if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                pars.splice(i, 1);
            }
        }

        url= urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : "");
        return url;
    } else {
        return url;
    }
}
