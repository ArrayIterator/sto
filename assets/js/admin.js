(function ($) {
    if (!$) {
        return;
    }

    var currentLanguage = document.documentElement.lang,
        apiUri = window.api_url || '/api/',
        Sto;
    if (!currentLanguage || typeof currentLanguage !== "string") {
        currentLanguage = 'en';
    }

    (function ($) {
        var Request = function Request() {
            this.Request = this;
            this.queue = {};
            this.last_id = null;
            this.last_processed_id = null;
            this.process = {};
            this.processed = {};
            return this;
        };
        Request.prototype.constructor = Request;
        Request.prototype.add = function (url, method, param, args, args2) {
            var newParams = param;
            if (!newParams || typeof newParams !== "object") {
                newParams = {};
            }

            if (url && typeof url === "object") {
                newParams = $.extend(url, newParams);
                url = null;
            } else {
                newParams.url = url;
            }
            if (typeof method === "function") {
                newParams.success = method;
                newParams.method = 'GET';
                method = null;
            }
            if (typeof param === "function") {
                newParams.error = param;
                param = null;
            }
            if (typeof args === "function") {
                newParams.done = args;
                args = null;
            }
            if (typeof args2 === "function") {
                newParams.always = args2;
                args2 = null;
            }

            if (method && typeof method === "object") {
                newParams = $.extend(method, newParams);
            }
            if (args && typeof args === "object") {
                newParams = $.extend(args, newParams);
            }
            if (args2 && typeof args2 === "object") {
                newParams = $.extend(args2, newParams);
            }

            if (!Sto) {
                Sto = window.Sto;
            }
            this.last_id = Sto.hash.sha1(JSON.stringify(newParams)).toString();
            this.queue[this.last_id] = newParams;
            return this;
        };
        Request.prototype.getQueueId = function () {
            return Object.keys(this.queue);
        };
        Request.prototype.getQueue = function () {
            return this.queue;
        };
        Request.prototype.getProcessedId = function () {
            return Object.keys(this.processed);
        };
        Request.prototype.getProcessId = function () {
            return Object.keys(this.process);
        };
        Request.prototype.getLasProcessedId = function () {
            return this.last_processed_id;
        };
        Request.prototype.delete = function (...args) {
            var deleted = 0,
                i = 0;
            for (; args.length > i;i++) {
                if (!args[i]) {
                    continue;
                }
                if (typeof args[i] === "string") {
                    if (this.queue[args[i]] !== undefined) {
                        deleted++;
                        delete this.queue[args[i]];
                    }
                    continue;
                }
                if (typeof args[i] === "object") {
                    deleted +=this.delete(...Object.values(args[i]));
                }
            }
            return deleted;
        };
        Request.prototype.clear = function () {
            this.queue = {};
            this.last_id = null;
            this.last_processed_id = null;
            this.process = {};
            this.processed = {};
        };
        Request.prototype.inQueue = function (id) {
            if (typeof id !== "string") {
                return;
            }
            return this.queue.hasOwnProperty(id);
        };
        Request.prototype.isProcessed = function (id) {
            if (typeof id !== "string") {
                return;
            }
            return this.processed.hasOwnProperty(id);
        };
        Request.prototype.inProcessed = function (id) {
            if (typeof id !== "string") {
                return;
            }
            return this.process.hasOwnProperty(id);
        };
        Request.prototype.getLastId = function () {
            return this.last_id;
        };

        Request.prototype.run = function () {
            var key;
            for (key in this.queue) {
                if (!this.queue.hasOwnProperty(key)) {
                    continue;
                }
                var params = this.queue[key],
                    always = null,
                    t = this,
                    time = new Date().getTime();
                if (params.always !== undefined) {
                    if (params.always && typeof params.always === "function") {
                        always = params.always;
                    }
                    delete params.always;
                }
                if (this.process[key]) {
                    delete t.processed[key];
                    this.process[key].abort();
                }
                if (typeof params.success === 'function') {
                    var ts = params.success;
                    params.success = function () {
                        var args = Object.values(arguments);
                        args.push(this);
                        ts.call(t, ...args);
                    }
                }
                if (typeof params.error === 'function') {
                    var tr = params.error;
                    params.error = function () {
                        var args = Object.values(arguments);
                        args.push(this);
                        re.call(t, ...args);
                    }
                }
                if (typeof params.xhr === 'function') {
                    var xr = params.xhr;
                    params.xhr = function () {
                        var args = Object.values(arguments);
                        args.push(this);
                        xr.call(t, ...args);
                    }
                }
                if (typeof params.beforeSend === 'function') {
                    var bf = params.beforeSend;
                    params.beforeSend = function () {
                        var args = Object.values(arguments);
                        args.push(this);
                        bf.call(t, ...args);
                    }
                }
                this.process[key] = $.ajax(params).always(function () {
                    delete t.process[key];
                    t.last_processed_id = key;
                    var args = Object.values(arguments);
                    var end = new Date().getTime();
                    t.processed[key] = end;
                    if (typeof always === "function") {
                        args.push({
                            time: {
                                start: time,
                                end: end,
                                time: (end / time) / 1000
                            },
                            result: arguments[0],
                            status: arguments[1],
                            xhr: arguments[2],
                        }, this);
                        always.call(this, ...args);
                    }
                });
                this.process[key].id = key;
                delete this.queue[key];
            }
            return this;
        };
        Request = new Request();
        $.request = Request;
    })($);

    $(document).ready(function () {
        if (typeof moment !== 'undefined' && typeof moment.locale === "function") {
            moment.locale(currentLanguage);
        }

        Sto = window.Sto;
        var nav_top = '.nav-menu[data-navigation=navigation-top]',
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
            },
            $modal_template = $('script#underscore_template_modal');

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
        $(document)
            .on('click', '[data-modal=true][data-api][data-template-id]', function (e) {
            e.preventDefault();
            var $this = $(this),
                attr = $this.data(),
                template_id = attr['templateId'],
                apis_path = attr['api'] || null,
                $template = template_id ? $('script#'+$.escapeSelector(template_id)) : null,
                $modal,
                $mod,
                template,
                data = {
                    content: '<div class="loading loading-dark"><div class="lds-dual-ring"></div></div>'
                }
            ;
            if (!$template || !$template.length || !apis_path || typeof apis_path !== 'string') {
                return;
            }

            if (!apis_path.match(/^(https?:)\/\//)) {
                apis_path = apis_path.replace(/^[\/]+/gi, '');
                apis_path = apiUri.replace(/[\/]+$/g, '') + '/' + apis_path;
            }

            $modal = $modal_template.html();
            if (attr.title) {
                data.title = attr.title;
            }
            template = _.template($modal)(data);
            $mod = $(template);
            $mod.modal('show');
            $mod.on('bs.hidden', function () {
                $(this).remove();
            });
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
        $('[data-clock=text]').each(function () {
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