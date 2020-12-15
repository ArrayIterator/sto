(function ($) {
    if (!$) {
        return;
    }
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
        var $li = $('#admin-sidebar > #navigation-sidebar > li ');
        $li.find(' > a').on('click', function (e) {
            var href = this.href;
            if ($(this).parent().find('> ul > li').length
                && $(this).parent().find('> ul > li > a:first-child').attr('href') === href
            ) {
                e.preventDefault();
                $li.not($(this).parent()).removeClass('has-active-submenu', '');
                $(this).parent().addClass('has-active-submenu');
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
})(window.jQuery);