(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-1a8478d2"],{"2ad1":function(e,t){},d251:function(e,t,o){"use strict";o.r(t);var l=function(){var e=this,t=e._self._c;return t("div",{staticClass:"wbs-content-inner"},["base"===e.editing?t("div",{staticClass:"wbs-main"},[e.isLoaded?t("el-tabs",{model:{value:e.actTab,callback:function(t){e.actTab=t},expression:"actTab"}},[t("el-tab-pane",{attrs:{label:"CDN管理",name:"cdn"}},[t("CdnOptions",{attrs:{opt:e.opt}})],1),t("el-tab-pane",{attrs:{label:"预加载",name:"preload"}},[t("LoadPreload",{attrs:{opt:e.opt},on:{editing:function(t){e.editing=t}}})],1),t("el-tab-pane",{attrs:{label:"懒加载",name:"lazyload"}},[t("LoadLazyload",{attrs:{opt:e.opt}})],1),t("el-tab-pane",{attrs:{label:"JS管理",name:"js"}},[t("LoadJs",{attrs:{opt:e.opt},on:{editing:function(t){e.editing=t}}})],1)],1):e._e()],1):e._e(),t("wbs-ctrl-bar",{directives:[{name:"show",rawName:"v-show",value:e.is_pro&&e.isLoaded&&"base"===e.editing,expression:"is_pro && isLoaded && editing === 'base'"}],on:{submit:e.updateData}}),"res"===e.editing?t("PreloadResItems",{attrs:{opt:e.opt.preload_resource},on:{save:function(t){e.opt.preload_resource=t,e.editing="base",e.updateData()},cancel:function(t){e.editing="base"}}}):e._e(),"url"===e.editing?t("PreloadUrlItems",{attrs:{opt:e.opt.preload_preconnect},on:{save:function(t){e.opt.preload_preconnect=t,e.editing="base",e.updateData()},cancel:function(t){e.editing="base"}}}):e._e(),"javascript"===e.editing?t("FormJsOptions",{attrs:{opt:e.opt},on:{save:function(t){e.opt=t,e.editing="base",e.updateData()},cancel:function(t){e.editing="base"}}}):e._e(),"custom-js"===e.editing?t("FormCustomJs",{attrs:{opt:e.opt},on:{save:function(t){e.opt=t,e.editing="base",e.updateData()},cancel:function(t){e.editing="base"}}}):e._e()],1)},a=[],s=function(){var e=this,t=e._self._c;return t("div",{staticClass:"opt-group"},[t("div",{staticClass:"opt-group-hd"},[e._m(0),t("div",{staticClass:"group-description"},[e._v(" 启用CDN网络，以多点分发网站静态资源，提升网站加载速度。 ")]),e.$cnf.is_pro?e._e():t("div",{staticClass:"mt"},[t("el-button",{attrs:{type:"primary"},on:{click:function(t){return e.getPro()}}},[e._v("升级至PRO")])],1)]),t("wbs-opt-box",{attrs:{name:"启用CDN重写",desc:"使用CDN URL重写网站URL地址，启用后可在下方CDN链接配置您的CDN链接。","doc-link":"https://www.wbolt.com/aliyun-cdn.html"},model:{value:e.opt.switch,callback:function(t){e.$set(e.opt,"switch",t)},expression:"opt.switch"}}),t("wbs-opt-box",{staticClass:"with-input-ctrl",attrs:{name:"CDN链接",desc:"输入您的CDN链接，需包含完整协议，但请勿输入反斜杠。注：类似cloudflare，无需配置CDN链接。","doc-link":"https://www.wbolt.com/aliyun-cdn.html"},scopedSlots:e._u([{key:"ctrlItems",fn:function(){return[t("el-input",{attrs:{size:"small",placeholder:"例如，https://static.yourdomain.com"},model:{value:e.opt.cdn_url,callback:function(t){e.$set(e.opt,"cdn_url",t)},expression:"opt.cdn_url"}})]},proxy:!0}])}),t("wbs-opt-box",{staticClass:"with-input-ctrl",attrs:{name:"包含的目录",desc:"支持自定义CDN重写目录，以逗号(,)分割，默认为wp-content和wp-includes。","doc-link":"https://www.wbolt.com/aliyun-cdn.html#include-excluded"},scopedSlots:e._u([{key:"ctrlItems",fn:function(){return[t("el-input",{attrs:{size:"small",placeholder:"wp-content,wp-includes"},model:{value:e.opt.cdn_include,callback:function(t){e.$set(e.opt,"cdn_include",t)},expression:"opt.cdn_include"}})]},proxy:!0}])}),t("wbs-opt-box",{staticClass:"with-input-ctrl",attrs:{name:"排除列表",desc:"可以将指定目录及文件扩展名排除在CDN重写之外，以逗号(,)分隔。默认值为.php。","doc-link":"https://www.wbolt.com/aliyun-cdn.html#include-excluded"},scopedSlots:e._u([{key:"ctrlItems",fn:function(){return[t("el-input",{attrs:{size:"small",placeholder:".php"},model:{value:e.opt.cdn_exclude,callback:function(t){e.$set(e.opt,"cdn_exclude",t)},expression:"opt.cdn_exclude"}})]},proxy:!0}])}),t("wb-prompt",{staticClass:"mt"})],1)},n=[function(){var e=this,t=e._self._c;return t("h3",{staticClass:"group-name"},[t("strong",[e._v("CDN管理")])])}],i={name:"CdnOptions",props:["opt"],provide(){return{needPro:!0}},inject:["getPro"]},r=i,c=o("2877"),p=Object(c["a"])(r,s,n,!1,null,null,null),d=p.exports,m=function(){var e=this,t=e._self._c;return t("div",{staticClass:"opt-group"},[t("div",{staticClass:"opt-group-hd"},[e._m(0),t("div",{staticClass:"group-description"},[e._v(" 通过DNS预取、预连接及预加载关键词和静态资源等方式，能够改善网站的用户浏览体验。 ")]),e.$cnf.is_pro?e._e():t("div",{staticClass:"mt"},[t("el-button",{attrs:{type:"primary"},on:{click:function(t){return e.getPro()}}},[e._v("升级至PRO")])],1)]),e._l(e.items,(function(o){return t("wbs-opt-box",{key:o.field,attrs:{name:o.name,desc:o.desc,"doc-link":o.doc},model:{value:e.opt[o.field],callback:function(t){e.$set(e.opt,o.field,t)},expression:"opt[item.field]"}},["textarea"==o.type?t("template",{slot:"ctrlItems"},[t("el-input",{attrs:{type:"textarea",autosize:{minRows:2,maxRows:4},placeholder:o.placeholder},model:{value:e.opt[o.field],callback:function(t){e.$set(e.opt,o.field,t)},expression:"opt[item.field]"}}),o.memo?t("div",{staticClass:"mt",domProps:{innerHTML:e._s(o.memo)}}):e._e()],1):"preconnect"==o.type?t("template",{slot:"ctrlItems"},[t("el-button",{attrs:{size:"small",type:"primary"},on:{click:function(t){return e.$emit("editing","url")}}},[e._v("管理")]),o.memo?t("div",{staticClass:"mt",domProps:{innerHTML:e._s(o.memo)}}):e._e()],1):"critical_image"==o.type?t("template",{slot:"ctrlItems"},[t("el-select",{attrs:{size:"mini",placeholder:"请选择"},model:{value:e.opt[o.field],callback:function(t){e.$set(e.opt,o.field,t)},expression:"opt[item.field]"}},e._l([0,1,2,3,4,5],(function(e,o){return t("el-option",{key:e,attrs:{label:e,value:e}})})),1)],1):"resource"==o.type?t("template",{slot:"ctrlItems"},[t("el-button",{attrs:{size:"small",type:"primary"},on:{click:function(t){return e.$emit("editing","res")}}},[e._v("管理")]),o.memo?t("div",{staticClass:"mt",domProps:{innerHTML:e._s(o.memo)}}):e._e()],1):e._e()],2)}))],2)},u=[function(){var e=this,t=e._self._c;return t("h3",{staticClass:"group-name"},[t("strong",[e._v("预加载")])])}],_={name:"LoadPreload",props:["opt"],provide(){return{needPro:!0}},inject:["getPro"],created(){const e=this;e.opt["preload_preconnect"]||(e.opt["preload_preconnect"]=[],e.opt["preload_preconnect"].push({url:"",cross:0})),e.opt["preload_resource"]&&"0"!=e.opt["preload_resource"]||(e.opt["preload_resource"]=[],e.opt["preload_resource"].push({url:"",cross:0,type:"",device:"all",match:""}))},data(){return{items:{preload_instant_page:{name:"启用Instant Page",desc:"Instant.page是一个免费的开源库，它使用即时预加载，这意味着它会在用户点击页面之前预加载页面。仅当用户很有可能访问页面时才会预加载页面，并且仅预加载HTML，尊重您的用户和服务器的带宽和CPU。它使用被动事件侦听器，以便您的页面保持流畅，并且在用户启用数据保护程序时不会预加载。",doc:"https://www.wbolt.com/instant-page.html",field:"preload_instant_page"},preload_dns_prefetch:{name:"DNS预取",desc:"使用DNS预取，这允许浏览器在后台在页面上执行DNS查找。填写完整域名即可，比如CDN域名，统计代码域名等。",doc:"https://www.wbolt.com/dns-prefetch.html",field:"preload_dns_prefetch",placeholder:"static.domain.com",type:"textarea",memo:"一行一条记录,格式：//example.com"},preload_preconnect:{name:"预连接",desc:"预连接是一种资源提示，它告诉浏览器与浏览器尚未确定需要建立的域建立主动 HTTP 连接。",doc:"https://www.wbolt.com/what-is-preconnect.html",field:"preload_preconnect",type:"preconnect"},preload_critical_image:{name:"预载关键图",desc:"预加载主要图片（首屏图片）有助于减少Core Web Vitals中的最大内容绘制(LCP) 时间。这些通常是图像，如徽标、博客文章上的特色图像、登录页面上的Hero图像等。",doc:"https://www.wbolt.com/preload-critical-images.html",field:"preload_critical_image",type:"critical_image"},preload_resource:{name:"预载资源",desc:"预加载是一种资源提示，它告诉浏览器在浏览器确定它需要发出请求之前请求内容。这可以提高性能，因为当浏览器确实意识到它需要请求一个已经预加载的资源时，它提前准备好了。",doc:"https://www.wbolt.com/preload-key-requests.html",field:"preload_resource",type:"resource"}}}},methods:{deletePreRow(e,t){const o=this;o.opt["preload_preconnect"].splice(t,1)},addPreRow(){const e=this;e.opt["preload_preconnect"].push({url:"",cross:0})},deleteResRow(e,t){const o=this;o.opt["preload_resource"].splice(t,1)},addResRow(){const e=this;e.opt["preload_resource"].push({url:"",cross:0,type:"",device:"all",match:""})}}},f=_,b=Object(c["a"])(f,m,u,!1,null,null,null),v=b.exports,h=function(){var e=this,t=e._self._c;return t("div",{staticClass:"opt-group"},[t("div",{staticClass:"opt-group-hd"},[e._m(0),t("div",{staticClass:"group-description"},[e._v(" 对WordPress网站页面的图像添加懒加载属性，可以大幅度提升网站首次加载的速度，提升网站用户体验。 ")]),e.$cnf.is_pro?e._e():t("div",{staticClass:"mt"},[t("el-button",{attrs:{type:"primary"},on:{click:function(t){return e.getPro()}}},[e._v("升级至PRO")])],1)]),e._l(e.items,(function(o){return t("wbs-opt-box",{key:o.field,attrs:{name:o.name,desc:o.desc,"doc-link":o.doc},model:{value:e.opt[o.field],callback:function(t){e.$set(e.opt,o.field,t)},expression:"opt[item.field]"}},["textarea"==o.type?t("template",{slot:"ctrlItems"},[t("el-input",{attrs:{type:"textarea",autosize:{minRows:2,maxRows:4},placeholder:o.placeholder},model:{value:e.opt[o.field],callback:function(t){e.$set(e.opt,o.field,t)},expression:"opt[item.field]"}}),o.memo?t("div",{staticClass:"mt",domProps:{innerHTML:e._s(o.memo)}}):e._e()],1):"text"==o.type?t("template",{slot:"ctrlItems"},[t("el-input",{attrs:{size:"small",placeholder:o.placeholder},model:{value:e.opt[o.field],callback:function(t){e.$set(e.opt,o.field,t)},expression:"opt[item.field]"}}),o.memo?t("div",{staticClass:"mt",domProps:{innerHTML:e._s(o.memo)}}):e._e()],1):"select"==o.type?t("template",{slot:"ctrlItems"},[t("el-select",{attrs:{placeholder:"请选择",size:"mini"},model:{value:e.opt[o.field],callback:function(t){e.$set(e.opt,o.field,t)},expression:"opt[item.field]"}},e._l(o.cnf.options,(function(e,o){return t("el-option",{key:e,attrs:{label:e,value:e}})})),1),o.memo?t("div",{staticClass:"mt",domProps:{innerHTML:e._s(o.memo)}}):e._e()],1):e._e()],2)}))],2)},w=[function(){var e=this,t=e._self._c;return t("h3",{staticClass:"group-name"},[t("strong",[e._v("懒加载")])])}],y={name:"LoadLazyload",props:["opt"],provide(){return{needPro:!0}},inject:["getPro"],data(){return{items:[{name:"图像懒加载",desc:"延迟加载（又称懒加载）是一种加载可见内容的优化技术，即延迟下载和呈现在首屏下方需要显示的内容。延迟加载不是一次加载所有图像，而是仅下载用户屏幕上可见的图像。",doc:"https://www.wbolt.com/wordpress-lazy-load-on-images-and-videos.html",field:"lazy_load_image"},{name:"跳过图像数量",desc:"页面首屏图像无需执行懒加载，比如网站首屏的页面Logo及首张轮播图，页眉的装饰图，文章页面的特色图片等。",doc:"https://www.wbolt.com/above-the-fold.html",field:"lazy_skip_image_num",type:"select",cnf:{options:[0,1,2,3,4,5]}},{name:"iFrame和视频懒加载",desc:"延迟加载iframe和视频。这包括在线视频网站嵌入视频、嵌入地图和任何类型的iframe。这也适用于使用video标签嵌入文件URL的视频。",doc:"https://www.wbolt.com/wordpress-lazy-load-on-images-and-videos.html",field:"lazy_frame_video"},{name:"懒加载例外列表",desc:'有时您可能希望从懒加载中排除特定图像。比如网站Logo，常规的首屏CSS图片等。添加图片链接或者图片class属性（class="do-not-lazyload"）来标记例外。',doc:"https://www.wbolt.com/wordpress-lazy-load-on-images-and-videos.html",field:"lazy_exclude",placeholder:"example.png",type:"textarea",memo:"一行一个"},{name:"临界点",desc:"默认值为最佳方案，但增加一定像素或者百分比，允许图像在viewpoint可见之前加载，用户体验可能会更佳。",doc:"https://www.wbolt.com/above-the-fold.html",field:"lazy_viewpoint",placeholder:"0px",type:"text"},{name:"DOM监控",desc:"监视DOM的变化并动态延迟加载新添加的元素。这提高了在加载页面时使用无限滚动的第三方插件和主题的兼容性。",doc:"",field:"lazy_new_dom"},{name:"补充图像尺寸",desc:"现代浏览器根据其宽度和高度属性设置图像的默认纵横比。如果您的图像缺少这些属性，可能会导致布局偏移，从而触发Core Web Vitals中的 CLS警告：图像元素没有明确的宽度和高度。",doc:"https://www.wbolt.com/cumulative-layout-shift.html",field:"lazy_fix_image"},{name:"图像加载效果",desc:"向下滚动页面时为延时加载图像添加淡入效果，更平滑的过渡，而不是在延迟加载后立即呈现。",doc:"",field:"lazy_image_fade_in"}]}}},x=y,g=Object(c["a"])(x,h,w,!1,null,null,null),k=g.exports,C=function(){var e=this,t=e._self._c;return t("div",[t("div",{staticClass:"opt-group"},[t("div",{staticClass:"opt-group-hd"},[e._m(0),t("div",{staticClass:"group-description"},[e._v(" JS管理允许管理页面加载JS脚本，JS加载方式及页面自定义JS等。 ")]),e.$cnf.is_pro?e._e():t("div",{staticClass:"mt"},[t("el-button",{attrs:{type:"primary"},on:{click:function(t){return e.getPro()}}},[e._v("升级至PRO")])],1)]),e._e(),t("wbs-opt-box",{attrs:{name:"JavaScript",desc:"站长可以根据网站实际情况，为网站所需的JavaScript文件添加defer属性或者delay处理逻辑，以提升网站性能。","doc-link":"https://www.wbolt.com/javascript-defer-delay.html"},scopedSlots:e._u([{key:"ctrlItems",fn:function(){return[t("el-button",{attrs:{type:"primary",size:"small"},on:{click:function(t){return e.$emit("editing","javascript")}}},[e._v("设置细则")])]},proxy:!0}])}),t("wbs-opt-box",{attrs:{name:"自定义JS",desc:"方便站长填入额外的JavaScript代码，比如统计代码JS等。一般情况下，添加自定义JS放置于footer。","doc-link":"https://www.wbolt.com/critical-rendering-path.html"},scopedSlots:e._u([{key:"ctrlItems",fn:function(){return[t("el-button",{attrs:{type:"primary",size:"small"},on:{click:function(t){return e.$emit("editing","custom-js")}}},[e._v("设置细则")])]},proxy:!0}])})],1)])},j=[function(){var e=this,t=e._self._c;return t("h3",{staticClass:"group-name"},[t("strong",[e._v("JS管理")])])}],$={name:"LoadLazyload",props:["opt"],provide(){return{needPro:!0}},inject:["getPro"],data(){return{items:[{name:"JS管理器",desc:"",doc:"",field:"a"},{name:"iFrame和视频懒加载",desc:"-",doc:"",field:"b"},{name:"懒加载例外列表",desc:"-",doc:"",field:"c"},{name:"临界点",desc:"-",doc:"",field:"d"},{name:"DOM监控",desc:"-",doc:"",field:"e"},{name:"添加图像尺寸",desc:"-",doc:"",field:"f"},{name:"图像加载效果",desc:"-",doc:"",field:"h"}]}}},P=$,D=Object(c["a"])(P,C,j,!1,null,null,null),L=D.exports,S=function(){var e=this,t=e._self._c;return t("div",{staticClass:"wbs-main"},[e._m(0),t("el-form",{ref:"form",attrs:{inline:!0,"label-width":"w6em",size:"small"}},[e._l(e.items,(function(o,l){return t("div",{key:"resource-url-"+l,staticClass:"wbs-opt-rec"},[t("el-form-item",{attrs:{label:"URL"}},[t("el-input",{staticStyle:{width:"200px"},attrs:{placeholder:"https://example.com/sample.js"},model:{value:o.url,callback:function(t){e.$set(o,"url",t)},expression:"r.url"}})],1),t("el-form-item",{staticClass:"mr"},[t("el-checkbox",{attrs:{"true-label":"1","false-label":"0"},model:{value:o.cross,callback:function(t){e.$set(o,"cross",t)},expression:"r.cross"}},[e._v("CrossOrigin")])],1),t("el-form-item",[t("el-select",{attrs:{placeholder:"请选择类型"},model:{value:o.type,callback:function(t){e.$set(o,"type",t)},expression:"r.type"}},e._l(e.cnf.type,(function(e,o){return t("el-option",{key:e.id,attrs:{label:e.name,value:e.id}})})),1)],1),t("el-form-item",[t("el-select",{attrs:{placeholder:"请选择终端"},model:{value:o.device,callback:function(t){e.$set(o,"device",t)},expression:"r.device"}},e._l(e.cnf.device,(function(e,o){return t("el-option",{key:e.id,attrs:{label:e.name,value:e.id}})})),1)],1),t("el-form-item",{attrs:{label:"加载位置"}},[t("el-input",{attrs:{placeholder:"输入post_id"},model:{value:o.location,callback:function(t){e.$set(o,"location",t)},expression:"r.location"}})],1),t("el-form-item",[0!=e.max?t("el-button",{attrs:{type:"danger",plain:"",icon:"el-icon-delete",circle:""},on:{click:function(t){return e.deleteRow(o,l)}}}):e._e(),l==e.max?t("el-button",{attrs:{type:"primary",plain:"",icon:"el-icon-plus",circle:""},on:{click:function(t){return e.addRow()}}}):e._e()],1)],1)})),t("el-form-item",[t("el-button",{attrs:{type:"primary"},on:{click:function(t){return e.$emit("save",e.opt)}}},[e._v("保存")]),t("el-button",{on:{click:function(t){return e.$emit("cancel")}}},[e._v("取消")])],1)],2),e.cnf.memo?t("wb-key-point",{attrs:{content:e.cnf.memo}}):e._e()],1)},z=[function(){var e=this,t=e._self._c;return t("h3",{staticClass:"sc-header"},[t("strong",[e._v("预载资源管理")])])}],R={name:"PreloadResItems",props:["opt"],data(){return{items:[],cnf:{type:[{id:"",name:"选择类型"},{id:"audio",name:"Audio"},{id:"document",name:"Document"},{id:"embed",name:"Embed"},{id:"fetch",name:"Fetch"},{id:"font",name:"Font"},{id:"image",name:"Image"},{id:"object",name:"Object"},{id:"script",name:"Script"},{id:"style",name:"Style"},{id:"track",name:"track"},{id:"worker",name:"Worker"},{id:"video",name:"Video"}],device:[{id:"all",name:"全部终端"},{id:"mobile",name:"手机"},{id:"desktop",name:"电脑"}]}}},mounted(){this.items=this.opt,0==this.items.length&&this.addRow(),window.scrollTo(0,0)},methods:{deleteRow(e,t){this.items.splice(t,1)},addRow(){this.items.push({url:"",cross:0,type:"",device:"all",match:""})}},computed:{max(){return this.items.length-1}}},J=R,O=o("2ad1"),I=o.n(O),N=Object(c["a"])(J,S,z,!1,null,null,null);"function"===typeof I.a&&I()(N);var T=N.exports,F=function(){var e=this,t=e._self._c;return t("div",{staticClass:"wbs-main"},[e._m(0),t("el-form",{ref:"form",attrs:{inline:!0,"label-width":"w6em",size:"small"}},[e._l(e.items,(function(o,l){return t("div",{key:"resource-url-"+l,staticClass:"wbs-opt-rec"},[t("el-form-item",{attrs:{label:"URL"}},[t("el-input",{attrs:{placeholder:"https://example.com"},model:{value:o.url,callback:function(t){e.$set(o,"url",t)},expression:"r.url"}})],1),t("el-form-item",[t("el-checkbox",{attrs:{"true-label":"1","false-label":"0"},model:{value:o.cross,callback:function(t){e.$set(o,"cross",t)},expression:"r.cross"}},[e._v("CrossOrigin")])],1),t("el-form-item",[0!=e.max?t("el-button",{attrs:{type:"danger",plain:"",icon:"el-icon-delete",circle:""},on:{click:function(t){return e.deleteRow(o,l)}}}):e._e(),l==e.max?t("el-button",{attrs:{type:"primary",plain:"",icon:"el-icon-plus",circle:""},on:{click:function(t){return e.addRow()}}}):e._e()],1)],1)})),t("el-form-item",[t("el-button",{attrs:{type:"primary"},on:{click:function(t){return e.$emit("save",e.opt)}}},[e._v("保存")]),t("el-button",{on:{click:function(t){return e.$emit("cancel")}}},[e._v("取消")])],1)],2),e.cnf.memo?t("wb-key-point",{attrs:{content:e.cnf.memo}}):e._e()],1)},M=[function(){var e=this,t=e._self._c;return t("h3",{staticClass:"sc-header"},[t("strong",[e._v("预连接管理")])])}],H={name:"PreloadUrlItems",props:["opt"],data(){return{items:[],cnf:{}}},mounted(){this.items=this.opt,0==this.items.length&&this.addRow(),window.scrollTo(0,0)},methods:{deleteRow(e,t){this.items.splice(t,1)},addRow(){this.items.push({url:"",cross:0})}},computed:{max(){return this.items.length-1}}},U=H,W=Object(c["a"])(U,F,M,!1,null,null,null),q=W.exports,V=function(){var e=this,t=e._self._c;return t("div",{staticClass:"wbs-main"},[e._m(0),t("el-form",{ref:"form",attrs:{size:"small",model:e.opt,"label-width":"160px"}},[t("el-form-item",{attrs:{label:"Defer JavaScript"}},[t("el-switch",{attrs:{"active-value":"1","inactive-value":"0"},model:{value:e.opt.js_defer,callback:function(t){e.$set(e.opt,"js_defer",t)},expression:"opt.js_defer"}})],1),e.opt.js_defer?t("el-form-item",{attrs:{label:"包含JQuery"}},[t("el-switch",{attrs:{"active-value":"1","inactive-value":"0"},model:{value:e.opt.js_defer_jquery,callback:function(t){e.$set(e.opt,"js_defer_jquery",t)},expression:"opt.js_defer_jquery"}})],1):e._e(),e.opt.js_defer?t("el-form-item",{attrs:{label:"Defer例外列表"}},[t("el-input",{attrs:{type:"textarea"},model:{value:e.opt.js_defer_exclude,callback:function(t){e.$set(e.opt,"js_defer_exclude",t)},expression:"opt.js_defer_exclude"}})],1):e._e(),t("el-form-item",{attrs:{label:"Delay JavaScript"}},[t("el-switch",{attrs:{"active-value":"1","inactive-value":"0"},model:{value:e.opt.js_delay,callback:function(t){e.$set(e.opt,"js_delay",t)},expression:"opt.js_delay"}})],1),e.opt.js_delay?t("el-form-item",{attrs:{label:"Delay方式"}},[t("el-select",{attrs:{placeholder:"请选择"},model:{value:e.opt.js_delay_type,callback:function(t){e.$set(e.opt,"js_delay_type",t)},expression:"opt.js_delay_type"}},[t("el-option",{attrs:{label:"全部JS",value:"1"}}),t("el-option",{attrs:{label:"指定JS",value:"2"}})],1)],1):e._e(),e.opt.js_delay?t("el-form-item",{attrs:{label:1==e.opt.js_delay_type?"Delay例外列表":"Delay指定JS"}},[t("el-input",{attrs:{type:"textarea"},model:{value:e.opt.js_delay_exclude,callback:function(t){e.$set(e.opt,"js_delay_exclude",t)},expression:"opt.js_delay_exclude"}})],1):e._e(),e.opt.js_delay?t("el-form-item",{attrs:{label:"延迟时间"}},[t("el-select",{attrs:{placeholder:"请选择"},model:{value:e.opt.js_delay_time,callback:function(t){e.$set(e.opt,"js_delay_time",t)},expression:"opt.js_delay_time"}},[t("el-option",{attrs:{label:"不限定",value:""}}),t("el-option",{attrs:{label:"1秒",value:"1"}}),t("el-option",{attrs:{label:"2秒",value:"2"}}),t("el-option",{attrs:{label:"3秒",value:"3"}}),t("el-option",{attrs:{label:"4秒",value:"4"}}),t("el-option",{attrs:{label:"5秒",value:"5"}}),t("el-option",{attrs:{label:"6秒",value:"6"}}),t("el-option",{attrs:{label:"7秒",value:"7"}}),t("el-option",{attrs:{label:"8秒",value:"8"}}),t("el-option",{attrs:{label:"9秒",value:"9"}}),t("el-option",{attrs:{label:"10秒",value:"10"}})],1)],1):e._e(),t("el-form-item",[t("el-button",{attrs:{type:"primary"},on:{click:function(t){return e.$emit("save",e.opt)}}},[e._v("保存")]),t("el-button",{on:{click:function(t){return e.$emit("cancel")}}},[e._v("取消")])],1)],1)],1)},A=[function(){var e=this,t=e._self._c;return t("h3",{staticClass:"sc-header"},[t("strong",[e._v("JavaScript")])])}],B={name:"FormJsOptions",props:["opt"],provide(){return{needPro:!0}},mounted(){window.scrollTo(0,0)}},E=B,Q=Object(c["a"])(E,V,A,!1,null,null,null),G=Q.exports,K=function(){var e=this,t=e._self._c;return t("div",{staticClass:"wbs-main"},[e._m(0),t("el-form",{ref:"form",attrs:{size:"small",model:e.opt,"label-width":"8em"}},[t("el-form-item",{attrs:{label:"加入头部代码"}},[t("el-input",{attrs:{type:"textarea"},model:{value:e.opt.js_custom_head,callback:function(t){e.$set(e.opt,"js_custom_head",t)},expression:"opt.js_custom_head"}})],1),t("el-form-item",{attrs:{label:"加入主体代码"}},[t("el-input",{attrs:{type:"textarea"},model:{value:e.opt.js_custom_body,callback:function(t){e.$set(e.opt,"js_custom_body",t)},expression:"opt.js_custom_body"}})],1),t("el-form-item",{attrs:{label:"加入页脚代码"}},[t("el-input",{attrs:{type:"textarea"},model:{value:e.opt.js_custom_footer,callback:function(t){e.$set(e.opt,"js_custom_footer",t)},expression:"opt.js_custom_footer"}})],1),t("el-form-item",[t("el-button",{attrs:{type:"primary"},on:{click:function(t){return e.$emit("save",e.opt)}}},[e._v("保存")]),t("el-button",{on:{click:function(t){return e.$emit("cancel")}}},[e._v("取消")])],1)],1)],1)},X=[function(){var e=this,t=e._self._c;return t("h3",{staticClass:"sc-header"},[t("strong",[e._v("自定义JS")])])}],Y={name:"FormCustomJs",props:["opt"],provide(){return{needPro:!0}},mounted(){window.scrollTo(0,0)}},Z=Y,ee=Object(c["a"])(Z,K,X,!1,null,null,null),te=ee.exports,oe={name:"WPTurboCdn",components:{CdnOptions:d,LoadPreload:v,LoadLazyload:k,LoadJs:L,PreloadResItems:T,PreloadUrlItems:q,FormJsOptions:G,FormCustomJs:te},data(){const e=this;return{wb_cnf:e.$cnf,formChanged:0,isLoaded:0,loading:0,is_pro:e.$cnf.is_pro,opt:{},actTab:"cdn",editing:"base"}},created(){const e=this;e.loadData(),e.$cnf.is_pro&&(e.$verify((t,o)=>{e.$cnf.is_pro=t,e.is_pro=t}),e.$isPrdActive(e.$WB))},methods:{loadData(){const e=this;e.loading=e.$wbui.loading(),e.$api.getData({_ajax_nonce:_wb_wpturbo_ajax_nonce,action:e.$cnf.action.act,op:"cdn_setting"}).then(t=>{e.$wbui.close(e.loading),e.opt=t["opt"],e.isLoaded=1})},updateData(e){const t=this;if(!t.$cnf.is_pro)return;const o={_ajax_nonce:_wb_wpturbo_ajax_nonce,action:t.$cnf.action.act,op:"cdn_update",opt:t.opt};t.$api.saveData(o).then(o=>{t.$wbui.toast("设置保存成功"),t.formChanged=1,e&&e()})}},watch:{opt:{handler(){this.formChanged++},deep:!0}},beforeRouteLeave(e,t,o){const l=this;l.formChanged>1?l.$wbui.open({content:"您修改的设置尚未保存，确定离开此页面吗？",btn:["保存并离开","放弃修改"],yes(){return o(!1),l.updateData((function(){o()})),!1},no(){return o(),!1}}):o()}},le=oe,ae=Object(c["a"])(le,l,a,!1,null,null,null);t["default"]=ae.exports}}]);