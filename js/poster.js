const POSTER_W = 600;
const POSTER_H = 900;

const GENRE_FONT_RULES = [
  ["horror", "Cinzel"],
  ["sci", "Orbitron"],
  ["romance", "Playfair Display"],
  ["comedy", "Bungee"],
  ["action", "Oswald"],
  ["drama", "Cormorant Garamond"],
  ["thriller", "Russo One"],
  ["document", "IBM Plex Sans"],
  ["fantasy", "Cinzel"],
  ["mystery", "Oswald"],
  ["noir", "Oswald"],
  ["anim", "Bungee"],
  ["western", "Cinzel"],
];

function fontFor(genre, titleStyle) {
  if (titleStyle === "elegant" || titleStyle === "editorial") {
    return "Playfair Display";
  }
  if (titleStyle === "condensed") {
    return "Oswald";
  }
  if (titleStyle === "geometric") {
    return "Orbitron";
  }
  const g = (genre || "").toLowerCase();
  for (const [needle, font] of GENRE_FONT_RULES) {
    if (g.includes(needle)) {
      return font;
    }
  }
  return "Bebas Neue";
}

function hexToRgb(hex) {
  const n = String(hex || "#000000").replace("#", "");
  if (n.length !== 6) {
    return [0, 0, 0];
  }
  return [
    parseInt(n.slice(0, 2), 16),
    parseInt(n.slice(2, 4), 16),
    parseInt(n.slice(4, 6), 16),
  ];
}

function nx(n) {
  return n * POSTER_W;
}

function ny(n) {
  return n * POSTER_H;
}

function fitTextSize(p, font, str, maxWidth, startSize, minSize) {
  p.textFont(font || "sans-serif");
  let size = startSize;
  p.textSize(size);
  while (p.textWidth(str) > maxWidth && size > minSize) {
    size -= 2;
    p.textSize(size);
  }
  return size;
}

function wrapLines(p, text, maxWidth, maxLines) {
  const words = String(text || "")
    .trim()
    .split(/\s+/)
    .filter(Boolean);
  if (!words.length) {
    return [];
  }

  const lines = [];
  let current = "";
  for (let i = 0; i < words.length; i += 1) {
    const word = words[i];
    const next = current ? `${current} ${word}` : word;
    const isLastSlot = lines.length === maxLines - 1;
    if (p.textWidth(next) > maxWidth && current) {
      if (isLastSlot) {
        const rest = [current].concat(words.slice(i)).join(" ");
        let clipped = rest;
        while (p.textWidth(`${clipped}…`) > maxWidth && clipped.length > 1) {
          clipped = clipped.slice(0, -1).trim();
        }
        lines.push(`${clipped}…`);
        return lines;
      }
      lines.push(current);
      current = word;
    } else {
      current = next;
    }
  }
  if (current && lines.length < maxLines) {
    lines.push(current);
  }
  return lines;
}

function layoutSpec(layout) {
  const id = layout || "centered";
  if (id === "off-center-top") {
    return {
      id,
      align: "left",
      title: { x: 0.08, y: 0.18 },
      crop: { x: 0.5, y: 0.62, scale: 1.18 },
      inset: 0,
      split: 0,
      titleSafe: { x: 0.04, y: 0.08, w: 0.72, h: 0.28 },
    };
  }
  if (id === "off-center-bottom") {
    return {
      id,
      align: "left",
      title: { x: 0.08, y: 0.78 },
      crop: { x: 0.5, y: 0.36, scale: 1.16 },
      inset: 0,
      split: 0,
      titleSafe: { x: 0.04, y: 0.66, w: 0.78, h: 0.28 },
    };
  }
  if (id === "split-editorial") {
    return {
      id,
      align: "left",
      title: { x: 0.07, y: 0.22 },
      crop: { x: 0.68, y: 0.48, scale: 1.28 },
      inset: 0,
      split: 0.38,
      titleSafe: { x: 0.03, y: 0.1, w: 0.4, h: 0.55 },
    };
  }
  if (id === "frame-inset") {
    return {
      id,
      align: "center",
      title: { x: 0.5, y: 0.76 },
      crop: { x: 0.5, y: 0.44, scale: 1.08 },
      inset: 0.055,
      split: 0,
      titleSafe: { x: 0.12, y: 0.64, w: 0.76, h: 0.26 },
    };
  }
  return {
    id: "centered",
    align: "center",
    title: { x: 0.5, y: 0.5 },
    crop: { x: 0.5, y: 0.48, scale: 1.12 },
    inset: 0,
    split: 0,
    titleSafe: { x: 0.12, y: 0.36, w: 0.76, h: 0.3 },
  };
}

function dnaPalette(dna) {
  return dna.palette || {};
}

function dnaProc(dna) {
  return dna.procedural || {};
}

function dnaComp(dna) {
  return dna.composition || {};
}

function dnaCinematic(dna) {
  return dna.cinematic || {};
}

function inRect(px, py, rect) {
  return px >= rect.x && px <= rect.x + rect.w && py >= rect.y && py <= rect.y + rect.h;
}

function protectionWeight(u, v, dna, spec) {
  let w = 1;
  if (inRect(u, v, spec.titleSafe)) {
    w *= 0.12;
  }
  const fx = Number(dna.anchors?.focalX ?? dnaComp(dna).focalX ?? 0.5);
  const fy = Number(dna.anchors?.focalY ?? dnaComp(dna).focalY ?? 0.42);
  const d = Math.hypot(u - fx, (v - fy) * 1.15);
  if (d < 0.14) {
    w *= 0.18 + d / 0.14 * 0.5;
  }
  return w;
}

function drawContrastBackdrop(p, centerY, blockHeight, contrast) {
  const ctx = p.drawingContext;
  const padding = 44;
  const top = centerY - blockHeight / 2 - padding;
  const bottom = centerY + blockHeight / 2 + padding;
  const mid = 0.42 + Number(contrast || 0.75) * 0.4;

  const gradient = ctx.createLinearGradient(0, top, 0, bottom);
  gradient.addColorStop(0, "rgba(0,0,0,0)");
  gradient.addColorStop(0.28, `rgba(0,0,0,${mid})`);
  gradient.addColorStop(0.72, `rgba(0,0,0,${mid})`);
  gradient.addColorStop(1, "rgba(0,0,0,0)");

  ctx.save();
  ctx.fillStyle = gradient;
  ctx.fillRect(0, top, p.width, bottom - top);
  ctx.restore();
}

function drawCinematicPlate(p, dna, spec, seed) {
  const pal = dnaPalette(dna);
  const cine = dnaCinematic(dna);
  const bg = hexToRgb(pal.background);
  const primary = hexToRgb(pal.primary);
  const secondary = hexToRgb(pal.secondary);
  const accent = hexToRgb(pal.accent);
  const fx = Number(dnaComp(dna).focalX ?? 0.5);
  const fy = Number(dnaComp(dna).focalY ?? 0.42);
  const lighting = cine.lighting || "chiaroscuro";

  p.background(...bg);
  p.noiseSeed(seed);
  p.randomSeed(seed);

  const horizon = spec.id === "off-center-top" ? 0.42 : spec.id === "off-center-bottom" ? 0.58 : 0.52;
  p.noStroke();
  for (let y = 0; y < POSTER_H; y += 3) {
    const t = y / POSTER_H;
    const sky = t < horizon;
    const mix = sky ? t / horizon : (t - horizon) / (1 - horizon);
    const a = sky ? bg : primary;
    const b = sky ? secondary : bg;
    const r = a[0] + (b[0] - a[0]) * mix;
    const g = a[1] + (b[1] - a[1]) * mix;
    const bl = a[2] + (b[2] - a[2]) * mix;
    p.fill(r, g, bl, 70);
    p.rect(0, y, POSTER_W, 4);
  }

  const glow = lighting === "neon" ? accent : lighting === "golden-hour" ? hexToRgb(pal.highlight || pal.accent) : secondary;
  p.drawingContext.save();
  const grd = p.drawingContext.createRadialGradient(
    nx(fx),
    ny(fy),
    20,
    nx(fx),
    ny(fy),
    POSTER_W * 0.72
  );
  grd.addColorStop(0, `rgba(${glow[0]},${glow[1]},${glow[2]},0.55)`);
  grd.addColorStop(1, "rgba(0,0,0,0)");
  p.drawingContext.fillStyle = grd;
  p.drawingContext.fillRect(0, 0, POSTER_W, POSTER_H);
  p.drawingContext.restore();

  // Soft figure / subject mass at the focal point — reads as photography, not a glyph.
  p.noStroke();
  p.fill(...primary, 90);
  p.ellipse(nx(fx), ny(fy + 0.04), POSTER_W * 0.22, POSTER_H * 0.42);
  p.fill(...bg, 120);
  p.ellipse(nx(fx), ny(fy - 0.02), POSTER_W * 0.12, POSTER_H * 0.14);

  for (let i = 0; i < 1400; i += 1) {
    const x = p.random(POSTER_W);
    const y = p.random(POSTER_H);
    const n = p.noise(x * 0.01, y * 0.01);
    p.fill(255, 255, 255, 6 + n * 10);
    p.rect(x, y, 1, 1);
  }

  p.noFill();
  p.stroke(...accent, 18);
  p.strokeWeight(1);
  p.line(0, ny(horizon), POSTER_W, ny(horizon));

  // Vignette
  p.drawingContext.save();
  const vig = p.drawingContext.createRadialGradient(
    POSTER_W / 2,
    POSTER_H / 2,
    POSTER_H * 0.18,
    POSTER_W / 2,
    POSTER_H / 2,
    POSTER_H * 0.72
  );
  vig.addColorStop(0, "rgba(0,0,0,0)");
  vig.addColorStop(1, "rgba(0,0,0,0.55)");
  p.drawingContext.fillStyle = vig;
  p.drawingContext.fillRect(0, 0, POSTER_W, POSTER_H);
  p.drawingContext.restore();
}

function drawKeyArt(p, img, spec) {
  const crop = spec.crop;
  const scale = Math.max(POSTER_W / img.width, POSTER_H / img.height) * (crop.scale || 1.12);
  const dw = img.width * scale;
  const dh = img.height * scale;
  const dx = POSTER_W / 2 - dw * crop.x;
  const dy = POSTER_H / 2 - dh * crop.y;
  p.image(img, dx, dy, dw, dh);
}

function buildSampler(p) {
  const cols = 40;
  const rows = 60;
  const grid = [];
  let brightest = { u: 0.5, v: 0.4, l: 0 };
  for (let y = 0; y < rows; y += 1) {
    grid[y] = [];
    for (let x = 0; x < cols; x += 1) {
      const px = ((x + 0.5) / cols) * POSTER_W;
      const py = ((y + 0.5) / rows) * POSTER_H;
      const c = p.get(px, py);
      const l = (c[0] * 0.299 + c[1] * 0.587 + c[2] * 0.114) / 255;
      grid[y][x] = l;
      if (l > brightest.l) {
        brightest = { u: (x + 0.5) / cols, v: (y + 0.5) / rows, l };
      }
    }
  }
  const at = (u, v) => {
    const x = Math.max(0, Math.min(cols - 1, Math.floor(u * cols)));
    const y = Math.max(0, Math.min(rows - 1, Math.floor(v * rows)));
    return grid[y][x];
  };
  const edge = (u, v) => {
    const step = 1 / cols;
    return Math.abs(at(u + step, v) - at(u - step, v)) + Math.abs(at(u, v + step) - at(u, v - step));
  };
  return { at, edge, brightest };
}

function drawFlowField(p, dna, seed, sampler, spec, weight) {
  const pal = dnaPalette(dna);
  const density = dnaProc(dna).density || 0.6;
  const count = Math.floor(420 * density * weight);
  p.noiseSeed(seed);
  p.randomSeed(seed);
  p.noFill();
  p.strokeWeight(1.05);
  for (let i = 0; i < count; i += 1) {
    let x = p.random(POSTER_W);
    let y = p.random(POSTER_H);
    const u = x / POSTER_W;
    const v = y / POSTER_H;
    const protect = protectionWeight(u, v, dna, spec);
    if (protect < 0.2) {
      continue;
    }
    const lum = sampler.at(u, v);
    const mix = i / count;
    const c = mix < 0.45 ? pal.primary : mix < 0.8 ? pal.secondary : pal.accent;
    p.stroke(...hexToRgb(c), 28 + lum * 50);
    p.beginShape();
    for (let s = 0; s < 36; s += 1) {
      const uu = x / POSTER_W;
      const vv = y / POSTER_H;
      if (protectionWeight(uu, vv, dna, spec) < 0.18) {
        break;
      }
      p.vertex(x, y);
      const n = p.noise(x * 0.004, y * 0.004);
      const edgeBias = sampler.edge(uu, vv);
      const angle = n * p.TWO_PI * 2.4 + edgeBias * 1.8;
      x += Math.cos(angle) * 4.4;
      y += Math.sin(angle) * 4.4;
      if (x < -20 || x > POSTER_W + 20 || y < -20 || y > POSTER_H + 20) {
        break;
      }
    }
    p.endShape();
  }
}

function drawGrid(p, dna, seed, sampler, spec, weight) {
  const pal = dnaPalette(dna);
  p.randomSeed(seed);
  const density = dnaProc(dna).density || 0.6;
  const cols = Math.floor(7 + density * 8);
  const rows = Math.floor(10 + density * 10);
  const cellW = POSTER_W / cols;
  const cellH = POSTER_H / rows;
  p.noFill();
  p.strokeWeight(1);
  for (let y = 0; y < rows; y += 1) {
    for (let x = 0; x < cols; x += 1) {
      const u = (x + 0.5) / cols;
      const v = (y + 0.5) / rows;
      const protect = protectionWeight(u, v, dna, spec);
      const edge = sampler.edge(u, v);
      const lum = sampler.at(u, v);
      if (protect < 0.2 || (edge < 0.04 && lum > 0.55)) {
        continue;
      }
      const inset = 3 + (1 - edge) * cellW * 0.25;
      p.push();
      p.translate(x * cellW + cellW / 2, y * cellH + cellH / 2);
      p.rotate(edge > 0.12 ? p.QUARTER_PI / 5 : 0);
      p.stroke(...hexToRgb(lum > 0.5 ? pal.accent : pal.primary), 40 * protect * weight);
      p.rectMode(p.CENTER);
      p.rect(0, 0, (cellW - inset) * weight, (cellH - inset * 0.6) * weight);
      p.pop();
    }
  }
}

function drawParticles(p, dna, seed, sampler, spec, weight) {
  const pal = dnaPalette(dna);
  p.randomSeed(seed);
  const density = dnaProc(dna).density || 0.6;
  const count = Math.floor(220 + density * 380);
  const fx = Number(dnaComp(dna).focalX ?? 0.5);
  const fy = Number(dnaComp(dna).focalY ?? 0.42);
  p.noStroke();
  for (let i = 0; i < count; i += 1) {
    let u = p.random();
    let v = p.random();
    // Cluster toward bright / focal regions.
    if (p.random() < 0.55) {
      u = fx + (p.random() - 0.5) * 0.42;
      v = fy + (p.random() - 0.5) * 0.36;
    }
    u = Math.max(0, Math.min(1, u));
    v = Math.max(0, Math.min(1, v));
    const protect = protectionWeight(u, v, dna, spec);
    const lum = sampler.at(u, v);
    if (protect < 0.22 || lum < 0.08) {
      continue;
    }
    const tier = p.random();
    const r = tier < 0.7 ? p.random(0.8, 1.8) : tier < 0.93 ? p.random(2.2, 4.5) : p.random(5, 9);
    const c = tier < 0.6 ? pal.primary : tier < 0.85 ? pal.secondary : pal.accent;
    p.fill(...hexToRgb(c), 40 + lum * 110 * protect * weight);
    p.circle(nx(u), ny(v), r * weight);
  }
}

function drawRings(p, dna, seed, sampler, spec, weight) {
  const pal = dnaPalette(dna);
  p.randomSeed(seed);
  p.noiseSeed(seed);
  const fx = sampler.brightest.u * 0.35 + Number(dnaComp(dna).focalX ?? 0.5) * 0.65;
  const fy = sampler.brightest.v * 0.35 + Number(dnaComp(dna).focalY ?? 0.42) * 0.65;
  const rings = 9 + Math.floor((dnaProc(dna).density || 0.6) * 6);
  p.noFill();
  p.strokeWeight(1);
  for (let i = 1; i <= rings; i += 1) {
    const t = i / rings;
    const rx = POSTER_W * (0.08 + t * 0.55);
    const ry = POSTER_H * (0.05 + t * 0.38) * (0.78 + p.noise(i * 0.2) * 0.4);
    const alpha = (70 - t * 50) * weight;
    p.stroke(...hexToRgb(i % 3 === 0 ? pal.accent : pal.secondary), alpha);
    p.push();
    p.translate(nx(fx), ny(fy));
    p.rotate((p.noise(i) - 0.5) * 0.35);
    const steps = 48;
    p.beginShape();
    for (let s = 0; s <= steps; s += 1) {
      const a = (s / steps) * p.TWO_PI;
      const jitter = (p.noise(i, s * 0.15) - 0.5) * 14;
      const x = Math.cos(a) * (rx + jitter);
      const y = Math.sin(a) * (ry + jitter);
      const u = (nx(fx) + x) / POSTER_W;
      const v = (ny(fy) + y) / POSTER_H;
      if (protectionWeight(u, v, dna, spec) < 0.2) {
        p.endShape();
        p.beginShape();
        continue;
      }
      p.vertex(x, y);
    }
    p.endShape();
    p.pop();
  }
}

function drawMesh(p, dna, seed, sampler, spec, weight) {
  const pal = dnaPalette(dna);
  p.randomSeed(seed);
  const cols = 8;
  const rows = 12;
  const points = [];
  for (let y = 0; y <= rows; y += 1) {
    points[y] = [];
    for (let x = 0; x <= cols; x += 1) {
      const u = x / cols;
      const v = y / rows;
      const edge = sampler.edge(u, v);
      const jx = (p.random() - 0.5) * 18 * (0.3 + edge * 2);
      const jy = (p.random() - 0.5) * 16 * (0.3 + edge * 2);
      points[y][x] = { x: nx(u) + jx, y: ny(v) + jy, u, v, edge };
    }
  }
  p.noFill();
  p.strokeWeight(0.9);
  for (let y = 0; y < rows; y += 1) {
    for (let x = 0; x < cols; x += 1) {
      const a = points[y][x];
      const b = points[y][x + 1];
      const c = points[y + 1][x];
      const protect = protectionWeight(a.u, a.v, dna, spec);
      if (protect < 0.22) {
        continue;
      }
      const alpha = (24 + a.edge * 90) * protect * weight;
      p.stroke(...hexToRgb(a.edge > 0.1 ? pal.accent : pal.primary), alpha);
      p.triangle(a.x, a.y, b.x, b.y, c.x, c.y);
    }
  }
}

function drawPattern(p, name, dna, seed, sampler, spec, weight) {
  if (name === "grid") {
    drawGrid(p, dna, seed, sampler, spec, weight);
  } else if (name === "particles") {
    drawParticles(p, dna, seed, sampler, spec, weight);
  } else if (name === "rings") {
    drawRings(p, dna, seed, sampler, spec, weight);
  } else if (name === "mesh") {
    drawMesh(p, dna, seed, sampler, spec, weight);
  } else {
    drawFlowField(p, dna, seed, sampler, spec, weight);
  }
}

function patternPlan(dna, role) {
  const proc = dnaProc(dna);
  const primary = proc.primaryPattern || dna.pattern || "flow";
  const secondary = proc.secondaryPattern || "grid";
  if (role === "hybrid") {
    return [
      { name: primary, weight: 0.85, seedShift: 0 },
      { name: secondary, weight: 0.45, seedShift: 91 },
    ];
  }
  if (role === "alternative") {
    return [
      { name: secondary, weight: 0.95, seedShift: 17 },
      { name: primary, weight: 0.22, seedShift: 140 },
    ];
  }
  return [{ name: primary, weight: 1, seedShift: 0 }];
}

function drawTypography(p, dna, spec) {
  const pal = dnaPalette(dna);
  const textColor = pal.text;
  const accent = pal.accent;
  const genre = dna.concept?.genre || dna.genre || "";
  const titleStyle = dna.typography?.titleStyle || dna.titleStyle || "bold";
  const font = fontFor(genre, titleStyle);
  const titleCase = String(dna.concept?.title || dna.title || "").toUpperCase();
  const startSize =
    titleStyle === "condensed" ? 46 : titleStyle === "elegant" || titleStyle === "editorial" ? 48 : 54;
  const minSize = 26;
  const maxTitleWidth = spec.id === "split-editorial" ? POSTER_W * 0.36 : POSTER_W * 0.78;
  const quoteWidth = spec.id === "split-editorial" ? POSTER_W * 0.34 : POSTER_W * 0.72;

  p.textFont(font);
  const fitted = fitTextSize(p, font, titleCase, maxTitleWidth, startSize, minSize);
  let titleLines;
  let titleSize = fitted;
  if (fitted > minSize || p.textWidth(titleCase) <= maxTitleWidth) {
    titleLines = [titleCase];
  } else {
    titleSize = Math.max(minSize, startSize - 10);
    p.textSize(titleSize);
    titleLines = wrapLines(p, titleCase, maxTitleWidth, 3);
    let longest = Math.max(...titleLines.map((line) => p.textWidth(line)), 0);
    while (longest > maxTitleWidth && titleSize > minSize) {
      titleSize -= 2;
      p.textSize(titleSize);
      titleLines = wrapLines(p, titleCase, maxTitleWidth, 3);
      longest = Math.max(...titleLines.map((line) => p.textWidth(line)), 0);
    }
  }

  const lineHeight = titleSize * 1.08;
  const genreSize = 12;
  const quoteSize = 15;
  p.textFont("Cormorant Garamond");
  p.textSize(quoteSize);
  const quoteLines = wrapLines(p, dna.concept?.quote || dna.quote || "", quoteWidth, 2);

  const genreGap = 16;
  const quoteGap = 20;
  const blockHeight =
    genreSize +
    genreGap +
    titleLines.length * lineHeight +
    (quoteLines.length ? quoteGap + quoteLines.length * 22 : 0);

  const centerY = ny(spec.title.y);
  const contrast = dnaProc(dna).contrast || dna.contrast || 0.75;
  drawContrastBackdrop(p, centerY, blockHeight, contrast);

  const top = centerY - blockHeight / 2;
  const x = nx(spec.title.x);
  const alignMode =
    spec.align === "center" ? p.CENTER : spec.align === "right" ? p.RIGHT : p.LEFT;

  p.noStroke();
  p.textAlign(alignMode, p.TOP);

  p.fill(...hexToRgb(accent));
  p.textFont("IBM Plex Sans");
  p.textSize(genreSize);
  p.text(String(genre).toUpperCase(), x, top);

  p.fill(...hexToRgb(textColor));
  p.textFont(font);
  p.textSize(titleSize);
  p.textLeading(lineHeight);
  p.text(titleLines.join("\n"), x, top + genreSize + genreGap);

  if (quoteLines.length) {
    const quoteY = top + genreSize + genreGap + titleLines.length * lineHeight + quoteGap;
    p.fill(...hexToRgb(accent));
    p.textFont("Cormorant Garamond");
    p.textSize(quoteSize);
    p.textLeading(22);
    p.text(`“${quoteLines.join("\n")}”`, x, quoteY);
  }
}

function finishFrame(p, dna, spec) {
  const pal = dnaPalette(dna);
  const bg = hexToRgb(pal.background);
  if (spec.inset > 0) {
    const m = spec.inset * POSTER_W;
    p.noFill();
    p.stroke(...hexToRgb(pal.accent), 70);
    p.strokeWeight(1);
    p.rect(m, m * 1.4, POSTER_W - m * 2, POSTER_H - m * 2.8);
    p.noStroke();
    p.fill(...bg);
    p.rect(0, 0, POSTER_W, m * 1.2);
    p.rect(0, POSTER_H - m * 1.2, POSTER_W, m * 1.2);
    p.rect(0, 0, m, POSTER_H);
    p.rect(POSTER_W - m, 0, m, POSTER_H);
  }
  if (spec.split > 0) {
    p.noStroke();
    p.fill(...bg, 70);
    p.rect(0, 0, POSTER_W * spec.split, POSTER_H);
  }
}

function createPoster(containerId, options = {}) {
  let current = null;
  let seed = 1;
  let role = "signature";
  let keyArt = null;
  let p5Instance = null;
  const density = options.pixelDensity || 2;

  const sketch = (p) => {
    p.setup = function setup() {
      const canvas = p.createCanvas(POSTER_W, POSTER_H);
      p.pixelDensity(density);
      p.noLoop();
      p.textAlign(p.LEFT, p.TOP);
      canvas.elt.style.width = "100%";
      canvas.elt.style.height = "auto";
      canvas.elt.style.maxWidth = "100%";
    };

    p.draw = function draw() {
      if (!current) {
        p.background(18, 16, 13);
        p.fill(236, 230, 216, 140);
        p.textFont("IBM Plex Sans");
        p.textSize(16);
        p.textAlign(p.CENTER, p.CENTER);
        p.text("Your poster will appear here.", POSTER_W / 2, POSTER_H / 2);
        return;
      }

      p.randomSeed(seed);
      p.noiseSeed(seed);
      const spec = layoutSpec(dnaComp(current).layout || current.layout);
      if (keyArt) {
        p.background(...hexToRgb(dnaPalette(current).background));
        drawKeyArt(p, keyArt, spec);
      } else {
        drawCinematicPlate(p, current, spec, seed);
      }
      finishFrame(p, current, spec);
      const sampler = buildSampler(p);
      const intensity = Number(dnaProc(current).intensity || 0.55);
      p.push();
      p.drawingContext.globalAlpha = 0.38 + intensity * 0.12;
      p.blendMode(p.OVERLAY);
      for (const layer of patternPlan(current, role)) {
        drawPattern(
          p,
          layer.name,
          current,
          seed + layer.seedShift,
          sampler,
          spec,
          layer.weight * (0.7 + intensity * 0.3)
        );
      }
      p.pop();
      p.blendMode(p.BLEND);
      drawTypography(p, current, spec);
    };
  };

  p5Instance = new p5(sketch, containerId);

  return {
    render(params, nextSeed, nextRole, image) {
      current = params;
      seed = nextSeed;
      role = nextRole || "signature";
      keyArt = image || null;
      p5Instance.redraw();
    },
    loadImage(dataUrl) {
      return new Promise((resolve) => {
        if (!dataUrl) {
          resolve(null);
          return;
        }
        p5Instance.loadImage(
          dataUrl,
          (img) => resolve(img),
          () => resolve(null)
        );
      });
    },
    download(filename) {
      p5Instance.saveCanvas(filename, "png");
    },
    hasPoster() {
      return current !== null;
    },
    canvas() {
      return p5Instance.canvas;
    },
  };
}

window.FrameFluxPoster = { createPoster, wrapLines, fitTextSize, layoutSpec };
