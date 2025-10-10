(function () {
  'use strict';

  function clamp(val, min, max) {
    val = Number(val);
    if (!Number.isFinite(val)) return min;
    return Math.min(max, Math.max(min, val));
  }

  function $(id) { return document.getElementById(id); }

  document.addEventListener('DOMContentLoaded', function () {
    var section = $('js-demo-waves');
    var canvas = $('wavesCanvas');
    if (!section || !canvas || !canvas.getContext) return;

    var ampInput = $('wavesAmplitude');
    var freqInput = $('wavesFrequency');
    var speedInput = $('wavesSpeed');
    var ampOut = $('wavesAmplitudeOut');
    var freqOut = $('wavesFrequencyOut');
    var speedOut = $('wavesSpeedOut');
    var resetBtn = $('wavesResetBtn');
    var infoBtn = $('wavesInfoBtn');

    var modal = $('wavesModal');
    var closeBtn = $('wavesCloseBtn');
    var lastFocusedBeforeModal = null;

    var ctx = canvas.getContext('2d');
    var width = canvas.width;
    var height = canvas.height;
    var mid = Math.floor(height / 2);

    var DEFAULTS = { amplitude: 40, frequency: 2, speed: 2 };

    var state = {
      amplitude: clamp(ampInput && ampInput.value, 0, 100) || DEFAULTS.amplitude,
      frequency: clamp(freqInput && freqInput.value, 0.5, 5) || DEFAULTS.frequency,
      speed: clamp(speedInput && speedInput.value, 0.1, 6) || DEFAULTS.speed,
      phase: 0,
      paused: false,
      modalOpen: false
    };

    function updateOutputs() {
      if (ampOut) ampOut.textContent = String(Math.round(state.amplitude));
      if (freqOut) freqOut.textContent = state.frequency.toFixed(1);
      if (speedOut) speedOut.textContent = state.speed.toFixed(1);
    }

    function readControls() {
      if (ampInput) state.amplitude = clamp(ampInput.value, 0, 100);
      if (freqInput) state.frequency = clamp(freqInput.value, 0.5, 5);
      if (speedInput) state.speed = clamp(speedInput.value, 0.1, 6);
      updateOutputs();
    }

    function resetControls() {
      if (ampInput) ampInput.value = String(DEFAULTS.amplitude);
      if (freqInput) freqInput.value = String(DEFAULTS.frequency);
      if (speedInput) speedInput.value = String(DEFAULTS.speed);
      state.phase = 0;
      state.paused = false;
      readControls();
    }

    function draw() {
      ctx.clearRect(0, 0, width, height);
      ctx.beginPath();
      ctx.lineWidth = 2;
      ctx.strokeStyle = '#2f6fed';

      var amp = clamp(state.amplitude, 0, 100);
      var freqScale = (clamp(state.frequency, 0.5, 5) * 2 * Math.PI) / width;
      var ph = Number.isFinite(state.phase) ? state.phase : 0;

      for (var x = 0; x <= width; x++) {
        var y = mid + amp * Math.sin(ph + x * freqScale);
        if (x === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
      }

      ctx.stroke();
    }

    var lastTime = null;
    function frame(ts) {
      if (lastTime == null) lastTime = ts;
      var dtMs = ts - lastTime;
      lastTime = ts;

      var dt = Math.min(0.1, Math.max(0, dtMs * 0.001));

      if (!state.paused) {
        state.phase += clamp(state.speed, 0.1, 6) * dt;
      }
      draw();
      requestAnimationFrame(frame);
    }

    if (ampInput) ampInput.addEventListener('input', readControls);
    if (freqInput) freqInput.addEventListener('input', readControls);
    if (speedInput) speedInput.addEventListener('input', readControls);
    if (resetBtn) resetBtn.addEventListener('click', resetControls);

    document.addEventListener('keydown', function (e) {
      if (state.modalOpen) return;
      var tag = (e.target && e.target.tagName) ? e.target.tagName.toLowerCase() : '';
      var isTyping = tag === 'input' || tag === 'textarea' || tag === 'select' || (e.target && e.target.isContentEditable);
      if (isTyping) return;

      if (e.code === 'Space') {
        e.preventDefault();
        state.paused = !state.paused;
      } else if (e.key === 'r' || e.key === 'R') {
        e.preventDefault();
        resetControls();
      }
    });

    function getFocusableInModal() {
      if (!modal) return [];
      var selectors = [
        'a[href]', 'button:not([disabled])', 'input:not([disabled])',
        'select:not([disabled])', 'textarea:not([disabled])',
        '[tabindex]:not([tabindex="-1"])'
      ];
      var nodes = modal.querySelectorAll(selectors.join(','));
      return Array.prototype.filter.call(nodes, function (el) {
        return el.offsetParent !== null || modal === el;
      });
    }

    function openModal() {
      if (!modal) return;
      state.modalOpen = true;
      lastFocusedBeforeModal = document.activeElement;
      modal.classList.add('open');
      modal.removeAttribute('hidden');
      var focusables = getFocusableInModal();
      if (focusables.length) {
        focusables[0].focus();
      } else {
        modal.focus();
      }
    }

    function closeModal() {
      if (!modal) return;
      state.modalOpen = false;
      modal.classList.remove('open');
      modal.setAttribute('hidden', '');
      if (lastFocusedBeforeModal && typeof lastFocusedBeforeModal.focus === 'function') {
        lastFocusedBeforeModal.focus();
      }
    }

    if (infoBtn) infoBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);

    document.addEventListener('keydown', function (e) {
      if (!state.modalOpen || !modal) return;
      if (e.key !== 'Tab') return;
      var focusables = getFocusableInModal();
      if (!focusables.length) return;
      var first = focusables[0];
      var last = focusables[focusables.length - 1];
      var active = document.activeElement;
      if (e.shiftKey) {
        if (active === first || !modal.contains(active)) {
          e.preventDefault();
          last.focus();
        }
      } else {
        if (active === last) {
          e.preventDefault();
          first.focus();
        }
      }
    });

    document.addEventListener('keydown', function (e) {
      if (state.modalOpen && e.code === 'Space') e.preventDefault();
    });

    readControls();
    draw();
    requestAnimationFrame(frame);
  });
})();
