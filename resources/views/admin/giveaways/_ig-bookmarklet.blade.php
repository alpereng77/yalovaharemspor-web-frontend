{{--
  "Instagram → Site" yer imi. instagram.com'da, yöneticinin kendi oturumuyla çalışır:
  1) Sitede aktarma penceresini açar (tıklama anında; açılır pencere engeline takılmamak için)
  2) Gönderi sayfasındaysa o gönderiyi, değilse hesabın son gönderilerini pencereye listeler
  3) Seçilen gönderinin tüm yorumlarını sayfa sayfa, araya bekleme koyarak okur
  4) Veriyi pencereye postMessage ile iletir (Instagram'ın CSP'si başka sitelere doğrudan isteği engeller)
--}}
@php
  $igTarget = route('admin.instagram.import');
  $igJs = <<<'JS'
(async()=>{
const S=__SITE__,O=new URL(S).origin;
if(!/(^|\.)instagram\.com$/.test(location.hostname)){alert("Önce instagram.com'u açın (giriş yapmış olarak), sonra bu yer imine tıklayın.");return;}
const w=window.open(S,"ig_aktar","width=1100,height=860");
if(!w){alert("Açılır pencere engellendi. instagram.com için açılır pencerelere izin verip tekrar deneyin.");return;}
const send=m=>{try{w.postMessage(m,O)}catch(e){}};
const wait=(t,ms)=>new Promise((res,rej)=>{const h=e=>{if(e.origin===O&&e.data&&e.data.type===t){clearTimeout(k);removeEventListener("message",h);res(e.data)}};const k=setTimeout(()=>{removeEventListener("message",h);rej(new Error("Site penceresi yanıt vermedi. Pencerede admin girişi yapıp tekrar deneyin."))},ms);addEventListener("message",h)});
const H={"X-IG-App-ID":"936619743392459","X-Requested-With":"XMLHttpRequest","Accept":"application/json"};
const j=async u=>{const r=await fetch(u,{headers:H,credentials:"include"});if(r.status===401||r.status===403)throw new Error("Instagram oturumu bulunamadı. instagram.com'a giriş yapıp tekrar deneyin.");if(r.status===429)throw new Error("Instagram çok fazla istek olduğunu söyledi. Birkaç dakika bekleyip tekrar deneyin.");if(!r.ok){const e=new Error("Instagram yanıtı: HTTP "+r.status+" ("+u.split("?")[0]+")");e.status=r.status;throw e}const t=await r.text();try{return JSON.parse(t)}catch(x){const e=new Error("Instagram beklenmeyen yanıt verdi ("+u.split("?")[0]+"). Sayfayı yenileyip tekrar deneyin.");e.status=0;throw e}};
const any=async us=>{let last;for(const u of us.filter(Boolean)){try{return {u,d:await j(u)}}catch(e){last=e;if(e.status!==404&&e.status!==400&&e.status!==0)throw e}}throw last};
const sleep=ms=>new Promise(r=>setTimeout(r,ms));
const A="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_";
const toId=c=>{let n=0n;for(const ch of c.slice(0,11)){const i=A.indexOf(ch);if(i<0)break;n=n*64n+BigInt(i)}return n.toString()};
const img=m=>{const c=(m.image_versions2&&m.image_versions2.candidates)||(m.carousel_media&&m.carousel_media[0]&&m.carousel_media[0].image_versions2&&m.carousel_media[0].image_versions2.candidates)||[];return c.length?c[c.length>1?1:0].url:null};
const typ=m=>m.media_type===8?"carousel":(m.media_type===2?(m.product_type==="clips"?"reel":"video"):"image");
try{
await wait("ig-ready",300000);
let code=(location.pathname.match(/\/(?:p|reel|reels|tv)\/([A-Za-z0-9_-]+)/)||[])[1],id;
if(code){id=toId(code)}else{
const uid=(document.cookie.match(/(?:^|; )ds_user_id=(\d+)/)||[])[1];
if(!uid)throw new Error("Instagram oturumu bulunamadı. instagram.com'a giriş yapıp tekrar deneyin.");
const R=["explore","reels","direct","accounts","stories","p","reel","tv","about","legal"];
let un=(location.pathname.match(/^\/([A-Za-z0-9._]{1,30})\/?$/)||[])[1];if(un&&R.includes(un))un=null;
if(!un){try{const i=await j("/api/v1/users/"+uid+"/info/");un=i.user&&i.user.username}catch(e){if(e.status!==404&&e.status!==400&&e.status!==0)throw e}}
let posts=[],u=null;
const add=m=>{u=u||m.user;posts.push({id:String(m.id||m.pk).split("_")[0],code:m.code,image:img(m),caption:(m.caption&&m.caption.text)||"",comments:m.comment_count||0,type:typ(m),time:m.taken_at})};
const bases=[un?"/api/v1/feed/user/"+encodeURIComponent(un)+"/username/?count=10":null,"/api/v1/feed/user/"+uid+"/?count=10"];
let first=null;try{first=await any(bases)}catch(e){if(!un)throw e}
if(first){(first.d.items||[]).forEach(add)}
else{const w2=await j("/api/v1/users/web_profile_info/?username="+encodeURIComponent(un));const us=w2.data&&w2.data.user;u={username:us&&us.username,profile_pic_url:us&&us.profile_pic_url};for(const e of (us&&us.edge_owner_to_timeline_media&&us.edge_owner_to_timeline_media.edges)||[]){const n=e.node;posts.push({id:String(n.id),code:n.shortcode,image:n.thumbnail_src||n.display_url,caption:(n.edge_media_to_caption&&n.edge_media_to_caption.edges[0]&&n.edge_media_to_caption.edges[0].node.text)||"",comments:(n.edge_media_to_comment&&n.edge_media_to_comment.count)||0,type:n.__typename==="GraphSidecar"?"carousel":(n.is_video?(n.product_type==="clips"?"reel":"video"):"image"),time:n.taken_at_timestamp})}}
posts=posts.slice(0,10);
if(!posts.length)throw new Error("Hesapta gönderi bulunamadı. Çekiliş gönderisini açıp yer imine o sayfadayken tıklayın.");
send({type:"ig-posts",user:{username:(u&&u.username)||un||"",pic:(u&&u.profile_pic_url)||""},posts});
const p=await wait("ig-pick",1800000);id=p.id;code=p.code}
let m={};try{const info=await j("/api/v1/media/"+id+"/info/");m=(info.items||[])[0]||{}}catch(e){if(e.status!==404&&e.status!==400&&e.status!==0)throw e}
let total=m.comment_count||0;const out=[],seen=new Set();let q="",g=0,cap="";
send({type:"ig-progress",count:0,total});
do{const d=await j("/api/v1/media/"+id+"/comments/?can_support_threading=true&permalink_enabled=false"+q);if(!total&&d.comment_count)total=d.comment_count;if(!cap&&d.caption&&d.caption.text)cap=d.caption.text;
for(const c of d.comments||[]){const k=typeof c.pk==="string"?c.pk:(c.user&&c.user.username)+"|"+c.created_at+"|"+c.text;if(seen.has(k))continue;seen.add(k);out.push({username:c.user&&c.user.username,text:c.text||"",time:c.created_at})}
send({type:"ig-progress",count:out.length,total});
q=d.next_min_id?"&min_id="+encodeURIComponent(typeof d.next_min_id==="string"?d.next_min_id:JSON.stringify(d.next_min_id)):(d.next_max_id?"&max_id="+encodeURIComponent(typeof d.next_max_id==="string"?d.next_max_id:JSON.stringify(d.next_max_id)):"");
if(q)await sleep(700+Math.random()*600)}while(q&&++g<400);
send({type:"ig-data",post:{media_id:String(id),shortcode:m.code||code,caption:(m.caption&&m.caption.text)||cap||"",image:img(m),owner:m.user&&m.user.username,comment_count:total},comments:out});
}catch(e){send({type:"ig-error",message:e.message});alert("Instagram aktarımı: "+e.message)}
})();
JS;
  $igBookmarklet = 'javascript:' . rawurlencode(str_replace('__SITE__', json_encode($igTarget), $igJs));
@endphp
<a href="{{ $igBookmarklet }}" class="btn btn-primary ig-bm" onclick="alert('Bu butonu tıklamayın: yer imleri çubuğuna sürükleyin. Sonra instagram.com açıkken oradan tıklayın.'); return false;" style="display: inline-flex; align-items: center; gap: 6px; cursor: grab;">
  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor"/></svg>
  <span>Instagram → Site</span>
</a>
