<?php

// end here cause I don't want throw error
if (!defined('ROOT_DIR')) {
    return;
}

return <<<'JS'
;(function () {
    try {
        var $switch = document.querySelector('[data-switch]');
        var $target = document.querySelector('input' + $switch.getAttribute('data-target')),
            sq = JSON.parse($switch.getAttribute('data-switch')),
            $current = 0;
        if (!$target) {
            return;
        }
        $switch.parentElement.addEventListener('click', function (e) {
            e.preventDefault();
            $target.setAttribute('type', $current === 0 ? 'text' : 'password');
            $switch.classList.replace(
                sq[$current],
                sq[$current ? 0 : 1]
            );
            $current = $current ? 0 : 1;
        });
    } catch (e) {
        // pass
    }
})(window);

JS;
