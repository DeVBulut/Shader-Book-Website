(function () {
  'use strict';

  var TAU = Math.PI * 2;

  function clamp(value, min, max) {
    value = Number(value);
    if (!Number.isFinite(value)) return min;
    return Math.min(max, Math.max(min, value));
  }

  function lerp(a, b, t) {
    return a + (b - a) * t;
  }

  function $(id) {
    return document.getElementById(id);
  }

  function linearToSrgb(val) {
    val = clamp(val, 0, 1);
    if (val <= 0.0031308) return 12.92 * val;
    return 1.055 * Math.pow(val, 1 / 2.4) - 0.055;
  }

  function setupGlobalPause(state) {
    document.addEventListener('keydown', function (e) {
      if (e.code !== 'Space') return;
      var tag = e.target && e.target.tagName ? e.target.tagName.toLowerCase() : '';
      if (tag === 'input' || tag === 'select' || tag === 'textarea' || (e.target && e.target.isContentEditable)) {
        return;
      }
      e.preventDefault();
      state.paused = !state.paused;
    });
  }

  function createAnimator(step, state, options) {
    options = options || {};
    var last = null;
    var accumulator = 0;
    var minDelta = options.minDelta || 0;

    function frame(ts) {
      if (last === null) last = ts;
      var dt = Math.min(0.1, Math.max(0, (ts - last) * 0.001));
      last = ts;

      if (!state.paused) {
        accumulator += dt;
        if (accumulator >= minDelta) {
          var stepDt = accumulator;
          accumulator = 0;
          step(stepDt, ts * 0.001);
        }
      } else if (typeof options.shouldRun === 'function' && options.shouldRun()) {
        accumulator = 0;
        step(0, ts * 0.001);
      }

      window.requestAnimationFrame(frame);
    }

    window.requestAnimationFrame(frame);
  }

  function initPlotter(globalState) {
    var canvas = $('plotCanvas');
    if (!canvas || !canvas.getContext) return;

    var ctx = canvas.getContext('2d');
    var width = canvas.width;
    var height = canvas.height;
    var midY = height / 2;

    var functionSelect = $('plotFunction');
    var amplitudeInput = $('plotAmplitude');
    var frequencyInput = $('plotFrequency');
    var speedInput = $('plotSpeed');

    var amplitudeOut = $('plotAmplitudeOut');
    var frequencyOut = $('plotFrequencyOut');
    var speedOut = $('plotSpeedOut');

    var state = {
      fn: functionSelect ? functionSelect.value : 'sin',
      amplitude: amplitudeInput ? Number(amplitudeInput.value) : 1,
      frequency: frequencyInput ? Number(frequencyInput.value) : 1,
      speed: speedInput ? Number(speedInput.value) : 0,
      phase: 0
    };

    var needsRender = true;

    var waves = {
      sin: Math.sin,
      cos: Math.cos,
      tri: function (x) {
        var t = (x / TAU) % 1;
        if (t < 0) t += 1;
        return 4 * Math.abs(t - 0.5) - 1;
      },
      saw: function (x) {
        var t = (x / TAU) % 1;
        if (t < 0) t += 1;
        return 2 * t - 1;
      }
    };

    function updateState() {
      if (functionSelect) state.fn = functionSelect.value;
      if (amplitudeInput) state.amplitude = clamp(amplitudeInput.value, 0.05, 1.5);
      if (frequencyInput) state.frequency = clamp(frequencyInput.value, 0.05, 6);
      if (speedInput) state.speed = clamp(speedInput.value, 0, 12);

      if (amplitudeOut) amplitudeOut.textContent = state.amplitude.toFixed(2);
      if (frequencyOut) frequencyOut.textContent = state.frequency.toFixed(2);
      if (speedOut) speedOut.textContent = state.speed.toFixed(1);

      needsRender = true;
    }

    if (functionSelect) functionSelect.addEventListener('change', updateState);
    if (amplitudeInput) amplitudeInput.addEventListener('input', updateState);
    if (frequencyInput) frequencyInput.addEventListener('input', updateState);
    if (speedInput) speedInput.addEventListener('input', updateState);

    updateState();

    function draw() {
      ctx.clearRect(0, 0, width, height);

      drawGrid();
      drawAxes();
      drawWave();
      drawLabels();
    }

    function drawGrid() {
      ctx.save();
      ctx.strokeStyle = 'rgba(255,255,255,0.08)';
      ctx.lineWidth = 1;
      ctx.beginPath();
      var stepX = width / 12;
      for (var x = stepX; x < width; x += stepX) {
        ctx.moveTo(x, 0);
        ctx.lineTo(x, height);
      }
      var stepY = height / 8;
      for (var y = stepY; y < height; y += stepY) {
        ctx.moveTo(0, y);
        ctx.lineTo(width, y);
      }
      ctx.stroke();
      ctx.restore();
    }

    function drawAxes() {
      ctx.save();
      ctx.strokeStyle = 'rgba(255,255,255,0.26)';
      ctx.lineWidth = 1.5;
      ctx.beginPath();
      ctx.moveTo(0, midY);
      ctx.lineTo(width, midY);
      ctx.stroke();
      ctx.setLineDash([6, 6]);
      var amp = clamp(state.amplitude, 0, 2) * midY * 0.8;
      ctx.beginPath();
      ctx.moveTo(0, midY - amp);
      ctx.lineTo(width, midY - amp);
      ctx.moveTo(0, midY + amp);
      ctx.lineTo(width, midY + amp);
      ctx.stroke();
      ctx.restore();
    }

    function drawWave() {
      ctx.save();
      ctx.strokeStyle = '#4cc9f0';
      ctx.lineWidth = 2.5;
      ctx.beginPath();

      var fn = waves[state.fn] || waves.sin;
      var amplitudePx = clamp(state.amplitude, 0, 2) * midY * 0.8;

      for (var x = 0; x <= width; x++) {
        var t = x / width;
        var sample = fn(t * TAU * state.frequency + state.phase);
        var y = midY - clamp(sample, -1, 1) * amplitudePx;
        if (x === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
      }

      ctx.stroke();
      ctx.restore();
    }

    function drawLabels() {
      ctx.save();
      ctx.fillStyle = 'rgba(255,255,255,0.7)';
      ctx.font = '14px "Fira Code", "Cascadia Mono", Consolas, monospace';
      ctx.fillText('2\u03C0', width - 48, midY + 18);
      ctx.fillText('0', 12, midY - 8);
      ctx.fillStyle = 'rgba(255,255,255,0.45)';
      ctx.fillText('+' + state.amplitude.toFixed(2), 12, midY - (state.amplitude * midY * 0.8) - 6);
      ctx.fillText('-' + state.amplitude.toFixed(2), 12, midY + (state.amplitude * midY * 0.8) + 20);
      ctx.restore();
    }

    createAnimator(function (dt) {
      state.phase += state.speed * dt;
      draw();
      needsRender = false;
    }, globalState, {
      minDelta: 1 / 60,
      shouldRun: function () { return needsRender; }
    });
  }

  function initDistanceField(globalState) {
    var canvas = $('distanceCanvas');
    if (!canvas || !canvas.getContext) return;

    var ctx = canvas.getContext('2d');
    var width = canvas.width;
    var height = canvas.height;
    var sampleCount = width * height;

    var distances = new Float32Array(sampleCount);
    var i = 0;
    for (var y = 0; y < height; y++) {
      var vy = ((y + 0.5) / height) * 2 - 1;
      for (var x = 0; x < width; x++) {
        var vx = ((x + 0.5) / width) * 2 - 1;
        distances[i++] = Math.sqrt(vx * vx + vy * vy);
      }
    }

    var imageData = ctx.createImageData(width, height);
    var data = imageData.data;

    var radiusInput = $('fieldRadius');
    var ringsInput = $('fieldRings');
    var sharpnessInput = $('fieldSharpness');
    var speedInput = $('fieldSpeed');

    var radiusOut = $('fieldRadiusOut');
    var ringsOut = $('fieldRingsOut');
    var sharpnessOut = $('fieldSharpnessOut');
    var speedOut = $('fieldSpeedOut');

    var state = {
      radius: radiusInput ? Number(radiusInput.value) : 0.5,
      rings: ringsInput ? Number(ringsInput.value) : 6,
      sharpness: sharpnessInput ? Number(sharpnessInput.value) : 1,
      speed: speedInput ? Number(speedInput.value) : 1
    };

    var needsRender = true;

    function updateState() {
      if (radiusInput) state.radius = clamp(radiusInput.value, 0.05, 1.2);
      if (ringsInput) state.rings = clamp(Math.round(Number(ringsInput.value)), 1, 16);
      if (sharpnessInput) state.sharpness = clamp(sharpnessInput.value, 0.1, 3);
      if (speedInput) state.speed = clamp(speedInput.value, 0, 6);

      if (radiusOut) radiusOut.textContent = state.radius.toFixed(2);
      if (ringsOut) ringsOut.textContent = String(state.rings);
      if (sharpnessOut) sharpnessOut.textContent = state.sharpness.toFixed(2);
      if (speedOut) speedOut.textContent = state.speed.toFixed(2);

      needsRender = true;
    }

    if (radiusInput) radiusInput.addEventListener('input', updateState);
    if (ringsInput) ringsInput.addEventListener('input', updateState);
    if (sharpnessInput) sharpnessInput.addEventListener('input', updateState);
    if (speedInput) speedInput.addEventListener('input', updateState);

    updateState();

    createAnimator(function (dt, elapsed) {
      var speed = state.speed;
      var pulse = elapsed * speed;
      var radius = state.radius;
      var rings = state.rings;
      var sharp = state.sharpness;

      var idx = 0;
      for (var n = 0; n < sampleCount; n++) {
        var dist = distances[n];
        var signed = dist - radius;
        var wave = Math.sin((dist * rings - pulse) * TAU);
        var ring = 0.5 + 0.5 * wave;
        var glow = Math.exp(-Math.abs(signed) * (3 + sharp * 4));
        var highlight = Math.exp(-Math.pow(Math.max(0, Math.abs(signed) - 0.005), 2) * (14 * sharp + 6));

        var value = clamp(ring * 0.7 + glow * 0.3, 0, 1);
        var warm = clamp(0.3 + value * 0.7, 0, 1);
        var cool = clamp(0.2 + glow * 0.8, 0, 1);

        var r = warm * 0.75 + highlight * 0.5;
        var g = Math.pow(value, 0.8) * 0.85 + highlight * 0.25;
        var b = cool * 0.85 + warm * 0.2;

        var gammaR = linearToSrgb(r);
        var gammaG = linearToSrgb(g);
        var gammaB = linearToSrgb(b);

        data[idx++] = Math.round(gammaR * 255);
        data[idx++] = Math.round(gammaG * 255);
        data[idx++] = Math.round(gammaB * 255);
        data[idx++] = 255;
      }

      ctx.putImageData(imageData, 0, 0);
      needsRender = false;
    }, globalState, {
      minDelta: 1 / 24,
      shouldRun: function () { return needsRender; }
    });
  }

  function initUvPainter(globalState) {
    var canvas = $('uvCanvas');
    if (!canvas || !canvas.getContext) return;

    var ctx = canvas.getContext('2d');
    var width = canvas.width;
    var height = canvas.height;
    var sampleCount = width * height;

    var uvCoords = new Float32Array(sampleCount * 2);
    var ptr = 0;
    for (var y = 0; y < height; y++) {
      var v = ((y + 0.5) / height) - 0.5;
      for (var x = 0; x < width; x++) {
        var u = ((x + 0.5) / width) - 0.5;
        uvCoords[ptr++] = u;
        uvCoords[ptr++] = v;
      }
    }

    var imageData = ctx.createImageData(width, height);
    var data = imageData.data;

    var rotationInput = $('uvRotation');
    var scaleInput = $('uvScale');
    var warpInput = $('uvWarp');
    var blendInput = $('uvBlend');

    var rotationOut = $('uvRotationOut');
    var scaleOut = $('uvScaleOut');
    var warpOut = $('uvWarpOut');
    var blendOut = $('uvBlendOut');

    var state = {
      rotation: rotationInput ? Number(rotationInput.value) : 0,
      scale: scaleInput ? Number(scaleInput.value) : 1,
      warp: warpInput ? Number(warpInput.value) : 0.3,
      blend: blendInput ? Number(blendInput.value) : 0.5
    };

    var needsRender = true;

    function updateState() {
      if (rotationInput) state.rotation = clamp(rotationInput.value, -180, 180);
      if (scaleInput) state.scale = clamp(scaleInput.value, 0.2, 3);
      if (warpInput) state.warp = clamp(warpInput.value, 0, 1.5);
      if (blendInput) state.blend = clamp(blendInput.value, 0, 1);

      if (rotationOut) rotationOut.textContent = state.rotation.toFixed(0) + ' deg';
      if (scaleOut) scaleOut.textContent = state.scale.toFixed(2);
      if (warpOut) warpOut.textContent = state.warp.toFixed(2);
      if (blendOut) blendOut.textContent = state.blend.toFixed(2);

      needsRender = true;
    }

    if (rotationInput) rotationInput.addEventListener('input', updateState);
    if (scaleInput) scaleInput.addEventListener('input', updateState);
    if (warpInput) warpInput.addEventListener('input', updateState);
    if (blendInput) blendInput.addEventListener('input', updateState);

    updateState();

    createAnimator(function (dt, elapsed) {
      var angle = state.rotation * (Math.PI / 180);
      var cosA = Math.cos(angle);
      var sinA = Math.sin(angle);
      var scale = state.scale;
      var warp = state.warp;
      var blend = state.blend;
      var time = elapsed * 0.6;

      var idx = 0;
      var coordIdx = 0;
      for (var n = 0; n < sampleCount; n++) {
        var u = uvCoords[coordIdx++];
        var v = uvCoords[coordIdx++];

        var ru = (u * cosA - v * sinA) * scale;
        var rv = (u * sinA + v * cosA) * scale;

        var warpedU = ru + Math.sin((rv + time) * 3.6) * warp;
        var warpedV = rv + Math.cos((ru - time * 0.8) * 4.0) * warp;

        var gradient = clamp(warpedU * 0.5 + 0.5, 0, 1);
        var vertical = clamp(warpedV * 0.5 + 0.5, 0, 1);
        var diagonal = clamp((u + v + 1) * 0.5, 0, 1);

        var stripes = 0.5 + 0.5 * Math.sin((warpedU * 2.5 - warpedV * 2.1) * TAU + time * 0.75);
        var checker = ((Math.floor(warpedU * 4) + Math.floor(warpedV * 4)) & 1) ? 1 : 0;
        var pattern = clamp(stripes * (checker ? 0.85 : 1.0), 0, 1);

        var baseR = clamp(0.18 + gradient * 0.65 + diagonal * 0.15, 0, 1);
        var baseG = clamp(0.25 + vertical * 0.55, 0, 1);
        var baseB = clamp(0.35 + (1 - gradient) * 0.55, 0, 1);

        var patR = clamp(0.3 + Math.pow(pattern, 0.7), 0, 1);
        var patG = clamp(0.15 + Math.pow(1 - pattern, 0.8) * 0.9, 0, 1);
        var patB = clamp(0.4 + pattern * 0.6, 0, 1);

        var r = lerp(baseR, patR, blend);
        var g = lerp(baseG, patG, blend);
        var b = lerp(baseB, patB, blend);

        var dist = Math.sqrt(u * u + v * v);
        var vignette = clamp(1 - dist * 1.35, 0, 1);
        var light = 0.55 + 0.45 * vignette;

        r *= light;
        g *= light * 0.98 + 0.02;
        b *= light * 1.05;

        data[idx++] = Math.round(linearToSrgb(r) * 255);
        data[idx++] = Math.round(linearToSrgb(g) * 255);
        data[idx++] = Math.round(linearToSrgb(b) * 255);
        data[idx++] = 255;
      }

      ctx.putImageData(imageData, 0, 0);
      needsRender = false;
    }, globalState, {
      minDelta: 1 / 30,
      shouldRun: function () { return needsRender; }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    var globalState = { paused: false };
    setupGlobalPause(globalState);
    initPlotter(globalState);
    initDistanceField(globalState);
    initUvPainter(globalState);
  });
})();
