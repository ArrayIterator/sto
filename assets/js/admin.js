(function ($) {
    if (!$) {
        return;
    }

    var currentLanguage = document.documentElement.lang;
    if (!currentLanguage || typeof currentLanguage !== "string") {
        currentLanguage = 'en';
    }

    $(document).ready(function () {
        if (typeof moment !== 'undefined' && typeof moment.locale === "function") {
            moment.locale(currentLanguage);
        }

        var Sto = window.Sto,
            nav_top = '.nav-menu[data-navigation=navigation-top]',
            nav_sidebar = '.nav-menu[data-navigation=navigation-sidebar]',
            h_a_s = 'has-active-submenu',
            $n = $('.nav-menu[data-navigation=navigation-top]'),
            $body = $('body');

        /*! NAVIGATION
         * ---------------------- */
        $(document).on('click', function (event) {
            var $c = $('.navbar-nav[data-navigation=navigation-account]:checked').parent(),
                $o = $n.find('> li.open');
            if (!$(event.target).closest($c).length) {
                $c.find('input[type=checkbox]').prop('checked', false);
            }
            if (!$(event.target).closest($o).length) {
                $o.removeClass('open');
            }
        });

        $(nav_sidebar + ' > li, ' + nav_top + ' > li')
            .find(' > a').on('click', function (e) {
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
                if ($ul.hasClass('top-menu')) {
                    $theParent.removeClass('open');
                    $parent.toggleClass('open');
                } else {
                    $theParent.removeClass(h_a_s);
                    $parent.toggleClass(h_a_s);
                }
            }
        });

        $('[data-switch] > .switcher').on('click', function (e) {
            e.preventDefault();
            var parent = $(this).closest('[data-switch]'),
                className = parent.attr('data-class') || 'sidebar-closed',
                cookieName = parent.attr('data-cookie') || 'sidebar-closed';
            if ($body.hasClass(className)) {
                Sto.cookie.delete(cookieName);
                $body.removeClass(className);
            } else {
                $body.addClass(className);
                Sto.cookie.set(cookieName, 'true');
            }
        });

        /*! CHECKBOX
         * ---------------------- */
        var checkBoxAll = $('input[type=checkbox][data-action=check]');
        checkBoxAll.on('change', function () {
            var $this = $(this),
                is_checked = this.checked,
                target = $this.attr('data-target');
            if (!target) {
                return;
            }
            var selector = 'input[type=checkbox][data-source='+$.escapeSelector(target)+']',
                $target = $(selector);
            $target
                .unbind('change')
                .on('change', function (e) {
                    var match = $(selector + ':checked').length === $target.length;
                    $this[0].checked = false;
                    checkBoxAll.each(function (e) {
                        this.checked = match;
                    });
                });
            $target.each(function (e) {this.checked = is_checked;});
            checkBoxAll.not($this).each(function () {this.checked = is_checked;});
        });

        /*! SELECT
         * ---------------------- */
        var parse_element_attributes = function (attributes) {
                var data = {data: {}};
                if (typeof attributes !== 'object' || ! attributes.length) {
                    return data;
                }
                try {
                    for (var i =0; attributes.length > i;i++) {
                        /* safe */
                        if (typeof attributes[i].nodeName === "undefined") {
                            return data;
                        }

                        var name = attributes[i].nodeName,
                            val = attributes[i].nodeValue;
                        /* if it was data */
                        if (name.toLowerCase() === 'data') {
                            continue;
                        }

                        if (typeof val === 'string') {
                            if (val === 'true') {
                                val = true;
                            } else if (val === 'false') {
                                val = false;
                            } else if (/^[0-9]+$/.test(val)) {
                                val = parseInt(val);
                            } else if (/^[0-9]+\.[0-9]+$/.test(val)) {
                                val = parseFloat(val);
                            }
                        }

                        if (/^data-/.test(name)) {
                            name = name.replace(/^data\-/, '');
                            data.data[name] = val;
                        }
                        data[name] = val;
                    }
                } catch (e) {
                    /* pass */
                }
                return data;
            },
            callback_template = function (e) {
                if (!e.element) {
                    return e.text;
                }
                var element = $(e.element),
                    $template = element.attr('data-template'),
                    data = parse_element_attributes(e.element.attributes);
                if ($template) {
                    try {
                        data = $.extend(true, {}, data, {data:element.data()});

                        $template = _.template(
                            $template
                        )(data);
                        return $template;
                    } catch (e) {
                        /* pass */
                    }
                }

                return e.text;
            };

        $('select[data-change-submit=true]').on('change', function () {
            $(this).closest('form').submit();
        });

        $('select[data-change=true][data-target]').on('change', function () {
            var $this = $(this),
                data_target = $this.attr('data-target'),
                data_template = $this.attr('data-template'),
                $selected = $this.find('option:selected'),
                $data_target;

            if (!data_target || ! $selected.length) {
                return;
            }
            try {
                $data_target = $this.closest(data_target);
                if (!$data_target.length) {
                    $data_target = $this.parents().find(data_target);
                }
            } catch (e) {
                $data_target = $this.closest($.escapeSelector(data_target));
            }
            if (!$data_target.length) {
                return;
            }

            var data   = parse_element_attributes(this.attributes),
                data_option = parse_element_attributes($selected[0].attributes),
                html = $selected.html();

            data  = $.extend(true, {}, data, {data:$this.data()});
            data  = $.extend(true,  {}, data, data_option);
            data  = $.extend(true, {}, data, {data: $selected.data()});
            if (data_template && typeof data_template === 'string') {
                try {
                    html = _.template(
                        data_template
                    )(data);
                } catch (e) {
                    html = $selected.html();
                }
            }

            $data_target.html(html);
        });

        if ($.fn.select2) {
            $('select[data-select=select2]').each(function () {
                var $this = $(this),
                    config = {},
                    placeholder = $this.data('placeholder'),
                    allowClear = $this.data('clear'),
                    allowHtml = $this.data('tag'),
                    data_options = $this.attr('data-options');

                if (!data_options) {
                    data_options = $this.attr('data-option');
                }
                if (data_options) {
                    try {
                        if (typeof data_options === 'string') {
                            try {
                                if (/\{([^":]+\s*:[^,]+[,]?)*}$/g.test(data_options)
                                    && !/\([^)]*\)/g.test(data_options)
                                ) {
                                    var obj = (function (e) {
                                        try {
                                            eval('var data_options =' + e);
                                            if (typeof data_options === 'object') {
                                                return data_options;
                                            }
                                        } catch (e) {
                                            console.log(e);
                                        }
                                    })(data_options);
                                    if (obj && typeof obj === 'object') {
                                        data_options = obj;
                                    }
                                }
                            } catch (E) {
                                /* pass */
                            }
                        }

                        data_options = typeof data_options === 'object' ? data_options : JSON.parse(data_options);
                        if (data_options && typeof data_options === 'object') {
                            for (var i in data_options) {
                                if (!data_options.hasOwnProperty(i)
                                    || typeof i === "number"
                                ) {
                                    continue;
                                }
                                config[i] = data_options[i];
                            }
                        }
                    } catch (e) {

                    }
                }
                if (placeholder) {
                    config['placeholder'] = placeholder;
                }
                if (allowClear === true || allowClear === '1' || allowClear === 'yes' || allowClear === 'true') {
                    config['allowClear'] = true;
                }
                if (allowHtml === true || allowHtml === 'true' || allowHtml === '1' || allowHtml === 'yes') {
                    config['escapeMarkup'] = function (e) {
                        return e;
                    };
                    config['templateResult'] = callback_template;
                    config['templateSelection'] = callback_template;
                }
                $this.select2(config);
            })
        }

        /*! CLOCKS
         * ---------------------- */
        $('[data-clock=true]').each(function () {
            // if no moment js
            if (!moment || typeof moment !== 'function') {
                return;
            }
            var $this = $(this),
                utcZ = 'Europe/London',
                time = window.current_gmt_time || null,
                time_zone = window.timezone_string || null,
                format = $this.attr('data-format') || 'D MMMM YYYY [-] H:mm:ss [(%location%)]',
                moment_js = typeof time === "number" && time > 0
                    ? moment.unix(time/1000).tz(utcZ)
                    : moment.tz(utcZ);
            if (typeof format !== 'string') {
                format = 'D MMMM YYYY [-] H:mm:ss [(%location%)]';
            }
            if (time_zone) {
                try {
                    moment_js = moment_js.tz(time_zone);
                } catch (E) {
                    // pass
                }
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