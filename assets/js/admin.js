(function ($) {
    if (!$) {
        return;
    }

    /*! META
     * ----------------------*/
    var currentHref = window.location.href;
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
})(window.jQuery);