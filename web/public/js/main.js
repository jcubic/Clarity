(function () {
  // Terminal copy button
  var btns = document.querySelectorAll('.terminal-copy');
  btns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var sel = btn.getAttribute('data-copy-target');
      var node = sel ? document.querySelector(sel) : null;
      if (!node) return;
      var text = node.textContent.trim();
      var done = function () {
        btn.setAttribute('data-copied', 'true');
        var label = btn.querySelector('.terminal-copy-label');
        if (label) {
          var prev = label.textContent;
          label.textContent = 'Copied';
          setTimeout(function () { btn.removeAttribute('data-copied'); label.textContent = prev; }, 1600);
        }
      };
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(done, function () {});
      } else {
        var ta = document.createElement('textarea');
        ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
        document.body.appendChild(ta); ta.select();
        try { document.execCommand('copy'); done(); } catch (_) {}
        document.body.removeChild(ta);
      }
    });
  });

  // Variant tabs — progressive enhancement
  var root = document.getElementById('variant-tabs');
  if (root) {
    var tabs = Array.prototype.slice.call(root.querySelectorAll('.variant-tab'));
    var panels = Array.prototype.slice.call(root.querySelectorAll('.variant-panel'));

    function activate(key, focus) {
      tabs.forEach(function (t) {
        var on = t.dataset.tab === key;
        t.classList.toggle('is-active', on);
        t.setAttribute('aria-selected', on ? 'true' : 'false');
        t.setAttribute('tabindex', on ? '0' : '-1');
        if (on && focus) t.focus();
      });
      panels.forEach(function (p) {
        var on = p.dataset.tabPanel === key;
        if (on) p.removeAttribute('hidden');
        else p.setAttribute('hidden', '');
      });
    }

    tabs.forEach(function (t, i) {
      t.addEventListener('click', function () { activate(t.dataset.tab); });
      t.addEventListener('keydown', function (e) {
        var dir = 0;
        if (e.key === 'ArrowRight' || e.key === 'ArrowDown') dir = 1;
        else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') dir = -1;
        else if (e.key === 'Home') { e.preventDefault(); activate(tabs[0].dataset.tab, true); return; }
        else if (e.key === 'End')  { e.preventDefault(); activate(tabs[tabs.length - 1].dataset.tab, true); return; }
        if (!dir) return;
        e.preventDefault();
        var next = tabs[(i + dir + tabs.length) % tabs.length];
        activate(next.dataset.tab, true);
      });
    });

    var initial = tabs[0].dataset.tab;
    activate(initial);
  }
})();
