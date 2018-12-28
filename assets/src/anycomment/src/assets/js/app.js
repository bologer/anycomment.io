import $ from 'jquery';
// import whatInput from 'what-input';
import 'select2'
import '@claviska/jquery-minicolors'

import CodeMirror from 'codemirror';
import 'codemirror/mode/javascript/javascript';
import 'codemirror/mode/php/php';

window.$ = $;

// import Foundation from 'foundation-sites';
// If you want to pick and choose which modules to include, comment out the above and uncomment
// the line below
//import './lib/foundation-explicit-pieces';

// jQuery(document).foundation();

$(document).ready(function ($) {

    $('.anycomment-select2').select2({
        width: '100%'
    });

    $('.anycomment-input-color').minicolors();


    var codes = document.querySelectorAll('.anycomment-code');

    if(codes.length > 0) {
        for(var i = 0; i <codes.length; i++) {
            let element = codes[i];
            let conf = {}; //lineNumbers: true

            const mode = element.dataset.mode || '';

            if (mode) {
                conf.mode = mode;
            }

            CodeMirror.fromTextArea(element, conf);
        }
    }
});