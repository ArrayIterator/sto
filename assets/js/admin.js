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

            function parse_element_data(attributes)
            {
                var data = {
                    data: {}
                };
                try {
                    for (var i =0; attributes.length > i;i++) {
                        var name = attributes[i].nodeName,
                            val = attributes[i].nodeValue;
                        if (name.toString().toLowerCase() === 'data') {
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

                        if (/^data\-/.test(name)) {
                            name = name.replace(/^data\-/, '');
                            data.data[name] = val;
                        }
                        data[name] = val;
                    }
                } catch (e) {
                    // console.log(e);
                    // pass
                }
                return data;
            }

            var callback_template = function (e) {
                if (!e.element) {
                    return e.text;
                }

                var element = $(e.element);
                var $template = element.attr('data-template');
                var data = parse_element_data(e.element.attributes);
                if ($template) {
                    try {
                        data = $.extend(true, {}, data, {data:element.data()});
                        $template = _.template(
                            $template
                        )(data);
                        return $template;
                    } catch (e) {
                        // console.log(e);
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
                    $selected = $this.find('option:selected');
                if (!data_target || ! $selected.length) {
                    return;
                }

                var $data_target;
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

                var data   = parse_element_data(this.attributes);
                    data   = $.extend(true, {}, data, {data:$this.data()});
                var data_option = parse_element_data($selected[0].attributes);
                    data = $.extend(true,  {}, data, data_option);
                    data  = $.extend(true, {}, data, {data: $selected.data()});
                var _html = $selected.html();
                if (data_template && typeof data_template === 'string') {
                    try {
                        _html = _.template(
                            data_template
                        )(data);
                    } catch (e) {
                        // console.log(e);
                        _html = $selected.html();
                    }
                }
                $data_target.html(_html);
            });

            $('select[data-select=select2]').each(function () {
                var $this = $(this);
                var config = {};
                var placeholder = $this.data('placeholder');
                var allowClear = $this.data('clear');
                var allowHtml = $this.data('tag');
                var data_options = $this.attr('data-options');
                if (!data_options) {
                    data_options = $this.attr('data-option');
                }
                if (data_options) {
                    try {
                        if (typeof data_options === 'string') {
                            try {
                                if (/\{([^":]+\s*:[^,]+[,]?)*\}$/g.test(data_options)
                                    && !/\([^\)]*\)/g.test(data_options)
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
                                // pass
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