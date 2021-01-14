(function ($) {
    if (!$) return;

    var currentLanguage = document.documentElement.lang,
        apiUri = window.api_url || '/api/',
        Sto = window.Sto || undefined;
    if (!currentLanguage || typeof currentLanguage !== "string") {
        currentLanguage = 'en';
    }

    /*!
     * --------------------------------------------
     * JQUERY FN INJECT
     * -------------------------------------------- */
    (function ($) {
        $.json_encode = function json_encode(params, space) {
            var cache = [];
            if (typeof space !== "number") {
                space = typeof space === "string" ? space.length : 0;
            }
            return JSON.stringify(params, function (key, value) {
                if (typeof value === 'object' && value !== null) {
                    /* Duplicate reference found, discard key */
                    if (cache.includes(value)) return;
                    /* Store value in our collection */
                    cache.push(value);
                }
                return value;
            }, space);
        };
        $.parse_url = function parse_url(url) {
            var i, vars, ret_val = {}, split = url.split('?');
            url = split[1] || (split.length === 1 ? split[0] : '');
            if (url === '') {
                return ret_val;
            }
            vars = url.split('&');
            for (i = 0; i < vars.length; i++) {
                var pair = vars[i].split('='),
                    first = decodeURIComponent(pair.shift()),
                    data = decodeURIComponent(pair.join('=') || '');
                if (data.trim().match(/^[0-9]+$/)) {
                    data = parseInt(data.trim());
                }
                ret_val[first] = data;
            }

            return ret_val;
        };
        var Request = function Request(...args) {
            /* this.Request = this; */
            this.queue = {};
            this.last_id = null;
            this.last_processed_id = null;
            this.process = {};
            this.processed = {};
            if (args.length) {
                this.add(...args);
            }
            return this;
        };
        Request.prototype.constructor = Request;
        Request.prototype.add = function (
            url,
            method,
            param,
            args,
            ...argsParam
        ) {
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

            if (method && typeof method === "object") {
                newParams = $.extend(newParams, method);
                url = null;
            }

            if (typeof param === "function") {
                newParams.error = param;
                param = null;
            }

            if (typeof args === "function") {
                newParams.done = args;
                args = null;
            }
            if (argsParam[0] && typeof argsParam[0] === "function") {
                newParams.always = argsParam[0];
                argsParam[0] = null;
            }

            if (method && typeof method === "object") {
                newParams = $.extend(method, newParams);
            }
            if (args && typeof args === "object") {
                newParams = $.extend(args, newParams);
            }
            if (argsParam[0] && typeof argsParam[0] === "object") {
                newParams = $.extend(argsParam[0], newParams);
            }

            if (argsParam[1] && typeof argsParam[1] === "object") {
                newParams = $.extend(argsParam[1], newParams);
            }

            if (!Sto) {
                Sto = window.Sto;
            }
            this.last_id = Sto.hash.sha1($.json_encode(newParams));
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
        Request.prototype.abort = function (...ids) {
            var i;
            if (ids.length === 0) {
                if (!this.last_id) {
                    return;
                }
                ids = [this.last_id];
            }
            for (i in ids) {
                if (!ids.hasOwnProperty(i)
                    || typeof ids[i] !== "string"
                    || !this.process.hasOwnProperty(ids[i])
                ) {
                    continue;
                }
                if (this.process[ids[i]] && typeof this.process[ids[i]] === 'object') {
                    if (typeof this.process[ids[i]].abort === "function") {
                        this.process[ids[i]].abort();
                    }
                }
                delete this.process[ids[i]];
            }
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
            for (; args.length > i; i++) {
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
                    deleted += this.delete(...Object.values(args[i]));
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
        Request.prototype.getId = function () {
            return this.last_processed_id || this.last_id;
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
            var key,
                _this = this;
            for (key in this.queue) {
                if (!this.queue.hasOwnProperty(key)) {
                    continue;
                }
                var params = this.queue[key] && typeof this.queue[key] === "object"
                    ? this.queue[key]
                    : null;
                if (!params
                    || typeof params !== "object"
                    || typeof params.url !== "string"
                ) {
                    continue;
                }

                var always,
                    done,
                    ts = params.success,
                    tr = params.error,
                    xr = params.xhr,
                    bf = params.beforeSend,
                    td = params.done,
                    time = new Date().getTime();
                if (params.always && typeof params.always === "function") {
                    always = params.always;
                }

                if (this.process[key]) {
                    delete _this.processed[key];
                    this.process[key].abort();
                }
                if (typeof ts === 'function') {
                    params.success = function () {
                        this.id = key;
                        var args = Object.values(arguments);
                        args.unshift(_this);
                        ts.call(this, ...args);
                    }
                }
                if (typeof tr === 'function') {
                    params.error = function () {
                        this.id = key;
                        var args = Object.values(arguments);
                        args.unshift(_this);
                        tr.call(this, ...args);
                    }
                }
                if (typeof xr === 'function') {
                    params.xhr = function () {
                        this.id = key;
                        var args = Object.values(arguments);
                        args.unshift(_this);
                        xr.call(this, ...args);
                    }
                }
                if (typeof bf === 'function') {
                    params.beforeSend = function () {
                        this.id = key;
                        var args = Object.values(arguments);
                        args.unshift(_this);
                        bf.call(this, ...args);
                    }
                }
                if (typeof td === "function") {
                    done = function () {
                        this.id = key;
                        var args = Object.values(arguments);
                        args.unshift(_this);
                        td.call(this, ...args);
                    };
                }
                delete params.done;
                delete params.always;
                this.process[key] = $.ajax(params).always(function () {
                    this.id = key;
                    delete _this.process[key];
                    _this.last_processed_id = key;
                    var args = Object.values(arguments);
                    var end = new Date().getTime();
                    _this.processed[key] = end;
                    if (typeof always === "function") {
                        this.benchmark = {
                            time: {
                                start: time,
                                end: end,
                                time: (end / time) / 1000
                            },
                            result: arguments[0],
                            status: arguments[1],
                            xhr: arguments[2],
                        };
                        args.unshift(_this);
                        always.call(this, ...args);
                    }
                });
                if (done) {
                    this.process[key].done(done);
                }
                this.process[key].id = key;
                delete this.queue[key];
            }
            return this;
        };

        Request.prototype.Request = Request;
        $.request = new Request();
    })($);

    /*!
     * --------------------------------------------
     * DOCUMENT READY
     * -------------------------------------------- */
    $(document).ready(function () {
        if (typeof moment !== 'undefined' && typeof moment.locale === "function") {
            moment.locale(currentLanguage);
        }

        Sto = window.Sto;

        var $body = $('body'),

            /*! NAVIGATION
             * ---------------------- */
            nav_top = '.nav-menu[data-navigation=navigation-top]',
            nav_sidebar = '.nav-menu[data-navigation=navigation-sidebar]',
            h_a_s = 'has-active-submenu',
            $n = $('.nav-menu[data-navigation=navigation-top]');

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

        $(document)
            .on(
                'click',
                '[data-switch] > .switcher',
                function (e) {
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
        $(document)
            .on(
                'change',
                'input[type=checkbox][data-action=check]',
                function () {
                    var $this = $(this),
                        is_checked = this.checked,
                        target = $this.attr('data-target'),
                        checkBoxAll = $('input[type=checkbox][data-action=check]');
                    if (!target) return;

                    var selector = 'input[type=checkbox][data-source=' + $.escapeSelector(target) + ']',
                        $target = $(selector);
                    $target
                        .unbind('change')
                        .on('change', function (e) {
                            var match = $(selector + ':checked').length === $target.length;
                            $this[0].checked = false;
                            checkBoxAll.each(function () {
                                this.checked = match;
                            });
                        });
                    $target.each(function () {
                        this.checked = is_checked
                    });
                    checkBoxAll.not($this).each(function () {
                        this.checked = is_checked
                    });
                });

        /*! SELECT
         * ---------------------- */
        var parse_element_attributes = function (attributes) {
                var data = {data: {}};
                if (typeof attributes !== 'object' || !attributes.length) {
                    return data;
                }
                try {
                    for (var i = 0; attributes.length > i; i++) {
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
            callback_template_select = function (e) {
                if (!e.element) {
                    return e.text;
                }
                var element = $(e.element),
                    $template = element.attr('data-template'),
                    data = parse_element_attributes(e.element.attributes);
                if ($template) {
                    try {
                        data = $.extend(true, {}, data, {data: element.data()});

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
        $(document)
            .on(
                'change',
                'select[data-change-submit=true]',
                function () {
                    if (this.form) {
                        $(this.form).submit();
                    }
                });
        $(document)
            .on(
                'change',
                'select[data-change=true][data-target]',
                function () {
                    var $this = $(this),
                        data_target = $this.attr('data-target'),
                        data_template = $this.attr('data-template'),
                        $selected = $this.find('option:selected'),
                        $data_target;

                    if (!data_target || !$selected.length) {
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
                    var data = parse_element_attributes(this.attributes),
                        data_option = parse_element_attributes($selected[0].attributes),
                        html = $selected.html();

                    data = $.extend(true, {}, data, {data: $this.data()});
                    data = $.extend(true, {}, data, data_option);
                    data = $.extend(true, {}, data, {data: $selected.data()});
                    if (data_template && typeof data_template === 'string') {
                        try {
                            html = _.template(data_template)(data);
                        } catch (e) {
                            html = $selected.html();
                        }
                    }

                    $data_target.html(html);
                });

        /*! SELECT2
         * ---------------------- */
        if ($.fn.select2) {
            $('select[data-select=select2]')
                .each(function () {
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
                                                var data_options;
                                                eval('data_options =' + e);
                                                if (data_options && typeof data_options === 'object') {
                                                    return data_options;
                                                }
                                            } catch (e) {
                                                /* console.log(e); */
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
                        config['templateResult'] = callback_template_select;
                        config['templateSelection'] = callback_template_select;
                    }
                    $this.select2(config);
                })
        }

        /*! MODALS
         * ---------------------- */
        var bs_last_id,
            $modal_template = $('script#underscore_template_modal'),
            cached_modal_data = {};
        /* freed */
        $(window).on('unload', function () {
            cached_modal_data = {}
        });
        $(document).on(
            'click',
            '[data-modal=true][data-api][data-template-id]',
            function (e) {
                if (!$modal_template.length) {
                    return;
                }
                var $this = $(this),
                    attr = $this.data(),
                    template_id = attr['templateId'],
                    use_cache = attr['cache'] !== false && attr['cache'] !== 0,
                    apis_path = attr['api'] || null,
                    $template = template_id ? $('script#' + $.escapeSelector(template_id)).html() : null,
                    $modal,
                    $mod,
                    data = {
                        content: '<div data-modal="loading" class="loading loading-dark"><div class="lds-dual-ring"></div></div>'
                    },
                    params = attr['params'] || {}
                ;
                if (attr.action === 'edit' || !$template || !apis_path || typeof apis_path !== 'string') {
                    return;
                }
                e.preventDefault();
                if (!apis_path.match(/^(https?:)\/\//)) {
                    apis_path = apis_path.replace(/^[\/]+/gi, '');
                    apis_path = apiUri.replace(/[\/]+$/g, '') + '/' + apis_path;
                }

                $modal = $modal_template.html();
                if (!$modal) {
                    return;
                }

                if (attr.title) {
                    data.title = attr.title;
                }
                if (typeof params !== 'object') {
                    if (typeof params === 'string') {
                        try {
                            var nn_params;
                            eval('nn_params = ' + params);
                            if (nn_params && typeof nn_params === 'object') {
                                params = nn_params;
                            }
                        } catch (e) {
                            params = {};
                        }
                    } else {
                        params = {};
                    }
                }

                $.request.abort(bs_last_id);

                function process_modal_data(data) {
                    var html = null;
                    try {
                        html = _.template($template)(data);
                    } catch (e) {
                        /* pass */
                    }
                    return html;
                }

                var $req = new $.request.Request(apis_path, params);
                bs_last_id = $req.getId();
                if (use_cache && cached_modal_data[bs_last_id]) {
                    data.content = process_modal_data(cached_modal_data[bs_last_id]);
                    $req.clear();
                    $req = null;
                    if (Object.size(cached_modal_data) > 128) {
                        var counted = 0;
                        for (var k in cached_modal_data) {
                            if (!cached_modal_data.hasOwnProperty(k)) {
                                continue;
                            }
                            if (counted++ >= 64) {
                                break;
                            }
                            delete cached_modal_data[k];
                        }
                    }
                }

                $mod = $(_.template($modal)(data));
                params.success = function (r, x) {
                    if (use_cache) {
                        cached_modal_data[this.id] = x;
                    }
                    $mod
                        .find('.loading[data-modal=loading]')
                        .replaceWith(process_modal_data(x));
                };
                params.fail = function () {
                };
                params.done = function () {
                };
                params.always = function () {
                    bs_last_id = null;
                };
                $mod
                    .on('hidden.bs.modal', function () {
                        $.request.abort(bs_last_id);
                        bs_last_id = null;
                        $(this).remove();
                        $mod = null;
                    })
                    .on('shown.bs.modal', function () {
                        if ($req) $req.run();
                    }).modal('show');
            }
        );

        $(document).on(
            'click',
            '[data-filter][data-wrap]',
            function (e) {
                var filterName = $(this).data('filter'),
                    filterWrap = $(this).data('wrap'),
                    filterClass = $(this).data('class');
                if (!filterName || !filterWrap) {
                    return;
                }

                if (!filterClass || typeof filterClass !== 'string') {
                    filterClass = 'hidden';
                }

                if ($(this).is('a')) {
                    e.preventDefault();
                }
                var $target = $('[data-wrap-target=' + $.escapeSelector(filterWrap) + ']'),
                    $filters = $target.find('[data-filter-source]'),
                    $filter = $target.find('[data-filter-source=' + $.escapeSelector(filterName) + ']');
                if (!$filter.length && (filterName === 'all' || $(this).data('reset') === true)) {
                    $filters.removeClass(filterClass.split(' '));
                    return;
                }

                $filters.not($filter).addClass(filterClass.split(' '));
                $filter.removeClass(filterClass);
            }
        );

        /*! CLOCKS
         * ---------------------- */
        $('[data-clock=text]').each(function () {
            /* if no moment js */
            if (!moment || typeof moment !== 'function') {
                return;
            }
            var $this = $(this),
                utcZ = 'Europe/London',
                time = window.current_gmt_time || null,
                time_zone = window.timezone_string || null,
                format = $this.attr('data-format') || 'D MMMM YYYY [-] H:mm:ss [(%location%)]',
                moment_js = typeof time === "number" && time > 0
                    ? moment.unix(time / 1000).tz(utcZ)
                    : moment.tz(utcZ);
            if (typeof format !== 'string') {
                format = 'D MMMM YYYY [-] H:mm:ss [(%location%)]';
            }
            if (time_zone) {
                try {
                    moment_js = moment_js.tz(time_zone);
                } catch (E) {
                    /* pass */
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