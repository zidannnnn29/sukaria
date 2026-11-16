<div class="w-full bg-batik-hero border-b border-sogan-500/40">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
    <div class="max-w-md mx-auto lg:mx-0 lg:ml-auto lg:mr-0 lg:flex lg:justify-end">
      <div class="w-full max-w-md bg-cotton-50 rounded-3xl border border-cotton-300 shadow-2xl overflow-hidden">
        <div class="batik-border-strip" aria-hidden="true"></div>
        <div class="p-6 sm:p-8 space-y-5">

          <div class="text-center space-y-1.5">
            <div class="w-14 h-14 mx-auto rounded-2xl bg-gradient-to-br from-espresso-900 via-espresso-800 to-osing-600 flex items-center justify-center border border-sogan-400/50 shadow-md">
              <svg class="w-8 h-8" viewBox="0 0 40 40" fill="none" aria-hidden="true">
                <polygon points="20,3 37,20 20,37 3,20" stroke="#F2D5AA" stroke-width="1.4" fill="rgba(200,120,40,0.14)" />
                <g transform="translate(20,20) rotate(-28)">
                  <ellipse cx="0" cy="0" rx="6.2" ry="9.2" fill="#C87828" stroke="#FFFDF9" stroke-width="1.2" />
                  <path d="M-1 -8.2 L1.4 -2.5 L-1.6 2.2 L1 8.2" stroke="#2A160C" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                </g>
              </svg>
            </div>
            <h2 class="font-serif text-xl sm:text-2xl font-bold text-espresso-900">Login Panel Pengelola</h2>
            <p class="text-xs sm:text-sm text-espresso-700">Masuk sebagai <strong>Administrator</strong> atau <strong>Pengelola Toko (Seller)</strong>. Pembeli tidak perlu akun.</p>
          </div>

          <?php if (!empty($errors)): ?>
          <div class="rounded-2xl bg-osing-100 border border-osing-500/40 p-3 text-xs text-osing-700 font-semibold space-y-1">
            <?php foreach ($errors as $err): ?><p><?= e($err) ?></p><?php endforeach; ?>
          </div>
          <?php endif; ?>

          <form method="post" action="<?= e(url('do-login')) ?>" class="space-y-4" novalidate>
            <?= csrf_field() ?>
            <div>
              <label for="login-email" class="block text-xs font-bold text-espresso-900 mb-1">Email</label>
              <input id="login-email" type="email" name="email" value="<?= e($emailOld) ?>" autocomplete="email"
                     placeholder="nama@contoh.id"
                     class="w-full px-4 py-2.5 rounded-2xl bg-white border border-cotton-300 text-sm text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sogan-500 focus:border-sogan-500 transition" />
            </div>
            <div>
              <label for="login-password" class="block text-xs font-bold text-espresso-900 mb-1">Password</label>
              <input id="login-password" type="password" name="password" autocomplete="current-password"
                     placeholder="••••••••"
                     class="w-full px-4 py-2.5 rounded-2xl bg-white border border-cotton-300 text-sm text-espresso-900 focus:outline-none focus:ring-2 focus:ring-sogan-500 focus:border-sogan-500 transition" />
            </div>
            <button type="submit"
                    class="w-full py-3 rounded-2xl bg-espresso-900 hover:bg-espresso-800 text-cotton-50 text-sm font-bold transition shadow-md border border-sogan-500/40 flex items-center justify-center gap-2">
              <svg class="w-4 h-4 text-sogan-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
              Masuk ke Panel
            </button>
          </form>

          <div class="batik-ornament-divider" aria-hidden="true"></div>

          <div class="bg-cotton-100/80 rounded-2xl border border-cotton-300 p-3.5 space-y-1.5">
            <p class="text-[11px] font-extrabold uppercase tracking-wider text-espresso-700">Akun Demo</p>
            <p class="text-xs text-espresso-700"><strong class="text-espresso-900">Admin:</strong> admin@sukaria.id / admin123</p>
            <p class="text-xs text-espresso-700"><strong class="text-espresso-900">Seller:</strong> seller@sukaria.id / seller123</p>
          </div>

          <p class="text-center text-xs text-espresso-700">
            Ingin membeli? <a href="<?= e(url('catalog')) ?>" class="font-bold text-coffeeleaf-700 hover:text-coffeeleaf-600 underline underline-offset-2">Lihat Katalog Pembeli</a> — tanpa login.
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
