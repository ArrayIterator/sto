(function ($) {
    if (!$) {
        return;
    }
    var currentHref = window.location.href;
    var trans = translation_text || {};
    function setCookie(name,value,days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }
    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i=0;i < ca.length;i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1,c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length,c.length);
        }
        return null;
    }
    function deleteCookie(name) {
        document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    }
    $(document).ready(function () {
        var $c = $('.admin-top-bar'); //' input[type=checkbox]');
        $(document).on('click', function (event) {
            if (!$(event.target).closest($c).length) {
                $c.find('input[type=checkbox]').prop('checked', false);
            }
        });

        var $li = $('#admin-sidebar > #navigation-sidebar > li ');
        $li.find(' > a').on('click', function (e) {
            var href = this.href;

            if ($(this).parent().find('> ul > li').length
                && $(this).parent().find('> ul > li > a:first-child').attr('href') === href
            ) {
                e.preventDefault();
                // e.stopPropagation();
                $li.not($(this).parent()).removeClass('has-active-submenu', '');
                $(this).parent().addClass('has-active-submenu');
            }
        });
        $li.find('a').on('click', function (e) {
            var href = this.href;
            if (currentHref === href && !href.toString().match(/-new\.php$/)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
        $('#sidebar-switch i').on('click', function (e) {
            e.preventDefault();
            var $leftArea = $('#left-area');
            if ($leftArea.hasClass('closed')) {
                deleteCookie('sidebar_closed');
                $leftArea.removeClass('closed');
            } else {
                $leftArea.addClass('closed');
                setCookie('sidebar_closed', 'true');
            }
        });
    });
    // login
    if (typeof login_url === 'string' && typeof ping_url === 'string') {
        var checkFail = 3000,
            checkSucceed = 5000;
        function check_loop_login()
        {
            var $global_message = $('#global-message');
            var $page = $('#page');
            $.get(
                ping_url,
                {},
                function (e) {
                    // console.log(e.data['as']['supervisor']);
                    if (e.data && (e.data['login'] === false || !e.data['as'] || typeof e.data['as']['supervisor'] === 'undefined')) {
                        var log = login_url.replace(/\?.+/, '');
                        var $inter = $('#interim-login');
                        if ($inter.length) {
                            $inter.html('');
                        } else {
                            $inter = $('<div id="interim-login"></div>');
                        }
                        var $iframe = $('<iframe id="iframe-interim-login" class="iframe-interim" src="'+log+'?interim=1"></iframe>');
                        $inter.html($iframe);
                        $page.append($inter);
                        $iframe.on('load', function () {
                            try {
                                var href = this.contentWindow.location.href;
                                if (!href) {
                                    return;
                                }
                            } catch (e) {
                                return;
                            }

                            if (href.match(/\?login=success(?:&.*|$)/)) {
                                if (typeof user_id === "number") {
                                    var $match = href.match(/user_id=([0-9]+)(?:&|$)/);
                                    var id = parseInt($match[1]);
                                    if (id !== user_id) {
                                        window.location.reload();
                                        return;
                                    }
                                }

                                $inter.remove();
                                $iframe.remove();
                                setTimeout(function () {
                                    check_loop_login();
                                }, checkSucceed);
                            }
                        });
                        return;
                    }
                    $global_message.html('');
                    setTimeout(function () {
                        check_loop_login();
                    }, checkFail);
                })
                .fail(function (e) {
                    // console.log(e);
                    if (e.status === 0) {
                        var text = trans['You seem to be offline']||'You seem to be offline';
                        $global_message.html(text);
                        setTimeout(function () {
                            text = trans['Reconnecting...']||'Reconnecting...';
                            $global_message.html(text);
                            check_loop_login();
                        }, checkFail);
                        return;
                    }
                    setTimeout(function () {
                        check_loop_login();
                    }, checkFail);
                });
        }

        $(document).ready(function () {
            setTimeout(function () {
                check_loop_login();
            }, checkSucceed);
        });
    }
})(window.jQuery);