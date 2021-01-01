(function (global, factory) {
    "use strict";
    if (typeof module === "object" && typeof module.exports === "object") {
        module.exports = factory(global);
        return;
    }
    factory(global);
})(typeof window !== "undefined" ? window : this, function (w) {
    var doc = w.document || {};
    if (!doc.cookie === undefined) {
        doc.cookie = '';
    }
    // add pi();
    Math.pi = function () {
        return Math.PI;
    }
    if (typeof Object.size !== "function") {
        Object.size = function (obj) {
            if (obj === null || typeof obj !== 'object') {
                return 0;
            }
            var size = 0, key;
            for (key in obj) {
                if (obj.hasOwnProperty(key)) size++;
            }
            return size;
        };
    }
    if (typeof Object.join !== "function") {
        Object.join = function (obj, splitter, sep) {
            if (obj === null || typeof obj !== 'object') {
                return obj;
            }
            sep = sep || ':';
            splitter = splitter || '';
            var res = '', key;
            for (key in obj) {
                if (!obj.hasOwnProperty(key)) continue;
                res += key + sep + obj[key] + splitter;
            }
            return res;
        };
    }

    if (typeof String.prototype.format !== "function") {
        String.prototype.format = function () {
            var args = arguments;
            return this.replace(/{(\d+)}/g, function (match, number) {
                return typeof args[number] != 'undefined'
                    ? args[number]
                    : match;
            });
        };
    }

    function convert_compat_string(e) {
        if (e === undefined || e === null || typeof e === 'boolean') {
            e = e ? '1' : '';
        }
        return String(e);
    }

    /* UTIL */
    function Sto() {
        return Sto;
    }

    function Cookie() {
        Cookie.val = function () {
            return doc.cookie;
        }
        Cookie.value = Cookie.val();
        return Cookie;
    }

    function Hash(algo, val) {
        if (algo && val !== undefined) {
            if (typeof this[algo] === "function") {
                return this[algo](convert_compat_string(val));
            }
            return;
        }

        return Hash;
    }

    function Str() {
        return Str;
    }

    function Clock() {
        return Clock;
    }

    /* UUID */
    function Uuid() {
        return Uuid;
    }

    function Url()
    {
        return Url;
    }

    Cookie.prototype.constructor = new Cookie();
    Str.prototype.constructor = new Str();
    Hash.prototype.constructor = new Hash();
    Url.prototype.constructor = new Url();
    Clock.prototype.constructor = new Clock();

    Cookie.prototype.Cookie = Cookie.prototype.cookie = Cookie;
    Cookie.prototype.uuid = Cookie.prototype.Uuid = Cookie;
    Str.prototype.string = Str.prototype.str = Str.prototype.Str = Str;
    Hash.prototype.Hash = Hash.prototype.hash = Hash;
    Hash.prototype.Url = Hash.prototype.url = Url;
    Clock.prototype.Clock = Hash.prototype.clock = Clock;

    Url.parse = function (url) {
        var ret_val = {},
            split = url.split('?');
        url = split[1] || (split.length === 1 ? split[0] : '');
        if (url === '') {
            return ret_val;
        }
        var vars = url.split('&');
        for (var i = 0; i < vars.length; i++) {
            var pair = vars[i].split('=');
            var first = decodeURIComponent(pair.shift());
            var data = decodeURIComponent(pair.join('=') || '');
            if (data.trim().match(/^[0-9]+$/)) {
                data = parseInt(data.trim());
            }
            ret_val[first] = data;
        }

        return ret_val;
    };
    Url.add_query = function (query, url) {
        url = url || Sto.href;
        var urlQuery = Url.parse(url);
        var i, c = 0;
        for (i in query) {
            if (!query.hasOwnProperty(i)) {
                continue;
            }
            urlQuery[i] = query[i];
        }
        url = url.split('?')[0];
        for (i in urlQuery) {
            if (!urlQuery.hasOwnProperty(i)) {
                continue;
            }
            url += c++ === 0 ? '?' : '&';
            url += encodeURIComponent(i) + '=' + encodeURIComponent(urlQuery[i]);
        }

        return url;
    }

    Uuid.getVersion = function getVersion(uuid) {
        if (typeof uuid !== 'string') {
            return false;
        }
        var match = /^[0-9a-f]{8}-[0-9a-f]{4}-([1-5])[0-9a-f]{3}-[089ab][0-9a-f]{3}-[0-9a-f]{12}$/i.exec(uuid);
        return match[1] ? 'v' + match[1] : false;
    };

    Uuid.v4 = function v4() {
        var uuid = "", i, random;
        for (i = 0; i < 32; i++) {
            random = Math.random() * 16 | 0;
            if (i === 8 || i === 12 || i === 16 || i === 20) {
                uuid += "-";
            }
            uuid += (i === 12 ? 4 : (i === 16 ? (random & 3 | 8) : random)).toString(16);
        }

        return uuid;
    };
    Uuid.v5 = function v5(namespace, name) {
        // namespace must be allocated by uuid
        // http://tools.ietf.org/html/rfc4122#section-4.3
        if (!Uuid.getVersion(namespace)) {
            return false;
        }
        name = convert_compat_string(name);
        // Binary Value
        var nStr = '',
            // Get hexadecimal components of namespace
            nHex = namespace.replace(/[-]/, ''),
            i;
        // Convert Namespace UUID to bits
        for (i = 0; i < nHex.length; i += 2) {
            nStr += String.fromCharCode(parseInt(nHex[i] + '' + nHex[i + 1], 16));
        }
        var sha1 = Crypto.sha1(nStr + name);
        return Util.sprintf('%08s-%04s-%04x-%04x-%12s',

            // 32 bits for "time_low"
            sha1.substr(0, 8),

            // 16 bits for "time_mid"
            sha1.substr(8, 4),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 5
            (Util.hexdec(sha1.substr(12, 4)) & 0x0fff) | 0x5000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            (Util.hexdec(sha1.substr(16, 4)) & 0x3fff) | 0x8000,
            // 48 bits for "node"
            sha1.substr(20, 12)
        );
    };

    Cookie.set = function (name, value, days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        var set = false;
        if (typeof window.cookie_domain === "string") {
            if (new RegExp(window.cookie_domain+'$', 'gi').test(Cookie.domain())) {
                set = true;
                doc.cookie = name + "=" + (value || "") + expires + "; path=/;domain=" + window.cookie_domain;
            }
        }
        if (!set) {
            doc.cookie = name + "=" + (value || "") + expires + "; path=/";
        }
    }

    Cookie.get = function (name) {
        var nameEQ = name + "=";
        var ca = doc.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    };
    Cookie.multi_domain = function () {
        var domain = Cookie.domain;
        var match = domain.match(/^[^.]+((?:\.[^.]+){2,3})$/);
        return match[1] || '.' + domain;
    };
    Cookie.domain = function () {
        return location.href.replace(/https?:\/\/([^\/]+)(?:\/.*)/, '$1');
    };
    Cookie.delete = function (name) {
        doc.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;Max-Age=-99999999;';
        if (typeof window.cookie_domain === "string") {
            if (new RegExp(window.cookie_domain+'$', 'gi').test(Cookie.domain())) {
                doc.cookie = name + '=; Domain=' + window.cookie_domain + ';Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;Max-Age=-99999999;';
            }
        }
        return doc.cookie;
    };

    Str.decoct = function decoct(number) {
        if (number < 0) {
            number = 0xFFFFFFFF + number + 1;
        }
        return parseInt(number, 10)
            .toString(8);
    };
    Str.octdec = function octdec(octString) {
        octString = (octString + '').replace(/[^0-7]/gi, '');
        return parseInt(octString, 8);
    };
    Str.hexdec = function hexdec(hexString) {
        hexString = (hexString + '').replace(/[^a-f0-9]/gi, '')
        return parseInt(hexString, 16);
    };
    Str.dechex = function dechex(number) {
        if (number < 0) {
            number = 0xFFFFFFFF + number + 1;
        }
        return parseInt(number, 10)
            .toString(16)
    };
    Str.bin2hex = function bin2hex(s) {
        var i, l, o = '', n;
        s += '';
        for (i = 0, l = s.length; i < l; i++) {
            n = s.charCodeAt(i)
                .toString(16);
            o += n.length < 2 ? '0' + n : n;
        }
        return o;
    };
    Str.hex2bin = function hex2bin(s) {
        var ret = [], i = 0, l;
        s += '';
        for (l = s.length; i < l; i += 2) {
            var c = parseInt(s.substr(i, 1), 16),
                k = parseInt(s.substr(i + 1, 1), 16);
            if (isNaN(c) || isNaN(k)) {
                return false;
            }
            ret.push((c << 4) | k);
        }
        return String.fromCharCode.apply(String, ret);
    };
    Str.ord = function ord(string) {
        var hi, low,
            str = convert_compat_string(string),
            code = str.charCodeAt(0)

        if (code >= 0xD800 && code <= 0xDBFF) {
            // High surrogate (could change last hex to 0xDB7F to treat
            // high private surrogates as single characters)
            hi = code
            if (str.length === 1) {
                // This is just a high surrogate with no following low surrogate,
                // so we return its value;
                return code
                // we could also throw an error as it is not a complete character,
                // but someone may want to know
            }
            low = str.charCodeAt(1)
            return ((hi - 0xD800) * 0x400) + (low - 0xDC00) + 0x10000
        }
        if (code >= 0xDC00 && code <= 0xDFFF) {
            // Low surrogate
            // This is just a low surrogate with no preceding high surrogate,
            // so we return its value;
            return code
            // we could also throw an error as it is not a complete character,
            // but someone may want to know
        }

        return code;
    };
    Str.chr = function chr(codePoint) {
        if (codePoint > 0xFFFF) {
            // Create a four-byte string (length 2) since this code point is high
            //   enough for the UTF-16 encoding (JavaScript internal use), to
            //   require representation with two surrogates (reserved non-characters
            //   used for building other characters; the first is "high" and the next "low")
            codePoint -= 0x10000
            return String.fromCharCode(0xD800 + (codePoint >> 10), 0xDC00 + (codePoint & 0x3FF))
        }

        return String.fromCharCode(codePoint)
    };
    Str.mt_rand = function mt_rand(min, max) {
        if (arguments.length === 0) {
            min = 0;
            max = 2147483647;
        } else if (arguments.length === 1) {
            min = parseInt(min, 10);
            max = 2147483647;
        } else {
            min = parseInt(min, 10);
            max = parseInt(max, 10);
        }
        return Math.floor(Math.random() * (max - min + 1)) + min
    };
    Str.utf8_encode = function utf8_encode(string) {
        string = convert_compat_string(string).replace(/\r\n/g, "\n");
        var utfText = "",
            c, n;
        for (n = 0; n < string.length; n++) {
            c = string.charCodeAt(n);
            if (c < 128) {
                utfText += String.fromCharCode(c);
            } else if ((c > 127) && (c < 2048)) {
                utfText += String.fromCharCode((c >> 6) | 192);
                utfText += String.fromCharCode((c & 63) | 128);
            } else {
                utfText += String.fromCharCode((c >> 12) | 224);
                utfText += String.fromCharCode(((c >> 6) & 63) | 128);
                utfText += String.fromCharCode((c & 63) | 128);
            }
        }
        return utfText;
    };
    Str.utf8_decode = function utf8_decode(utfText) {
        utfText = convert_compat_string(utfText);
        var string = "",
            i = 0,
            c, c2, c3;
        while (i < utfText.length) {
            c = utfText.charCodeAt(i);
            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            } else if ((c > 191) && (c < 224)) {
                c2 = utfText.charCodeAt(i + 1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            } else {
                c2 = utfText.charCodeAt(i + 1);
                c3 = utfText.charCodeAt(i + 2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }
        }
        return string;
    };
    Str.sprintf = function sprintf() {
        var regex = /%%|%(?:(\d+)\$)?((?:[-+#0 ]|'[\s\S])*)(\d+)?(?:\.(\d*))?([\s\S])/g,
            args = arguments,
            i = 0,
            format = args[i++];
        var _pad = function (str, len, chr, leftJustify) {
            if (!chr) {
                chr = ' ';
            }
            var padding = (str.length >= len) ? '' : new Array(1 + len - str.length >>> 0).join(chr);
            return leftJustify ? str + padding : padding + str;
        }

        var justify = function (value, prefix, leftJustify, minWidth, padChar) {
            var diff = minWidth - value.length;
            if (diff > 0) {
                // when padding with zeros
                // on the left side
                // keep sign (+ or -) in front
                if (!leftJustify && padChar === '0') {
                    value = [
                        value.slice(0, prefix.length),
                        _pad('', diff, '0', true),
                        value.slice(prefix.length)
                    ].join('');
                } else {
                    value = _pad(value, minWidth, padChar, leftJustify);
                }
            }
            return value;
        }

        var _formatBaseX = function (value, radix, leftJustify, minWidth, precision, padChar) {
            // Note: casts negative numbers to positive ones
            var num = value >>> 0;
            value = _pad((radix < 2 || typeof num !== 'number' ? num.toString() : num.toString(radix)), precision || 0, '0', false);
            return justify(value, '', leftJustify, minWidth, padChar);
        };

        // _formatString()
        var _formatString = function (value, leftJustify, minWidth, precision, customPadChar) {
            if (precision !== null && precision !== undefined) {
                value = value.slice(0, precision);
            }
            return justify(value, '', leftJustify, minWidth, customPadChar);
        };
        // doFormat()
        var doFormat = function (substring, argIndex, modifiers, minWidth, precision, specifier) {
            var number, prefix, method, textTransform, value;
            if (substring === '%%') {
                return '%';
            }
            // parse modifiers
            var padChar = ' ', // pad with spaces by default
                leftJustify = false,
                positiveNumberPrefix = '',
                j,
                l;

            for (j = 0, l = modifiers.length; j < l; j++) {
                switch (modifiers.charAt(j)) {
                    case ' ':
                    case '0':
                        padChar = modifiers.charAt(j)
                        break
                    case '+':
                        positiveNumberPrefix = '+'
                        break
                    case '-':
                        leftJustify = true
                        break
                    case "'":
                        if (j + 1 < l) {
                            padChar = modifiers.charAt(j + 1);
                            j++
                        }
                        break;
                }
            }

            if (!minWidth) {
                minWidth = 0;
            } else {
                minWidth = +minWidth;
            }

            if (!isFinite(minWidth)) {
                throw new Error('Width must be finite');
            }

            if (!precision) {
                precision = (specifier === 'd') ? 0 : 'fFeE'.indexOf(specifier) > -1 ? 6 : undefined;
            } else {
                precision = +precision;
            }

            if (argIndex && +argIndex === 0) {
                throw new Error('Argument number must be greater than zero');
            }

            if (argIndex && +argIndex >= args.length) {
                throw new Error('Too few arguments');
            }

            value = argIndex ? args[+argIndex] : args[i++];

            switch (specifier) {
                case '%':
                    return '%';
                case 's':
                    return _formatString(value + '', leftJustify, minWidth, precision, padChar);
                case 'c':
                    return _formatString(String.fromCharCode(+value), leftJustify, minWidth, precision, padChar);
                case 'b':
                    return _formatBaseX(value, 2, leftJustify, minWidth, precision, padChar);
                case 'o':
                    return _formatBaseX(value, 8, leftJustify, minWidth, precision, padChar);
                case 'x':
                    return _formatBaseX(value, 16, leftJustify, minWidth, precision, padChar);
                case 'X':
                    return _formatBaseX(value, 16, leftJustify, minWidth, precision, padChar)
                        .toUpperCase();
                case 'u':
                    return _formatBaseX(value, 10, leftJustify, minWidth, precision, padChar);
                case 'i':
                case 'd':
                    number = +value || 0;
                    // Plain Math.round doesn't just truncate
                    number = Math.round(number - number % 1);
                    prefix = number < 0 ? '-' : positiveNumberPrefix;
                    value = prefix + _pad(String(Math.abs(number)), precision, '0', false);

                    if (leftJustify && padChar === '0') {
                        // can't right-pad 0s on integers
                        padChar = ' ';
                    }
                    return justify(value, prefix, leftJustify, minWidth, padChar);
                case 'e':
                case 'E':
                case 'f': // @todo: Should handle locales (as per setlocale)
                case 'F':
                case 'g':
                case 'G':
                    number = +value;
                    prefix = number < 0 ? '-' : positiveNumberPrefix;
                    method = ['toExponential', 'toFixed', 'toPrecision']['efg'.indexOf(specifier.toLowerCase())];
                    textTransform = ['toString', 'toUpperCase']['eEfFgG'.indexOf(specifier) % 2];
                    value = prefix + Math.abs(number)[method](precision);
                    return justify(value, prefix, leftJustify, minWidth, padChar)[textTransform]();
                default:
                    // unknown specifier, consume that char and return empty
                    return '';
            }
        };

        try {
            return format.replace(regex, doFormat);
        } catch (err) {
            return false;
        }
    };

    Str.unserialize = function unserialize(data) {
        data = convert_compat_string(data);
        var utf8Overhead = function (str) {
                var s = str.length, i;
                for (i = str.length - 1; i >= 0; i--) {
                    var code = str.charCodeAt(i);
                    if (code > 0x7f && code <= 0x7ff) {
                        s++;
                    } else if (code > 0x7ff && code <= 0xffff) {
                        s += 2;
                    }
                    // trail surrogate
                    if (code >= 0xDC00 && code <= 0xDFFF) {
                        i--;
                    }
                }
                return s - 1;
            },
            readUntil = function (data, offset, stopchr) {
                var i = 2,
                    buf = [],
                    chr = data.slice(offset, offset + 1);
                while (chr !== stopchr) {
                    if ((i + offset) > data.length) {
                        throw Error('Invalid');
                    }
                    buf.push(chr);
                    chr = data.slice(offset + (i - 1), offset + i);
                    i += 1;
                }
                return [buf.length, buf.join('')];
            }
        var readChrs = function (data, offset, length) {
            var i,
                chr,
                buf
            buf = [];
            for (i = 0; i < length; i++) {
                chr = data.slice(offset + (i - 1), offset + i);
                buf.push(chr);
                length -= utf8Overhead(chr);
            }
            return [buf.length, buf.join('')];
        }

        function _unserialize(data, offset) {
            var dtype,
                dataoffset,
                keyandchrs,
                keys,
                contig,
                length,
                array,
                obj,
                readdata,
                readData,
                ccount,
                stringlength,
                i,
                key,
                kprops,
                kchrs,
                vprops,
                vchrs,
                value,
                chrs = 0,
                typeConvert = function (x) {
                    return x
                };

            if (!offset) {
                offset = 0;
            }
            dtype = (data.slice(offset, offset + 1));
            dataoffset = offset + 2;
            switch (dtype) {
                case 'i':
                    typeConvert = function (x) {
                        return parseInt(x, 10)
                    };
                    readData = readUntil(data, dataoffset, ';')
                    chrs = readData[0];
                    readdata = readData[1];
                    dataoffset += chrs + 1;
                    break;
                case 'b':
                    typeConvert = function (x) {
                        var value = parseInt(x, 10);
                        switch (value) {
                            case 0:
                                return false;
                            case 1:
                                return true;
                            default:
                                throw SyntaxError('Invalid boolean value');
                        }
                    };

                    readData = readUntil(data, dataoffset, ';');
                    chrs = readData[0];
                    readdata = readData[1];
                    dataoffset += chrs + 1;
                    break;
                case 'd':
                    typeConvert = function (x) {
                        return parseFloat(x);
                    }
                    readData = readUntil(data, dataoffset, ';');
                    chrs = readData[0];
                    readdata = readData[1];
                    dataoffset += chrs + 1;
                    break;
                case 'n':
                    readdata = null;
                    break;
                case 's':
                    ccount = readUntil(data, dataoffset, ':');
                    chrs = ccount[0];
                    stringlength = ccount[1];
                    dataoffset += chrs + 2;
                    readData = readChrs(data, dataoffset + 1, parseInt(stringlength, 10))
                    chrs = readData[0];
                    readdata = readData[1];
                    dataoffset += chrs + 2;
                    if (chrs !== parseInt(stringlength, 10) && chrs !== readdata.length) {
                        throw SyntaxError('String length mismatch');
                    }
                    break;
                case 'a':
                    readdata = {};
                    keyandchrs = readUntil(data, dataoffset, ':');
                    chrs = keyandchrs[0];
                    keys = keyandchrs[1];
                    dataoffset += chrs + 2;
                    length = parseInt(keys, 10);
                    contig = true;
                    for (i = 0; i < length; i++) {
                        kprops = _unserialize(data, dataoffset);
                        kchrs = kprops[1];
                        key = kprops[2];
                        dataoffset += kchrs;
                        vprops = _unserialize(data, dataoffset);
                        vchrs = vprops[1];
                        value = vprops[2];
                        dataoffset += vchrs;
                        if (key !== i) {
                            contig = false;
                        }

                        readdata[key] = value;
                    }

                    if (contig) {
                        array = new Array(length);
                        for (i = 0; i < length; i++) {
                            array[i] = readdata[i];
                        }
                        readdata = array;
                    }

                    dataoffset += 1;
                    break
                case 'O': {
                    // O:<class name length>:"class name":<prop count>:{<props and values>}
                    // O:8:"stdClass":2:{s:3:"foo";s:3:"bar";s:3:"bar";s:3:"baz";}
                    readData = readUntil(data, dataoffset, ':'); // read class name length
                    dataoffset += readData[0] + 1;
                    readData = readUntil(data, dataoffset, ':');
                    if (readData[1] !== '"stdClass"') {
                        throw Error('Unsupported object type: ' + readData[1]);
                    }
                    dataoffset += readData[0] + 1; // skip ":"
                    readData = readUntil(data, dataoffset, ':');
                    keys = parseInt(readData[1], 10);
                    dataoffset += readData[0] + 2; // skip ":{"
                    obj = {};
                    for (i = 0; i < keys; i++) {
                        readData = _unserialize(data, dataoffset);
                        key = readData[2];
                        dataoffset += readData[1];
                        readData = _unserialize(data, dataoffset);
                        dataoffset += readData[1];
                        obj[key] = readData[2];
                    }

                    dataoffset += 1; // skip "}"
                    readdata = obj;
                    break;
                }
                default:
                    throw SyntaxError('Unknown / Unhandled data type(s): ' + dtype);
            }
            return [dtype, dataoffset - offset, typeConvert(readdata)];
        }

        try {
            if (typeof data !== 'string') {
                return false;
            }
            return _unserialize(data, 0)[2];
        } catch (err) {
            console.error(err);
            return false;
        }
    };
    Str.serialize = function serialize(mixedValue) {
        var val,
            key,
            oKey,
            values = '',
            kType = '',
            count = 0;

        var _utf8Size = function (str) {
            return ~-encodeURI(str).split(/%..|./).length;
        }
        var _getType = function (inp) {
            var match,
                key,
                cons,
                types,
                type = typeof inp;
            if (type === 'object' && !inp) {
                return 'null';
            }

            if (type === 'object') {
                if (!inp.constructor) {
                    return 'object';
                }
                cons = inp.constructor.toString();
                match = cons.match(/(\w+)\(/);
                if (match) {
                    cons = match[1].toLowerCase();
                }

                types = ['boolean', 'number', 'string', 'array'];
                for (key in types) {
                    if (cons === types[key]) {
                        type = types[key];
                        break;
                    }
                }
            }
            return type;
        }

        var type = _getType(mixedValue);
        switch (type) {
            case 'function':
                val = '';
                break;
            case 'boolean':
                val = 'b:' + (mixedValue ? '1' : '0');
                break;
            case 'number':
                val = (Math.round(mixedValue) === mixedValue ? 'i' : 'd') + ':' + mixedValue;
                break;
            case 'string':
                val = 's:' + _utf8Size(mixedValue) + ':"' + mixedValue + '"';
                break;
            case 'array':
            case 'object':
                val = 'a';
                /*
                if (type === 'object') {
                  var objname = mixedValue.constructor.toString().match(/(\w+)\(\)/);
                  if (objname === undefined) {
                    return;
                  }
                  objname[1] = serialize(objname[1]);
                  val = 'O' + objname[1].substring(1, objname[1].length - 1);
                }
                */

                for (key in mixedValue) {
                    if (mixedValue.hasOwnProperty(key)) {
                        kType = _getType(mixedValue[key]);
                        if (kType === 'function') {
                            continue;
                        }

                        oKey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key)
                        values += serialize(oKey) + serialize(mixedValue[key]);
                        count++
                    }
                }
                val += ':' + count + ':{' + values + '}';
                break;
            case 'undefined':
            default:
                // Fall-through
                // if the JS object has a property which contains a null value,
                // the string cannot be unserialized by PHP
                val = 'N';
                break;
        }
        if (type !== 'object' && type !== 'array') {
            val += ';';
        }

        return val;
    };
    Str.is_string = function is_string(e) {
        return typeof e === 'string' || Object.prototype.toString.call(e) === '[object String]';
    };
    Str.isUuid = function isUuid(e) {
        return Str.is_string(e) && /^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[089ab][0-9a-f]{3}-[0-9a-f]{12}$/i.test(e);
    };

    Str.isBase64 = function isBase64(e) {
        return Str.is_string(e) && /^(?:[A-Za-z\d+\/]{4})*?(?:[A-Za-z\d+\/]{2}(?:==)?|[A-Za-z\d+\/]{3}=?)?$/.test(e);
    };
    Str.isBinary = function isBinary(e) {
        return Str.is_string(e) && /[^\x20-\x7E]/.test(e);
    };

    Hash.sha1 = function (e) {
        return CryptoJS.SHA1(convert_compat_string(e)).toString();
    }
    Hash.md5 = function (e) {
        return CryptoJS.MD5(convert_compat_string(e)).toString();
    };
    Hash.sha256 = function (e) {
        return CryptoJS.SHA256(convert_compat_string(e)).toString();
    };
    Hash.sha224 = function (e) {
        return CryptoJS.SHA224(convert_compat_string(e)).toString();
    };
    Hash.sha384 = function (e) {
        return CryptoJS.SHA384(convert_compat_string(e)).toString();
    };
    Hash.sha512 = function (e) {
        return CryptoJS.SHA512(convert_compat_string(e)).toString();
    };
    Hash.hmac = function (algo, val)
    {
        return CryptoJS.HMAC(algo, convert_compat_string(val)).toString();
    }

    Clock.calculate_second = function (hours, minutes, seconds)
    {
        seconds = (360 / 100) * ((seconds / 60) * 100);
        minutes = (360 / 100) * ((minutes / 60) * 100);
        hours = (360 / 100) * ((hours / 12) * 100);
        var secondsAngle = seconds,
            minutesAngle = minutes,
            hoursAngle = hours;
        return {
            second: {
                '-webkit-transform' : 'rotate('+ secondsAngle +'deg)',
                '-moz-transform' : 'rotate('+ secondsAngle +'deg)',
                '-ms-transform' : 'rotate('+ secondsAngle +'deg)',
                'transform' : 'rotate('+ secondsAngle +'deg)'
            },
            minutes: {
                '-webkit-transform' : 'rotate('+ minutesAngle +'deg)',
                '-moz-transform' : 'rotate('+ minutesAngle +'deg)',
                '-ms-transform' : 'rotate('+ minutesAngle +'deg)',
                'transform' : 'rotate('+ minutesAngle +'deg)'
            },
            hours: {
                '-webkit-transform' : 'rotate('+ hoursAngle +'deg)',
                '-moz-transform' : 'rotate('+ hoursAngle +'deg)',
                '-ms-transform' : 'rotate('+ hoursAngle +'deg)',
                'transform' : 'rotate('+ hoursAngle +'deg)'
            }
        }
    };
    // reverse param
    Clock.calculate_delay = function (
        currentSecond,
        currentMinute,
        currentHour,
        currentDate,
        currentMonth,
        currentYear
    ) {

        var defaultDate = new Date(),
            currentYearString = defaultDate.getFullYear().toString();
        if (arguments.length < 6
            || typeof currentYear !== "number" && (
                typeof currentYear !== 'string' || ! /^[0-9]{1,4}$/.test(currentYear)
            )
        ) {
            currentYear = defaultDate.getFullYear();
        }

        if (currentYear.toString().length < 3) {
            currentYear = ''
                + currentYearString.substr(0, 4 - currentYear.toString().length)
                + currentYear;
        }

        if (arguments.length < 5
            || typeof currentMonth !== "number" && (
                typeof currentMonth !== 'string'
                || /^[0-9]{1,2}$/.test(currentMonth)
                || (currentMonth < 1 || currentMonth > 12)
            ) || (currentMonth < 1 || currentMonth > 12)
        ) {
            currentMonth = defaultDate.getMonth();
        }

        if (arguments.length < 4
            || typeof currentDate !== "number" && (
                typeof currentDate !== 'string'
                || /^[0-9]{1,2}$/.test(currentDate)
                || (currentDate < 1 || currentDate > 31)
            ) || (currentDate < 1 || currentDate > 31)
        ) {
            currentDate = defaultDate.getDate();
        }

        if (arguments.length < 3
            || typeof currentHour !== "number" && (
                typeof currentHour !== 'string'
                || /^[0-9]{1,2}$/.test(currentHour)
                || (currentHour < 0 || currentHour > 24)
            ) || (currentHour < 0 || currentHour > 24)
        ) {
            currentHour = defaultDate.getHours();
        }

        if (arguments.length < 2
            || typeof currentMinute !== "number" && (
                typeof currentMinute !== 'string'
                || /^[0-9]{1,2}$/.test(currentMinute)
                || (currentMinute < 0 || currentMinute > 60)
            ) || (currentMinute < 0 || currentMinute > 60)
        ) {
            currentMinute = defaultDate.getMinutes();
        }

        if (arguments.length < 1
            || typeof currentSecond !== "number" && (
                typeof currentSecond !== 'string'
                || /^[0-9]{1,2}$/.test(currentSecond)
                || (currentSecond < 0 || currentSecond > 60)
            ) || (currentSecond < 0 || currentSecond > 60)
        ) {
            currentSecond = defaultDate.getSeconds();
        }

        defaultDate = new Date(currentYear, currentMonth, currentDate);
        var date = new Date(
                currentYear,
                currentMonth,
                currentDate,
                currentHour,
                currentMinute,
                currentSecond
            ),
            diff = (date.getTime() - defaultDate.getTime())/1000;
            currentSecond = (60 * ((diff / 60) % 1)) * -1;
            currentMinute = (3600 * ((diff / 3600) % 1)) * -1;
            currentHour   = (43200 * ((diff / 43200) % 1)) * -1;
        return {
            date,
            seconds: {
                '-webkit-animation-delay' : currentSecond + 's',
                '-moz-animation-delay' : currentSecond + 's',
                '-ms-animation-delay' : currentSecond + 's',
                'animation-delay' : currentSecond + 's',
            },
            minutes: {
                '-webkit-animation-delay' : currentMinute + 's',
                '-moz-animation-delay' : currentMinute + 's',
                '-ms-animation-delay' : currentMinute + 's',
                'animation-delay' : currentMinute + 's',
            },
            hours: {
                '-webkit-animation-delay' : currentHour + 's',
                '-moz-animation-delay' : currentHour + 's',
                '-ms-animation-delay' : currentHour + 's',
                'animation-delay' : currentHour + 's',
            }
        }
    }

    Sto.href = w.location.href;
    Sto.cookie = Cookie;
    Sto.hash = Hash;
    Sto.crypto = CryptoJS;
    Sto.string = Str;
    Sto.hash = Hash;
    Sto.uuid = Uuid;
    Sto.url = Url;
    Sto.clock = Clock;
    w.Sto = Sto;
    w.location.query_string = Url.parse(location.search || location.href);
});