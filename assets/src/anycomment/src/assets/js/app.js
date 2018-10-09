// import jq from 'jquery';
// import whatInput from 'what-input';
import 'select2'

// window.$ = $;

// import Foundation from 'foundation-sites';
// If you want to pick and choose which modules to include, comment out the above and uncomment
// the line below
//import './lib/foundation-explicit-pieces';

// jQuery(document).foundation();

jQuery(document).on('ready', function () {
    console.log('ready');
    jQuery('.anycomment-select2').select2();
    console.log('should be loaded');
});