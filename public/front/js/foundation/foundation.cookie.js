/*!
 * jQuery Cookie Plugin v1.3
 * https://github.com/carhartl/jquery-cookie
 *
 * Copyright 2011, Klaus Hartl
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.opensource.org/licenses/GPL-2.0
 *
 * Modified to work with Zepto.js by ZURB
 */
!function(a,b,c){function d(a){return a}function e(a){return decodeURIComponent(a.replace(f," "))}var f=/\+/g,g=a.cookie=function(f,h,i){if(h!==c){if(i=a.extend({},g.defaults,i),null===h&&(i.expires=-1),"number"==typeof i.expires){var j=i.expires,k=i.expires=new Date;k.setDate(k.getDate()+j)}return h=g.json?JSON.stringify(h):String(h),b.cookie=[encodeURIComponent(f),"=",g.raw?h:encodeURIComponent(h),i.expires?"; expires="+i.expires.toUTCString():"",i.path?"; path="+i.path:"",i.domain?"; domain="+i.domain:"",i.secure?"; secure":""].join("")}for(var l=g.raw?d:e,m=b.cookie.split("; "),n=0,o=m.length;o>n;n++){var p=m[n].split("=");if(l(p.shift())===f){var q=l(p.join("="));return g.json?JSON.parse(q):q}}return null};g.defaults={},a.removeCookie=function(b,c){return null!==a.cookie(b)?(a.cookie(b,null,c),!0):!1}}(Foundation.zj,document);