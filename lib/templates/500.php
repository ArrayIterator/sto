<?php
$error = $error ?? error_get_last();
$realMessage = strtok($error['message'], "\n");
?>
<!DOCTYPE html>
<html<?= get_html_attributes(); ?>>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php esc_html_trans_e('500 Internal Server Error'); ?></title>
    <style type="text/css">
        *,
        *::after,
        *::before {
            box-sizing: border-box;
        }

        body {
            padding: 0;
            margin: 0;
            font-family: Helvetica, Arial, sans-serif;
            font-size: 14px;
            color: #444;
            line-height: 1;
            background: #fff;
        }

        pre {
            max-width: 100%;
            border-radius: 5px;
            overflow: auto;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #f1f1f1;
            margin: 0 0 1em;
            font-size: 13px;
            font-family: monospace, Monaco, Arial, sans-serif;
        }

        code {
            font-family: monospace, Monaco, Arial, sans-serif;
            font-size: 13px;
            word-wrap: break-word;
            word-break: break-word;
            white-space: pre-wrap;
        }

        pre#message {
            line-height: 1.4;
            word-wrap: break-word;
            word-break: break-word;
            white-space: pre-wrap;
        }

        .trace-wrap {
            margin: 10vh auto 10vh;
            display: block;
            max-width: 100%;
            width: 920px;
            padding: 20px;
        }

        .inner-trace {
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
            box-shadow: 0 5px 2px rgba(0, 0, 0, .1);
            background: #fff;
        }

        .trace-wrap p {
            margin: 0.1em 0 1.2em;
        }

        .trace-wrap p span:first-child {
            font-weight: bold;
            width: 100px;
            display: inline-block;
        }

        .code-line,
        .code-string {
            display: inline-block;
            background-color: #ca4646;
            padding: 5px 1em;
            font-size: 13px;
            color: #fff;
            font-weight: bold;
        }

        .code-line {
            padding: 2px 0.5em;
            background-color: #3e733e;
        }
    </style>
</head>
<body<?= get_body_attributes(); ?>>
<div class="trace-wrap">
    <div class="inner-trace">
        <p><span><?php esc_html_trans_e('Type'); ?></span>: <span
                    class="code-string"><?= get_error_string_by_code($error['type']); ?></span></p>
        <p><span><?php esc_html_trans_e('Code'); ?></span>: <span class="code-line"><?= $error['type']; ?></span></p>
        <p><span><?php esc_html_trans_e('File'); ?></span>: <code><?= $error['file']; ?></code></p>
        <p><span><?php esc_html_trans_e('Line'); ?></span>: <span class="code-line"><?= $error['line']; ?></span></p>
        <p><span><?php esc_html_trans_e('Message'); ?></span></p>
        <pre id="message"><?= $realMessage; ?></pre>
        <pre id="trace"><?= strchr($error['message'], "Stack trace:\n"); ?></pre>
    </div>
</div>
</body>
</html>