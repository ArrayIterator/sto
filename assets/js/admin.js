(function ($) {
    if (!$) {
        return;
    }

    var currentLanguage = document.documentElement.lang;
    if (!currentLanguage || typeof currentLanguage !== "string") {
        currentLanguage = 'en';
    }

    /*! META
     * ----------------------*/
    $(document).ready(function () {
        if (typeof moment !== 'undefined' && typeof moment.locale === "function") {
            moment.locale(currentLanguage);
        }

        var Sto = window.Sto,
            $c = $('.navbar-account'), //' input[type=checkbox]');
            $n = $('#navigation-top'),
            $p = $('#page');

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
                Sto.cookie.delete('sidebar_closed');
                $leftArea.removeClass('sidebar-closed');
            } else {
                $leftArea.addClass('sidebar-closed');
                Sto.cookie.set('sidebar_closed', 'true');
            }
        });
        var checkBoxAll = $('input[type=checkbox][data-action=check]');
        checkBoxAll.on('change', function () {
            var $this = $(this),
                is_checked = this.checked,
                target = $this.attr('data-target');
            if (!target) {
                return;
            }
            var selector = 'input[type=checkbox][data-source='+$.escapeSelector(target)+']';
            var $target = $(selector);
            $target
                .unbind('change')
                .on('change', function (e) {
                    $this[0].checked = false;
                    if ($(selector + ':checked').length === $target.length) {
                        checkBoxAll.each(function () {
                            this.checked = true;
                        });
                    } else {
                        checkBoxAll.each(function () {
                            this.checked = false;
                        });
                    }
                });
            $target.each(function (e) {this.checked = is_checked;});
            checkBoxAll.not($this).each(function () {this.checked = is_checked;});
        });

        if ($.fn.select2) {
            $('select[data-change=true]').on('change', function () {
                $(this).closest('form').submit();
            });

            $('select[data-select=select2]').each(function () {
                var $this = $(this);
                var config = {};
                var placeholder = $this.data('placeholder');
                var allowClear = $this.data('clear');
                if (placeholder) {
                    config['placeholder'] = placeholder;
                }
                if (allowClear === '1' || allowClear === 'yes' || allowClear === 'true') {
                    config['allowClear'] = true;
                }
                $this.select2(config);
            })
        }
        $('[data-clock]').each(function () {
            var $this = $(this);
            var time = window.current_gmt_time;
            var current_date_string = window.current_date_string;
            var time_zone = window.timezone_string;
            var moment_js = moment.unix(time/1000).tz('Europe/London');
            var format = $this.attr('data-format') || 'D MMMM YYYY [-] H:mm:ss [(%location%)]';
            if (typeof format !== 'string') {
                format = 'D MMMM YYYY [-] H:mm:ss [(%location%)]';
            }
            if (time_zone) {
                moment_js = moment_js.tz(time_zone);
            }
            format = format.replace(/%location%/, moment_js.tz());
            function update() {
                moment_js.add(1, 'seconds');
                $this.html(moment_js.format(format));
            }
            $this.html(moment_js.format(format));
            setInterval(update, 1000);
        });
    });

})(window.jQuery);