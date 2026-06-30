@extends('client.layouts.app')

@section('title', 'About - Pizza Ria')

@push('styles')
<style>
    /* ============================================================
       ABOUT PAGE — ZERO-GRAVITY SCROLLYTELLING v3
       100% tanpa gambar background — semua elemen transparan
       ============================================================ */

    .about-page {
        position: relative;
        width: 100%;
        background: #0a0a0a;
        color: #f5f0e8;
        overflow-x: hidden;
    }

    /* ============================================================
       SECTION 1 — HERO (Zero-Gravity)
       ============================================================ */
    .zg-hero {
        position: relative;
        width: 100%;
        height: 250vh;
    }
    .zg-hero__pin {
        position: relative;
        width: 100%;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        cursor: default;
    }

    /* ---- Hero Title ---- */
    .zg-hero__title {
        position: relative;
        z-index: 20;
        text-align: center;
        padding: 0 24px;
        pointer-events: none;
    }
    .zg-hero__title h1 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(2rem, 6vw, 4.5rem);
        font-weight: 900;
        line-height: 1.15;
        margin: 0;
        text-shadow: 0 4px 40px rgba(0,0,0,0.7);
    }
    .zg-hero__title h1 span { color: #E8304A; }
    .zg-hero__subtitle {
        font-family: 'Inter', sans-serif;
        font-size: clamp(0.75rem, 1.6vw, 1rem);
        color: rgba(245,240,232,0.4);
        letter-spacing: 5px;
        text-transform: uppercase;
        margin-top: 18px;
    }

    /* ---- Floating Ingredient (Emoji — no background ever) ---- */
    .zg-ing {
        position: absolute;
        z-index: 10;
        will-change: transform;
        pointer-events: none;
        user-select: none;
        display: flex;
        align-items: center;
        justify-content: center;
        filter: drop-shadow(0 8px 20px rgba(0,0,0,0.6));
    }
    .zg-ing img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    /* ---- Central Pizza (assembly target) ---- */
    .zg-pizza {
        position: absolute;
        width: 380px;
        height: 380px;
        z-index: 5;
        opacity: 0;
        transform: scale(0.4);
        border-radius: 50%;
        overflow: hidden;
        box-shadow: 0 30px 80px rgba(0,0,0,0.8);
    }
    .zg-pizza img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* ---- Particle Canvas ---- */
    #particleCanvas {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        z-index: 1;
        pointer-events: none;
    }

    /* ============================================================
       SECTION 2 — OVEN (400°C)
       ============================================================ */
    .zg-oven {
        position: relative;
        width: 100%;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #0a0a0a;
        overflow: hidden;
    }
    .zg-oven__text {
        position: relative;
        z-index: 10;
        text-align: center;
    }
    .zg-oven__text h2 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(2.5rem, 7vw, 5.5rem);
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 3px;
        color: #1a1a1a;
        transition: color 0.3s;
    }

    /* Canvas fire (drawn via JS — no images, no background) */
    #fireCanvas {
        position: absolute;
        bottom: 0; left: 0;
        width: 100%; height: 100%;
        z-index: 2;
        pointer-events: none;
        opacity: 0;
    }

    /* CSS Ember sparks */
    .zg-oven__embers {
        position: absolute;
        bottom: 0; left: 0;
        width: 100%; height: 100%;
        z-index: 3;
        pointer-events: none;
        opacity: 0;
    }
    .ember {
        position: absolute;
        bottom: 0;
        border-radius: 50%;
        background: #ffcc00;
        box-shadow: 0 0 6px 2px rgba(255,140,0,0.9);
        animation: emberFloat linear infinite;
    }
    @keyframes emberFloat {
        0%   { transform: translateY(0) translateX(0) scale(1); opacity: 1; }
        50%  { transform: translateY(-40vh) translateX(20px) scale(0.7); opacity: 0.8; }
        100% { transform: translateY(-85vh) translateX(-10px) scale(0); opacity: 0; }
    }

    /* Heat shimmer overlay */
    .zg-oven__shimmer {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        z-index: 4;
        pointer-events: none;
        opacity: 0;
        background: linear-gradient(
            180deg,
            transparent 0%,
            rgba(255, 69, 0, 0.03) 30%,
            rgba(255, 140, 0, 0.06) 60%,
            rgba(255, 69, 0, 0.1) 100%
        );
        animation: shimmerPulse 2s ease-in-out infinite alternate;
    }
    @keyframes shimmerPulse {
        0% { opacity: 0; }
        100% { opacity: 1; }
    }

    /* ============================================================
       SECTION 3 — STORY OUTRO
       ============================================================ */
    .zg-story {
        position: relative;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #050505;
        padding: 100px 24px;
    }
    .zg-story__content { max-width: 800px; }
    .zg-story__line {
        overflow: hidden;
        margin-bottom: 8px;
    }
    .zg-story__line-inner {
        font-family: 'Playfair Display', serif;
        font-size: clamp(1.2rem, 3vw, 2.2rem);
        line-height: 1.65;
        color: #f5f0e8;
        transform: translateY(120%);
        opacity: 0;
        display: block;
    }
    .zg-story__accent { color: #E8304A; margin-top: 24px; }

    /* ============================================================
       RESPONSIVE
       ============================================================ */
    @media (max-width: 768px) {
        .zg-pizza { width: 240px; height: 240px; }
        .zg-ing { font-size: 50% !important; }
    }
</style>
@endpush

@section('full-width-content')
<div class="about-page" id="aboutPage">

    <!-- ============================================
         SECTION 1 — HERO (Zero-Gravity)
         ============================================ -->
    <section class="zg-hero" id="zg-hero">
        <div class="zg-hero__pin" id="zg-hero-pin">
            <canvas id="particleCanvas"></canvas>

            <!-- Central Pizza -->
            <div class="zg-pizza" id="zg-pizza">
                <img src="/assets/images/about/clear_hero_pizza.png" alt="Pizza Ria">
            </div>

            <!-- Floating Ingredients -->
            <div class="zg-ing" id="ing-0" style="width: 140px;"><img src="/assets/images/about/clear_item_tomato.png" alt="Tomato"></div>
            <div class="zg-ing" id="ing-1" style="width: 90px;"><img src="/assets/images/about/clear_item_tomato.png" alt="Tomato"></div>
            <div class="zg-ing" id="ing-2" style="width: 110px;"><img src="/assets/images/about/clear_item_basil.png" alt="Basil"></div>
            <div class="zg-ing" id="ing-3" style="width: 70px;"><img src="/assets/images/about/clear_item_basil.png" alt="Basil"></div>
            <div class="zg-ing" id="ing-4" style="width: 130px;"><img src="/assets/images/about/clear_item_cheese.png" alt="Cheese"></div>
            <div class="zg-ing" id="ing-5" style="width: 80px;"><img src="/assets/images/about/clear_item_cheese.png" alt="Cheese"></div>
            <div class="zg-ing" id="ing-6" style="width: 90px;"><img src="/assets/images/about/clear_item_mushroom.png" alt="Mushroom"></div>
            <div class="zg-ing" id="ing-7" style="width: 60px;"><img src="/assets/images/about/clear_item_mushroom.png" alt="Mushroom"></div>
            <!-- Added some variations using scaled existing items to fill the scene -->
            <div class="zg-ing" id="ing-8" style="width: 100px;"><img src="/assets/images/about/clear_item_tomato.png" alt="Tomato"></div>
            <div class="zg-ing" id="ing-9" style="width: 85px;"><img src="/assets/images/about/clear_item_basil.png" alt="Basil"></div>
            <div class="zg-ing" id="ing-10" style="width: 120px;"><img src="/assets/images/about/clear_item_cheese.png" alt="Cheese"></div>
            <div class="zg-ing" id="ing-11" style="width: 75px;"><img src="/assets/images/about/clear_item_mushroom.png" alt="Mushroom"></div>

            <!-- Title -->
            <div class="zg-hero__title" id="zg-hero-title">
                <h1>Lebih dari Sekadar Potongan Pizza.<br>Ini <span>Pizza Ria</span>.</h1>
                <p class="zg-hero__subtitle">Scroll untuk merasakan kisah kami</p>
            </div>
        </div>
    </section>

    <!-- ============================================
         SECTION 2 — OVEN (400°C)
         ============================================ -->
    <section class="zg-oven" id="zg-oven">
        <canvas id="fireCanvas"></canvas>
        <div class="zg-oven__shimmer" id="oven-shimmer"></div>
        <div class="zg-oven__embers" id="oven-embers"></div>
        <div class="zg-oven__text">
            <h2 id="oven-text">Dipanggang Sempurna<br>pada 400°C</h2>
        </div>
    </section>

    <!-- ============================================
         SECTION 3 — STORY OUTRO
         ============================================ -->
    <section class="zg-story" id="zg-story">
        <div class="zg-story__content">
            <div class="zg-story__line"><span class="zg-story__line-inner">Kisah kami dimulai dari satu adonan sederhana.</span></div>
            <div class="zg-story__line"><span class="zg-story__line-inner">Tidak ada rahasia besar, hanya dedikasi mutlak</span></div>
            <div class="zg-story__line"><span class="zg-story__line-inner">untuk menghidangkan mahakarya Italia</span></div>
            <div class="zg-story__line"><span class="zg-story__line-inner">langsung ke meja Anda.</span></div>
            <div class="zg-story__line" style="margin-top:24px"><span class="zg-story__line-inner">Setiap gigitan adalah perayaan.</span></div>
            <div class="zg-story__line"><span class="zg-story__line-inner">Setiap loyang adalah dedikasi.</span></div>
            <div class="zg-story__line"><span class="zg-story__line-inner zg-story__accent">Selamat datang di keluarga Pizza Ria.</span></div>
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    gsap.registerPlugin(ScrollTrigger);

    /* ============================================================
       CONFIG — Ingredient scatter positions
       ============================================================ */
    const configs = [
        { id:"ing-0",  x:-340, y:-200, rot:15,  fy:28, fd:3.2 },
        { id:"ing-1",  x: 400, y: 200, rot:-22, fy:32, fd:2.9 },
        { id:"ing-2",  x: 300, y:-270, rot:40,  fy:22, fd:4.1 },
        { id:"ing-3",  x:-420, y: 140, rot:-30, fy:35, fd:3.6 },
        { id:"ing-4",  x:-200, y: 300, rot:12,  fy:24, fd:3.9 },
        { id:"ing-5",  x: 440, y:-120, rot:-18, fy:30, fd:3.1 },
        { id:"ing-6",  x: 160, y: 330, rot:50,  fy:18, fd:2.6 },
        { id:"ing-7",  x:-370, y:-160, rot:-42, fy:33, fd:3.4 },
        { id:"ing-8",  x: 350, y: 260, rot:25,  fy:20, fd:2.8 },
        { id:"ing-9",  x:-270, y:-310, rot:-28, fy:26, fd:3.7 },
        { id:"ing-10", x: 100, y:-340, rot:35,  fy:30, fd:3.0 },
        { id:"ing-11", x:-150, y: 350, rot:-15, fy:22, fd:3.5 },
    ];

    const floats = [];
    const items  = [];

    /* ============================================================
       PHASE 1 — Position + Zero-G Float
       ============================================================ */
    configs.forEach(c => {
        const el = document.getElementById(c.id);
        if (!el) return;
        items.push({ el, c });

        gsap.set(el, { x: c.x, y: c.y, rotation: c.rot, opacity: 1, scale: 1 });

        const tw = gsap.to(el, {
            y: c.y + c.fy,
            rotation: c.rot + 10,
            duration: c.fd,
            ease: "sine.inOut",
            yoyo: true,
            repeat: -1,
        });
        floats.push(tw);
    });

    /* ============================================================
       PHASE 2 — Mouse Repel
       ============================================================ */
    const pin = document.getElementById("zg-hero-pin");

    pin.addEventListener("mousemove", e => {
        const rect = pin.getBoundingClientRect();
        const mx = e.clientX - rect.left;
        const my = e.clientY - rect.top;

        items.forEach(({ el, c }) => {
            const r = el.getBoundingClientRect();
            const cx = r.left + r.width/2 - rect.left;
            const cy = r.top  + r.height/2 - rect.top;
            const dx = cx - mx, dy = cy - my;
            const dist = Math.hypot(dx, dy);

            if (dist < 220) {
                const force = (1 - dist / 220) * 50;
                const ang = Math.atan2(dy, dx);
                gsap.to(el, {
                    x: c.x + Math.cos(ang) * force,
                    y: c.y + Math.sin(ang) * force,
                    duration: 0.5,
                    ease: "power2.out",
                    overwrite: "auto",
                });
            }
        });
    });

    /* ============================================================
       PHASE 3 — Scroll → Gravity (Assemble Pizza)
       ============================================================ */
    ScrollTrigger.create({
        trigger: "#zg-hero",
        start: "top top",
        end: "bottom bottom",
        pin: "#zg-hero-pin",
        pinSpacing: false,
    });

    const gravTL = gsap.timeline({
        scrollTrigger: {
            trigger: "#zg-hero",
            start: "top top",
            end: "80% top",
            scrub: 1.5,
        }
    });

    // Title fades away
    gravTL.to("#zg-hero-title", { y: -100, opacity: 0, duration: 0.3 }, 0);

    // Ingredients spiral inward
    items.forEach(({ el }, i) => {
        gravTL.to(el, {
            x: 0, y: 0,
            rotation: 720,
            scale: 0,
            opacity: 0,
            duration: 0.6,
            ease: "power3.in",
        }, 0.02 * i);
    });

    // Pizza assembles
    gravTL.to("#zg-pizza", {
        opacity: 1,
        scale: 1,
        rotation: 360,
        duration: 0.5,
        ease: "back.out(1.7)",
    }, 0.25);

    gravTL.to("#zg-pizza", {
        rotation: 720,
        scale: 1.1,
        duration: 0.5,
        ease: "none",
    }, 0.7);

    /* ============================================================
       PHASE 4 — 400°C OVEN — Canvas Fire (REAL animated fire)
       ============================================================ */
    const fireCanvas = document.getElementById("fireCanvas");
    const fCtx = fireCanvas.getContext("2d");
    let fireParticles = [];
    let fireActive = false;
    let fireAnimId;

    function resizeFire() {
        fireCanvas.width  = fireCanvas.parentElement.offsetWidth;
        fireCanvas.height = fireCanvas.parentElement.offsetHeight;
    }
    resizeFire();
    window.addEventListener("resize", resizeFire);

    // Fire particle system
    class FireParticle {
        constructor(w, h) {
            this.reset(w, h);
        }
        reset(w, h) {
            this.x  = Math.random() * w;
            this.y  = h + Math.random() * 40;
            this.vx = (Math.random() - 0.5) * 3; // Wider spread
            this.vy = -(4 + Math.random() * 8); // Faster upwards (more roaring)
            this.life    = 1;
            this.decay   = 0.005 + Math.random() * 0.012; // Lives slightly longer to reach higher
            this.radius  = 25 + Math.random() * 50; // Bigger flames
            this.hue     = 15 + Math.random() * 30;
        }
    }

    function initFire() {
        fireParticles = [];
        const w = fireCanvas.width, h = fireCanvas.height;
        for (let i = 0; i < 300; i++) { // More particles!
            const p = new FireParticle(w, h);
            p.life = Math.random(); 
            fireParticles.push(p);
        }
    }

    function drawFire() {
        const w = fireCanvas.width, h = fireCanvas.height;
        fCtx.clearRect(0, 0, w, h);

        fireParticles.forEach(p => {
            p.x += p.vx + (Math.random() - 0.5) * 1.5;
            p.y += p.vy;
            p.life -= p.decay;

            if (p.life <= 0) p.reset(w, h);

            const alpha = p.life * 0.6;
            const r = p.radius * p.life;

            // Multi-layer glow
            const grad = fCtx.createRadialGradient(p.x, p.y, 0, p.x, p.y, r);

            if (p.life > 0.7) {
                // Hot core — white/yellow
                grad.addColorStop(0, `rgba(255, 255, 220, ${alpha})`);
                grad.addColorStop(0.3, `rgba(255, 200, 50, ${alpha * 0.8})`);
                grad.addColorStop(1, `rgba(255, 80, 0, 0)`);
            } else if (p.life > 0.4) {
                // Middle — orange
                grad.addColorStop(0, `rgba(255, 160, 20, ${alpha})`);
                grad.addColorStop(0.5, `rgba(255, 80, 0, ${alpha * 0.5})`);
                grad.addColorStop(1, `rgba(200, 30, 0, 0)`);
            } else {
                // Outer — red/dark
                grad.addColorStop(0, `rgba(200, 50, 0, ${alpha})`);
                grad.addColorStop(0.5, `rgba(120, 20, 0, ${alpha * 0.3})`);
                grad.addColorStop(1, `rgba(60, 0, 0, 0)`);
            }

            fCtx.beginPath();
            fCtx.arc(p.x, p.y, r, 0, Math.PI * 2);
            fCtx.fillStyle = grad;
            fCtx.fill();
        });

        if (fireActive) fireAnimId = requestAnimationFrame(drawFire);
    }

    // Generate CSS embers
    const embersEl = document.getElementById("oven-embers");
    for (let i = 0; i < 70; i++) { // More embers
        const e = document.createElement("div");
        e.classList.add("ember");
        e.style.left = Math.random() * 100 + "%";
        e.style.animationDuration  = (1.5 + Math.random() * 3) + "s"; // Faster embers
        e.style.animationDelay     = Math.random() * 4 + "s";
        const sz = 3 + Math.random() * 6; // slightly bigger embers
        e.style.width  = sz + "px";
        e.style.height = sz + "px";
        embersEl.appendChild(e);
    }

    // ScrollTrigger for oven
    ScrollTrigger.create({
        trigger: "#zg-oven",
        start: "top 80%",
        end: "bottom 20%",
        onEnter: () => {
            fireActive = true;
            initFire();
            drawFire();
        },
        onLeave: ()      => { fireActive = false; },
        onEnterBack: ()   => { fireActive = true; initFire(); drawFire(); },
        onLeaveBack: ()   => { fireActive = false; },
    });

    const ovenTL = gsap.timeline({
        scrollTrigger: {
            trigger: "#zg-oven",
            start: "top 60%",
            end: "center center",
            scrub: 1,
        }
    });

    ovenTL.to("#zg-oven",      { backgroundColor: "#2a0800", duration: 1 }, 0);
    ovenTL.to("#fireCanvas",   { opacity: 1, duration: 1 }, 0);
    ovenTL.to("#oven-embers",  { opacity: 1, duration: 1 }, 0.15);
    ovenTL.to("#oven-shimmer", { opacity: 1, duration: 1 }, 0.1);
    ovenTL.to("#oven-text", {
        color: "#fff3e0",
        textShadow: "0 0 20px #ff6a00, 0 0 50px #ff3300, 0 0 90px #ff0000, 0 0 140px #ff4500",
        duration: 1,
    }, 0);

    /* ============================================================
       PHASE 5 — Story Outro
       ============================================================ */
    const lines = document.querySelectorAll(".zg-story__line-inner");
    lines.forEach((line, i) => {
        gsap.to(line, {
            y: 0, opacity: 1,
            duration: 0.9,
            ease: "power3.out",
            scrollTrigger: { trigger: "#zg-story", start: "top 65%" },
            delay: i * 0.15,
        });
    });

    /* ============================================================
       PHASE 6 — Ambient Particles (Hero sparkles)
       ============================================================ */
    const pCanvas = document.getElementById("particleCanvas");
    const pCtx = pCanvas.getContext("2d");
    let dots = [];
    let pAnimId;

    function resizeP() {
        pCanvas.width  = pCanvas.parentElement.offsetWidth;
        pCanvas.height = pCanvas.parentElement.offsetHeight;
    }
    resizeP();
    window.addEventListener("resize", resizeP);

    for (let i = 0; i < 70; i++) {
        dots.push({
            x: Math.random() * pCanvas.width,
            y: Math.random() * pCanvas.height,
            vx: (Math.random() - 0.5) * 0.3,
            vy: (Math.random() - 0.5) * 0.3,
            r: Math.random() * 2.5 + 0.5,
            a: Math.random() * 0.4 + 0.05,
        });
    }

    function drawDots() {
        pCtx.clearRect(0, 0, pCanvas.width, pCanvas.height);
        dots.forEach(d => {
            pCtx.beginPath();
            pCtx.arc(d.x, d.y, d.r, 0, Math.PI * 2);
            pCtx.fillStyle = `rgba(232,48,74,${d.a})`;
            pCtx.fill();
            d.x += d.vx; d.y += d.vy;
            if (d.x < 0) d.x = pCanvas.width;
            if (d.x > pCanvas.width)  d.x = 0;
            if (d.y < 0) d.y = pCanvas.height;
            if (d.y > pCanvas.height) d.y = 0;
        });
        pAnimId = requestAnimationFrame(drawDots);
    }
    drawDots();

    /* ============================================================
       PHASE 7 — Navbar Replay
       ============================================================ */
    const nav = document.getElementById("nav-about");
    if (nav) {
        nav.addEventListener("click", e => {
            if (window.location.pathname === "/about") {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: "smooth" });

                setTimeout(() => {
                    gravTL.progress(0).pause();
                    gsap.set("#zg-pizza", { opacity:0, scale:0.4, rotation:0 });
                    gsap.set("#zg-hero-title", { y:0, opacity:1 });

                    floats.forEach(t => t.kill());
                    floats.length = 0;

                    items.forEach(({ el, c }) => {
                        gsap.set(el, { x:c.x, y:c.y, rotation:c.rot, scale:1, opacity:1 });
                        floats.push(gsap.to(el, {
                            y: c.y + c.fy, rotation: c.rot + 10,
                            duration: c.fd, ease:"sine.inOut", yoyo:true, repeat:-1,
                        }));
                    });

                    gsap.set("#zg-oven",      { backgroundColor:"#0a0a0a" });
                    gsap.set("#fireCanvas",    { opacity:0 });
                    gsap.set("#oven-embers",   { opacity:0 });
                    gsap.set("#oven-shimmer",  { opacity:0 });
                    gsap.set("#oven-text",     { color:"#1a1a1a", textShadow:"none" });

                    lines.forEach(l => gsap.set(l, { y:"120%", opacity:0 }));

                    ScrollTrigger.refresh(true);
                    gravTL.play();
                }, 800);
            }
        });
    }

    window.addEventListener("beforeunload", () => {
        cancelAnimationFrame(pAnimId);
        cancelAnimationFrame(fireAnimId);
    });
});
</script>
@endpush
