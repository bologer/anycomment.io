"use strict";var precacheConfig=[["/wp-content/plugins/anycomment/index.html","d4cffe75bac352aeccc4bfd73945a0ab"],["/wp-content/plugins/anycomment/static/css/main.da9f2145.css","f1eaac65c53a3b215486a0daf253823a"],["/wp-content/plugins/anycomment/static/js/main.8d32e1c2.js","d2fbe98e63d491a6a42c0980b703ba58"],["/wp-content/plugins/anycomment/static/media/dribbble.8f2d0d70.svg","8f2d0d70a239f7710b0647dc35a62394"],["/wp-content/plugins/anycomment/static/media/dropzone.2cd703f4.svg","2cd703f47eadb4cc1394f1522d91d6fd"],["/wp-content/plugins/anycomment/static/media/facebook.9f840c97.svg","9f840c975d88b1242e5c0d907cc6b721"],["/wp-content/plugins/anycomment/static/media/github.a584bb87.svg","a584bb8729ea81827236833a81ec794f"],["/wp-content/plugins/anycomment/static/media/google.b6e53cb5.svg","b6e53cb572aafc6c38b9a3a1c2b21d10"],["/wp-content/plugins/anycomment/static/media/instagram.242afcde.svg","242afcde81c7199ec4935f1d76b2cad9"],["/wp-content/plugins/anycomment/static/media/mailru.3cc0d9d8.svg","3cc0d9d89b9225eb53daf491f5c4da97"],["/wp-content/plugins/anycomment/static/media/mini-logo.68334f3e.svg","68334f3e95ee58431cfb6e8631a5d86e"],["/wp-content/plugins/anycomment/static/media/odnoklassniki.31259f7c.svg","31259f7cd3445c9ef3c74f998fa24c93"],["/wp-content/plugins/anycomment/static/media/steam.67d4b7c7.svg","67d4b7c716b056f0169ce971ef06bcf5"],["/wp-content/plugins/anycomment/static/media/telegram.34cd4f5b.svg","34cd4f5b6521cbc6d09d26f2db2a80fd"],["/wp-content/plugins/anycomment/static/media/twitch.baefdb2a.svg","baefdb2a57bb99a0f620c22f74bc6651"],["/wp-content/plugins/anycomment/static/media/twitter.63e540d2.svg","63e540d2831d558d735075288cfc100a"],["/wp-content/plugins/anycomment/static/media/vkontakte.9ec9d581.svg","9ec9d5812b45c8c63ad131e49686b9c9"],["/wp-content/plugins/anycomment/static/media/wordpress.e08e61be.svg","e08e61bea668d6da952c01edff9630a7"],["/wp-content/plugins/anycomment/static/media/yahoo.4913eb5e.svg","4913eb5e1664e9f6fe629b10a3eb9280"],["/wp-content/plugins/anycomment/static/media/yandex.20017308.svg","20017308e774693643807fe8313e3c87"]],cacheName="sw-precache-v3-sw-precache-webpack-plugin-"+(self.registration?self.registration.scope:""),ignoreUrlParametersMatching=[/^utm_/],addDirectoryIndex=function(e,t){var n=new URL(e);return"/"===n.pathname.slice(-1)&&(n.pathname+=t),n.toString()},cleanResponse=function(t){return t.redirected?("body"in t?Promise.resolve(t.body):t.blob()).then(function(e){return new Response(e,{headers:t.headers,status:t.status,statusText:t.statusText})}):Promise.resolve(t)},createCacheKey=function(e,t,n,a){var c=new URL(e);return a&&c.pathname.match(a)||(c.search+=(c.search?"&":"")+encodeURIComponent(t)+"="+encodeURIComponent(n)),c.toString()},isPathWhitelisted=function(e,t){if(0===e.length)return!0;var n=new URL(t).pathname;return e.some(function(e){return n.match(e)})},stripIgnoredUrlParameters=function(e,n){var t=new URL(e);return t.hash="",t.search=t.search.slice(1).split("&").map(function(e){return e.split("=")}).filter(function(t){return n.every(function(e){return!e.test(t[0])})}).map(function(e){return e.join("=")}).join("&"),t.toString()},hashParamName="_sw-precache",urlsToCacheKeys=new Map(precacheConfig.map(function(e){var t=e[0],n=e[1],a=new URL(t,self.location),c=createCacheKey(a,hashParamName,n,/\.\w{8}\./);return[a.toString(),c]}));function setOfCachedUrls(e){return e.keys().then(function(e){return e.map(function(e){return e.url})}).then(function(e){return new Set(e)})}self.addEventListener("install",function(e){e.waitUntil(caches.open(cacheName).then(function(a){return setOfCachedUrls(a).then(function(n){return Promise.all(Array.from(urlsToCacheKeys.values()).map(function(t){if(!n.has(t)){var e=new Request(t,{credentials:"same-origin"});return fetch(e).then(function(e){if(!e.ok)throw new Error("Request for "+t+" returned a response with status "+e.status);return cleanResponse(e).then(function(e){return a.put(t,e)})})}}))})}).then(function(){return self.skipWaiting()}))}),self.addEventListener("activate",function(e){var n=new Set(urlsToCacheKeys.values());e.waitUntil(caches.open(cacheName).then(function(t){return t.keys().then(function(e){return Promise.all(e.map(function(e){if(!n.has(e.url))return t.delete(e)}))})}).then(function(){return self.clients.claim()}))}),self.addEventListener("fetch",function(t){if("GET"===t.request.method){var e,n=stripIgnoredUrlParameters(t.request.url,ignoreUrlParametersMatching),a="index.html";(e=urlsToCacheKeys.has(n))||(n=addDirectoryIndex(n,a),e=urlsToCacheKeys.has(n));var c="/wp-content/plugins/anycomment/index.html";!e&&"navigate"===t.request.mode&&isPathWhitelisted(["^(?!\\/__).*"],t.request.url)&&(n=new URL(c,self.location).toString(),e=urlsToCacheKeys.has(n)),e&&t.respondWith(caches.open(cacheName).then(function(e){return e.match(urlsToCacheKeys.get(n)).then(function(e){if(e)return e;throw Error("The cached response that was expected is missing.")})}).catch(function(e){return console.warn('Couldn\'t serve response for "%s" from cache: %O',t.request.url,e),fetch(t.request)}))}});