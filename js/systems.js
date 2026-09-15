/**
 * FrameFlux compositional systems: 12-column grid, topography shade,
 * translucent frame, connecting lines, archive marks, and print finish.
 * Brand identity (print tactility, editorial restraint) is fixed.
 * Story DNA (mode, metaphor, palette, line language) is variable.
 */
(function (root) {
  const W = 600;
  const H = 900;
  const MARGIN = 36;
  const COLS = 12;
  const GUTTER = 8;
  const BASELINE = 8;

  function hexToRgb(hex) {
    const h = String(hex || "#000000").replace("#", "");
    const n = parseInt(h.length === 3 ? h.split("").map((c) => c + c).join("") : h, 16);
    return [(n >> 16) & 255, (n >> 8) & 255, n & 255];
  }

  function mix(a, b, t) {
    return [a[0] + (b[0] - a[0]) * t, a[1] + (b[1] - a[1]) * t, a[2] + (b[2] - a[2]) * t];
  }

  function clamp(n, lo, hi) {
    return Math.max(lo, Math.min(hi, n));
  }

  function makeGrid() {
    const inner = W - MARGIN * 2;
    const colW = (inner - GUTTER * (COLS - 1)) / COLS;
    const col = (index, span = 1) => {
      const i = clamp(index, 0, COLS - 1);
      const s = clamp(span, 1, COLS - i);
      return {
        x: MARGIN + i * (colW + GUTTER),
        w: s * colW + (s - 1) * GUTTER,
        col: i,
        span: s,
      };
    };
    const snapX = (x) => {
      let best = MARGIN;
      let dist = Infinity;
      for (let i = 0; i < COLS; i += 1) {
        const cx = MARGIN + i * (colW + GUTTER);
        const d = Math.abs(cx - x);
        if (d < dist) {
          dist = d;
          best = cx;
        }
      }
      return best;
    };
    const baseline = (n) => MARGIN + n * BASELINE;
    return { margin: MARGIN, cols: COLS, gutter: GUTTER, colW, col, snapX, baseline, inner };
  }

  function compositionMode(dna) {
    return dna.composition?.mode || "central-focus";
  }

  function artFamily(dna) {
    return dna.semantic?.artFamily || "organic";
  }

  /**
   * Snap every major region to the 12-column grid. Mode is DNA; seed only
   * nudges within the mode so regenerate stays in the same campaign.
   */
  function compositionPlan(dna, seed, grid) {
    const g = grid || makeGrid();
    const mode = compositionMode(dna);
    const energy = Number(dna.semantic?.narrativeEnergy ?? 0.5);
    const air = Number(dna.composition?.negativeSpace ?? 0.5);
    const nudge = ((seed % 5) - 2) * 4;
    const headerY = g.baseline(4);
    const footerY = H - MARGIN - 96;

    const plan = {
      mode,
      grid: g,
      title: { x: g.col(0, 10).x, y: headerY, w: g.col(0, 10).w, align: "left", placement: "upper-third" },
      quote: { x: g.col(0, 9).x, y: footerY, w: g.col(0, 9).w, align: "left" },
      frame: { x: g.col(1, 10).x, y: 210 + nudge, w: g.col(1, 10).w, h: 430 },
      status: { x: g.col(10, 2).x, y: 250, w: g.col(10, 2).w, h: 220 },
      ref: { x: g.col(8, 4).x, y: headerY + 8, w: g.col(8, 4).w },
      showStatus: true,
      showFrame: true,
    };

    if (mode === "editorial") {
      plan.title = { x: g.col(0, 8).x, y: headerY, w: g.col(0, 8).w, align: "left", placement: "upper-third" };
      plan.frame = { x: g.col(4, 8).x, y: 200 + nudge, w: g.col(4, 8).w, h: 400 };
      plan.quote = { x: g.col(0, 7).x, y: footerY, w: g.col(0, 7).w, align: "left" };
      plan.status = { x: g.col(10, 2).x, y: 560, w: g.col(10, 2).w, h: 160 };
      plan.ref = { x: g.col(8, 4).x, y: headerY + 6, w: g.col(8, 4).w };
    } else if (mode === "split-field") {
      plan.title = { x: g.col(0, 6).x, y: headerY + 12, w: g.col(0, 6).w, align: "left", placement: "upper-third" };
      plan.frame = { x: g.col(6, 6).x, y: 160 + nudge, w: g.col(6, 6).w, h: 520 };
      plan.quote = { x: g.col(0, 6).x, y: 520, w: g.col(0, 6).w, align: "left" };
      plan.status = null;
      plan.showStatus = false;
      plan.ref = { x: g.col(0, 6).x, y: headerY + 70, w: g.col(0, 6).w };
    } else if (mode === "framed-object") {
      plan.title = { x: g.col(0, 8).x, y: headerY, w: g.col(0, 8).w, align: "left", placement: "upper-third" };
      plan.frame = { x: g.col(1, 10).x, y: 196 + nudge, w: g.col(1, 10).w, h: 448 };
      plan.quote = { x: g.col(1, 9).x, y: footerY - 8, w: g.col(1, 9).w, align: "left" };
      plan.status = { x: g.col(10, 2).x, y: 220, w: g.col(10, 2).w, h: 240 };
      plan.ref = { x: g.col(8, 4).x, y: headerY + 4, w: g.col(8, 4).w };
    } else if (mode === "type-dominant") {
      plan.title = { x: g.col(0, 12).x, y: headerY + 10, w: g.col(0, 12).w, align: "left", placement: "upper-third" };
      plan.frame = { x: g.col(6, 6).x, y: 340 + nudge, w: g.col(6, 6).w, h: 320 };
      plan.quote = { x: g.col(0, 7).x, y: 420, w: g.col(0, 7).w, align: "left" };
      plan.status = { x: g.col(10, 2).x, y: 700, w: g.col(10, 2).w, h: 120 };
    } else if (mode === "edge-flow") {
      plan.title = { x: g.col(1, 10).x, y: footerY - 20, w: g.col(1, 10).w, align: "left", placement: "lower-third" };
      plan.frame = { x: g.col(2, 8).x, y: 150 + nudge, w: g.col(2, 8).w, h: 460 };
      plan.quote = { x: g.col(1, 8).x, y: headerY, w: g.col(1, 8).w, align: "left" };
      plan.status = { x: g.col(10, 2).x, y: 180, w: g.col(10, 2).w, h: 200 };
    } else if (mode === "diagonal") {
      plan.title = { x: g.col(0, 8).x, y: headerY + 24, w: g.col(0, 8).w, align: "left", placement: "upper-third" };
      plan.frame = { x: g.col(3, 9).x, y: 250 + nudge, w: g.col(3, 9).w, h: 400 };
      plan.quote = { x: g.col(0, 7).x, y: 640, w: g.col(0, 7).w, align: "left" };
      plan.status = { x: g.col(10, 2).x, y: 160, w: g.col(10, 2).w, h: 180 };
    } else if (mode === "quiet-minimal") {
      plan.title = { x: g.col(1, 10).x, y: footerY - 10, w: g.col(1, 10).w, align: "left", placement: "lower-third" };
      plan.frame = { x: g.col(3, 6).x, y: 200 + nudge, w: g.col(3, 6).w, h: 360 };
      plan.quote = { x: g.col(1, 9).x, y: footerY + 52, w: g.col(1, 9).w, align: "left" };
      plan.status = null;
      plan.showStatus = false;
      plan.ref = { x: g.col(1, 4).x, y: headerY, w: g.col(1, 4).w };
    } else {
      // central-focus
      plan.title = { x: g.col(0, 10).x, y: headerY, w: g.col(0, 10).w, align: "left", placement: "upper-third" };
      plan.frame = { x: g.col(2, 8).x, y: 210 + nudge, w: g.col(2, 8).w, h: 420 };
      plan.quote = { x: g.col(2, 8).x, y: footerY, w: g.col(2, 8).w, align: "left" };
      plan.status = air > 0.62 ? null : { x: g.col(10, 2).x, y: 240, w: g.col(10, 2).w, h: 200 };
      plan.showStatus = !!plan.status;
    }

    if (energy < 0.28) {
      plan.showStatus = false;
      plan.status = null;
    }

    if (plan.frame) {
      plan.focalX = (plan.frame.x + plan.frame.w / 2) / W;
      plan.focalY = (plan.frame.y + plan.frame.h / 2) / H;
    } else {
      plan.focalX = Number(dna.composition?.focalX ?? 0.5);
      plan.focalY = Number(dna.composition?.focalY ?? 0.42);
    }

    plan.titleSafe = {
      x: (plan.title.x - 8) / W,
      y: (plan.title.y - 12) / H,
      w: (plan.title.w + 16) / W,
      h: 0.16,
    };
    plan.quoteSafe = {
      x: (plan.quote.x - 6) / W,
      y: (plan.quote.y - 8) / H,
      w: (plan.quote.w + 12) / W,
      h: 0.12,
    };

    return plan;
  }

  function specFromPlan(spec, plan) {
    return {
      ...spec,
      align: plan.title.align,
      title: { x: plan.title.x / W, y: (plan.title.y + 28) / H },
      titleSafe: plan.titleSafe,
      quoteAnchor: (plan.quote.y + 10) / H,
      quoteBox: plan.quote,
      titlePlacement: plan.title.placement,
      frame: plan.frame,
      status: plan.status,
      ref: plan.ref,
      mode: plan.mode,
      plan,
    };
  }

  function dusty(rgb, amount) {
    const paper = [232, 224, 210];
    return mix(rgb, paper, amount);
  }

  function drawTopographyShade(p, dna, seed, plan) {
    const pal = dna.palette || {};
    const bg = hexToRgb(pal.background || "#efe6d8");
    const hi = dusty(hexToRgb(pal.highlight || pal.accent || "#9fd8c8"), 0.35);
    const mid = dusty(hexToRgb(pal.secondary || "#c9b8a4"), 0.2);
    const rose = dusty(hexToRgb(pal.accent || "#c97b84"), 0.45);
    const ctx = p.drawingContext;
    ctx.save();
    const g = ctx.createLinearGradient(0, 0, W * 0.92, H);
    g.addColorStop(0, `rgba(${hi[0]},${hi[1]},${hi[2]},0.72)`);
    g.addColorStop(0.42, `rgba(${mid[0]},${mid[1]},${mid[2]},0.38)`);
    g.addColorStop(1, `rgba(${rose[0]},${rose[1]},${rose[2]},0.55)`);
    ctx.fillStyle = g;
    ctx.globalCompositeOperation = "multiply";
    ctx.fillRect(0, 0, W, H);
    ctx.globalCompositeOperation = "source-over";
    // Organic noise wash so it is not a cheap two-stop blend.
    p.randomSeed(seed + 3);
    p.noiseSeed(seed + 3);
    p.noStroke();
    for (let i = 0; i < 18; i += 1) {
      const x = p.noise(i * 0.2, 0.1) * W;
      const y = p.noise(0.4, i * 0.18) * H;
      const c = i % 2 ? hi : rose;
      p.fill(c[0], c[1], c[2], 10);
      p.ellipse(x, y, 180 + p.noise(i) * 220, 90 + p.noise(i, 2) * 140);
    }
    ctx.restore();
    p.blendMode(p.BLEND);
  }

  function drawContourField(p, dna, seed, plan) {
    const pal = dna.palette || {};
    const ink = hexToRgb(pal.text || "#2a241e");
    const energy = Number(dna.semantic?.narrativeEnergy ?? 0.5);
    const density = 0.35 + (1 - Number(dna.composition?.negativeSpace ?? 0.5)) * 0.4;
    const fx = plan.focalX;
    const fy = plan.focalY;
    p.noiseSeed(seed + 21);
    p.noFill();
    p.stroke(ink[0], ink[1], ink[2], 22 + density * 18);
    p.strokeWeight(0.7);
    const levels = Math.floor(7 + density * 8);
    for (let i = 0; i < levels; i += 1) {
      const iso = 0.18 + i * (0.62 / levels);
      p.beginShape();
      let drawing = false;
      for (let x = 0; x <= W; x += 6) {
        let y = H * (0.08 + iso * 0.84);
        y += (p.noise(x * 0.004, iso * 3, seed * 0.001) - 0.5) * 120 * (0.4 + energy);
        // Bend around the focal object so the field belongs to the artwork.
        const dx = x / W - fx;
        const dy = y / H - fy;
        const d = Math.hypot(dx, dy * 1.2);
        if (d < 0.22) {
          y += (0.22 - d) * 90 * (dy >= 0 ? 1 : -1);
        }
        const u = x / W;
        const v = y / H;
        const inTitle = u > plan.titleSafe.x && u < plan.titleSafe.x + plan.titleSafe.w && v > plan.titleSafe.y && v < plan.titleSafe.y + plan.titleSafe.h;
        if (inTitle || y < MARGIN || y > H - MARGIN) {
          if (drawing) {
            p.endShape();
            drawing = false;
          }
          continue;
        }
        if (!drawing) {
          p.beginShape();
          drawing = true;
        }
        p.vertex(x, y);
      }
      if (drawing) {
        p.endShape();
      }
    }
  }

  function frameColor(dna) {
    const pal = dna.palette || {};
    const family = dna.semantic?.grammarFamily || "drama";
    if (family === "comedy") {
      return hexToRgb(pal.secondary || "#2bb3b1");
    }
    if (family === "romance") {
      return mix(hexToRgb(pal.primary || "#c97b84"), hexToRgb("#4a3a58"), 0.35);
    }
    if (family === "contemporary") {
      return mix(hexToRgb(pal.primary || "#6a1228"), hexToRgb("#3a2a48"), 0.4);
    }
    return mix(hexToRgb(pal.primary || "#3d2a6a"), hexToRgb("#241838"), 0.25);
  }

  function drawCentralFrame(p, dna, seed, plan) {
    if (!plan.showFrame || !plan.frame) {
      return;
    }
    const { x, y, w, h } = plan.frame;
    const c = frameColor(dna);
    const ctx = p.drawingContext;
    ctx.save();
    ctx.globalCompositeOperation = "multiply";
    p.noStroke();
    p.fill(c[0], c[1], c[2], 55);
    p.rect(x, y, w, h, 16);
    ctx.restore();
    // Soft internal tint — background remains visible through the membrane.
    ctx.save();
    ctx.globalCompositeOperation = "overlay";
    p.fill(c[0], c[1], c[2], 28);
    p.rect(x + 8, y + 8, w - 16, h - 16, 12);
    ctx.restore();
    p.noFill();
    p.stroke(c[0], c[1], c[2], 110);
    p.strokeWeight(1.3);
    p.rect(x, y, w, h, 16);
    const family = artFamily(dna);
    if (family === "angular" || family === "ordered-grid" || family === "radial") {
      p.stroke(c[0], c[1], c[2], 50);
      p.strokeWeight(0.8);
      p.line(x + 18, y + 18, x + w - 18, y + 18);
      p.line(x + 18, y + 18, x + 18, y + h - 18);
      p.line(x + w * 0.5, y + 22, x + w * 0.5, y + h - 22);
      const steps = 4;
      for (let i = 1; i < steps; i += 1) {
        p.line(x + 22, y + (h * i) / steps, x + w * 0.22, y + (h * i) / steps);
      }
    }
    p.blendMode(p.BLEND);
  }

  function drawConnectionLines(p, dna, seed, plan, spec) {
    if (!plan.frame) {
      return;
    }
    const pal = dna.palette || {};
    const accent = hexToRgb(pal.accent);
    const secondary = hexToRgb(pal.secondary);
    const energy = Number(dna.semantic?.narrativeEnergy ?? 0.5);
    const kind = dna.semantic?.lineSemantics || "threads";
    const fx = plan.frame.x + plan.frame.w / 2;
    const fy = plan.frame.y + plan.frame.h / 2;
    const origins = [
      { x: plan.frame.x + plan.frame.w * 0.18, y: plan.frame.y + plan.frame.h * 0.28 },
      { x: plan.frame.x + plan.frame.w * 0.82, y: plan.frame.y + plan.frame.h * 0.32 },
      { x: plan.frame.x + plan.frame.w * 0.5, y: plan.frame.y + 8 },
      { x: plan.title.x + 24, y: plan.title.y + 36 },
    ];
    p.randomSeed(seed + 77);
    p.noiseSeed(seed + 77);
    p.noFill();
    const count = energy > 0.7 ? 7 : energy < 0.35 ? 3 : 5;
    for (let i = 0; i < count; i += 1) {
      const origin = origins[i % origins.length];
      const amp = (kind === "cords" || kind === "ribbons" ? 18 : 11) * (0.6 + energy);
      const freq = kind === "waves" || kind === "coastline" ? 0.035 : 0.055;
      p.stroke(i % 2 ? accent[0] : secondary[0], i % 2 ? accent[1] : secondary[1], i % 2 ? accent[2] : secondary[2], 90);
      p.strokeWeight(kind === "cords" ? 1.8 + (i % 3) * 0.6 : 1.1);
      p.beginShape();
      let x = origin.x;
      let y = origin.y;
      const dir = i % 2 === 0 ? 1 : -1;
      for (let s = 0; s < 42; s += 1) {
        p.vertex(x, y);
        const n = p.noise(i, s * freq);
        x += Math.cos(s * 0.12 * dir + i) * 7;
        y += (n - 0.45) * amp + dir * 2.2;
        if (x < MARGIN || x > W - MARGIN || y < MARGIN || y > H - MARGIN) {
          break;
        }
        const u = x / W;
        const v = y / H;
        if (spec?.titleSafe && u > spec.titleSafe.x && u < spec.titleSafe.x + spec.titleSafe.w && v > spec.titleSafe.y && v < spec.titleSafe.y + spec.titleSafe.h * 0.7) {
          p.endShape();
          p.beginShape();
        }
      }
      p.endShape();
    }
  }

  function drawStatusColumn(p, dna, seed, plan) {
    if (!plan.showStatus || !plan.status) {
      return;
    }
    const pal = dna.palette || {};
    const ink = hexToRgb(pal.text);
    const acc = hexToRgb(pal.accent);
    const { x, y, w } = plan.status;
    p.randomSeed(seed + 301);
    p.noStroke();
    const bars = 5;
    for (let i = 0; i < bars; i += 1) {
      const yy = y + i * 28;
      const ww = w * (0.35 + p.random() * 0.55);
      p.fill(acc[0], acc[1], acc[2], 160);
      p.rect(x + (p.random() - 0.5) * 2, yy, ww, 7, 1);
      p.fill(ink[0], ink[1], ink[2], 90);
      p.rect(x, yy + 10, w * 0.22, 2);
    }
  }

  function drawRefLabel(p, dna, seed, plan) {
    if (!plan.ref) {
      return;
    }
    const pal = dna.palette || {};
    const ink = hexToRgb(pal.text);
    const ctx = p.drawingContext;
    const code = `REF://${1000 + (seed % 9000)}.${seed % 9}`;
    ctx.save();
    ctx.font = '500 11px "IBM Plex Mono", monospace';
    ctx.fillStyle = `rgba(${ink[0]},${ink[1]},${ink[2]},0.55)`;
    ctx.textAlign = "right";
    ctx.textBaseline = "top";
    ctx.fillText(code, plan.ref.x + plan.ref.w, plan.ref.y);
    ctx.restore();
  }

  function drawPrintFinish(p, dna, seed, sampler) {
    const print = dna.print || { registration: 0.3, halftone: 0.3, scanlines: 0.12, grain: 0.4 };
    const pal = dna.palette || {};
    const ink = hexToRgb(pal.text || "#1a1410");
    p.randomSeed(seed + 880);
    // Halftone — denser where the plate is darker.
    const dots = Math.floor(900 * print.halftone);
    p.noStroke();
    for (let i = 0; i < dots; i += 1) {
      const u = p.random();
      const v = p.random();
      const lum = sampler && sampler.at ? sampler.at(u, v) : 0.5;
      if (lum > 0.72) {
        continue;
      }
      const r = (1.1 - lum) * 1.6 * print.halftone;
      p.fill(ink[0], ink[1], ink[2], 18 + (1 - lum) * 22);
      p.circle(u * W, v * H, r);
    }
    // Faint scan-lines — texture, not CRT.
    if (print.scanlines > 0.04) {
      p.stroke(ink[0], ink[1], ink[2], 8 + print.scanlines * 14);
      p.strokeWeight(1);
      for (let y = MARGIN; y < H - MARGIN; y += 3) {
        if (p.random() < print.scanlines) {
          p.line(MARGIN, y, W - MARGIN, y);
        }
      }
    }
    // Analog grain specks.
    p.noStroke();
    const grain = Math.floor(1400 * print.grain);
    for (let i = 0; i < grain; i += 1) {
      p.fill(ink[0], ink[1], ink[2], 10);
      p.rect(p.random(W), p.random(H), 1.1, p.random() > 0.6 ? 2.4 : 1);
    }
  }

  root.FrameFluxSystems = {
    makeGrid,
    compositionPlan,
    specFromPlan,
    compositionMode,
    artFamily,
    drawTopographyShade,
    drawContourField,
    drawCentralFrame,
    drawConnectionLines,
    drawStatusColumn,
    drawRefLabel,
    drawPrintFinish,
    MARGIN,
  };
})(window);
