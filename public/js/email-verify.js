(window.webpackJsonp=window.webpackJsonp||[]).push([[52],{"+QwP":function(e,t,n){"use strict";n.r(t);var r=n("o0o1"),a=n.n(r),o=n("vDqi"),s=n.n(o);function i(e,t,n,r,a,o,s){try{var i=e[o](s),c=i.value}catch(e){return void n(e)}i.done?t(c):Promise.resolve(c).then(r,a)}var c=function(e){return Object.keys(e).map((function(t){return"".concat(t,"=").concat(e[t])})).join("&")},u={middleware:["auth","not_verified"],metaInfo:function(){return{title:this.$t("Email verification")}},beforeRouteEnter:function(e,t,n){return(r=a.a.mark((function t(){var r,o;return a.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.prev=0,t.next=3,s.a.post("/api/email/verify/".concat(e.params.id,"/").concat(e.params.hash,"?").concat(c(e.query)));case 3:r=t.sent,o=r.data,n((function(e){console.log("done"),e.$store.dispatch("auth/updateUser",o),e.$store.dispatch("message/success",{text:e.$t("Your email address is successfully verified.")}),e.$router.push({name:"home"})})),t.next=11;break;case 8:t.prev=8,t.t0=t.catch(0),n((function(e){e.$store.dispatch("message/error",{text:t.t0.response.data.message}),e.$router.push({name:"verification.index"})}));case 11:case"end":return t.stop()}}),t,null,[[0,8]])})),function(){var e=this,t=arguments;return new Promise((function(n,a){var o=r.apply(e,t);function s(e){i(o,n,a,s,c,"next",e)}function c(e){i(o,n,a,s,c,"throw",e)}s(void 0)}))})();var r}},f=n("KHd+"),p=Object(f.a)(u,(function(){var e=this.$createElement;return(this._self._c||e)("div")}),[],!1,null,null,null);t.default=p.exports}}]);
//# sourceMappingURL=email-verify.js.map