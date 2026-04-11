<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PT. Andalan Agro Persada</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
  :root {
    --green:#2E7D32;--green-light:#43A047;--green-pale:#C8E6C9;
    --blue:#1565C0;--blue-light:#1E88E5;--gold:#F9A825;--gold-light:#FFD54F;
    --bg:#F5F7FA;--bg2:#FFFFFF;--bg3:#EEF2F7;
    --text:#1A2340;--text2:#4A5568;--text3:#718096;
    --border:#E2E8F0;--card:#FFFFFF;--nav-bg:rgba(255,255,255,0.92);
    --shadow:0 4px 24px rgba(21,101,192,0.08);--shadow-lg:0 12px 48px rgba(21,101,192,0.12);
  }
  [data-theme="dark"] {
    --bg:#0F1623;--bg2:#162032;--bg3:#1A2840;
    --text:#EDF2F7;--text2:#CBD5E0;--text3:#90A3C0;
    --border:#243450;--card:#1A2840;--nav-bg:rgba(15,22,35,0.95);
    --shadow:0 4px 24px rgba(0,0,0,0.3);--shadow-lg:0 12px 48px rgba(0,0,0,0.4);
  }
  *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
  html{scroll-behavior:smooth;}
  body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text);line-height:1.65;transition:background .3s,color .3s;overflow-x:hidden;}

  /* NAV */
  nav{position:fixed;top:0;left:0;right:0;z-index:1000;background:var(--nav-bg);backdrop-filter:blur(16px);border-bottom:1px solid var(--border);transition:all .3s;}
  nav.scrolled{box-shadow:var(--shadow);}
  .nav-inner{max-width:1280px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;padding:0 24px;height:68px;}
  .nav-logo{display:flex;align-items:center;gap:10px;text-decoration:none;}
  .logo-icon{width:38px;height:38px;background:linear-gradient(135deg,var(--green) 0%,var(--blue) 100%);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;}
  .logo-text{font-family:'Sora',sans-serif;font-weight:700;font-size:15px;color:var(--text);line-height:1.2;}
  .logo-sub{font-size:10px;font-weight:400;color:var(--text3);letter-spacing:.5px;}
  .nav-links{display:flex;align-items:center;gap:2px;list-style:none;}
  .nav-links a{text-decoration:none;color:var(--text2);font-size:14px;font-weight:500;padding:6px 14px;border-radius:8px;transition:all .2s;}
  .nav-links a:hover,.nav-links a.active{background:var(--bg3);color:var(--green);}
  .nav-actions{display:flex;align-items:center;gap:10px;}
  .theme-toggle{width:40px;height:40px;border-radius:10px;border:1px solid var(--border);background:var(--bg3);color:var(--text2);cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:15px;transition:all .2s;}
  .theme-toggle:hover{background:var(--border);color:var(--green);}
  .login-wrapper{position:relative;}
  .btn-login{display:flex;align-items:center;gap:8px;background:linear-gradient(135deg,var(--green) 0%,var(--blue) 100%);color:#fff;border:none;border-radius:10px;padding:9px 18px;font-size:14px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .2s;white-space:nowrap;}
  .btn-login:hover{opacity:.9;transform:translateY(-1px);box-shadow:0 6px 20px rgba(46,125,50,.3);}
  .login-dropdown{position:absolute;top:calc(100% + 8px);right:0;background:var(--card);border:1px solid var(--border);border-radius:14px;box-shadow:var(--shadow-lg);min-width:210px;overflow:hidden;opacity:0;visibility:hidden;transform:translateY(-8px);transition:all .25s;z-index:999;}
  .login-dropdown.open{opacity:1;visibility:visible;transform:translateY(0);}
  .dropdown-header{padding:12px 16px;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.8px;color:var(--text3);border-bottom:1px solid var(--border);}
  .dropdown-item{display:flex;align-items:center;gap:12px;padding:12px 16px;text-decoration:none;color:var(--text);font-size:14px;font-weight:500;transition:all .15s;cursor:pointer;border:none;background:none;width:100%;}
  .dropdown-item:hover{background:var(--bg3);color:var(--green);}
  .dropdown-item .di-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;}
  .di-superadmin .di-icon{background:#FEE2E2;color:#DC2626;}
  .di-admin .di-icon{background:#DBEAFE;color:#2563EB;}
  .di-marketing .di-icon{background:#D1FAE5;color:#059669;}
  .di-finance .di-icon{background:#FEF3C7;color:#D97706;}
  .di-logistik .di-icon{background:#EDE9FE;color:#7C3AED;}
  [data-theme="dark"] .di-superadmin .di-icon{background:rgba(220,38,38,.2);}
  [data-theme="dark"] .di-admin .di-icon{background:rgba(37,99,235,.2);}
  [data-theme="dark"] .di-marketing .di-icon{background:rgba(5,150,105,.2);}
  [data-theme="dark"] .di-finance .di-icon{background:rgba(217,119,6,.2);}
  [data-theme="dark"] .di-logistik .di-icon{background:rgba(124,58,237,.2);}
  .hamburger{display:none;flex-direction:column;gap:5px;cursor:pointer;padding:6px;border:none;background:none;color:var(--text);}
  .hamburger span{width:22px;height:2px;background:currentColor;border-radius:2px;transition:.3s;}
  .hamburger.open span:nth-child(1){transform:translateY(7px) rotate(45deg);}
  .hamburger.open span:nth-child(2){opacity:0;}
  .hamburger.open span:nth-child(3){transform:translateY(-7px) rotate(-45deg);}
  .mobile-menu{display:none;position:fixed;top:68px;left:0;right:0;bottom:0;background:var(--bg);z-index:999;padding:24px;flex-direction:column;gap:8px;overflow-y:auto;}
  .mobile-menu.open{display:flex;}
  .mobile-menu a{text-decoration:none;color:var(--text);font-size:16px;font-weight:500;padding:14px 16px;border-radius:10px;border:1px solid var(--border);display:flex;align-items:center;gap:12px;transition:all .2s;}
  .mobile-menu a:hover{background:var(--bg3);border-color:var(--green);}
  .mobile-menu a i{width:20px;text-align:center;}

  /* HERO */
  .hero{min-height:100vh;display:flex;align-items:center;background:var(--bg);position:relative;overflow:hidden;padding-top:68px;}
  .hero-bg{position:absolute;inset:0;pointer-events:none;}
  .hero-blob{position:absolute;border-radius:50%;filter:blur(80px);opacity:.12;}
  .hero-blob-1{width:600px;height:600px;background:var(--green);top:-200px;right:-100px;animation:floatBlob 8s ease-in-out infinite;}
  .hero-blob-2{width:400px;height:400px;background:var(--blue);bottom:-100px;left:-50px;animation:floatBlob 10s ease-in-out infinite reverse;}
  .hero-blob-3{width:300px;height:300px;background:var(--gold);top:50%;left:40%;animation:floatBlob 7s ease-in-out infinite 2s;}
  @keyframes floatBlob{0%,100%{transform:translate(0,0) scale(1);}50%{transform:translate(20px,-20px) scale(1.05);}}
  .hero-grid{position:absolute;inset:0;background-image:radial-gradient(circle,var(--border) 1px,transparent 1px);background-size:40px 40px;opacity:.4;}
  .hero-inner{max-width:1280px;margin:0 auto;padding:80px 24px;display:grid;grid-template-columns:1fr 1fr;gap:64px;align-items:center;width:100%;position:relative;z-index:1;}
  .hero-badge{display:inline-flex;align-items:center;gap:8px;background:var(--bg2);border:1px solid var(--border);border-radius:100px;padding:6px 16px 6px 6px;font-size:13px;font-weight:500;color:var(--text2);margin-bottom:24px;width:fit-content;animation:fadeUp .6s ease both;}
  .hero-badge-dot{width:24px;height:24px;border-radius:50%;background:linear-gradient(135deg,var(--green),var(--blue));display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;}
  .hero-title{font-family:'Sora',sans-serif;font-size:clamp(36px,5vw,58px);font-weight:800;line-height:1.1;color:var(--text);margin-bottom:20px;animation:fadeUp .6s ease .1s both;}
  .hero-title span.green{color:var(--green);}
  .hero-title span.blue{color:var(--blue);}
  .hero-title span.gold{color:var(--gold);}
  .hero-desc{font-size:16px;color:var(--text2);line-height:1.75;margin-bottom:36px;max-width:480px;animation:fadeUp .6s ease .2s both;}
  .hero-cta{display:flex;gap:12px;flex-wrap:wrap;animation:fadeUp .6s ease .3s both;}
  .btn-primary{display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,var(--green) 0%,var(--blue) 100%);color:#fff;text-decoration:none;border-radius:12px;padding:14px 28px;font-size:15px;font-weight:600;font-family:'DM Sans',sans-serif;transition:all .25s;border:none;cursor:pointer;}
  .btn-primary:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(46,125,50,.35);}
  .btn-secondary{display:inline-flex;align-items:center;gap:8px;background:transparent;color:var(--text);border:1.5px solid var(--border);border-radius:12px;padding:14px 28px;font-size:15px;font-weight:600;font-family:'DM Sans',sans-serif;text-decoration:none;transition:all .25s;}
  .btn-secondary:hover{background:var(--bg3);border-color:var(--green);color:var(--green);}
  .hero-stats{display:flex;gap:32px;margin-top:48px;animation:fadeUp .6s ease .4s both;}
  .stat-num{font-family:'Sora',sans-serif;font-size:28px;font-weight:800;background:linear-gradient(135deg,var(--green),var(--blue));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
  .stat-label{font-size:13px;color:var(--text3);margin-top:2px;}
  .hero-visual{position:relative;animation:fadeUp .7s ease .2s both;}
  .hero-card-main{background:var(--card);border:1px solid var(--border);border-radius:20px;padding:28px;box-shadow:var(--shadow-lg);}
  .hcard-top{display:flex;align-items:center;gap:12px;margin-bottom:20px;}
  .hcard-logo{width:48px;height:48px;border-radius:12px;background:linear-gradient(135deg,var(--green),var(--blue));display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;}
  .hcard-name{font-family:'Sora',sans-serif;font-weight:700;font-size:16px;}
  .hcard-sub{font-size:12px;color:var(--text3);}
  .hcard-badge{margin-left:auto;background:#D1FAE5;color:#065F46;font-size:11px;font-weight:600;padding:4px 10px;border-radius:100px;}
  [data-theme="dark"] .hcard-badge{background:rgba(5,150,105,.2);color:#34D399;}
  .hcard-services{display:grid;grid-template-columns:1fr 1fr;gap:10px;}
  .hcard-service{background:var(--bg3);border-radius:12px;padding:14px;display:flex;flex-direction:column;gap:6px;transition:transform .2s;}
  .hcard-service:hover{transform:translateY(-2px);}
  .hcard-service i{font-size:18px;}
  .hcard-service span{font-size:12px;font-weight:500;color:var(--text2);}
  .s-green{color:var(--green);}.s-blue{color:var(--blue);}.s-gold{color:var(--gold);}.s-purple{color:#7C3AED;}
  .float-card{position:absolute;background:var(--card);border:1px solid var(--border);border-radius:14px;padding:12px 16px;box-shadow:var(--shadow);display:flex;align-items:center;gap:10px;font-size:13px;font-weight:500;animation:float 4s ease-in-out infinite;}
  .float-card-1{top:-24px;right:-20px;animation-delay:0s;}
  .float-card-2{bottom:-24px;left:-20px;animation-delay:1.5s;}
  .float-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;}
  @keyframes float{0%,100%{transform:translateY(0);}50%{transform:translateY(-8px);}}
  @keyframes fadeUp{from{opacity:0;transform:translateY(24px);}to{opacity:1;transform:translateY(0);}}

  /* SECTIONS */
  section{padding:96px 24px;}
  .section-inner{max-width:1280px;margin:0 auto;}
  .section-label{display:inline-flex;align-items:center;gap:8px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:1.2px;color:var(--green);margin-bottom:12px;}
  .section-label::before{content:'';width:20px;height:2px;background:var(--green);border-radius:2px;}
  .section-title{font-family:'Sora',sans-serif;font-size:clamp(28px,4vw,42px);font-weight:800;color:var(--text);line-height:1.15;margin-bottom:16px;}
  .section-desc{font-size:16px;color:var(--text2);max-width:560px;line-height:1.7;}

  /* ABOUT */
  .about{background:var(--bg2);}
  .about-grid{display:grid;grid-template-columns:1fr 1fr;gap:80px;align-items:center;}
  .about-visual{position:relative;}
  .about-img-wrap{background:linear-gradient(135deg,var(--green-pale) 0%,#BBDEFB 100%);border-radius:24px;padding:40px;display:flex;align-items:center;justify-content:center;min-height:340px;}
  [data-theme="dark"] .about-img-wrap{background:linear-gradient(135deg,rgba(46,125,50,.2) 0%,rgba(21,101,192,.2) 100%);}
  .about-icon-big{font-size:100px;color:var(--green);opacity:.7;}
  .about-tag{position:absolute;background:var(--card);border:1px solid var(--border);border-radius:14px;padding:14px 18px;box-shadow:var(--shadow);display:flex;align-items:center;gap:10px;}
  .about-tag-1{top:20px;right:-20px;}
  .about-tag-2{bottom:20px;left:-20px;}
  .about-tag i{font-size:20px;}
  .about-tag-text{font-size:12px;font-weight:600;color:var(--text);}
  .about-tag-sub{font-size:11px;color:var(--text3);}
  .about-features{margin-top:32px;display:flex;flex-direction:column;gap:16px;}
  .about-feat{display:flex;gap:16px;align-items:flex-start;background:var(--bg3);border-radius:14px;padding:18px;transition:transform .2s;}
  .about-feat:hover{transform:translateX(4px);}
  .feat-icon{width:44px;height:44px;border-radius:12px;flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:18px;}
  .feat-green{background:#D1FAE5;color:var(--green);}
  .feat-blue{background:#DBEAFE;color:var(--blue);}
  .feat-gold{background:#FEF3C7;color:#D97706;}
  [data-theme="dark"] .feat-green{background:rgba(46,125,50,.2);}
  [data-theme="dark"] .feat-blue{background:rgba(21,101,192,.2);}
  [data-theme="dark"] .feat-gold{background:rgba(249,168,37,.15);}
  .feat-title{font-weight:700;font-size:15px;margin-bottom:4px;}
  .feat-desc{font-size:13px;color:var(--text3);line-height:1.5;}

  /* VISMIS */
  .vismis{background:var(--bg);}
  .vismis-grid{display:grid;grid-template-columns:1fr 1fr;gap:28px;margin-top:48px;}
  .vm-card{background:var(--card);border:1px solid var(--border);border-radius:20px;padding:36px;position:relative;overflow:hidden;transition:transform .25s,box-shadow .25s;}
  .vm-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-lg);}
  .vm-card::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;}
  .vm-vision::before{background:linear-gradient(90deg,var(--green),var(--green-light));}
  .vm-mission::before{background:linear-gradient(90deg,var(--blue),var(--blue-light));}
  .vm-icon{width:56px;height:56px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:24px;margin-bottom:20px;}
  .vm-vision .vm-icon{background:#D1FAE5;color:var(--green);}
  .vm-mission .vm-icon{background:#DBEAFE;color:var(--blue);}
  [data-theme="dark"] .vm-vision .vm-icon{background:rgba(46,125,50,.2);}
  [data-theme="dark"] .vm-mission .vm-icon{background:rgba(21,101,192,.2);}
  .vm-title{font-family:'Sora',sans-serif;font-size:22px;font-weight:700;margin-bottom:14px;}
  .vm-text{font-size:15px;color:var(--text2);line-height:1.7;}
  .values-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:16px;margin-top:28px;}
  .value-card{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:24px 16px;text-align:center;transition:all .25s;}
  .value-card:hover{transform:translateY(-4px);box-shadow:var(--shadow);border-color:var(--green);}
  .val-icon{width:48px;height:48px;border-radius:14px;margin:0 auto 14px;display:flex;align-items:center;justify-content:center;font-size:20px;}
  .v1{background:#D1FAE5;color:var(--green);}
  .v2{background:#DBEAFE;color:var(--blue);}
  .v3{background:#FEF3C7;color:#D97706;}
  .v4{background:#EDE9FE;color:#7C3AED;}
  .v5{background:#FCE7F3;color:#DB2777;}
  [data-theme="dark"] .v1{background:rgba(46,125,50,.2);}
  [data-theme="dark"] .v2{background:rgba(21,101,192,.2);}
  [data-theme="dark"] .v3{background:rgba(249,168,37,.15);}
  [data-theme="dark"] .v4{background:rgba(124,58,237,.2);}
  [data-theme="dark"] .v5{background:rgba(219,39,119,.2);}
  .val-name{font-family:'Sora',sans-serif;font-size:13px;font-weight:700;margin-bottom:6px;}
  .val-desc{font-size:12px;color:var(--text3);line-height:1.5;}

  /* SERVICES */
  .services{background:var(--bg2);}
  .services-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-top:48px;}
  .svc-card{background:var(--card);border:1px solid var(--border);border-radius:20px;padding:32px;position:relative;overflow:hidden;transition:all .3s;}
  .svc-card:hover{transform:translateY(-6px);box-shadow:var(--shadow-lg);}
  .svc-card::after{content:'';position:absolute;bottom:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--green),var(--blue));transform:scaleX(0);transition:transform .3s;}
  .svc-card:hover::after{transform:scaleX(1);}
  .svc-num{font-family:'Sora',sans-serif;font-size:48px;font-weight:800;color:var(--border);position:absolute;top:16px;right:24px;line-height:1;}
  .svc-icon{width:56px;height:56px;border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:22px;margin-bottom:20px;}
  .svc-title{font-family:'Sora',sans-serif;font-size:17px;font-weight:700;margin-bottom:10px;}
  .svc-desc{font-size:14px;color:var(--text2);line-height:1.65;}
  .svc-tags{display:flex;flex-wrap:wrap;gap:6px;margin-top:16px;}
  .svc-tag{font-size:11px;font-weight:600;padding:4px 10px;border-radius:100px;border:1px solid var(--border);color:var(--text3);}

  /* PRODUCTS */
  .products{background:var(--bg);}
  .products-tabs{display:flex;gap:8px;flex-wrap:wrap;margin:32px 0 24px;}
  .tab-btn{padding:10px 20px;border-radius:100px;border:1.5px solid var(--border);background:transparent;color:var(--text2);font-size:14px;font-weight:600;cursor:pointer;font-family:'DM Sans',sans-serif;transition:all .2s;}
  .tab-btn.active,.tab-btn:hover{background:linear-gradient(135deg,var(--green),var(--blue));border-color:transparent;color:#fff;}
  .products-panel{display:none;animation:fadeUp .35s ease;}
  .products-panel.active{display:block;}
  .products-list{display:grid;grid-template-columns:repeat(4,1fr);gap:20px;}
  .prod-card{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:24px;text-align:center;transition:all .25s;}
  .prod-card:hover{transform:translateY(-4px);box-shadow:var(--shadow);border-color:var(--green-light);}
  .prod-icon{font-size:36px;margin-bottom:12px;}
  .prod-name{font-weight:700;font-size:14px;color:var(--text);}
  .prod-sub{font-size:12px;color:var(--text3);margin-top:4px;}

  /* CLIENTS */
  .clients{background:var(--bg2);}
  .clients-marquee-wrap{overflow:hidden;margin-top:48px;position:relative;}
  .clients-marquee-wrap::before,.clients-marquee-wrap::after{content:'';position:absolute;top:0;bottom:0;width:80px;z-index:2;pointer-events:none;}
  .clients-marquee-wrap::before{left:0;background:linear-gradient(to right,var(--bg2),transparent);}
  .clients-marquee-wrap::after{right:0;background:linear-gradient(to left,var(--bg2),transparent);}
  .clients-marquee{display:flex;gap:20px;width:max-content;animation:marquee 28s linear infinite;}
  .clients-marquee-wrap:hover .clients-marquee{animation-play-state:paused;}
  @keyframes marquee{from{transform:translateX(0);}to{transform:translateX(-50%);}}
  .client-card{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:28px 32px;text-align:center;transition:all .25s;flex-shrink:0;min-width:180px;}
  .client-card:hover{transform:translateY(-4px);box-shadow:var(--shadow);border-color:var(--green);}
  .client-logo{font-size:32px;margin-bottom:12px;}
  .client-name{font-weight:700;font-size:14px;color:var(--text);}
  .client-type{font-size:11px;color:var(--text3);margin-top:3px;}

  /* WHY US */
  .whyus{background:var(--bg);}
  .whyus-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-top:48px;}
  .why-card{background:var(--card);border:1px solid var(--border);border-radius:20px;padding:32px;text-align:center;transition:all .3s;position:relative;overflow:hidden;}
  .why-card:hover{transform:translateY(-6px);box-shadow:var(--shadow-lg);border-color:var(--green);}
  .why-icon{width:64px;height:64px;border-radius:20px;margin:0 auto 20px;display:flex;align-items:center;justify-content:center;font-size:26px;}
  .wi-1{background:#D1FAE5;color:var(--green);}
  .wi-2{background:#DBEAFE;color:var(--blue);}
  .wi-3{background:#FEF3C7;color:#D97706;}
  .wi-4{background:#EDE9FE;color:#7C3AED;}
  .wi-5{background:#FCE7F3;color:#DB2777;}
  .wi-6{background:linear-gradient(135deg,var(--green),var(--blue));color:#fff;}
  [data-theme="dark"] .wi-1{background:rgba(46,125,50,.2);}
  [data-theme="dark"] .wi-2{background:rgba(21,101,192,.2);}
  [data-theme="dark"] .wi-3{background:rgba(249,168,37,.15);}
  [data-theme="dark"] .wi-4{background:rgba(124,58,237,.2);}
  [data-theme="dark"] .wi-5{background:rgba(219,39,119,.2);}
  .why-title{font-family:'Sora',sans-serif;font-size:16px;font-weight:700;margin-bottom:10px;}
  .why-desc{font-size:13px;color:var(--text3);line-height:1.6;}

  /* CTA */
  .cta-strip{background:linear-gradient(135deg,var(--green) 0%,var(--blue) 60%,#1A1080 100%);padding:80px 24px;position:relative;overflow:hidden;}
  .cta-strip::before{content:'';position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,.08) 1px,transparent 1px);background-size:32px 32px;}
  .cta-inner{max-width:800px;margin:0 auto;text-align:center;position:relative;z-index:1;}
  .cta-title{font-family:'Sora',sans-serif;font-size:clamp(26px,4vw,42px);font-weight:800;color:#fff;margin-bottom:16px;}
  .cta-desc{font-size:16px;color:rgba(255,255,255,.8);margin-bottom:36px;}
  .cta-btns{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;}
  .btn-white{display:inline-flex;align-items:center;gap:8px;background:#fff;color:var(--green);border-radius:12px;padding:14px 28px;font-size:15px;font-weight:700;text-decoration:none;transition:all .25s;}
  .btn-white:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(0,0,0,.25);}
  .btn-outline-white{display:inline-flex;align-items:center;gap:8px;background:transparent;color:#fff;border:2px solid rgba(255,255,255,.5);border-radius:12px;padding:14px 28px;font-size:15px;font-weight:600;text-decoration:none;transition:all .25s;}
  .btn-outline-white:hover{background:rgba(255,255,255,.12);border-color:#fff;}

  /* CONTACT */
  .contact{background:var(--bg2);}
  .contact-layout{display:grid;grid-template-columns:1fr 1fr;gap:48px;margin-top:48px;align-items:start;}
  .contact-cards{display:flex;flex-direction:column;gap:16px;}
  .contact-item{display:flex;align-items:flex-start;gap:16px;background:var(--card);border:1px solid var(--border);border-radius:16px;padding:20px;transition:all .25s;}
  .contact-item:hover{transform:translateX(5px);border-color:var(--green);box-shadow:var(--shadow);}
  .contact-icon{width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
  .ci-green{background:#D1FAE5;color:var(--green);}
  .ci-blue{background:#DBEAFE;color:var(--blue);}
  .ci-gold{background:#FEF3C7;color:#D97706;}
  .ci-purple{background:#EDE9FE;color:#7C3AED;}
  [data-theme="dark"] .ci-green{background:rgba(46,125,50,.2);}
  [data-theme="dark"] .ci-blue{background:rgba(21,101,192,.2);}
  [data-theme="dark"] .ci-gold{background:rgba(249,168,37,.15);}
  [data-theme="dark"] .ci-purple{background:rgba(124,58,237,.2);}
  .ci-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:var(--text3);}
  .ci-value{font-size:14px;font-weight:500;color:var(--text);margin-top:4px;line-height:1.6;}
  .ci-value a{color:var(--text);text-decoration:none;transition:color .2s;}
  .ci-value a:hover{color:var(--green);}
  .contact-actions{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:4px;}
  .contact-btn{display:flex;align-items:center;justify-content:center;gap:8px;padding:14px;border-radius:12px;text-decoration:none;font-size:14px;font-weight:600;transition:all .25s;border:1.5px solid var(--border);color:var(--text2);background:var(--card);}
  .contact-btn:hover{transform:translateY(-2px);box-shadow:var(--shadow);}
  .contact-btn.wa{background:#25D366;color:#fff;border-color:#25D366;}
  .contact-btn.wa:hover{background:#1db954;}
  .contact-btn.tel{background:var(--blue);color:#fff;border-color:var(--blue);}
  .contact-btn.tel:hover{background:var(--blue-light);}
  .contact-btn.mail{border-color:var(--green);color:var(--green);}
  .contact-btn.mail:hover{background:var(--green);color:#fff;}
  .contact-btn.maps{border-color:var(--gold);color:#D97706;}
  .contact-btn.maps:hover{background:var(--gold);color:#fff;}
  .contact-map-card{background:var(--card);border:1px solid var(--border);border-radius:20px;overflow:hidden;box-shadow:var(--shadow);}
  .map-header{background:linear-gradient(135deg,var(--green),var(--blue));padding:24px 28px;color:#fff;}
  .map-header-title{font-family:'Sora',sans-serif;font-size:18px;font-weight:700;margin-bottom:4px;}
  .map-header-sub{font-size:13px;opacity:.85;}
  .map-visual{background:var(--bg3);min-height:200px;display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;}
  .map-visual::before{content:'';position:absolute;inset:0;background-image:linear-gradient(var(--border) 1px,transparent 1px),linear-gradient(90deg,var(--border) 1px,transparent 1px);background-size:32px 32px;opacity:.6;}
  .map-pin-wrap{position:relative;z-index:1;text-align:center;}
  .map-pin{width:56px;height:56px;border-radius:50%;background:linear-gradient(135deg,var(--green),var(--blue));display:flex;align-items:center;justify-content:center;color:#fff;font-size:22px;margin:0 auto 12px;box-shadow:0 0 0 12px rgba(46,125,50,.15),0 0 0 24px rgba(46,125,50,.06);animation:pulse 2.5s ease-in-out infinite;}
  @keyframes pulse{0%,100%{box-shadow:0 0 0 12px rgba(46,125,50,.15),0 0 0 24px rgba(46,125,50,.06);}50%{box-shadow:0 0 0 16px rgba(46,125,50,.1),0 0 0 32px rgba(46,125,50,.04);}}
  .map-pin-label{background:var(--card);border:1px solid var(--border);border-radius:10px;padding:8px 16px;font-size:13px;font-weight:600;color:var(--text);box-shadow:var(--shadow);display:inline-block;}
  .map-footer{padding:20px 28px;display:grid;grid-template-columns:1fr 1fr;gap:16px;border-top:1px solid var(--border);}
  .map-info-label{font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:var(--text3);margin-bottom:4px;}
  .map-info-val{font-size:13px;font-weight:600;color:var(--text);}

  /* FOOTER */
  footer{background:var(--bg);border-top:1px solid var(--border);padding:60px 24px 32px;}
  .footer-inner{max-width:1280px;margin:0 auto;}
  .footer-top{display:grid;grid-template-columns:2fr 1fr 1fr 1.2fr;gap:48px;margin-bottom:48px;}
  .footer-logo{display:flex;align-items:center;gap:10px;margin-bottom:16px;}
  .footer-desc{font-size:14px;color:var(--text3);line-height:1.7;max-width:280px;}
  .footer-social{display:flex;gap:10px;margin-top:20px;}
  .social-btn{width:38px;height:38px;border-radius:10px;background:var(--bg3);color:var(--text3);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:15px;text-decoration:none;transition:all .2s;}
  .social-btn:hover{background:var(--green);color:#fff;border-color:var(--green);}
  .footer-col-title{font-family:'Sora',sans-serif;font-size:14px;font-weight:700;margin-bottom:16px;color:var(--text);}
  .footer-links{list-style:none;display:flex;flex-direction:column;gap:10px;}
  .footer-links li{display:flex;align-items:center;gap:8px;}
  .footer-links li i{width:16px;text-align:center;flex-shrink:0;}
  .footer-links a{color:var(--text3);text-decoration:none;font-size:14px;transition:color .2s;}
  .footer-links a:hover{color:var(--green);}
  .footer-bottom{border-top:1px solid var(--border);padding-top:28px;display:flex;align-items:center;justify-content:space-between;font-size:13px;color:var(--text3);}

  /* SCROLL TOP */
  .scroll-top{position:fixed;bottom:28px;right:28px;width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,var(--green),var(--blue));color:#fff;border:none;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(46,125,50,.4);opacity:0;visibility:hidden;transform:translateY(12px);transition:all .3s;z-index:900;}
  .scroll-top.visible{opacity:1;visibility:visible;transform:translateY(0);}
  .scroll-top:hover{transform:translateY(-2px);}

  /* TOAST */
  #toast{position:fixed;bottom:80px;left:50%;transform:translateX(-50%) translateY(20px);background:var(--green);color:#fff;padding:12px 24px;border-radius:12px;font-size:14px;font-weight:600;opacity:0;transition:all .3s;z-index:9999;white-space:nowrap;box-shadow:0 8px 28px rgba(46,125,50,.4);pointer-events:none;}
  #toast.show{opacity:1;transform:translateX(-50%) translateY(0);}

  /* REVEAL */
  .reveal{opacity:0;transform:translateY(28px);transition:opacity .6s ease,transform .6s ease;}
  .reveal.visible{opacity:1;transform:translateY(0);}
  .reveal-delay-1{transition-delay:.1s;}
  .reveal-delay-2{transition-delay:.2s;}
  .reveal-delay-3{transition-delay:.3s;}
  .reveal-delay-4{transition-delay:.4s;}

  /* RESPONSIVE */
  @media(max-width:1100px){.values-grid{grid-template-columns:repeat(3,1fr);}.footer-top{grid-template-columns:1fr 1fr;}.whyus-grid{grid-template-columns:1fr 1fr;}}
  @media(max-width:900px){.nav-links{display:none;}.hamburger{display:flex;}.hero-inner{grid-template-columns:1fr;gap:40px;}.hero-visual{display:none;}.about-grid{grid-template-columns:1fr;}.about-visual{display:none;}.vismis-grid{grid-template-columns:1fr;}.services-grid{grid-template-columns:1fr 1fr;}.products-list{grid-template-columns:repeat(2,1fr);}.contact-layout{grid-template-columns:1fr;}.footer-top{grid-template-columns:1fr 1fr;}.values-grid{grid-template-columns:repeat(2,1fr);}.whyus-grid{grid-template-columns:1fr 1fr;}}
  @media(max-width:600px){section{padding:72px 16px;}.hero-inner{padding:48px 16px;}.hero-stats{gap:20px;}.services-grid{grid-template-columns:1fr;}.products-list{grid-template-columns:1fr 1fr;}.footer-top{grid-template-columns:1fr;}.footer-bottom{flex-direction:column;gap:8px;text-align:center;}.values-grid{grid-template-columns:1fr 1fr;}.whyus-grid{grid-template-columns:1fr;}.hero-cta{flex-direction:column;}.cta-btns{flex-direction:column;align-items:center;}.btn-primary,.btn-secondary,.btn-white,.btn-outline-white{justify-content:center;}.contact-actions{grid-template-columns:1fr 1fr;}.map-footer{grid-template-columns:1fr;}}
  @media(max-width:400px){.values-grid{grid-template-columns:1fr;}.contact-actions{grid-template-columns:1fr;}}
</style>
</head>
<body>

<nav id="navbar">
  <div class="nav-inner">
    <a href="#" class="nav-logo">
      <div class="logo-icon"><i class="fa-solid fa-leaf"></i></div>
      <div>
        <div class="logo-text">Andalan Agro Persada</div>
        <div class="logo-sub">PT. Andalan Agro Persada</div>
      </div>
    </a>
    <ul class="nav-links" id="navLinks">
      <li><a href="#about">Tentang Kami</a></li>
      <li><a href="#vismis">Visi &amp; Misi</a></li>
      <li><a href="#services">Layanan</a></li>
      <li><a href="#products">Produk</a></li>
      <li><a href="#clients">Klien</a></li>
      <li><a href="#contact">Kontak</a></li>
    </ul>
    <div class="nav-actions">
      <button class="theme-toggle" id="themeToggle" title="Ganti tema">
        <i class="fa-solid fa-moon" id="themeIcon"></i>
      </button>
      <div class="login-wrapper">
        <button class="btn-login" id="loginBtn">
          <i class="fa-solid fa-right-to-bracket"></i>
          Login Portal
          <i class="fa-solid fa-chevron-down" style="font-size:11px;transition:transform .2s;" id="loginChevron"></i>
        </button>
        <div class="login-dropdown" id="loginDropdown">
          <div class="dropdown-header">Pilih Role Login</div>
          <button class="dropdown-item di-admin" onclick="loginAs('Admin')">
            <div class="di-icon"><i class="fa-solid fa-user-gear"></i></div><span>Admin</span>
          </button>
          <button class="dropdown-item di-marketing" onclick="loginAs('Marketing')">
            <div class="di-icon"><i class="fa-solid fa-bullhorn"></i></div><span>Marketing</span>
          </button>
          <button class="dropdown-item di-finance" onclick="loginAs('Finance')">
            <div class="di-icon"><i class="fa-solid fa-chart-line"></i></div><span>Finance</span>
          </button>
          <button class="dropdown-item di-logistik" onclick="loginAs('Logistik')">
            <div class="di-icon"><i class="fa-solid fa-truck"></i></div><span>Logistik</span>
          </button>
        </div>
      </div>
      <button class="hamburger" id="hamburger" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</nav>

<div class="mobile-menu" id="mobileMenu">
  <a href="#about"    onclick="closeMobile()"><i class="fa-solid fa-circle-info"  style="color:var(--green)"></i>Tentang Kami</a>
  <a href="#vismis"   onclick="closeMobile()"><i class="fa-solid fa-eye"          style="color:var(--blue)"></i>Visi &amp; Misi</a>
  <a href="#services" onclick="closeMobile()"><i class="fa-solid fa-briefcase"    style="color:var(--gold)"></i>Layanan</a>
  <a href="#products" onclick="closeMobile()"><i class="fa-solid fa-box"          style="color:var(--green)"></i>Produk</a>
  <a href="#clients"  onclick="closeMobile()"><i class="fa-solid fa-handshake"    style="color:var(--blue)"></i>Klien</a>
  <a href="#contact"  onclick="closeMobile()"><i class="fa-solid fa-envelope"     style="color:var(--gold)"></i>Kontak</a>
</div>

<!-- HERO -->
<section class="hero" id="home">
  <div class="hero-bg">
    <div class="hero-grid"></div>
    <div class="hero-blob hero-blob-1"></div>
    <div class="hero-blob hero-blob-2"></div>
    <div class="hero-blob hero-blob-3"></div>
  </div>
  <div class="hero-inner">
    <div class="hero-content">
      <div class="hero-badge">
        <div class="hero-badge-dot"><i class="fa-solid fa-check"></i></div>
        Berdiri Sejak 2012 &middot; Samarinda, Kalimantan Timur
      </div>
      <h1 class="hero-title">
        Solusi Terpercaya untuk<br>
        <span class="green">Perkebunan</span> &amp;<br>
        <span class="blue">Pertambangan</span> <span class="gold">Indonesia</span>
      </h1>
      <p class="hero-desc">Kami hadir sebagai mitra andalan Anda — menyediakan produk dan jasa berkualitas tinggi mulai dari peralatan teknikal, sistem filtrasi, hingga solusi energi yang kompetitif dan tepat sasaran.</p>
      <div class="hero-cta">
        <a href="#services" class="btn-primary"><i class="fa-solid fa-rocket"></i>Jelajahi Layanan Kami</a>
        <a href="#contact"  class="btn-secondary"><i class="fa-solid fa-headset"></i>Hubungi Kami</a>
      </div>
      <div class="hero-stats">
        <div class="hero-stat"><div class="stat-num" data-target="12">0</div><div class="stat-label">Tahun Pengalaman</div></div>
        <div class="hero-stat"><div class="stat-num" data-target="5">0</div><div class="stat-label">Klien Korporat</div></div>
        <div class="hero-stat"><div class="stat-num" data-target="5">0</div><div class="stat-label">Lini Layanan</div></div>
      </div>
    </div>
    <div class="hero-visual">
      <div class="hero-card-main">
        <div class="hcard-top">
          <div class="hcard-logo"><i class="fa-solid fa-leaf"></i></div>
          <div><div class="hcard-name">PT. Andalan Agro Persada</div><div class="hcard-sub">Samarinda, Kalimantan Timur</div></div>
          <div class="hcard-badge">&#x25cf; Aktif</div>
        </div>
        <div class="hcard-services">
          <div class="hcard-service"><i class="fa-solid fa-bolt s-gold"></i><span>Elektrikal &amp; Mekanikal</span></div>
          <div class="hcard-service"><i class="fa-solid fa-bolt-lightning s-blue"></i><span>Genset &amp; Pompa</span></div>
          <div class="hcard-service"><i class="fa-solid fa-seedling s-green"></i><span>Alat Perkebunan</span></div>
          <div class="hcard-service"><i class="fa-solid fa-droplet s-purple"></i><span>Water Filtration</span></div>
          <div class="hcard-service"><i class="fa-solid fa-snowflake s-blue"></i><span>Sistem Pendingin</span></div>
          <div class="hcard-service"><i class="fa-solid fa-screwdriver-wrench s-gold"></i><span>Instalasi &amp; Servis</span></div>
        </div>
      </div>
      <div class="float-card float-card-1">
        <div class="float-icon" style="background:#D1FAE5;color:var(--green)"><i class="fa-solid fa-star"></i></div>
        <div><div style="font-size:12px;font-weight:700;">Kualitas Terjamin</div><div style="font-size:11px;color:var(--text3);">Produk berstandar tinggi</div></div>
      </div>
      <div class="float-card float-card-2">
        <div class="float-icon" style="background:#DBEAFE;color:var(--blue)"><i class="fa-solid fa-truck-fast"></i></div>
        <div><div style="font-size:12px;font-weight:700;">Pengiriman Tepat Waktu</div><div style="font-size:11px;color:var(--text3);">Seluruh wilayah Indonesia</div></div>
      </div>
    </div>
  </div>
</section>

<!-- ABOUT -->
<section class="about" id="about">
  <div class="section-inner">
    <div class="about-grid">
      <div class="about-visual reveal">
        <div class="about-img-wrap"><i class="fa-solid fa-industry about-icon-big"></i></div>
        <div class="about-tag about-tag-1">
          <i class="fa-solid fa-calendar-check" style="color:var(--green);font-size:20px;"></i>
          <div><div class="about-tag-text">Est. 2012</div><div class="about-tag-sub">Samarinda, Kaltim</div></div>
        </div>
        <div class="about-tag about-tag-2">
          <i class="fa-solid fa-map-location-dot" style="color:var(--blue);font-size:20px;"></i>
          <div><div class="about-tag-text">Beroperasi</div><div class="about-tag-sub">Seluruh Indonesia</div></div>
        </div>
      </div>
      <div>
        <div class="section-label reveal"><i class="fa-solid fa-building-columns"></i> Tentang Kami</div>
        <h2 class="section-title reveal">Mitra Andalan yang Sudah Teruji Lebih dari Satu Dekade</h2>
        <p class="section-desc reveal">PT. Andalan Agro Persada adalah perusahaan penyedia produk dan jasa yang berfokus di bidang perkebunan dan pertambangan. Kami bukan sekadar vendor — kami adalah <strong>mitra strategis</strong> yang memahami kebutuhan Anda secara mendalam.</p>
        <p class="section-desc reveal" style="margin-top:12px;">Dengan tim yang kompeten, inovatif, dan berintegritas tinggi, kami hadir memberikan solusi yang tidak hanya memenuhi ekspektasi — tapi melampaui harapan Anda.</p>
        <div class="about-features">
          <div class="about-feat reveal">
            <div class="feat-icon feat-green"><i class="fa-solid fa-medal"></i></div>
            <div><div class="feat-title">Kualitas yang Tidak Kami Kompromikan</div><div class="feat-desc">Setiap produk dan jasa yang kami berikan melewati standar kualitas ketat sebelum sampai ke tangan Anda.</div></div>
          </div>
          <div class="about-feat reveal reveal-delay-1">
            <div class="feat-icon feat-blue"><i class="fa-solid fa-lightbulb"></i></div>
            <div><div class="feat-title">Inovasi Jadi Cara Kerja Kami</div><div class="feat-desc">Kami terus beradaptasi dengan perkembangan teknologi dan tren industri untuk memberikan solusi terbaik.</div></div>
          </div>
          <div class="about-feat reveal reveal-delay-2">
            <div class="feat-icon feat-gold"><i class="fa-solid fa-users-gear"></i></div>
            <div><div class="feat-title">Tim Solid, Hasil Nyata</div><div class="feat-desc">Didukung SDM berpengalaman yang mengedepankan kerja sama tim dan komunikasi terbuka di setiap proyek.</div></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- VISMIS -->
<section class="vismis" id="vismis">
  <div class="section-inner">
    <div style="text-align:center">
      <div class="section-label reveal" style="justify-content:center"><i class="fa-solid fa-eye"></i> Visi &amp; Misi</div>
      <h2 class="section-title reveal">Ke Mana Kami Melangkah &amp; Bagaimana Kami Melakukannya</h2>
      <p class="section-desc reveal" style="margin:0 auto">Lebih dari sekadar target bisnis — ini adalah komitmen kami kepada setiap klien, mitra, dan masyarakat sekitar.</p>
    </div>
    <div class="vismis-grid">
      <div class="vm-card vm-vision reveal">
        <div class="vm-icon"><i class="fa-solid fa-binoculars"></i></div>
        <div class="vm-title">Visi Kami</div>
        <div class="vm-text">Menjadi perusahaan penyedia produk dan jasa <strong>terpercaya dan terkemuka</strong> yang memiliki keunggulan daya saing berkelanjutan di seluruh wilayah Indonesia.</div>
      </div>
      <div class="vm-card vm-mission reveal reveal-delay-1">
        <div class="vm-icon"><i class="fa-solid fa-bullseye"></i></div>
        <div class="vm-title">Misi Kami</div>
        <div class="vm-text">Menyediakan produk dan jasa berkualitas tinggi dengan harga kompetitif, serta memberikan <strong>kepuasan pelanggan</strong> dan membina hubungan jangka panjang yang saling menguntungkan dengan seluruh mitra.</div>
      </div>
    </div>
    <div style="margin-top:64px;">
      <div style="text-align:center;margin-bottom:32px;">
        <div class="section-label reveal" style="justify-content:center"><i class="fa-solid fa-heart"></i> Nilai Kami</div>
        <h3 class="section-title reveal" style="font-size:clamp(22px,3vw,32px);">Lima Pilar yang Menopang Cara Kami Bekerja</h3>
      </div>
      <div class="values-grid">
        <div class="value-card reveal"><div class="val-icon v1"><i class="fa-solid fa-gem"></i></div><div class="val-name">Quality</div><div class="val-desc">Menjaga standar kualitas terbaik di setiap produk &amp; layanan</div></div>
        <div class="value-card reveal reveal-delay-1"><div class="val-icon v2"><i class="fa-solid fa-user-check"></i></div><div class="val-name">Customer Focus</div><div class="val-desc">Kebutuhan Anda adalah prioritas utama kami selalu</div></div>
        <div class="value-card reveal reveal-delay-2"><div class="val-icon v3"><i class="fa-solid fa-wand-magic-sparkles"></i></div><div class="val-name">Innovation</div><div class="val-desc">Terus mendorong ide baru untuk solusi yang lebih baik</div></div>
        <div class="value-card reveal"><div class="val-icon v4"><i class="fa-solid fa-people-group"></i></div><div class="val-name">Collaboration</div><div class="val-desc">Teamwork &amp; komunikasi terbuka jadi fondasi kami</div></div>
        <div class="value-card reveal reveal-delay-1"><div class="val-icon v5"><i class="fa-solid fa-scale-balanced"></i></div><div class="val-name">Integrity</div><div class="val-desc">Berbisnis dengan jujur, transparan, dan bertanggung jawab</div></div>
      </div>
    </div>
  </div>
</section>

<!-- SERVICES -->
<section class="services" id="services">
  <div class="section-inner">
    <div class="section-label reveal"><i class="fa-solid fa-gears"></i> Layanan Kami</div>
    <h2 class="section-title reveal">Apa yang Bisa Kami Lakukan untuk Anda</h2>
    <p class="section-desc reveal">Dari pengadaan alat teknikal hingga sistem pendingin — kami punya solusi lengkap yang siap disesuaikan dengan kebutuhan operasional Anda.</p>
    <div class="services-grid">
      <div class="svc-card reveal"><div class="svc-num">01</div><div class="svc-icon" style="background:#FEF3C7;color:#D97706;"><i class="fa-solid fa-bolt"></i></div><div class="svc-title">Peralatan Elektrikal &amp; Mekanikal</div><div class="svc-desc">Penyediaan &amp; instalasi berbagai peralatan kelistrikan dan mekanikal untuk kebutuhan industri perkebunan dan pertambangan Anda.</div><div class="svc-tags"><span class="svc-tag">Panel Listrik</span><span class="svc-tag">Kabel</span><span class="svc-tag">Trafo</span><span class="svc-tag">Instalasi</span></div></div>
      <div class="svc-card reveal reveal-delay-1"><div class="svc-num">02</div><div class="svc-icon" style="background:#DBEAFE;color:var(--blue);"><i class="fa-solid fa-bolt-lightning"></i></div><div class="svc-title">Genset &amp; Sistem Pompa</div><div class="svc-desc">Solusi daya cadangan dan distribusi air yang andal — mulai dari genset portabel hingga pompa industri berdaya tinggi.</div><div class="svc-tags"><span class="svc-tag">Generator Set</span><span class="svc-tag">Centrifugal Pump</span><span class="svc-tag">Submersible</span></div></div>
      <div class="svc-card reveal reveal-delay-2"><div class="svc-num">03</div><div class="svc-icon" style="background:#D1FAE5;color:var(--green);"><i class="fa-solid fa-seedling"></i></div><div class="svc-title">Peralatan Perkebunan</div><div class="svc-desc">Lengkapi operasional kebun Anda dengan tools, mesin, dan perlengkapan keselamatan kerja yang tepat dan berkualitas.</div><div class="svc-tags"><span class="svc-tag">Mesin Kebun</span><span class="svc-tag">Hand Tools</span><span class="svc-tag">APD</span><span class="svc-tag">STIHL</span></div></div>
      <div class="svc-card reveal"><div class="svc-num">04</div><div class="svc-icon" style="background:#EDE9FE;color:#7C3AED;"><i class="fa-solid fa-droplet"></i></div><div class="svc-title">Water Filtration System</div><div class="svc-desc">Sistem pengolahan air bersih lengkap — dari water treatment plant, filter media, hingga valve dan komponen pendukung lainnya.</div><div class="svc-tags"><span class="svc-tag">RO System</span><span class="svc-tag">Sand Filter</span><span class="svc-tag">Valve</span></div></div>
      <div class="svc-card reveal reveal-delay-1"><div class="svc-num">05</div><div class="svc-icon" style="background:#FCE7F3;color:#DB2777;"><i class="fa-solid fa-snowflake"></i></div><div class="svc-title">Sistem Pendingin Ruangan</div><div class="svc-desc">Instalasi AC split, cassette, hingga sistem HVAC untuk kantor, mess, dan fasilitas operasional di area perkebunan &amp; tambang.</div><div class="svc-tags"><span class="svc-tag">AC Split</span><span class="svc-tag">Cassette AC</span><span class="svc-tag">HVAC</span><span class="svc-tag">Instalasi</span></div></div>
      <div class="svc-card reveal reveal-delay-2" style="background:linear-gradient(135deg,var(--green) 0%,var(--blue) 100%);border-color:transparent;">
        <div class="svc-num" style="color:rgba(255,255,255,.15);">+</div>
        <div class="svc-icon" style="background:rgba(255,255,255,.2);color:#fff;"><i class="fa-solid fa-handshake-angle"></i></div>
        <div class="svc-title" style="color:#fff;">Konsultasi &amp; Proyek Khusus</div>
        <div class="svc-desc" style="color:rgba(255,255,255,.85);">Punya kebutuhan spesifik? Tim kami siap berdiskusi dan menyusun solusi yang tepat untuk Anda.</div>
        <a href="#contact" class="btn-white" style="margin-top:20px;font-size:13px;padding:10px 18px;"><i class="fa-solid fa-arrow-right"></i>Diskusikan Sekarang</a>
      </div>
    </div>
  </div>
</section>

<!-- PRODUCTS -->
<section class="products" id="products">
  <div class="section-inner">
    <div class="section-label reveal"><i class="fa-solid fa-box-open"></i> Produk</div>
    <h2 class="section-title reveal">Katalog Produk Kami</h2>
    <p class="section-desc reveal">Beragam pilihan produk dari brand-brand terpercaya, siap kirim ke lokasi proyek Anda di seluruh Indonesia.</p>
    <div class="products-tabs reveal">
      <button class="tab-btn active" data-tab="elektrikal">Elektrikal &amp; Mekanikal</button>
      <button class="tab-btn" data-tab="genset">Genset &amp; Pompa</button>
      <button class="tab-btn" data-tab="kebun">Peralatan Kebun</button>
      <button class="tab-btn" data-tab="water">Water Treatment</button>
      <button class="tab-btn" data-tab="ac">Pendingin Ruangan</button>
    </div>
    <div class="products-panel active" data-panel="elektrikal">
      <div class="products-list">
        <div class="prod-card reveal"><div class="prod-icon" style="color:var(--gold)"><i class="fa-solid fa-plug-circle-bolt"></i></div><div class="prod-name">Panel Listrik &amp; MCB</div><div class="prod-sub">MDP, SDP, Circuit Breaker</div></div>
        <div class="prod-card reveal reveal-delay-1"><div class="prod-icon" style="color:var(--blue)"><i class="fa-solid fa-cable-car"></i></div><div class="prod-name">Kabel &amp; Konduktor</div><div class="prod-sub">NYY, NYFGBY, NYM</div></div>
        <div class="prod-card reveal reveal-delay-2"><div class="prod-icon" style="color:var(--green)"><i class="fa-solid fa-tower-broadcast"></i></div><div class="prod-name">Trafo &amp; Gardu</div><div class="prod-sub">Step-up / Step-down Transformer</div></div>
        <div class="prod-card reveal reveal-delay-3"><div class="prod-icon" style="color:#7C3AED"><i class="fa-solid fa-pipe-section"></i></div><div class="prod-name">Pipa &amp; Fitting</div><div class="prod-sub">PVC, HDPE, GI Pipe</div></div>
      </div>
    </div>
    <div class="products-panel" data-panel="genset">
      <div class="products-list">
        <div class="prod-card reveal"><div class="prod-icon" style="color:var(--gold)"><i class="fa-solid fa-bolt-lightning"></i></div><div class="prod-name">Genset Open Type</div><div class="prod-sub">5 – 500 kVA, Yamaha / Perkins</div></div>
        <div class="prod-card reveal reveal-delay-1"><div class="prod-icon" style="color:var(--blue)"><i class="fa-solid fa-server"></i></div><div class="prod-name">Genset Silent</div><div class="prod-sub">10 – 1000 kVA, Soundproof</div></div>
        <div class="prod-card reveal reveal-delay-2"><div class="prod-icon" style="color:var(--green)"><i class="fa-solid fa-arrows-spin"></i></div><div class="prod-name">Centrifugal Pump</div><div class="prod-sub">Industri &amp; Irigasi</div></div>
        <div class="prod-card reveal reveal-delay-3"><div class="prod-icon" style="color:#DB2777"><i class="fa-solid fa-water"></i></div><div class="prod-name">Submersible Pump</div><div class="prod-sub">Deep Well &amp; Drainage</div></div>
      </div>
    </div>
    <div class="products-panel" data-panel="kebun">
      <div class="products-list">
        <div class="prod-card reveal"><div class="prod-icon" style="color:var(--green)"><i class="fa-solid fa-screwdriver-wrench"></i></div><div class="prod-name">Hand Tools Lengkap</div><div class="prod-sub">Cangkul, Dodos, Egrek</div></div>
        <div class="prod-card reveal reveal-delay-1"><div class="prod-icon" style="color:var(--gold)"><i class="fa-solid fa-tractor"></i></div><div class="prod-name">Mesin Kebun STIHL</div><div class="prod-sub">Chainsaw, Blower, Brushcutter</div></div>
        <div class="prod-card reveal reveal-delay-2"><div class="prod-icon" style="color:var(--blue)"><i class="fa-solid fa-helmet-safety"></i></div><div class="prod-name">APD Kebun</div><div class="prod-sub">Helm, Sepatu, Sarung Tangan</div></div>
        <div class="prod-card reveal reveal-delay-3"><div class="prod-icon" style="color:#7C3AED"><i class="fa-solid fa-cart-flatbed"></i></div><div class="prod-name">Wheelbarrow &amp; Trolley</div><div class="prod-sub">Kapasitas 80 – 200 kg</div></div>
      </div>
    </div>
    <div class="products-panel" data-panel="water">
      <div class="products-list">
        <div class="prod-card reveal"><div class="prod-icon" style="color:var(--blue)"><i class="fa-solid fa-filter"></i></div><div class="prod-name">Water Treatment Plant</div><div class="prod-sub">RO System, Full Set</div></div>
        <div class="prod-card reveal reveal-delay-1"><div class="prod-icon" style="color:var(--green)"><i class="fa-solid fa-flask"></i></div><div class="prod-name">Sand &amp; Carbon Filter</div><div class="prod-sub">Filter Media &amp; Housing</div></div>
        <div class="prod-card reveal reveal-delay-2"><div class="prod-icon" style="color:#7C3AED"><i class="fa-solid fa-tank-water"></i></div><div class="prod-name">Storage Tank</div><div class="prod-sub">FRP, GRP, Stainless Steel</div></div>
        <div class="prod-card reveal reveal-delay-3"><div class="prod-icon" style="color:var(--gold)"><i class="fa-solid fa-gauge"></i></div><div class="prod-name">Valve &amp; Aksesoris</div><div class="prod-sub">Gate, Ball, Check Valve</div></div>
      </div>
    </div>
    <div class="products-panel" data-panel="ac">
      <div class="products-list">
        <div class="prod-card reveal"><div class="prod-icon" style="color:var(--blue)"><i class="fa-solid fa-snowflake"></i></div><div class="prod-name">AC Split Inverter</div><div class="prod-sub">0.5 – 2.5 PK, Hemat Energi</div></div>
        <div class="prod-card reveal reveal-delay-1"><div class="prod-icon" style="color:#7C3AED"><i class="fa-solid fa-wind"></i></div><div class="prod-name">AC Cassette</div><div class="prod-sub">4 Arah Sirkulasi Udara</div></div>
        <div class="prod-card reveal reveal-delay-2"><div class="prod-icon" style="color:var(--green)"><i class="fa-solid fa-temperature-low"></i></div><div class="prod-name">Sistem HVAC</div><div class="prod-sub">Untuk Gedung &amp; Fasilitas Besar</div></div>
        <div class="prod-card reveal reveal-delay-3"><div class="prod-icon" style="color:var(--gold)"><i class="fa-solid fa-toolbox"></i></div><div class="prod-name">Material Instalasi</div><div class="prod-sub">Pipa, Braket, Isolasi</div></div>
      </div>
    </div>
  </div>
</section>

<!-- CLIENTS -->
<section class="clients" id="clients">
  <div class="section-inner">
    <div style="text-align:center">
      <div class="section-label reveal" style="justify-content:center"><i class="fa-solid fa-handshake"></i> Klien Kami</div>
      <h2 class="section-title reveal">Dipercaya oleh Pemain Besar di Industri</h2>
      <p class="section-desc reveal" style="margin:0 auto;">Kami bangga menjadi bagian dari perjalanan sukses perusahaan-perusahaan terkemuka di sektor perkebunan dan pertambangan Indonesia.</p>
    </div>
    <div class="clients-marquee-wrap reveal">
      <div class="clients-marquee">
        <div class="client-card"><div class="client-logo" style="color:var(--green)"><i class="fa-solid fa-leaf"></i></div><div class="client-name">KLK Group</div><div class="client-type">Perkebunan Kelapa Sawit</div></div>
        <div class="client-card"><div class="client-logo" style="color:var(--blue)"><i class="fa-solid fa-building-wheat"></i></div><div class="client-name">Triputra Agro Persada</div><div class="client-type">Agrobisnis Terpadu</div></div>
        <div class="client-card"><div class="client-logo" style="color:var(--gold)"><i class="fa-solid fa-tractor"></i></div><div class="client-name">MKH Group</div><div class="client-type">Perkebunan &amp; Industri</div></div>
        <div class="client-card"><div class="client-logo" style="color:#7C3AED"><i class="fa-solid fa-seedling"></i></div><div class="client-name">Airco Agro Mandiri</div><div class="client-type">Perkebunan Sawit</div></div>
        <div class="client-card"><div class="client-logo" style="color:#DB2777"><i class="fa-solid fa-mountain"></i></div><div class="client-name">Mitra Korporat</div><div class="client-type">Sektor Pertambangan</div></div>
        <div class="client-card"><div class="client-logo" style="color:var(--green)"><i class="fa-solid fa-leaf"></i></div><div class="client-name">KLK Group</div><div class="client-type">Perkebunan Kelapa Sawit</div></div>
        <div class="client-card"><div class="client-logo" style="color:var(--blue)"><i class="fa-solid fa-building-wheat"></i></div><div class="client-name">Triputra Agro Persada</div><div class="client-type">Agrobisnis Terpadu</div></div>
        <div class="client-card"><div class="client-logo" style="color:var(--gold)"><i class="fa-solid fa-tractor"></i></div><div class="client-name">MKH Group</div><div class="client-type">Perkebunan &amp; Industri</div></div>
        <div class="client-card"><div class="client-logo" style="color:#7C3AED"><i class="fa-solid fa-seedling"></i></div><div class="client-name">Airco Agro Mandiri</div><div class="client-type">Perkebunan Sawit</div></div>
        <div class="client-card"><div class="client-logo" style="color:#DB2777"><i class="fa-solid fa-mountain"></i></div><div class="client-name">Mitra Korporat</div><div class="client-type">Sektor Pertambangan</div></div>
      </div>
    </div>
  </div>
</section>

<!-- WHY US -->
<section class="whyus" id="whyus">
  <div class="section-inner">
    <div style="text-align:center">
      <div class="section-label reveal" style="justify-content:center"><i class="fa-solid fa-trophy"></i> Mengapa Kami</div>
      <h2 class="section-title reveal">Kenapa Harus Pilih PT. Andalan Agro Persada?</h2>
      <p class="section-desc reveal" style="margin:0 auto">Bukan hanya soal produk — kami memberikan pengalaman bermitra yang nyaman, transparan, dan berorientasi pada hasil nyata untuk bisnis Anda.</p>
    </div>
    <div class="whyus-grid">
      <div class="why-card reveal"><div class="why-icon wi-1"><i class="fa-solid fa-clock-rotate-left"></i></div><div class="why-title">Respons Cepat &amp; Tepat</div><div class="why-desc">Tim kami berkomitmen merespons setiap permintaan penawaran dan pertanyaan dalam waktu singkat tanpa birokrasi yang rumit.</div></div>
      
      <div class="why-card reveal reveal-delay-1">
        <div class="why-icon wi-2">
          <i class="fa-solid fa-shield-halved"></i> 
        </div>
        <div class="why-title">Produk Bergaransi Resmi</div>
        <div class="why-desc">
          Seluruh produk yang kami jual dilengkapi garansi resmi dari produsen, memberikan ketenangan pikiran untuk operasional jangka panjang.
        </div>
      </div>
      <div class="why-card reveal reveal-delay-2"><div class="why-icon wi-3"><i class="fa-solid fa-tags"></i></div><div class="why-title">Harga Kompetitif</div><div class="why-desc">Dengan jaringan distribusi yang kuat, kami mampu menawarkan harga terbaik tanpa mengorbankan kualitas produk yang Anda terima.</div></div>
      <div class="why-card reveal"><div class="why-icon wi-4"><i class="fa-solid fa-route"></i></div><div class="why-title">Pengiriman ke Seluruh Indonesia</div><div class="why-desc">Jaringan logistik kami memastikan produk tiba di lokasi proyek Anda tepat waktu dan dalam kondisi sempurna.</div></div>
      <div class="why-card reveal reveal-delay-1"><div class="why-icon wi-5"><i class="fa-solid fa-headset"></i></div><div class="why-title">Purna Jual yang Handal</div><div class="why-desc">Tim after-sales kami siap mendampingi instalasi, perawatan, dan konsultasi teknis setelah transaksi selesai.</div></div>
      <div class="why-card reveal reveal-delay-2"><div class="why-icon wi-6"><i class="fa-solid fa-handshake"></i></div><div class="why-title">Mitra Jangka Panjang</div><div class="why-desc">Kami membangun hubungan bisnis berbasis kepercayaan. Sebagian besar klien kami telah bermitra bertahun-tahun bersama kami.</div></div>
    </div>
  </div>
</section>

<!-- CTA STRIP -->
<div class="cta-strip">
  <div class="cta-inner">
    <h2 class="cta-title">Siap Bekerja Sama dengan Kami?</h2>
    <p class="cta-desc">Dapatkan penawaran terbaik untuk kebutuhan produk dan jasa operasional perusahaan Anda. Tim kami siap merespons dalam waktu cepat.</p>
    <div class="cta-btns">
      <a href="https://wa.me/628115839800" target="_blank" class="btn-white"><i class="fa-brands fa-whatsapp"></i>Chat via WhatsApp</a>
      <a href="tel:+628115839800" class="btn-outline-white"><i class="fa-solid fa-phone"></i>+62 811-5839-800</a>
    </div>
  </div>
</div>

<!-- CONTACT -->
<section class="contact" id="contact">
  <div class="section-inner">
    <div class="section-label reveal"><i class="fa-solid fa-location-dot"></i> Hubungi Kami</div>
    <h2 class="section-title reveal">Kami Ada di Sini untuk Anda</h2>
    <p class="section-desc reveal">Kunjungi kantor kami atau hubungi lewat kanal yang paling nyaman buat Anda — kami selalu siap membantu.</p>
    <div class="contact-layout">
      <div class="contact-cards">
        <div class="contact-item reveal">
          <div class="contact-icon ci-green"><i class="fa-solid fa-map-pin"></i></div>
          <div><div class="ci-label">Alamat Kantor</div><div class="ci-value">Jl. Panjaitan No. 25-D,<br>Kota Samarinda, Kalimantan Timur</div></div>
        </div>
        <div class="contact-item reveal">
          <div class="contact-icon ci-blue"><i class="fa-solid fa-phone"></i></div>
          <div><div class="ci-label">Telepon</div><div class="ci-value"><a href="tel:054177912">0541 777912</a><br><a href="tel:+628115839800">+62 811-5839-800</a><br><a href="tel:+628115837800">+62 811-5837-800</a></div></div>
        </div>
        <div class="contact-item reveal">
          <div class="contact-icon ci-gold"><i class="fa-solid fa-envelope"></i></div>
          <div><div class="ci-label">Email</div><div class="ci-value"><a href="mailto:marketing@groupmitra.co.id">marketing@groupmitra.co.id</a><br><a href="mailto:marketing2@groupmitra.co.id">marketing2@groupmitra.co.id</a></div></div>
        </div>
        <div class="contact-item reveal">
          <div class="contact-icon ci-purple"><i class="fa-solid fa-clock"></i></div>
          <div><div class="ci-label">Jam Operasional</div><div class="ci-value">Senin – Jumat: 08.00 – 17.00 WITA<br>Sabtu: 08.00 – 13.00 WITA</div></div>
        </div>
        <div class="contact-actions reveal">
          <a href="https://wa.me/628115839800" target="_blank" class="contact-btn wa"><i class="fa-brands fa-whatsapp"></i>WhatsApp</a>
          <a href="tel:+628115839800" class="contact-btn tel"><i class="fa-solid fa-phone"></i>Telepon</a>
          <a href="mailto:marketing@groupmitra.co.id" class="contact-btn mail"><i class="fa-solid fa-envelope"></i>Email</a>
          <a href="https://maps.google.com/?q=Jl.+Panjaitan+No+25-D+Samarinda" target="_blank" class="contact-btn maps"><i class="fa-solid fa-map"></i>Peta</a>
        </div>
      </div>
      <div class="contact-map-card reveal reveal-delay-1">
        <div class="map-header">
          <div class="map-header-title"><i class="fa-solid fa-building" style="margin-right:8px;"></i>Kantor Pusat Kami</div>
          <div class="map-header-sub">Samarinda, Kalimantan Timur &mdash; Indonesia</div>
        </div>
        <div class="map-visual">
          <div class="map-pin-wrap">
            <div class="map-pin"><i class="fa-solid fa-location-dot"></i></div>
            <div class="map-pin-label">PT. Andalan Agro Persada</div>
          </div>
        </div>
        <div class="map-footer">
          <div><div class="map-info-label"><i class="fa-solid fa-city" style="margin-right:4px;"></i>Kota</div><div class="map-info-val">Samarinda</div></div>
          <div><div class="map-info-label"><i class="fa-solid fa-map" style="margin-right:4px;"></i>Provinsi</div><div class="map-info-val">Kalimantan Timur</div></div>
          <div><div class="map-info-label"><i class="fa-solid fa-road" style="margin-right:4px;"></i>Jalan</div><div class="map-info-val">Jl. Panjaitan No. 25-D</div></div>
          <div><div class="map-info-label"><i class="fa-solid fa-flag" style="margin-right:4px;"></i>Negara</div><div class="map-info-val">Indonesia</div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-inner">
    <div class="footer-top">
      <div>
        <div class="footer-logo">
          <div class="logo-icon"><i class="fa-solid fa-leaf"></i></div>
          <div><div class="logo-text">Andalan Agro Persada</div><div class="logo-sub">PT. Andalan Agro Persada &middot; Est. 2012</div></div>
        </div>
        <p class="footer-desc">Mitra terpercaya di bidang perkebunan dan pertambangan — menyediakan produk berkualitas dan solusi teknis yang tepat untuk kemajuan bisnis Anda.</p>
        <div class="footer-social">
          <a href="#" class="social-btn" title="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
          <a href="#" class="social-btn" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#" class="social-btn" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
          <a href="mailto:marketing@groupmitra.co.id" class="social-btn" title="Email"><i class="fa-solid fa-envelope"></i></a>
          <a href="https://wa.me/628115839800" target="_blank" class="social-btn" title="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
        </div>
      </div>
      <div>
        <div class="footer-col-title">Navigasi</div>
        <ul class="footer-links">
          <li><i class="fa-solid fa-chevron-right" style="color:var(--green);font-size:10px;"></i><a href="#about">Tentang Kami</a></li>
          <li><i class="fa-solid fa-chevron-right" style="color:var(--green);font-size:10px;"></i><a href="#vismis">Visi &amp; Misi</a></li>
          <li><i class="fa-solid fa-chevron-right" style="color:var(--green);font-size:10px;"></i><a href="#services">Layanan</a></li>
          <li><i class="fa-solid fa-chevron-right" style="color:var(--green);font-size:10px;"></i><a href="#products">Produk</a></li>
          <li><i class="fa-solid fa-chevron-right" style="color:var(--green);font-size:10px;"></i><a href="#clients">Klien</a></li>
          <li><i class="fa-solid fa-chevron-right" style="color:var(--green);font-size:10px;"></i><a href="#contact">Kontak</a></li>
        </ul>
      </div>
      <div>
        <div class="footer-col-title">Layanan</div>
        <ul class="footer-links">
          <li><i class="fa-solid fa-chevron-right" style="color:var(--blue);font-size:10px;"></i><a href="#services">Elektrikal &amp; Mekanikal</a></li>
          <li><i class="fa-solid fa-chevron-right" style="color:var(--blue);font-size:10px;"></i><a href="#services">Genset &amp; Pompa</a></li>
          <li><i class="fa-solid fa-chevron-right" style="color:var(--blue);font-size:10px;"></i><a href="#services">Alat Perkebunan</a></li>
          <li><i class="fa-solid fa-chevron-right" style="color:var(--blue);font-size:10px;"></i><a href="#services">Water Filtration</a></li>
          <li><i class="fa-solid fa-chevron-right" style="color:var(--blue);font-size:10px;"></i><a href="#services">Sistem Pendingin</a></li>
        </ul>
      </div>
      <div>
        <div class="footer-col-title">Kontak Cepat</div>
        <ul class="footer-links">
          <li><i class="fa-solid fa-phone" style="color:var(--green);"></i><a href="tel:054177912">0541 777912</a></li>
          <li><i class="fa-brands fa-whatsapp" style="color:var(--green);"></i><a href="https://wa.me/628115839800" target="_blank">+62 811-5839-800</a></li>
          <li><i class="fa-solid fa-envelope" style="color:var(--blue);"></i><a href="mailto:marketing@groupmitra.co.id">marketing@groupmitra.co.id</a></li>
          <li><i class="fa-solid fa-map-pin" style="color:var(--gold);"></i><a href="https://maps.google.com/?q=Jl.+Panjaitan+No+25-D+Samarinda" target="_blank">Samarinda, Kaltim</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>&copy; 2024 PT. Andalan Agro Persada. Hak cipta dilindungi undang-undang.</span>
      <span>Dibuat dengan <i class="fa-solid fa-heart" style="color:#DB2777;margin:0 4px;"></i> untuk kemajuan industri Indonesia</span>
    </div>
  </div>
</footer>

<button class="scroll-top" id="scrollTop" onclick="window.scrollTo({top:0,behavior:'smooth'})">
  <i class="fa-solid fa-chevron-up"></i>
</button>
<div id="toast"></div>

<script>
  // THEME
  const html=document.documentElement,themeToggle=document.getElementById('themeToggle'),themeIcon=document.getElementById('themeIcon');
  let isDark=localStorage.getItem('theme')==='dark'||(!localStorage.getItem('theme')&&window.matchMedia('(prefers-color-scheme: dark)').matches);
  function applyTheme(){html.setAttribute('data-theme',isDark?'dark':'light');themeIcon.className=isDark?'fa-solid fa-sun':'fa-solid fa-moon';}
  applyTheme();
  themeToggle.addEventListener('click',()=>{isDark=!isDark;localStorage.setItem('theme',isDark?'dark':'light');applyTheme();});

  // LOGIN
  const loginBtn=document.getElementById('loginBtn'),loginDropdown=document.getElementById('loginDropdown'),loginChevron=document.getElementById('loginChevron');
  loginBtn.addEventListener('click',e=>{e.stopPropagation();const o=loginDropdown.classList.toggle('open');loginChevron.style.transform=o?'rotate(180deg)':'rotate(0)';});
  document.addEventListener('click',()=>{loginDropdown.classList.remove('open');loginChevron.style.transform='rotate(0)';});
  function loginAs(role){
    loginDropdown.classList.remove('open');loginChevron.style.transform='rotate(0)';
    showToast('Mengarahkan ke portal '+role+'...');
    if(role==='Superadmin')  window.location.href="{{ route('filament.superadmin.pages.dashboard') }}";
    else if(role==='Admin')  window.location.href="{{ route('filament.admin.home') }}";
    else if(role==='Marketing') window.location.href="{{ route('filament.marketing.pages.dashboard') }}";
    else if(role==='Finance')   window.location.href="{{ route('filament.finance.pages.dashboard') }}";
    else                        window.location.href="{{ route('filament.logistik.pages.dashboard') }}";
  }

  // HAMBURGER
  const hamburger=document.getElementById('hamburger'),mobileMenu=document.getElementById('mobileMenu');
  hamburger.addEventListener('click',()=>{hamburger.classList.toggle('open');mobileMenu.classList.toggle('open');});
  function closeMobile(){hamburger.classList.remove('open');mobileMenu.classList.remove('open');}

  // NAVBAR SCROLL + ACTIVE NAV
  const navbar=document.getElementById('navbar');
  function highlightNav(){
    const secs=document.querySelectorAll('section[id]'),links=document.querySelectorAll('#navLinks a');
    let cur='';
    secs.forEach(s=>{if(window.scrollY>=s.offsetTop-120)cur=s.id;});
    links.forEach(a=>a.classList.toggle('active',a.getAttribute('href')==='#'+cur));
  }
  window.addEventListener('scroll',()=>{
    navbar.classList.toggle('scrolled',window.scrollY>20);
    document.getElementById('scrollTop').classList.toggle('visible',window.scrollY>400);
    highlightNav();
  });

  // PRODUCT TABS
  document.querySelectorAll('.tab-btn').forEach(btn=>{
    btn.addEventListener('click',()=>{
      document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
      document.querySelectorAll('.products-panel').forEach(p=>p.classList.remove('active'));
      btn.classList.add('active');
      const panel=document.querySelector('[data-panel="'+btn.dataset.tab+'"]');
      panel.classList.add('active');
      panel.querySelectorAll('.reveal').forEach(el=>{el.classList.remove('visible');requestAnimationFrame(()=>requestAnimationFrame(()=>el.classList.add('visible')));});
    });
  });

  // SCROLL REVEAL
  const ro=new IntersectionObserver(entries=>entries.forEach(e=>{if(e.isIntersecting)e.target.classList.add('visible');}),{threshold:.1});
  document.querySelectorAll('.reveal').forEach(el=>ro.observe(el));

  // COUNTER ANIMATION
  function animateCounter(el,target){
    let start=0;const dur=1800;
    const step=ts=>{if(!start)start=ts;const p=Math.min((ts-start)/dur,1),e=1-Math.pow(1-p,3);el.textContent=Math.floor(e*target)+(p===1?'+':'');if(p<1)requestAnimationFrame(step);};
    requestAnimationFrame(step);
  }
  const co=new IntersectionObserver(entries=>entries.forEach(e=>{if(e.isIntersecting){animateCounter(e.target,parseInt(e.target.dataset.target));co.unobserve(e.target);}}),{threshold:.5});
  document.querySelectorAll('.stat-num[data-target]').forEach(el=>co.observe(el));

  // TOAST
  function showToast(msg){const t=document.getElementById('toast');t.textContent=msg;t.classList.add('show');clearTimeout(t._t);t._t=setTimeout(()=>t.classList.remove('show'),3000);}
</script>
</body>
</html>