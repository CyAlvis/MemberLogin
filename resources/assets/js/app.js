
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
import Vue from 'vue'
import VeeValidate from 'vee-validate';

window.Vue = require('vue');
Vue.use(VeeValidate);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('example-component', require('./components/UserLogin.vue'));
Vue.component('test', require('./components/test.vue'));
Vue.component('user-login', require('./components/UserLogin.vue'));
Vue.component('user-edit', require('./components/UserEdit.vue'));
Vue.component('user-search', require('./components/UserSearch.vue'));

const app = new Vue({
    el: '#app'
});
