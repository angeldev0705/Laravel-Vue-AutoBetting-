(window.webpackJsonp=window.webpackJsonp||[]).push([[25],{Zd3P:function(t,r,e){"use strict";e.r(r);var a=e("o0o1"),o=e.n(a),n=e("fpkf"),s=e("4HBT"),i=e.n(s);function l(t,r,e,a,o,n,s){try{var i=t[n](s),l=i.value}catch(t){return void e(t)}i.done?r(l):Promise.resolve(l).then(a,o)}var u={middleware:["auth","verified","2fa_not_passed"],mixins:[e("C3Ci").a],metaInfo:function(){return{title:this.$t("Two-factor authentication")}},data:function(){return{formIsValid:null,form:new i.a({one_time_password:null})}},computed:{appLogoUrl:function(){return Object(n.a)("app.logo")}},methods:{verify:function(){var t,r=this;return(t=o.a.mark((function t(){var e,a;return o.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,r.form.post("/api/user/security/2fa/verify");case 2:e=t.sent,a=e.data,r.$store.dispatch("auth/updateUser",a),r.$router.push({name:"home"});case 6:case"end":return t.stop()}}),t)})),function(){var r=this,e=arguments;return new Promise((function(a,o){var n=t.apply(r,e);function s(t){l(n,a,o,s,i,"next",t)}function i(t){l(n,a,o,s,i,"throw",t)}s(void 0)}))})()}}},c=e("KHd+"),m=e("ZUTo"),d=e.n(m),f=e("ghKu"),p=e("gzZi"),v=e("sK+t"),_=e("mdmw"),w=e("Yq0q"),h=e("pSOK"),V=e("S9TP"),b=e("rdoz"),y=e("D9m0"),g=e("L6Q5"),x=e("hlRy"),T=e("cdmR"),C=e("Kn9U"),k=Object(c.a)(u,(function(){var t=this,r=t.$createElement,e=t._self._c||r;return e("v-container",{staticClass:"fill-height",attrs:{fluid:""}},[e("v-row",{attrs:{align:"center",justify:"center"}},[e("v-col",{attrs:{cols:"12",sm:"8",md:"6",lg:"4"}},[e("v-card",{staticClass:"elevation-12"},[e("v-toolbar",{attrs:{color:"primary"}},[e("router-link",{attrs:{to:{name:"home"}}},[e("v-avatar",{attrs:{size:"40",tile:""}},[e("v-img",{attrs:{src:t.appLogoUrl}})],1)],1),t._v(" "),e("v-toolbar-title",{staticClass:"ml-2"},[t._v("\n            "+t._s(t.$t("Two-factor authentication"))+"\n          ")]),t._v(" "),e("v-spacer")],1),t._v(" "),e("v-card-text",[e("v-form",{on:{submit:function(r){return r.preventDefault(),t.verify(r)}},model:{value:t.formIsValid,callback:function(r){t.formIsValid=r},expression:"formIsValid"}},[e("v-text-field",{attrs:{label:t.$t("One-time password"),type:"text",name:"one_time_password",rules:[t.validationRequired,function(r){return t.validationMinLength(r,6)}],error:t.form.errors.has("one_time_password"),"error-messages":t.form.errors.get("one_time_password"),outlined:""},on:{keydown:function(r){return t.clearFormErrors(r,"one_time_password",t.form)}},model:{value:t.form.one_time_password,callback:function(r){t.$set(t.form,"one_time_password",r)},expression:"form.one_time_password"}}),t._v(" "),e("v-btn",{attrs:{type:"submit",color:"primary",disabled:!t.formIsValid||t.form.busy,loading:t.form.busy}},[t._v("\n              "+t._s(t.$t("Verify"))+"\n            ")])],1)],1)],1)],1)],1)],1)}),[],!1,null,null,null);r.default=k.exports;d()(k,{VAvatar:f.a,VBtn:p.a,VCard:v.a,VCardText:_.c,VCol:w.a,VContainer:h.a,VForm:V.a,VImg:b.a,VRow:y.a,VSpacer:g.a,VTextField:x.a,VToolbar:T.a,VToolbarTitle:C.a})}}]);
//# sourceMappingURL=2fa.js.map