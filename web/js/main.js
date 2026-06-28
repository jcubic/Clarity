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
    var groups = Array.prototype.slice.call(root.querySelectorAll('[data-tab-panel]'));

    function activate(key, focus) {
      tabs.forEach(function (t) {
        var on = t.dataset.tab === key;
        t.classList.toggle('is-active', on);
        t.setAttribute('aria-selected', on ? 'true' : 'false');
        t.setAttribute('tabindex', on ? '0' : '-1');
        if (on && focus) t.focus();
      });
      groups.forEach(function (g) {
        var on = g.dataset.tabPanel === key;
        if (on) g.removeAttribute('hidden');
        else g.setAttribute('hidden', '');
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

    activate(tabs[0].dataset.tab);
    root.classList.add('variant-tabs-enabled');

    // Sub-toggle within variant groups
    groups.forEach(function (group) {
      var switchEl = group.querySelector('.variant-switch');
      if (!switchEl) return;
      var subPanels = Array.prototype.slice.call(group.querySelectorAll('.variant-panel'));
      var switchBtns = Array.prototype.slice.call(switchEl.querySelectorAll('.variant-switch-btn'));

      for (var si = 1; si < subPanels.length; si++) {
        subPanels[si].setAttribute('hidden', '');
      }

      switchBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
          var targetId = btn.dataset.switch;
          subPanels.forEach(function (p) {
            if (p.classList.contains('variant-panel--' + targetId)) {
              p.removeAttribute('hidden');
            } else {
              p.setAttribute('hidden', '');
            }
          });
          switchBtns.forEach(function (b) {
            var isActive = b === btn;
            b.classList.toggle('is-active', isActive);
            b.setAttribute('aria-checked', isActive ? 'true' : 'false');
          });
        });
      });
    });
  }

  // Upload wizard — stepper & drag-and-drop
  var wizard = document.querySelector('.wizard-form');
  if (wizard) {
    wizard.setAttribute('novalidate', '');
    var steps = Array.prototype.slice.call(wizard.querySelectorAll('.wizard-step'));
    var stepperItems = Array.prototype.slice.call(document.querySelectorAll('.stepper-step'));
    var currentStep = 1;

    function showStep(n) {
      currentStep = n;
      steps.forEach(function (s) {
        var sn = parseInt(s.dataset.wizardStep, 10);
        if (sn === n) s.removeAttribute('hidden');
        else s.setAttribute('hidden', '');
      });
      stepperItems.forEach(function (s) {
        var sn = parseInt(s.dataset.step, 10);
        s.classList.toggle('is-active', sn === n);
        s.classList.toggle('is-done', sn < n);
      });
    }

    showStep(1);

    // Validation state
    var badge = wizard.querySelector('.validation-badge');
    var checkItems = wizard.querySelectorAll('.check-item');
    var nextBtn2 = wizard.querySelector('[data-wizard-next="3"]');

    function setCheck(name, pass, hint) {
      var item = wizard.querySelector('.check-item[data-check="' + name + '"]');
      if (!item) return;
      item.classList.toggle('is-pass', pass);
      item.classList.toggle('is-fail', !pass);
      var existing = item.querySelector('.check-hint');
      if (existing) existing.remove();
      if (!pass && hint) {
        var span = document.createElement('span');
        span.className = 'check-hint';
        span.textContent = hint;
        item.appendChild(span);
      }
    }

    function resetChecks() {
      checkItems.forEach(function (item) {
        item.classList.remove('is-pass', 'is-fail');
        var h = item.querySelector('.check-hint');
        if (h) h.remove();
      });
      if (badge) {
        badge.textContent = 'Waiting for upload';
        badge.dataset.status = 'waiting';
      }
      if (nextBtn2) nextBtn2.disabled = true;
    }

    function runValidation() {
      if (!fileInput || !fileInput.files || !fileInput.files.length) return;

      resetChecks();
      if (badge) {
        badge.textContent = 'Running checks…';
        badge.dataset.status = 'running';
      }

      var formData = new FormData();
      formData.append('svg_file', fileInput.files[0]);

      fetch('/api/validate', { method: 'POST', body: formData })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (data.error) {
            if (badge) {
              badge.textContent = data.error;
              badge.dataset.status = 'fail';
            }
            return;
          }

          var checks = data.checks || {};
          var failCount = 0;
          var keys = Object.keys(checks);
          keys.forEach(function (key) {
            var c = checks[key];
            setCheck(key, c.pass, c.hint);
            if (!c.pass) failCount++;
          });

          if (badge) {
            if (failCount === 0) {
              badge.textContent = 'All checks passed';
              badge.dataset.status = 'pass';
            } else {
              badge.textContent = failCount + ' issue' + (failCount > 1 ? 's' : '') + ' found';
              badge.dataset.status = 'fail';
            }
          }

          if (nextBtn2) nextBtn2.disabled = failCount > 0;
        })
        .catch(function () {
          if (badge) {
            badge.textContent = 'Validation request failed';
            badge.dataset.status = 'fail';
          }
        });
    }

    // Preview rendering
    function convertCircle(circleMatch, pathD) {
      var attrs = circleMatch
        .replace(/^<circle/, '')
        .replace(/\/?>(\s*<\/circle>)?$/, '');
      attrs = attrs.replace(/\s*id="icon-placeholder"/g, '');
      attrs = attrs.replace(/\s*cx="[^"]*"/g, '');
      attrs = attrs.replace(/\s*cy="[^"]*"/g, '');
      attrs = attrs.replace(/\s*\br="[^"]*"/g, '');
      return '<path d="' + pathD + '"' + attrs + '/>';
    }

    function renderPreview() {
      var grid = document.getElementById('upload-preview-grid');
      var icons = window.__previewIcons;
      if (!grid || !icons || !fileInput || !fileInput.files || !fileInput.files.length) return;

      var reader = new FileReader();
      reader.onload = function () {
        var svgText = reader.result;
        grid.innerHTML = '';
        icons.forEach(function (icon) {
          var rendered = svgText
            .replace(/<circle[^>]*id="icon-placeholder"[^>]*\/?>(\s*<\/circle>)?/i,
              function (match) { return convertCircle(match, icon.d); })
            .replace(/<title>[^<]*<\/title>/, '<title>' + icon.name + '</title>');
          var blob = new Blob([rendered], { type: 'image/svg+xml' });
          var url = URL.createObjectURL(blob);
          var tile = document.createElement('div');
          tile.className = 'upload-preview-tile';
          var img = document.createElement('img');
          img.src = url;
          img.alt = icon.name;
          var name = document.createElement('span');
          name.className = 'upload-preview-tile-name';
          name.textContent = icon.name;
          tile.appendChild(img);
          tile.appendChild(name);
          grid.appendChild(tile);
        });
      };
      reader.readAsText(fileInput.files[0]);
    }

    wizard.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-wizard-next]');
      if (btn && !btn.disabled) {
        var target = parseInt(btn.dataset.wizardNext, 10);
        showStep(target);
        if (target === 2) runValidation();
        if (target === 3) renderPreview();
        return;
      }
      btn = e.target.closest('[data-wizard-prev]');
      if (btn) {
        showStep(parseInt(btn.dataset.wizardPrev, 10));
      }
    });

    // Dropzone
    var dropzone = document.getElementById('dropzone');
    var fileInput = document.getElementById('svg-file');
    var dropLabel = dropzone ? dropzone.querySelector('.dropzone-label') : null;
    var dropFile = wizard.querySelector('.dropzone-file');
    var dropName = dropFile ? dropFile.querySelector('.dropzone-filename') : null;
    var dropSize = dropFile ? dropFile.querySelector('.dropzone-filesize') : null;
    var dropRemove = dropFile ? dropFile.querySelector('.dropzone-remove') : null;
    var nextBtn = wizard.querySelector('[data-wizard-next="2"]');
    var themeInput = document.getElementById('theme-name');
    var descInput = document.getElementById('theme-description');

    function formatSize(bytes) {
      if (bytes < 1024) return bytes + ' B';
      return (bytes / 1024).toFixed(1) + ' KB';
    }

    function updateNextBtn() {
      if (!nextBtn) return;
      var hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
      nextBtn.disabled = !hasFile;
    }

    // Step 3 next button gating (preview → account)
    var nextBtn3 = wizard.querySelector('[data-wizard-next="4"]');

    function updateNextBtn3() {
      if (!nextBtn3) return;
      var hasName = themeInput && themeInput.value.trim().length >= 2 && themeInput.validity.valid;
      var hasDesc = descInput && descInput.value.trim().length > 0;
      var overwriteOk = !themeNameTaken || (overwriteCheck && overwriteCheck.checked);
      nextBtn3.disabled = !(hasName && hasDesc && overwriteOk);
    }

    function showFile(file) {
      if (!file || !dropFile) return;
      dropName.textContent = file.name;
      dropSize.textContent = formatSize(file.size);
      if (dropzone) dropzone.setAttribute('hidden', '');
      dropFile.removeAttribute('hidden');
      updateNextBtn();
    }

    function clearFile() {
      if (!dropFile) return;
      fileInput.value = '';
      if (dropzone) dropzone.removeAttribute('hidden');
      dropFile.setAttribute('hidden', '');
      updateNextBtn();
    }

    if (fileInput) {
      fileInput.addEventListener('change', function () {
        if (fileInput.files.length) showFile(fileInput.files[0]);
      });
    }

    if (dropRemove) {
      dropRemove.addEventListener('click', function (e) {
        e.preventDefault();
        clearFile();
      });
    }

    function validateField(input, errorId, getMessage, onUpdate) {
      var errorEl = document.getElementById(errorId);
      if (!input || !errorEl) return;

      function check() {
        var val = input.value.trim();
        var msg = val.length > 0 ? getMessage(val, input) : '';
        errorEl.textContent = msg;
        errorEl.classList.toggle('is-visible', msg.length > 0);
        input.classList.toggle('is-invalid', msg.length > 0);
      }

      input.addEventListener('blur', check);
      input.addEventListener('input', function () {
        if (input.classList.contains('is-invalid')) check();
        if (onUpdate) onUpdate();
      });
    }

    var overwriteWarn = document.getElementById('overwrite-warning');
    var overwriteCheck = document.getElementById('overwrite');
    var themeNameTaken = false;

    function checkThemeName(val) {
      if (!val || val.length < 2 || !/^[A-Za-z0-9_-]+$/.test(val)) {
        if (overwriteWarn) overwriteWarn.setAttribute('hidden', '');
        themeNameTaken = false;
        updateNextBtn3();
        return;
      }
      fetch('/api/check-name?name=' + encodeURIComponent(val))
        .then(function (r) { return r.json(); })
        .then(function (data) {
          themeNameTaken = !data.available;
          if (data.available) {
            if (overwriteWarn) overwriteWarn.setAttribute('hidden', '');
          } else {
            if (overwriteWarn) overwriteWarn.removeAttribute('hidden');
            if (overwriteCheck) overwriteCheck.checked = false;
          }
          updateNextBtn3();
        })
        .catch(function () {});
    }

    validateField(themeInput, 'theme-name-error', function (val) {
      if (val.length < 2) return 'Name must be at least 2 characters.';
      if (val.length > 32) return 'Name must be at most 32 characters.';
      if (!/^[A-Za-z0-9_-]+$/.test(val)) return 'Only letters, numbers, hyphens, and underscores.';
      return '';
    }, updateNextBtn3);

    validateField(descInput, 'theme-description-error', function (val) {
      if (val.length === 0) return 'Description is required.';
      if (val.length > 200) return 'Description must be at most 200 characters.';
      return '';
    }, updateNextBtn3);

    if (themeInput) {
      themeInput.addEventListener('blur', function () {
        checkThemeName(themeInput.value.trim());
      });
    }
    if (overwriteCheck) {
      overwriteCheck.addEventListener('change', updateNextBtn3);
    }

    if (dropzone) {
      ['dragenter', 'dragover'].forEach(function (evt) {
        dropzone.addEventListener(evt, function (e) {
          e.preventDefault();
          dropzone.classList.add('is-drag');
        });
      });
      ['dragleave', 'drop'].forEach(function (evt) {
        dropzone.addEventListener(evt, function (e) {
          e.preventDefault();
          dropzone.classList.remove('is-drag');
        });
      });
      dropzone.addEventListener('drop', function (e) {
        var files = e.dataTransfer && e.dataTransfer.files;
        if (files && files.length) {
          var file = files[0];
          if (file.name.toLowerCase().endsWith('.svg')) {
            fileInput.files = e.dataTransfer.files;
            showFile(file);
          }
        }
      });
    }

    // Step 4: submit button gating
    var submitBtn = wizard.querySelector('button[type="submit"]');
    var emailInput = document.getElementById('email');
    var usernameInput = document.getElementById('username');
    var licenseCheck = wizard.querySelector('input[name="license_agree"]');

    function updateSubmitBtn() {
      if (!submitBtn) return;
      var emailOk = !emailInput || (emailInput.value.trim().length > 0 && emailInput.validity.valid);
      var userOk = !usernameInput || (usernameInput.value.trim().length >= 2 && usernameInput.validity.valid);
      var licenseOk = licenseCheck && licenseCheck.checked;
      submitBtn.disabled = !(emailOk && userOk && licenseOk);
    }

    if (submitBtn) {
      submitBtn.disabled = true;
      if (licenseCheck) licenseCheck.addEventListener('change', updateSubmitBtn);
    }

    validateField(emailInput, 'email-error', function (val, input) {
      if (!input.validity.valid) return 'Enter a valid email address.';
      return '';
    }, updateSubmitBtn);

    validateField(usernameInput, 'username-error', function (val) {
      if (val.length < 2) return 'Username must be at least 2 characters.';
      if (val.length > 32) return 'Username must be at most 32 characters.';
      if (!/^[A-Za-z0-9_-]+$/.test(val)) return 'Only letters, numbers, hyphens, and underscores.';
      return '';
    }, updateSubmitBtn);

  }

  // Background toggle for icon grids
  var toggles = document.querySelectorAll('.bg-toggle');
  toggles.forEach(function (btn) {
    var panel = btn.closest('.variant-panel') || btn.closest('.td-preview');
    var grid = panel ? (panel.querySelector('.icon-grid') || panel.querySelector('.td-preview-grid')) : null;
    if (!grid) return;
    var lightClass = grid.classList.contains('icon-grid') ? 'icon-grid--light' : 'td-preview-grid--light';
    var isLight = btn.dataset.light === 'true';
    btn.addEventListener('click', function () {
      isLight = !isLight;
      grid.classList.toggle(lightClass, isLight);
    });
  });

  // Like button
  var likeBtn = document.querySelector('.td-like-btn');
  if (likeBtn && !likeBtn.disabled) {
    likeBtn.addEventListener('click', function () {
      var slug = likeBtn.dataset.themeSlug;
      likeBtn.disabled = true;
      fetch('/api/like/' + slug, { method: 'POST' })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          likeBtn.classList.add('td-like-btn--liked');
          var count = likeBtn.querySelector('.td-like-count');
          if (count) count.textContent = data.count;
        })
        .catch(function () { likeBtn.disabled = false; });
    });
  }
})();
