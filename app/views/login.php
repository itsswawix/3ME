<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>NOVACORE · Sign in</title>
  <!-- Tailwind + Font Awesome + Google Font (Outfit) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    /* smooth global – matches sidebar vibe */
    body {
      font-family: 'Outfit', sans-serif;
      background: #f9fafc;
    }
    /* custom glass utilities (since Tailwind doesn't have backdrop-blur built-in by default, we extend) */
    .glass-card {
      background: rgba(255, 255, 255, 0.75);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.9);
    }
    .glass-input {
      background: rgba(255, 255, 255, 0.7);
      backdrop-filter: blur(6px);
      -webkit-backdrop-filter: blur(6px);
      border: 1px solid rgba(99, 102, 241, 0.15);
      transition: all 0.2s ease;
    }
    .glass-input:focus {
      background: rgba(255, 255, 255, 0.9);
      border-color: #4f46e5;
      box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
      outline: none;
    }
    /* subtle gradient background matching sidebar brand */
    .bg-aurora {
      background: radial-gradient(circle at 20% 30%, rgba(99, 102, 241, 0.04) 0%, transparent 50%),
                  radial-gradient(circle at 80% 70%, rgba(124, 58, 237, 0.03) 0%, transparent 50%),
                  #ffffff;
    }
    /* logo gradient — same as sidebar */
    .logo-badge {
      background: linear-gradient(135deg, #4f46e5, #7c3aed);
      box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);
    }
    .btn-primary {
      background: linear-gradient(135deg, #4f46e5, #7c3aed);
      box-shadow: 0 8px 18px -6px rgba(79, 70, 229, 0.3);
      transition: all 0.2s;
    }
    .btn-primary:hover {
      background: linear-gradient(135deg, #4338ca, #6d28d9);
      box-shadow: 0 10px 22px -8px rgba(79, 70, 229, 0.4);
      transform: translateY(-1px);
    }
    /* mimic brand name */
    .brand-text {
      letter-spacing: -0.2px;
    }
  </style>
</head>
<body class="bg-aurora min-h-screen flex items-center justify-center p-5">

<!-- main login card — exactly the same glass + radius + border as sidebar -->
<div class="w-full max-w-md">
  
  <!-- logo + brand header (sidebar style) -->
  <div class="flex items-center justify-center gap-3 mb-7">
    <div class="logo-badge w-11 h-11 rounded-xl flex items-center justify-center">
      <i class="fas fa-bolt text-white text-lg"></i>
      <!-- fallback if icon fails (but fa works) — we use icon instead of img for simplicity -->
    </div>
    <h1 class="text-2xl font-semibold text-slate-800 tracking-tight brand-text">3ME Manp. Serv.</h1>
  </div>

  <!-- glass card — same as sidebar: white/75 blur, border, shadow -->
  <div class="glass-card rounded-2xl p-8 shadow-[0_20px_40px_-12px_rgba(79,70,229,0.12)] border-white/80">
    
    <!-- greeting -->
    <div class="mb-7">
      <h2 class="text-2xl font-semibold text-slate-800">Welcome back</h2>
      <p class="text-sm text-slate-500 mt-1">Sign in to your workspace</p>
    </div>

    <!-- login form -->
<form id="login-form" class="space-y-5">
  <!-- email field -->
  <div>
    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5 ml-1">Email</label>
    <input type="email" id="email" placeholder="admin@gmail.com" 
           class="w-full px-4 py-3 rounded-xl glass-input text-slate-700 placeholder:text-slate-400 text-sm font-medium"
           value="admin@gmail.com">
  </div>
  
  <!-- password field -->
  <div>
    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1.5 ml-1">Password</label>
    <input type="password" id="password" placeholder="password" 
           class="w-full px-4 py-3 rounded-xl glass-input text-slate-700 placeholder:text-slate-400 text-sm font-medium">
  </div>

      <!-- remember + forgot (sidebar-like spacing) -->
      <div class="flex items-center justify-between pt-1">
        <label class="flex items-center gap-2 cursor-pointer select-none">
          <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-400/40">
          <span class="text-xs font-medium text-slate-600">Remember me</span>
        </label>
        <a href="#" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition">Forgot password?</a>
      </div>

      <!-- sign in button — gradient, same as sidebar logo -->
      <button type="submit" class="btn-primary w-full py-3 rounded-xl text-white font-semibold text-sm tracking-wide flex items-center justify-center gap-2 mt-2">
        <span>Sign in</span>
        <i class="fas fa-arrow-right text-xs"></i>
      </button>
    </form>


  <!-- subtle footer (like sidebar collapsed tooltip style) -->
  <p class="text-center text-xs text-slate-400 mt-6 flex items-center justify-center gap-2">
    <span class="w-1 h-1 rounded-full bg-indigo-300/60"></span>
    LexSoft · workforce
    <span class="w-1 h-1 rounded-full bg-indigo-300/60"></span>
  </p>
</div>

<script>
  (function() {
    'use strict';
    
    // ---------- password toggle (eye icon) ----------
    const toggleBtn = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    
    if (toggleBtn && passwordInput) {
      toggleBtn.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // change icon
        const icon = this.querySelector('i');
        if (type === 'text') {
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
        } else {
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
        }
      });
    }

    // ---------- login form with database authentication ----------
    const form = document.getElementById('login-form');
    if (form) {
      form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        
        // Simple validation
        if (!email || !password) {
          alert('⚠️ Please enter both email and password.');
          return;
        }
        
        // Show loading state
        const btn = form.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = `<i class="fas fa-circle-notch fa-spin"></i> Signing in...`;
        btn.disabled = true;
        
        try {
          // Call login API
          const response = await fetch('../../api/auth/login.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, password })
          });
          
          const data = await response.json();
          
          if (data.success) {
            alert(`✨ Welcome back, ${data.user.name}!`);
            // Redirect to dashboard
            window.location.href = 'dashboard.php';
          } else {
            alert('❌ ' + data.message);
            btn.innerHTML = originalText;
            btn.disabled = false;
          }
        } catch (error) {
          console.error('Login error:', error);
          alert('❌ Connection error. Please try again.');
          btn.innerHTML = originalText;
          btn.disabled = false;
        }
      });
    }

    // ---------- extra: auto-fill hint (like the sidebar popout) ----------
    // just a small quality: click on demo email hint fills the field
    const demoHint = document.querySelector('.nc-popout-email, .text-sm.font-semibold'); // not exactly, we use specific
    const emailField = document.getElementById('email');
    const demoName = document.querySelector('.text-sm.font-semibold.text-slate-700');
    
    // if user clicks on the "James Delaney" line, fill with demo email
    if (demoName) {
      demoName.style.cursor = 'pointer';
      demoName.addEventListener('click', function() {
        if (emailField) {
          emailField.value = 'james.d@novacore.com';
          // subtle focus
          emailField.focus();
        }
      });
    }

    // also clicking avatar fills demo
    const avatarBadge = document.querySelector('.logo-badge.w-9');
    if (avatarBadge) {
      avatarBadge.style.cursor = 'pointer';
      avatarBadge.addEventListener('click', function() {
        if (emailField) emailField.value = 'james.d@novacore.com';
        if (passwordInput) passwordInput.value = 'password123';
      });
    }

    // sidebar like active state — no navigation but style consistency
    
    // optional: add tiny 'nc-active' feel to form on focus (already with glass-input)
    
    // also mimic tooltip? not necessary but adds charm — we don't need tooltip here.
    
    // ---------- repurpose sidebar's localStorage? not needed, but keep clean.
    
    console.log('✓ Login page — Tailwind + Novacore sidebar design');
  })();
</script>

<!-- additional micro-interactions: hover state on card matches .nc-item -->
<style>
  /* replicate .nc-item hover from sidebar for any interactive element */
  .glass-card {
    transition: box-shadow 0.25s ease, transform 0.2s ease;
  }
  .glass-card:hover {
    box-shadow: 0 24px 48px -12px rgba(79, 70, 229, 0.18);
  }
  /* button active state */
  .btn-primary:active {
    transform: translateY(1px);
    box-shadow: 0 6px 14px -6px rgba(79, 70, 229, 0.3);
  }
  /* ensure font rendering matches sidebar */
  body, input, button {
    font-family: 'Outfit', sans-serif;
  }
  /* sidebar-like active dot (if we want to decorate something) */
  .nc-active-dot {
    width: 4px;
    height: 4px;
    background: #4f46e5;
    border-radius: 50%;
  }
  /* border radius consistency */
  .rounded-xl, .rounded-2xl {
    border-radius: 16px; /* 2xl is 16px, matches sidebar popout */
  }
  /* custom for avatar */
  .logo-badge {
    border-radius: 10px; /* matches nc-logo */
  }
  /* adjust for small screens */
  @media (max-width: 480px) {
    .glass-card {
      padding: 1.5rem;
    }
  }
</style>
</body>
</html>