
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

//include datatable functionality
import 'datatables.net/js/jquery.dataTables.js';
//include bootstrap 4 integration for datatables
import 'datatables.net-bs4/js/dataTables.bootstrap4.js';

//include jquery slider functionality
import 'jquery-ui/ui/widgets/slider.js';
window.Vue = require('vue');

Vue.component('githubapiinterface', require('./components/GitHubAPIInterface.vue').default);

const app = new Vue({
    el: '#app'
});
