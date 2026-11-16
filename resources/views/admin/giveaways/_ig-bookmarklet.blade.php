{{--
  "Instagram → Site" yer imi. instagram.com'da, yöneticinin kendi oturumuyla çalışır ve
  Instagram'a KENDİ isteğini atmaz; sadece Instagram sayfasının zaten yüklediğini okur:
  1) Sitede aktarma penceresini açar (tıklama anında; açılır pencere engeline takılmamak için)
  2) Profil ızgarasındaki gönderileri sayfadan okuyup pencereye listeler (istek yok)
  3) Seçilen gönderiyi Instagram'da açar (sayfanın kendi tıklamasıyla), Instagram'ın yüklediği
     yorum yanıtlarını yakalar ve yorum listesini kaydırarak devamını Instagram'a yükletir
  4) Veriyi pencereye postMessage ile iletir (Instagram'ın CSP'si başka sitelere doğrudan isteği engeller)
--}}
@php
  $igTarget = route('admin.instagram.import');
  $igJs = <<<'JS'
(async()=>{
const S=__SITE__,O=new URL(S).origin;
if(!/(^|\.)instagram\.com$/.test(location.hostname)){alert("Önce instagram.com'da kulüp profilini açın (giriş yapmış olarak), sonra bu yer imine tıklayın.");return;}
if(/\/(p|reel|reels|tv)\//.test(location.pathname)&&!document.querySelector('div[role="dialog"]')){alert("Kulüp profil sayfasını açın (instagram.com/kullaniciadi), sayfayı yenileyin (F5) ve yer imine orada tıklayın.");return;}
const w=window.open(S,"ig_aktar","width=1100,height=860");
if(!w){alert("Açılır pencere engellendi. instagram.com için açılır pencerelere izin verip tekrar deneyin.");return;}
const send=m=>{try{w.postMessage(m,O)}catch(e){}};
const wait=(t,ms)=>new Promise((res,rej)=>{const h=e=>{if(e.origin===O&&e.data&&e.data.type===t){clearTimeout(k);removeEventListener("message",h);res(e.data)}};const k=setTimeout(()=>{removeEventListener("message",h);rej(new Error("Site penceresi yanıt vermedi. Pencerede admin girişi yapıp tekrar deneyin."))},ms);addEventListener("message",h)});
const sleep=ms=>new Promise(r=>setTimeout(r,ms));
const A="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_";
const toId=c=>{let n=0n;for(const ch of c.slice(0,11)){const i=A.indexOf(ch);if(i<0)break;n=n*64n+BigInt(i)}return n.toString()};
const img=m=>{const c=(m.image_versions2&&m.image_versions2.candidates)||(m.carousel_media&&m.carousel_media[0]&&m.carousel_media[0].image_versions2&&m.carousel_media[0].image_versions2.candidates)||[];return c.length?c[c.length>1?1:0].url:null};
/* Instagram'ın kendi isteklerini dinle (fetch + XHR); yanıtları değiştirmeden kopyasını okur */
const G=window.__igCap2||(window.__igCap2={subs:new Set()});
if(!G.on){G.on=1;
const bs=b=>{try{if(!b)return"";if(typeof b==="string")return b;if(b instanceof URLSearchParams)return b.toString();if(typeof FormData!=="undefined"&&b instanceof FormData)return new URLSearchParams(b).toString()}catch(e){}return""};
const hd=h=>{const o={};try{if(!h)return o;if(typeof Headers!=="undefined"&&h instanceof Headers)h.forEach((v,k)=>{o[k]=v});else if(Array.isArray(h))h.forEach(([k,v])=>{o[k]=v});else Object.assign(o,h)}catch(e){}return o};
const emit=(q,txt)=>{if(!txt||txt.length<20)return;const t=txt.replace(/^for \(;;\);/,"");let d;try{d=JSON.parse(t)}catch(e){d=t.split("\n").map(l=>{try{return JSON.parse(l)}catch(x){return null}}).filter(Boolean);if(!d.length)return}G.subs.forEach(f=>{try{f(d,q.u+"\n"+q.b,q)}catch(e){}})};
const watch=u=>/\/graphql|\/api\/v1\//.test(u);
const of=window.fetch;window.fetch=async function(i,o){const r=await of.apply(this,arguments);try{const u=typeof i==="string"?i:((i&&i.url)||"");if(watch(u)){const q={u,b:bs(o&&o.body),h:hd(o&&o.headers),m:(o&&o.method)||"GET",x:0,s:r.status};r.clone().text().then(t=>emit(q,t)).catch(()=>{})}}catch(e){}return r};
const xo=XMLHttpRequest.prototype.open,xs=XMLHttpRequest.prototype.send,xh=XMLHttpRequest.prototype.setRequestHeader;
XMLHttpRequest.prototype.open=function(m,u){this.__igu=String(u);this.__igm=m;this.__igh={};return xo.apply(this,arguments)};
XMLHttpRequest.prototype.setRequestHeader=function(k,v){try{(this.__igh=this.__igh||{})[k]=v}catch(e){}return xh.apply(this,arguments)};
XMLHttpRequest.prototype.send=function(b){if(watch(this.__igu||"")){const q={u:this.__igu,b:bs(b),h:this.__igh||{},m:this.__igm||"GET",x:1};this.addEventListener("load",()=>{try{q.s=this.status;if(this.responseType===""||this.responseType==="text")emit(q,this.responseText)}catch(e){}})}return xs.apply(this,arguments)};
}
const SKIP={caption:1,preview_comments:1,preview_child_comments:1,child_comments:1};
const walk=(o,f,d=0)=>{if(!o||typeof o!=="object"||d>60)return;if(Array.isArray(o)){for(const x of o)walk(x,f,d+1);return}f(o);for(const k in o){if(!SKIP[k])walk(o[k],f,d+1)}};
const isC=o=>typeof o.text==="string"&&o.user&&typeof o.user.username==="string"&&(o.created_at||o.created_at_utc)&&(o.pk||o.id)&&!o.parent_comment_id&&!o.code&&!("media_type" in o);
/* Profil ızgarası: sayfada zaten var */
const grid=()=>{const seen=new Set(),out=[];for(const a of document.querySelectorAll('a[href*="/p/"],a[href*="/reel/"]')){if(a.closest('div[role="dialog"]'))continue;const m=(a.getAttribute("href")||"").match(/\/(p|reel)\/([A-Za-z0-9_-]+)/);if(!m||seen.has(m[2]))continue;const im=a.querySelector("img");if(!im)continue;seen.add(m[2]);out.push({id:toId(m[2]),code:m[2],image:im.currentSrc||im.src||null,caption:im.alt||"",comments:null,type:m[1]==="reel"?"reel":(a.querySelector('svg[aria-label*="arousel" i],svg[aria-label*="ecim" i],svg[aria-label*="Albüm" i]')?"carousel":"image"),time:null,pinned:!!a.querySelector('svg[aria-label*="Pinned" i],svg[aria-label*="Sabit" i]')})}return out};
const scroller=()=>{const root=document.querySelector('div[role="dialog"]')||document.querySelector("main")||document.body;let best=null;for(const el of root.querySelectorAll("div,ul,section")){if(el.scrollHeight<=el.clientHeight+20)continue;const cs=getComputedStyle(el);if(/(auto|scroll)/.test(cs.overflowY)&&(!best||el.scrollHeight>best.scrollHeight))best=el}return best};
const more=()=>{for(const sv of document.querySelectorAll('svg[aria-label]')){if(/load more comments|daha fazla yorum|yorum yükle/i.test(sv.getAttribute("aria-label")||"")){const b=sv.closest('button,[role="button"]');if(b){b.click();return true}}}for(const b of document.querySelectorAll('button,[role="button"]')){const t=(b.textContent||"").trim();if(t.length<60&&/(view|load) (all|more).*comments|daha fazla yorum|yorumun tümünü gör|tüm yorumları gör/i.test(t)){b.click();return true}}return false};
const D={v:3,reqs:[],pages:0,v1pages:0,replayed:0,replayErr:"",more:0,scroll:0,pageInfo:null,direct:0,dialog:false,url:""};
const ck=n=>(document.cookie.match(new RegExp("(?:^|; )"+n+"=([^;]*)"))||[])[1]||"";
try{
await wait("ig-ready",300000);
const owner=((location.pathname.match(/^\/([A-Za-z0-9._]{1,30})\//)||[])[1]||"").toLowerCase();
const posts=grid().slice(0,12);
if(!posts.length)throw new Error("Sayfada gönderi bulunamadı. instagram.com'da kulüp profil sayfasını açın, gönderiler görünene kadar bekleyin ve yer imine tekrar tıklayın.");
const pic=(document.querySelector('header img')||{}).src||"";
send({type:"ig-posts",user:{username:owner,pic},posts});
const p=await wait("ig-pick",1800000);
const pre=posts.find(x=>x.id===String(p.id))||{id:String(p.id),code:p.code};
const id=pre.id,code=pre.code,got=new Map();let total=null,meta=null,pg=null;
const sub=(d,m,q)=>{const mine=m.includes(id)||m.includes(code);
if(q){const fn=(m.match(/fb_api_req_friendly_name=([^&\n]+)/)||[])[1];D.reqs.push((q.x?"xhr ":"fetch ")+(q.s||"")+" "+(fn||q.u.split("?")[0].slice(-45))+(mine?" *":""));if(D.reqs.length>40)D.reqs.shift()}
if(mine&&q&&!Array.isArray(d)&&/\/comments\//.test(q.u)){D.v1pages++;if(total===null&&typeof d.comment_count==="number")total=d.comment_count;pg=(d.next_min_id||d.next_max_id)?{q,v1:{min:d.next_min_id,max:d.next_max_id}}:{q,v1:{}}}
walk(d,o=>{if(mine&&q&&o.page_info&&Array.isArray(o.edges)&&o.edges.some(e=>e&&e.node&&isC(e.node))){pg={q,pi:o.page_info};D.pages++;D.pageInfo=o.page_info}
if(o.code===code){meta=o;if(typeof o.comment_count==="number")total=o.comment_count}else if(mine&&isC(o)){const k=String(o.pk||o.id);const prev=got.get(k);got.set(k,{username:o.user.username,text:o.text,time:o.created_at||o.created_at_utc,r:Math.max(typeof o.child_comment_count==="number"?o.child_comment_count:0,prev?prev.r:0)})}})};
const reps=()=>{let s=0;got.forEach(c=>{s+=c.r});return s};
/* Instagram'ın kendi yorum isteğini "sonraki sayfa" imleciyle tekrarla (arayüzün kaydırınca yaptığının aynısı) */
const asked=new Set();
const next=async()=>{if(!pg)return false;const{q,pi,v1}=pg;let cur=pi?(pi.has_next_page&&pi.end_cursor):(v1&&(v1.min||v1.max));if(!cur)return false;cur=typeof cur==="string"?cur:JSON.stringify(cur);if(asked.has(cur))return false;asked.add(cur);
const h=Object.assign({},q.h);
if(v1){const u=new URL(q.u,location.origin);u.searchParams.delete("min_id");u.searchParams.delete("max_id");u.searchParams.set(v1.min?"min_id":"max_id",cur);await fetch(u.toString(),{headers:h,credentials:"include"})}
else{const p=new URLSearchParams(q.b);let v;try{v=JSON.parse(p.get("variables")||"")}catch(e){D.replayErr="variables okunamadı";return false}v.after=cur;if("before" in v)v.before=null;p.set("variables",JSON.stringify(v));if(!Object.keys(h).some(k=>k.toLowerCase()==="content-type"))h["Content-Type"]="application/x-www-form-urlencoded";const r=await fetch(q.u,{method:"POST",headers:h,body:p.toString(),credentials:"include"});if(!r.ok)D.replayErr="HTTP "+r.status}
D.replayed++;return true};
G.subs.add(sub);
for(const s of document.querySelectorAll('script[type="application/json"]')){const t=s.textContent;if(t.includes(code)){try{sub(JSON.parse(t),id)}catch(e){}}}
const link=[...document.querySelectorAll('a[href*="/'+code+'/"]')].find(a=>!a.closest('div[role="dialog"]'));
if(!link)throw new Error("Gönderi sayfada bulunamadı. Profil sayfasını yenileyip tekrar deneyin.");
link.click();
send({type:"ig-progress",count:0,total:0,phase:"open"});
let idle=0,last=-1;const t0=Date.now();await sleep(2500);
while(Date.now()-t0<1800000){const n=got.size,rp=reps();send({type:"ig-progress",count:n+rp,total:total||0});
if(total!==null&&n+rp>=total)break;if(total===0&&Date.now()-t0>6000)break;
if(n===last)idle++;else{idle=0;last=n}
if(pg&&((pg.pi&&!pg.pi.has_next_page)||(pg.v1&&!pg.v1.min&&!pg.v1.max))&&idle>=3)break;if(idle>=10)break;
let did=false;
/* Sayfanın kendi yorum isteği görünmediyse yorumları doğrudan Instagram'ın yorum adresinden oku (bir kez başlatılır, sonra imleçle devam eder) */
if(!pg&&!got.size&&!D.direct&&idle>=3){D.direct=1;D.dialog=!!document.querySelector('div[role="dialog"]');D.url=location.pathname;try{const r=await fetch("/api/v1/media/"+id+"/comments/?can_support_threading=true&permalink_enabled=false",{headers:{"X-IG-App-ID":"936619743392459","X-ASBD-ID":"129477","X-CSRFToken":ck("csrftoken"),"X-Requested-With":"XMLHttpRequest","Accept":"application/json"},credentials:"include"});D.direct=r.status;did=true}catch(e){D.replayErr="direct: "+(e&&e.message||e)}}
if(!did)try{did=await next()}catch(e){D.replayErr=String(e&&e.message||e)}
if(!did){if(more())D.more++;const sc=scroller();if(sc){sc.scrollTop=sc.scrollHeight;D.scroll++}}
await sleep(1200+Math.random()*1000)}
G.subs.delete(sub);
const replies=reps(),out=[...got.values()].map(c=>({username:c.username,text:c.text,time:c.time}));
if(!out.length&&total)throw new Error("Yorumlar yakalanamadı. instagram.com'a giriş yaptığınızdan emin olun, profil sayfasını yenileyip (F5) tekrar deneyin.");
const mm=meta||{};
send({type:"ig-data",post:{media_id:id,shortcode:code,caption:(mm.caption&&mm.caption.text)||pre.caption||"",image:img(mm)||pre.image||null,owner:(mm.user&&mm.user.username)||owner||null,comment_count:total,replies},comments:out,diag:D});
}catch(e){send({type:"ig-error",message:e.message,diag:D});alert("Instagram aktarımı: "+e.message)}
})();
JS;
  $igBookmarklet = 'javascript:' . rawurlencode(str_replace('__SITE__', json_encode($igTarget), $igJs));
@endphp
<a href="{{ $igBookmarklet }}" class="btn btn-primary ig-bm" onclick="alert('Bu butonu tıklamayın: yer imleri çubuğuna sürükleyin. Sonra instagram.com açıkken oradan tıklayın.'); return false;" style="display: inline-flex; align-items: center; gap: 6px; cursor: grab;">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>
  <span>Instagram → Site</span>
</a>
