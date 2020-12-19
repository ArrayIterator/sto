(function ($) {
    if (!$) {
        return;
    }

    /*! META
     * ----------------------*/
    var currentHref = window.location.href;
    var trans = translation_text || {};

    function setCookie(name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    function getCookie(name) {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    function deleteCookie(name) {
        document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    }

    $(document).ready(function () {
        var $c = $('.navbar-account'); //' input[type=checkbox]');
        var $n = $('#navigation-top');
        var $p = $('#page');
        $(document).on('click', function (event) {
            var $c = $('.navbar-account input[type=checkbox]:checked').parent();
            if (!$(event.target).closest($c).length) {
                $c.find('input[type=checkbox]').prop('checked', false);
            }
            var $o = $n.find('> li.open');
            if (!$(event.target).closest($o).length) {
                $o.removeClass('open');
            }
        });

        var $li = $('#admin-sidebar > #navigation-sidebar > li, #navigation-top > li');
        $li.find(' > a').on('click', function (e) {
            var href = this.href,
                $parent = $(this).parent(),
                $ul = $parent.parent('ul');
            if ($parent.find('> ul > li').length
                && $parent.find('> ul > li > a:first-child').attr('href') === href
            ) {
                e.preventDefault();
                var $theParent = $ul
                    .find('> li')
                    .not($parent);
                // e.stopPropagation();
                if ($ul.hasClass('top-menu')) {
                    $theParent.removeClass('open');
                    $parent.toggleClass('open');
                } else {
                    $theParent.removeClass('has-active-submenu');
                    $parent.toggleClass('has-active-submenu');
                }
            }
        });
        /*
        $li.find('a').on('click', function (e) {
            var href = this.href;
            if (currentHref === href && !href.toString().match(/-new\.php$/)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });*/

        $('#sidebar-switch > .switcher').on('click', function (e) {
            e.preventDefault();
            var $leftArea = $('#page');// $('#left-area');
            if ($leftArea.hasClass('sidebar-closed')) {
                deleteCookie('sidebar_closed');
                $leftArea.removeClass('sidebar-closed');
            } else {
                $leftArea.addClass('sidebar-closed');
                setCookie('sidebar_closed', 'true');
            }
        });
    });

    /*! PING ACCOUNT
    --------------------------------- */
    if (typeof login_url === 'string' && typeof ping_url === 'string') {
        var checkFail = 3000,
            checkSucceed = 5000;
        var $iframeInterim;
        var $interimLayout;
        function check_loop_login() {
            var $global_message = $('#global-message');
            var $page = $('#page');
            $.get(
                ping_url,
                {},
                function (e) {
                    // console.log(e.data['as']['supervisor']);
                    if (e.data && (e.data['login'] === false || !e.data['as'] || typeof e.data['as']['supervisor'] === 'undefined')) {
                        if (!$iframeInterim) {
                            var log = login_url.replace(/\?.+/, '');
                            $interimLayout = $('#interim-login');
                            if ($interimLayout.length) {
                                $interimLayout.html('');
                            } else {
                                $interimLayout = $('<div id="interim-login"></div>');
                            }
                            $iframeInterim = $('<iframe id="iframe-interim-login" class="iframe-interim" src="' + log + '?interim=1"></iframe>');
                            $interimLayout.html($iframeInterim);
                            $page.append($interimLayout);
                            $iframeInterim.on('load', function () {
                                try {
                                    var href = this.contentWindow.location.href;
                                    if (!href) {
                                        return;
                                    }
                                } catch (e) {
                                    return;
                                }

                                if (href.match(/\?login=success(?:&.*|$)/)) {
                                    $iframeInterim = null;
                                    if (typeof user_id === "number") {
                                        var $match = href.match(/user_id=([0-9]+)(?:&|$)/);
                                        var id = parseInt($match[1]);
                                        if (id !== user_id) {
                                            window.location.reload();
                                            return;
                                        }
                                    }

                                    $interimLayout.remove();
                                    $iframeInterim.remove();
                                    setTimeout(function () {
                                        check_loop_login();
                                    }, checkSucceed);
                                }
                            });
                        }
                        setTimeout(function () {
                            check_loop_login();
                        }, checkFail);

                        return;
                    }

                    if (user_id && e.data['as']['supervisor'].id && e.data['as']['supervisor'].id !== user_id) {
                        window.location.reload();
                        return;
                    }

                    if ($interimLayout) {
                        $interimLayout.remove();
                        $interimLayout = null;
                    }
                    if ($iframeInterim) {
                        $iframeInterim.remove();
                        $iframeInterim = null;
                    }

                    $global_message.html('');
                    setTimeout(function () {
                        check_loop_login();
                    }, checkFail);
                })
                .fail(function (e) {
                    // console.log(e);
                    if (e.status === 0) {
                        var text = trans['You seem to be offline'] || 'You seem to be offline';
                        $global_message.html(text);
                        setTimeout(function () {
                            text = trans['Reconnecting...'] || 'Reconnecting...';
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