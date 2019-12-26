import $ from 'jquery';
// import whatInput from 'what-input';
import 'select2';
import '@claviska/jquery-minicolors';
import introJs from 'intro.js';

import CodeMirror from 'codemirror';
import 'codemirror/mode/css/css';
import 'codemirror/mode/php/php';
import 'codemirror/mode/javascript/javascript';

window.$ = $;

// import Foundation from 'foundation-sites';
// If you want to pick and choose which modules to include, comment out the above and uncomment
// the line below
//import './lib/foundation-explicit-pieces';

/**
 * Starts tour on admin functionality.
 *
 * When tour should be enable, this is not executed internally.
 */
function maybeStartTour() {
    const intro = introJs();
    const AnyComment = window.AnyComment || {};
    const tourSteps = AnyComment.TourSteps || {};

    const stepsPageMap = tourSteps.steps || {};

    const wrapperRoot = document.getElementById('anycomment-wrapper');

    if (wrapperRoot && wrapperRoot.dataset) {
        const introShow = parseInt(wrapperRoot.dataset.introShow);
        const currentTab = wrapperRoot.dataset.currentTab || '';

        if (introShow === 1) {
            if (stepsPageMap[currentTab]) {
                intro.setOptions({
                    steps: stepsPageMap[currentTab],
                    nextLabel: 'Далее',
                    prevLabel: 'Назад',
                    skipLabel: 'Пропустить',
                    doneLabel: 'Скрыть',
                });
                intro.start();

                intro.onexit(() => {
                    if (typeof tourSteps.onExit === 'function') {
                        tourSteps.onExit('onexit', intro);
                    }
                });
            }
        }
    }
}

// jQuery(document).foundation();

$(document).ready(function ($) {

    $('.anycomment-select2').select2({
        width: '100%'
    });

    $('.anycomment-input-color').minicolors();


    var codes = document.querySelectorAll('.anycomment-code');

    if (codes.length > 0) {
        for (var i = 0; i < codes.length; i++) {
            let element = codes[i];
            let conf = {}; //lineNumbers: true

            const mode = element.dataset.mode || '';

            if (mode) {
                conf.mode = mode;
            }

            conf.theme = element.dataset.theme || 'monokai';

            CodeMirror.fromTextArea(element, conf).on('change', function (instance, changeObj) {
                element.value = instance.getValue();
            });
        }
    }

    maybeStartTour();
});