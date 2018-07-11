window.fbAsyncInit = function () {
    FB.init({
        appId: WEF.fb_id,
        version: WEF.version,
        xfbml: true
    });
    if (!(typeof WEF.ajaxurl === "undefined")) {
        FB.Event.subscribe('comment.create', wef_comment_callback);
        FB.Event.subscribe('comment.remove', wef_comment_callback);
    }

};

(function (d, s, id) {
    let js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    // js.async = true;
    js.src = "//connect.facebook.net/" + WEF.local + "/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

const wef_serialize = function (obj, prefix) {
    let str = [], p;
    for (p in obj) {
        if (obj.hasOwnProperty(p)) {
            const k = prefix ? prefix + "[" + p + "]" : p, v = obj[p];
            str.push((v !== null && typeof v === "object") ?
                wef_serialize(v, k) :
                encodeURIComponent(k) + "=" + encodeURIComponent(v));
        }
    }
    return str.join("&");
};

const wef_comment_callback = function (response) {

    // console.log(response);

    const wef_ajax = new XMLHttpRequest();

    // wef_ajax.onreadystatechange = function()
    // {
    //     if(wef_ajax.readyState === 4 && wef_ajax.status === 200)
    //     {
    //         alert(wef_ajax.responseText);
    //     }
    // };

    const data = wef_serialize({action: 'wpemfb_comments', response: response});

    wef_ajax.open("POST", WEF.ajaxurl, true);
    wef_ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    wef_ajax.send(data);
};

if (WEF.hasOwnProperty('adaptive')) {
    (function ($) {
        $(".wef-measure").each(function () {
            $(this).next().attr("data-width", $(this).outerWidth() + "px")
        })
    })(jQuery);
}

