const wef_comment_callback = function (response) {
	var wef_ajax = new XMLHttpRequest();
	var data = wef_serialize({action: 'wpemfb_comments', nonce: WEF.comments_nonce, response: response});

	wef_ajax.open("POST", WEF.ajaxurl, true);
	wef_ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	wef_ajax.send(data);
};
window.fbAsyncInit = () => {
	FB.init(
			{
				appId: WEF.fb_id,
				version: WEF.version,
				xfbml: true
			}
	);
	if (!(typeof WEF.ajaxurl === "undefined")) {
		FB.Event.subscribe('comment.create', wef_comment_callback);
		FB.Event.subscribe('comment.remove', wef_comment_callback);
	}

};

(function (d, s, id) {
	let js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) {
		return;
	}
	js = d.createElement(s);
	js.id = id;
	// js.async = true;
	js.src = "//connect.facebook.net/" + WEF.local + "/sdk.js";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

const wef_serialize = (obj, prefix) => {
	let str = [], p;
	for (p in obj) {
		if (obj.hasOwnProperty(p)) {
			const k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
			str.push(
					(v !== null && typeof v === "object") ?
							wef_serialize(v, k) :
							encodeURIComponent(k) + "=" + encodeURIComponent(v)
			);
		}
	}
	return str.join("&");
};


if (WEF.hasOwnProperty('adaptive')) {
	(function ($) {
		$(".wef-measure").each(
				function () {
					$(this).next().attr("data-width", $(this).outerWidth() + "px")
				}
		)
	})(jQuery);
}
