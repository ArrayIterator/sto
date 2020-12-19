<?php
return (function () {
    $login_url = is_admin_page() ? get_admin_login_url() : get_login_url();
    $ping_url = json_encode(get_api_url('/ping'), JSON_UNESCAPED_SLASHES);
    $reconnect_text = json_encode(trans('Reconnecting...'));
    $offline_text = json_encode(trans('You seem to be offline'));
    return <<<JS
    (function (jq) {
        if (!jq) {
            return;
        }
        var api_ping = {$ping_url},
            checkFail = 3000,
            checkSucceed = 5000,
            interimIframe,
            interimLayout,
            loginUrl = {$login_url},
            offlineText = {$offline_text},
            connectingText = {$reconnect_text},
            _global_message = jq('#global-message'),
            _page = jq('#page'),
            loop_back_succeed = function (e) {
                var notLogged = e.data && (e.data['login'] === false || !e.data['as'] || typeof e.data['as']['supervisor'] === 'undefined');
                if (!notLogged) {
                    if (user_id && e.data['as']['supervisor'].id && e.data['as']['supervisor'].id !== user_id) {
                        window.location.reload();
                        return;
                    }
                    if (interimLayout) {
                        interimLayout.remove();
                        interimLayout = null;
                    }
                    if (interimIframe) {
                        interimIframe.remove();
                        interimIframe = null;
                    }

                    _global_message.html('');
                    setTimeout(account_loop_check, checkFail);
                }
                if (interimIframe) {
                    setTimeout(account_loop_check, checkFail);
                    return;
                }
                if (!interimIframe) {
                    var log = loginUrl.replace(/\?.+/, '');
                    interimLayout = jq('#interim-login');
                    if (interimLayout.length) {
                        interimLayout.html('');
                    } else {
                        interimLayout = jq('<div id="interim-login"></div>');
                    }

                    interimIframe = jq('<iframe id="iframe-interim-login" class="iframe-interim" src="' + log + '?interim=1"></iframe>');
                    interimLayout.html(interimIframe);
                    _page.append(interimLayout);
                    interimIframe.on('load', function () {
                        try {
                            var href = this.contentWindow.location.href;
                            if (!href) {
                                return;
                            }
                        } catch (e) {
                            return;
                        }

                        if (href.match(/\?login=success(?:&.*|$)/)) {
                            interimIframe = null;
                            if (typeof user_id === "number") {
                                var _match = href.match(/user_id=([0-9]+)(?:&|$)/);
                                var id = parseInt(_match[1]);
                                if (id !== user_id) {
                                    window.location.reload();
                                    return;
                                }
                            }

                            interimLayout.remove();
                            interimIframe.remove();
                            setTimeout(account_loop_check, checkSucceed);
                        }
                    });
                }
                setTimeout(account_loop_check, checkFail);
            },
            loop_back_fail = function (e) {
                if (e.status === 0) {
                    _global_message.html(offlineText);
                    setTimeout(function () {
                        _global_message.html(connectingText);
                        account_loop_check();
                    }, checkFail);
                    return;
                }
                setTimeout(account_loop_check, checkFail);
            },
            account_loop_check = function () {
                jq.get(ping_url, {}, loop_back_succeed).fail(loop_back_fail);
            };

        jq(document).ready(function () {
            setTimeout(account_loop_check, checkSucceed);
        });
    })(window.jQuery);
JS;
})();
