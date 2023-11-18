/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
/*!*****************************!*\
  !*** ./resources/js/app.js ***!
  \*****************************/
window.Vue = __webpack_require__(Object(function webpackMissingModule() { var e = new Error("Cannot find module 'vue'"); e.code = 'MODULE_NOT_FOUND'; throw e; }()));
Vue.component('passportClients', Object(function webpackMissingModule() { var e = new Error("Cannot find module './components/passport/Clients.vue'"); e.code = 'MODULE_NOT_FOUND'; throw e; }()));
Vue.component('passportAuthorizedClients', Object(function webpackMissingModule() { var e = new Error("Cannot find module './components/passport/AuthorizedClients.vue'"); e.code = 'MODULE_NOT_FOUND'; throw e; }()));
Vue.component('passportPersonalAccessTokens', Object(function webpackMissingModule() { var e = new Error("Cannot find module './components/passport/PersonalAccessTokens.vue'"); e.code = 'MODULE_NOT_FOUND'; throw e; }()));
new Vue({
  el: '#passport'
});
/******/ })()
;