<?php

// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

return (function () {
    $login_url = json_ns(
        add_query_args(
            ['interim' => 1],
            is_admin_page() ? get_admin_login_url() : get_login_url()
        )
    );
    $checkSucceedSecond = 10000;
    $checkFailSecond = 5000;
    $ping_url = json_ns(get_api_url('/ping'));
    $reconnect_text = json_ns(trans('Reconnecting...'));
    $offline_text = json_ns(trans('You seem to be offline'));
    return <<<JS
    (function (jq) {
        if (!jq) {
            return;
        }
        var current_href = window.location.href.replace(/\#.*/g, ''),
            stop_first = false,
            api_ping = {$ping_url},
            checkFail = {$checkFailSecond},
            checkSucceed = {$checkSucceedSecond},
            interimIframe = null,
            interimLayout = null,
            loginUrl = {$login_url},
            offlineText = {$offline_text},
            connectingText = {$reconnect_text},
            _global_message = jq('#global-message'),
            _body = jq('body'),
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
                    return;
                }

                if (interimIframe) {
                    setTimeout(account_loop_check, checkFail);
                    return;
                }
                
                var log = loginUrl.replace(/\?.+/, '');
                interimLayout = jq('#interim-login');
                if (interimLayout.length) {
                    interimLayout.html('');
                } else {
                    interimLayout = jq('<div id="interim-login"></div>');
                }

                interimIframe = jq('<iframe id="iframe-interim-login" class="iframe-interim" src="' + log + '?interim=1"></iframe>');
                interimLayout.html(interimIframe);
                _body.append(interimLayout);
                var call_iframe_stat = function (href, close) {
                    if (!interimIframe) {
                        return;
                    }
                    if (close === true) {
                        interimLayout.remove();
                        interimIframe.remove(function () {
                           interimIframe = null; 
                        });
                        setTimeout(account_loop_check, checkSucceed);
                        return;
                    }
                    if (/\?login=success(?:&.*|$)/.test(href)) {
                        if (typeof user_id === "number") {
                            var _match = href.match(/user_id=([0-9]+)(?:&|$)/);
                            var id = parseInt(_match[1]);
                            if (id !== user_id) {
                                window.location.reload();
                                return;
                            }
                        }
                        interimLayout.remove();
                        interimIframe.remove(function () {
                           interimIframe = null; 
                        });
                        setTimeout(account_loop_check, checkSucceed);
                    }
                }
                interimIframe[0].contentWindow.call_iframe_stat = call_iframe_stat;
                interimIframe.on('load', function () {
                    try {
                        var href = this.contentWindow.location.href;
                        if (!href) {
                            return;
                        }
                    } catch (e) {
                        return;
                    }
                    call_iframe_stat(href);
                });
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
                if (stop_first) {
                    setTimeout(account_loop_check, checkSucceed);
                    return;
                }
                jq.get(api_ping, {}, loop_back_succeed).fail(loop_back_fail);
            };
        window.location.onchange = function () {
            var href = window.location.href.replace(/\#.*/g, '');
            stop_first = current_href !== href;
        };
        jq(document).ready(function () {
            setTimeout(account_loop_check, checkSucceed);
        });
    })(window.jQuery);

JS;
})();
