const POSTER_W = 600;
const POSTER_H = 900;

// Font families keyed by letterform language, with the weights actually loaded
// in index.html so we never fall back to a synthesised face.
const FONT_FAMILIES = {
  "Playfair Display": { weights: [400, 500, 700, 900], italic: true },
  "Cormorant Garamond": { weights: [400, 500, 600], italic: true },
  Cinzel: { weights: [400, 500, 700, 900], italic: false },
  "Roboto Slab": { weights: [100, 300, 400, 700, 900], italic: false },
  Jost: { weights: [200, 300, 400, 500, 700], italic: false },
  "Space Grotesk": { weights: [300, 400, 500, 700], italic: false },
  Oswald: { weights: [200, 300, 400, 500, 700], italic: false },
  Archivo: { weights: [100, 300, 400, 700, 900], italic: false },
  "Saira Stencil One": { weights: [400], italic: false },
  Caveat: { weights: [400, 500, 600, 700], italic: false },
  "IBM Plex Mono": { weights: [400, 600], italic: false },
  "IBM Plex Sans": { weights: [400, 500, 600], italic: false },
};

const LETTERFORM_FAMILY = {
  "classical-serif": "Playfair Display",
  "slab-serif": "Roboto Slab",
  "geometric-sans": "Jost",
  grotesque: "Space Grotesk",
  condensed: "Oswald",
  extended: "Archivo",
  "hand-lettered": "Caveat",
  distressed: "Cinzel",
  "technical-stencil": "Saira Stencil One",
};

const WEIGHT_VALUE = { hairline: 100, light: 300, regular: 400, bold: 700, black: 900 };

const TRACKING_EM = { tight: -0.035, normal: 0, wide: 0.14 };

// Each quote style carries its own face, slant, and size relationship to the title.
const QUOTE_STYLE_FACE = {
  "editorial-italic": { family: "Cormorant Garamond", italic: true, weight: 500, ratio: 0.34, min: 15, lead: 1.45 },
  caption: { family: "IBM Plex Sans", italic: false, weight: 500, ratio: 0.26, min: 12, lead: 1.55, upper: true, track: 0.08 },
  "cinematic-subtitle": { family: "Space Grotesk", italic: false, weight: 400, ratio: 0.3, min: 14, lead: 1.5 },
  typewriter: { family: "IBM Plex Mono", italic: false, weight: 400, ratio: 0.26, min: 12, lead: 1.6 },
  handwritten: { family: "Caveat", italic: false, weight: 600, ratio: 0.42, min: 18, lead: 1.35 },
};

function snapWeight(family, requested) {
  const meta = FONT_FAMILIES[family];
  if (!meta) {
    return requested;
  }
  return meta.weights.reduce(
    (best, w) => (Math.abs(w - requested) < Math.abs(best - requested) ? w : best),
    meta.weights[0]
  );
}

function cssFont(family, weight, size, italic) {
  const slant = italic && FONT_FAMILIES[family]?.italic ? "italic " : "";
  return `${slant}${weight} ${Math.round(size)}px "${family}", sans-serif`;
}

const LETTER_SPACING_SUPPORTED =
  typeof CanvasRenderingContext2D !== "undefined" &&
  "letterSpacing" in CanvasRenderingContext2D.prototype;

/**
 * Title and quote faces resolved from Visual DNA. Genre is deliberately absent:
 * it informs the DNA upstream but never selects a face here.
 */
function titleFace(dna) {
  const dir = dna.typography?.title || {};
  const letterforms = dir.letterforms || "grotesque";
  const family = LETTERFORM_FAMILY[letterforms] || "Space Grotesk";
  return {
    family,
    weight: snapWeight(family, WEIGHT_VALUE[dir.weight] ?? 400),
    italic: false,
    tracking: TRACKING_EM[dir.tracking] ?? 0,
    letterforms,
    case: dir.case || "uppercase",
    structure: dir.structure || "solid",
    placement: dir.placement || "centered",
  };
}

function quoteFace(dna) {
  const dir = dna.typography?.quote || {};
  const style = dir.style || "editorial-italic";
  const face = QUOTE_STYLE_FACE[style] || QUOTE_STYLE_FACE["editorial-italic"];
  return {
    ...face,
    style,
    weight: snapWeight(face.family, face.weight),
    legibility: dir.legibility || "scrim",
    placement: dir.placement || "below-title",
  };
}

/**
 * Ask the browser for the exact faces this DNA needs. Resolves to true when a
 * font arrived that was not previously available, meaning a redraw is worthwhile.
 */
function ensureTypeFaces(dna) {
  if (typeof document === "undefined" || !document.fonts || !document.fonts.load) {
    return Promise.resolve(false);
  }
  const title = titleFace(dna);
  const quote = quoteFace(dna);
  const specs = [
    cssFont(title.family, title.weight, 48, false),
    cssFont(quote.family, quote.weight, 16, quote.italic),
  ];
  const missing = specs.filter((s) => {
    try {
      return !document.fonts.check(s);
    } catch (err) {
      return true;
    }
  });
  if (!missing.length) {
    return Promise.resolve(false);
  }
  return Promise.all(missing.map((s) => document.fonts.load(s).catch(() => null))).then(() => true);
}

function applyCase(text, mode) {
  const raw = String(text || "");
  if (mode === "uppercase") {
    return raw.toUpperCase();
  }
  if (mode === "lowercase") {
    return raw.toLowerCase();
  }
  if (mode === "title") {
    return raw.replace(/\w\S*/g, (w) => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase());
  }
  if (mode === "mixed") {
    return raw
      .split(/\s+/)
      .map((w, i) => (i % 2 === 0 ? w.toUpperCase() : w.toLowerCase()))
      .join(" ");
  }
  return raw;
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

function styleCtx(ctx, face, size) {
  ctx.font = cssFont(face.family, face.weight, size, face.italic);
  const track = Number(face.tracking ?? face.track ?? 0);
  if (LETTER_SPACING_SUPPORTED) {
    ctx.letterSpacing = `${track}em`;
  }
}

function measureStyled(ctx, face, size, str) {
  styleCtx(ctx, face, size);
  let w = ctx.measureText(str).width;
  if (!LETTER_SPACING_SUPPORTED) {
    const track = Number(face.tracking ?? face.track ?? 0);
    w += track * size * Math.max(0, str.length - 1);
  }
  return w;
}

/**
 * Split a word that can never fit on one line into chunks that can, so a long
 * unbroken title hard-breaks instead of running past the safe width.
 */
function breakLongWord(ctx, face, size, word, maxWidth) {
  const chunks = [];
  let chunk = "";
  for (const ch of word) {
    const next = chunk + ch;
    if (chunk && measureStyled(ctx, face, size, `${next}-`) > maxWidth) {
      chunks.push(`${chunk}-`);
      chunk = ch;
    } else {
      chunk = next;
    }
  }
  if (chunk) {
    chunks.push(chunk);
  }
  return chunks.length ? chunks : [word];
}

function wrapStyled(ctx, face, size, text, maxWidth, maxLines) {
  const raw = String(text || "").trim().split(/\s+/).filter(Boolean);
  if (!raw.length) {
    return [];
  }
  // Pre-split any single word that is wider than the whole safe area.
  const words = [];
  for (const word of raw) {
    if (measureStyled(ctx, face, size, word) > maxWidth) {
      words.push(...breakLongWord(ctx, face, size, word, maxWidth));
    } else {
      words.push(word);
    }
  }
  const lines = [];
  let current = "";
  for (let i = 0; i < words.length; i += 1) {
    const next = current ? `${current} ${words[i]}` : words[i];
    const isLastSlot = lines.length === maxLines - 1;
    if (measureStyled(ctx, face, size, next) > maxWidth && current) {
      if (isLastSlot) {
        // Rejoin without reintroducing spaces inside a hard-broken word.
        let clipped = [current]
          .concat(words.slice(i))
          .join(" ")
          .replace(/-\s+/g, "-");
        while (
          clipped.length > 1 &&
          measureStyled(ctx, face, size, `${clipped}…`) > maxWidth
        ) {
          clipped = clipped.slice(0, -1).trim();
        }
        lines.push(`${clipped}…`);
        return lines;
      }
      lines.push(current);
      current = words[i];
    } else {
      current = next;
    }
  }
  if (current && lines.length < maxLines) {
    lines.push(current);
  }
  return lines;
}

/**
 * Title scale is derived from length and the available safe width. Long titles
 * break to more lines before they are allowed to shrink below a legible floor.
 */
function fitTitleBlock(ctx, face, text, maxWidth, maxSize, minSize) {
  for (let maxLines = 1; maxLines <= 3; maxLines += 1) {
    // A single long word can never wrap, so allow it to scale down further.
    const floor = maxLines === 3 ? minSize : Math.max(minSize, maxSize * (maxLines === 1 ? 0.62 : 0.46));
    for (let size = maxSize; size >= floor; size -= 2) {
      const lines = wrapStyled(ctx, face, size, text, maxWidth, maxLines);
      if (lines.length > maxLines) {
        continue;
      }
      const widest = Math.max(...lines.map((l) => measureStyled(ctx, face, size, l)), 0);
      if (widest <= maxWidth && !lines.some((l) => l.endsWith("…"))) {
        return { lines, size };
      }
    }
  }
  const lines = wrapStyled(ctx, face, minSize, text, maxWidth, 3);
  return { lines, size: minSize };
}

/**
 * Resolve where the title and quote sit, based on the DNA placement decision and
 * the focal mass, so type lands in negative space rather than a fixed slot.
 * Returns an extended spec; titleSafe is updated so procedural marks stay clear.
 */
function typeLayout(dna, spec) {
  const face = titleFace(dna);
  const quote = quoteFace(dna);
  const focalY = Number(dnaComp(dna).focalY ?? 0.42);
  let placement = face.placement;

  // Never stack the title on top of the focal mass.
  if (placement === "centered" && Math.abs(focalY - 0.5) < 0.12) {
    placement = focalY <= 0.5 ? "lower-third" : "upper-third";
  }

  const isSplitColumn = spec.id === "split-editorial";
  const bandFor = (id) => {
    if (id === "upper-third") return 0.19;
    if (id === "lower-third") return 0.79;
    if (id === "split") return focalY <= 0.5 ? 0.76 : 0.22;
    return 0.5;
  };
  const titleY = bandFor(placement);

  const align = isSplitColumn ? "left" : spec.align;
  const titleX = isSplitColumn ? 0.07 : align === "center" ? 0.5 : 0.08;
  const halfH = placement === "centered" ? 0.16 : 0.13;

  let quoteY;
  if (quote.placement === "bottom-anchored") {
    quoteY = 0.9;
  } else if (quote.placement === "focal-adjacent") {
    quoteY = Math.min(0.9, Math.max(0.12, focalY + (focalY > titleY ? -0.24 : 0.24)));
  } else {
    quoteY = null; // below-title, resolved against the measured title block
  }

  return {
    ...spec,
    align,
    title: { x: titleX, y: titleY },
    titleSafe: {
      x: Math.max(0.02, titleX - (align === "center" ? 0.42 : 0.04)),
      y: Math.max(0.02, titleY - halfH),
      w: isSplitColumn ? 0.46 : align === "center" ? 0.84 : 0.86,
      h: halfH * 2,
    },
    quoteAnchor: quoteY,
    titlePlacement: placement,
  };
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

function dnaSemantic(dna) {
  return dna.semantic || {};
}

function dnaLight(dna) {
  return dna.lighting || {};
}

function inRect(px, py, rect) {
  return px >= rect.x && px <= rect.x + rect.w && py >= rect.y && py <= rect.y + rect.h;
}

function lightOrigin(dna, seed) {
  const base = Number(dnaLight(dna).direction ?? dna.lightDirection ?? 0.35);
  // Seed varies lighting direction slightly without changing Visual DNA.
  const wobble = ((seed % 17) / 17 - 0.5) * 0.12;
  const t = Math.max(0, Math.min(1, base + wobble));
  return { x: 0.12 + t * 0.76, y: 0.1 + ((seed % 11) / 11) * 0.22, t };
}

function materialEmphasis(dna, seed) {
  const base = Number(dnaProc(dna).materialEmphasis ?? 0.7);
  return Math.max(0.35, Math.min(1, base + ((seed % 9) / 9 - 0.5) * 0.08));
}

function anchorScale(dna, seed) {
  const base = Number(dnaComp(dna).anchorScale ?? 0.92);
  return Math.max(0.7, Math.min(1.2, base + ((seed % 13) / 13 - 0.5) * 0.08));
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

function drawMaterialGrain(p, dna, seed, intensity) {
  const material = dnaSemantic(dna).material || "paper";
  const texture = dnaSemantic(dna).texture || "grainy";
  p.randomSeed(seed + 401);
  p.noiseSeed(seed + 401);
  const count =
    material === "concrete" || material === "dust" || texture === "grainy"
      ? 2800
      : material === "film-stock" || texture === "photographic"
        ? 2200
        : material === "glass" || material === "plastic"
          ? 900
          : 1600;
  const alphaBase = 8 + intensity * 14;
  for (let i = 0; i < count; i += 1) {
    const x = p.random(POSTER_W);
    const y = p.random(POSTER_H);
    const n = p.noise(x * 0.02, y * 0.02);
    let a = alphaBase * (0.4 + n);
    let w = 1.1;
    let h = 1.1;
    if (material === "metal" || texture === "scratched") {
      w = p.random(2, 9);
      h = 0.7;
      a *= 0.7;
    } else if (material === "fabric" || texture === "fibrous") {
      w = 0.8;
      h = p.random(2, 7);
    } else if (material === "smoke" || material === "water") {
      a *= 0.45;
      w = p.random(1.5, 4);
      h = w;
    }
    p.noStroke();
    p.fill(255, 255, 255, a);
    p.rect(x, y, w, h);
  }
}

function drawDirectionalLight(p, dna, seed, fx, fy) {
  const pal = dnaPalette(dna);
  const cine = dnaCinematic(dna);
  const lighting = cine.lighting || "chiaroscuro";
  const shadow = Number(dnaLight(dna).shadowDensity ?? dna.shadowDensity ?? 0.55);
  const origin = lightOrigin(dna, seed);
  const accent = hexToRgb(pal.accent);
  const secondary = hexToRgb(pal.secondary);
  const highlight = hexToRgb(pal.highlight || pal.accent);

  let glow = secondary;
  let glowAlpha = 0.42;
  if (lighting === "neon") {
    glow = accent;
    glowAlpha = 0.58;
  } else if (lighting === "golden-hour") {
    glow = highlight;
    glowAlpha = 0.5;
  } else if (lighting === "moonlit" || lighting === "backlit") {
    glow = secondary;
    glowAlpha = 0.48;
  } else if (lighting === "harsh") {
    glow = highlight;
    glowAlpha = 0.35;
  } else if (lighting === "rim") {
    glow = accent;
    glowAlpha = 0.32;
  }

  p.drawingContext.save();
  const key = p.drawingContext.createRadialGradient(
    nx(origin.x),
    ny(origin.y),
    10,
    nx(fx),
    ny(fy),
    POSTER_W * (0.55 + (1 - shadow) * 0.25)
  );
  key.addColorStop(0, `rgba(${glow[0]},${glow[1]},${glow[2]},${glowAlpha})`);
  key.addColorStop(1, "rgba(0,0,0,0)");
  p.drawingContext.fillStyle = key;
  p.drawingContext.fillRect(0, 0, POSTER_W, POSTER_H);

  // Shadow falloff opposite the light.
  const shade = p.drawingContext.createRadialGradient(
    nx(1 - origin.x),
    ny(Math.min(0.95, fy + 0.28)),
    20,
    nx(fx),
    ny(fy),
    POSTER_H * 0.75
  );
  shade.addColorStop(0, `rgba(0,0,0,${0.25 + shadow * 0.45})`);
  shade.addColorStop(1, "rgba(0,0,0,0)");
  p.drawingContext.fillStyle = shade;
  p.drawingContext.fillRect(0, 0, POSTER_W, POSTER_H);
  p.drawingContext.restore();
}

function drawNarrativeAnchor(p, dna, seed, fx, fy) {
  const pal = dnaPalette(dna);
  const semantic = dnaSemantic(dna);
  const metaphor = semantic.narrativeAnchor || semantic.visualMetaphor || "silhouette-threshold";
  const material = semantic.material || "paper";
  const spatial = semantic.spatial || "isolated";
  const scale = anchorScale(dna, seed);
  const emphasis = materialEmphasis(dna, seed);
  const primary = hexToRgb(pal.primary);
  const secondary = hexToRgb(pal.secondary);
  const accent = hexToRgb(pal.accent);
  const bg = hexToRgb(pal.background);
  const cx = nx(fx);
  const cy = ny(fy);
  const origin = lightOrigin(dna, seed);

  p.randomSeed(seed + 77);
  p.noiseSeed(seed + 77);
  p.push();
  p.translate(cx, cy);

  // Soft contact shadow under the anchor.
  p.noStroke();
  p.fill(0, 0, 0, 70 + emphasis * 40);
  p.ellipse(12, POSTER_H * 0.12 * scale, POSTER_W * 0.28 * scale, POSTER_H * 0.04 * scale);

  const metalish = material === "metal" || material === "rust" || material === "plastic";
  const glassy = material === "glass" || material === "water" || material === "plastic";
  const papery = material === "paper" || material === "ink" || material === "film-stock";

  if (metaphor === "fractured-glass" || metaphor === "distorted-reflection") {
    // Dominant cracked pane — one clear glass object, not decorative sparkle.
    p.noStroke();
    p.fill(...secondary, glassy ? 70 : 100);
    p.rectMode(p.CENTER);
    p.rect(0, 0, POSTER_W * 0.46 * scale, POSTER_H * 0.34 * scale, 2);
    p.fill(...primary, 40);
    p.rect(-POSTER_W * 0.02 * scale, -POSTER_H * 0.01 * scale, POSTER_W * 0.42 * scale, POSTER_H * 0.3 * scale, 2);
    p.stroke(...accent, 170 * emphasis);
    p.strokeWeight(1.6);
    const shards = 9;
    for (let i = 0; i < shards; i += 1) {
      const a0 = (i / shards) * p.TWO_PI + p.random(-0.08, 0.08);
      const a1 = a0 + p.TWO_PI / shards * (0.45 + p.random() * 0.35);
      const r0 = POSTER_W * (0.06 + p.random() * 0.08) * scale;
      const r1 = POSTER_W * (0.16 + p.random() * 0.14) * scale;
      p.fill(...primary, glassy ? 45 : 75);
      p.beginShape();
      p.vertex(0, 0);
      p.vertex(Math.cos(a0) * r0, Math.sin(a0) * r1 * 0.75);
      p.vertex(Math.cos(a1) * r1, Math.sin(a1) * r0);
      p.endShape(p.CLOSE);
      p.stroke(...accent, 120);
      p.line(0, 0, Math.cos(a0) * r1 * 1.15, Math.sin(a0) * r1 * 1.15);
    }
    if (metaphor === "distorted-reflection") {
      p.noFill();
      p.stroke(...accent, 90);
      p.ellipse(0, 0, POSTER_W * 0.22 * scale, POSTER_H * 0.28 * scale);
    }
  } else if (metaphor === "eclipse" || metaphor === "orbital-system" || metaphor === "biological-cell" || metaphor === "signal") {
    const rings = metaphor === "orbital-system" ? 5 : metaphor === "biological-cell" ? 4 : 3;
    p.noFill();
    for (let i = 1; i <= rings; i += 1) {
      const t = i / rings;
      p.stroke(...(i === rings ? accent : secondary), (150 - i * 18) * emphasis);
      p.strokeWeight(metaphor === "signal" ? 1.3 : 2);
      p.ellipse(0, 0, POSTER_W * (0.16 + t * 0.5) * scale, POSTER_H * (0.1 + t * 0.34) * scale);
    }
    p.noStroke();
    p.fill(...(metaphor === "eclipse" ? bg : primary), metaphor === "eclipse" ? 230 : 180);
    p.circle(0, 0, POSTER_W * 0.22 * scale);
    if (metaphor === "eclipse") {
      p.fill(...accent, 50);
      p.circle(-POSTER_W * 0.04 * scale, -POSTER_H * 0.015 * scale, POSTER_W * 0.23 * scale);
      p.noFill();
      p.stroke(...accent, 120);
      p.strokeWeight(2);
      p.circle(0, 0, POSTER_W * 0.34 * scale);
    }
    if (metaphor === "orbital-system" || metaphor === "signal") {
      p.stroke(...accent, 170);
      p.strokeWeight(1.6);
      p.noFill();
      const ox = POSTER_W * 0.22 * scale;
      const oy = -POSTER_H * 0.07 * scale;
      p.circle(ox, oy, 16);
      p.line(0, 0, ox, oy);
      p.fill(...accent, 160);
      p.noStroke();
      p.circle(ox, oy, 6);
    }
  } else if (metaphor === "locked-mechanism" || metaphor === "clock-mechanism" || metaphor === "keyhole") {
    p.noStroke();
    p.fill(...primary, metalish ? 200 : 150);
    p.circle(0, 0, POSTER_W * 0.46 * scale);
    p.fill(...secondary, 90);
    p.circle(0, 0, POSTER_W * 0.38 * scale);
    p.fill(...bg, 220);
    p.circle(0, 0, POSTER_W * 0.24 * scale);
    if (metaphor === "keyhole") {
      // Unmistakable keyhole void.
      p.fill(...bg, 255);
      p.circle(0, -POSTER_H * 0.02 * scale, POSTER_W * 0.12 * scale);
      p.rectMode(p.CENTER);
      p.rect(0, POSTER_H * 0.05 * scale, POSTER_W * 0.07 * scale, POSTER_H * 0.12 * scale, 4);
      p.noFill();
      p.stroke(...accent, 160);
      p.strokeWeight(2);
      p.circle(0, 0, POSTER_W * 0.46 * scale);
    } else {
      p.stroke(...accent, 200);
      p.strokeWeight(2.4);
      p.noFill();
      const teeth = metaphor === "clock-mechanism" ? 12 : 8;
      for (let i = 0; i < teeth; i += 1) {
        const a = (i / teeth) * p.TWO_PI;
        p.line(
          Math.cos(a) * POSTER_W * 0.14 * scale,
          Math.sin(a) * POSTER_W * 0.14 * scale,
          Math.cos(a) * POSTER_W * 0.21 * scale,
          Math.sin(a) * POSTER_W * 0.21 * scale
        );
      }
      p.strokeWeight(2.6);
      p.line(0, 0, Math.cos(-0.7) * POSTER_W * 0.14 * scale, Math.sin(-0.7) * POSTER_W * 0.14 * scale);
      p.line(0, 0, Math.cos(1.1) * POSTER_W * 0.09 * scale, Math.sin(1.1) * POSTER_W * 0.09 * scale);
      p.fill(...accent, 180);
      p.noStroke();
      p.circle(0, 0, 10);
    }
  } else if (metaphor === "decaying-photograph" || metaphor === "burning-document" || metaphor === "map-fold") {
    const w = POSTER_W * 0.52 * scale;
    const h = POSTER_H * 0.36 * scale;
    p.rectMode(p.CENTER);
    p.noStroke();
    p.fill(0, 0, 0, 70);
    p.rect(10, 14, w, h);
    p.fill(...(papery ? secondary : primary), 190);
    p.rect(0, 0, w, h);
    p.stroke(...accent, 90);
    p.strokeWeight(1.2);
    p.noFill();
    p.rect(0, 0, w * 0.92, h * 0.9);
    if (metaphor === "map-fold") {
      p.line(-w * 0.5, 0, w * 0.5, 0);
      p.line(0, -h * 0.5, 0, h * 0.5);
      for (let i = 0; i < 5; i += 1) {
        p.noFill();
        p.stroke(...primary, 110);
        p.beginShape();
        for (let x = -w * 0.4; x <= w * 0.4; x += 8) {
          p.vertex(x, Math.sin(x * 0.08 + i) * 10 + i * 8 - 16);
        }
        p.endShape();
      }
    } else if (metaphor === "burning-document") {
      p.noStroke();
      for (let i = 0; i < 22; i += 1) {
        const bx = p.random(-w * 0.45, w * 0.45);
        const by = h * 0.5 - p.random(0, h * 0.6);
        p.fill(...accent, 50 + p.random(90));
        p.ellipse(bx, by, p.random(8, 22), p.random(12, 34));
      }
    } else {
      // Faded portrait plane inside the photograph.
      p.noStroke();
      p.fill(...bg, 110);
      p.rect(0, -h * 0.04, w * 0.62, h * 0.55);
      p.fill(...primary, 80);
      p.ellipse(0, -h * 0.08, w * 0.28, h * 0.28);
      p.fill(...accent, 45);
      p.ellipse(w * 0.16, h * 0.14, w * 0.34, h * 0.28);
      // Emulsion damage.
      for (let i = 0; i < 14; i += 1) {
        p.fill(...bg, 40 + p.random(50));
        p.ellipse(p.random(-w * 0.4, w * 0.4), p.random(-h * 0.4, h * 0.4), p.random(4, 18), p.random(3, 12));
      }
    }
  } else if (metaphor === "tangled-roots" || metaphor === "maze" || metaphor === "architectural-ruin") {
    p.noFill();
    p.stroke(...primary, 180 * emphasis);
    p.strokeWeight(metaphor === "tangled-roots" ? 2.2 : 1.6);
    const branches = metaphor === "maze" ? 12 : 18;
    for (let i = 0; i < branches; i += 1) {
      let x = 0;
      let y = metaphor === "architectural-ruin" ? POSTER_H * 0.14 * scale : POSTER_H * 0.08 * scale;
      p.beginShape();
      for (let s = 0; s < 28; s += 1) {
        p.vertex(x, y);
        const n = p.noise(i * 0.35, s * 0.12);
        if (metaphor === "maze") {
          x += (n > 0.5 ? 1 : -1) * 10 * scale;
          y += 8 * scale;
        } else if (metaphor === "architectural-ruin") {
          x += (p.random() - 0.5) * 12;
          y -= 9 * scale;
        } else {
          x += Math.cos(n * p.TWO_PI + i * 0.2) * 9 * scale;
          y -= Math.abs(Math.sin(n * p.TWO_PI + i)) * 8 * scale + 1;
        }
      }
      p.endShape();
    }
    if (metaphor === "architectural-ruin") {
      p.stroke(...accent, 120);
      p.strokeWeight(1.8);
      p.rectMode(p.CENTER);
      p.rect(0, POSTER_H * 0.02 * scale, POSTER_W * 0.34 * scale, POSTER_H * 0.22 * scale);
    } else if (metaphor === "tangled-roots") {
      p.noStroke();
      p.fill(...accent, 70);
      p.ellipse(0, POSTER_H * 0.1 * scale, POSTER_W * 0.2 * scale, POSTER_H * 0.06 * scale);
    }
  } else {
    // silhouette-threshold default — figure at a doorway / threshold
    p.noStroke();
    p.fill(...primary, 170);
    p.rectMode(p.CENTER);
    p.rect(0, POSTER_H * 0.02 * scale, POSTER_W * 0.28 * scale, POSTER_H * 0.5 * scale, 2);
    p.fill(...bg, 200);
    p.rect(0, POSTER_H * 0.02 * scale, POSTER_W * 0.16 * scale, POSTER_H * 0.4 * scale);
    p.fill(...secondary, 180);
    p.ellipse(0, -POSTER_H * 0.1 * scale, POSTER_W * 0.11 * scale, POSTER_H * 0.14 * scale);
    p.rect(0, POSTER_H * 0.07 * scale, POSTER_W * 0.09 * scale, POSTER_H * 0.26 * scale, 8);
  }

  // Specular / material highlight from light origin.
  if (metalish || glassy) {
    const hx = (origin.x - fx) * POSTER_W * 0.15;
    const hy = (origin.y - fy) * POSTER_H * 0.12;
    p.noStroke();
    p.fill(255, 255, 255, glassy ? 55 : 35);
    p.ellipse(hx, hy, POSTER_W * 0.08 * scale, POSTER_H * 0.04 * scale);
  }

  if (spatial === "fragmented" || spatial === "collapsing") {
    p.stroke(...accent, 50);
    p.strokeWeight(1);
    for (let i = 0; i < 5; i += 1) {
      const a = p.random(p.TWO_PI);
      p.line(0, 0, Math.cos(a) * POSTER_W * 0.2 * scale, Math.sin(a) * POSTER_H * 0.14 * scale);
    }
  }

  p.pop();
}

function drawCinematicPlate(p, dna, seed, spec) {
  const pal = dnaPalette(dna);
  const semantic = dnaSemantic(dna);
  const bg = hexToRgb(pal.background);
  const primary = hexToRgb(pal.primary);
  const secondary = hexToRgb(pal.secondary);
  const fx = Number(dnaComp(dna).focalX ?? 0.5);
  const fy = Number(dnaComp(dna).focalY ?? 0.42);
  const material = semantic.material || "paper";
  const spatial = semantic.spatial || "isolated";

  p.background(...bg);
  p.noiseSeed(seed);
  p.randomSeed(seed);

  const horizon =
    spatial === "rising" || spatial === "expanding"
      ? 0.62
      : spatial === "claustrophobic" || spatial === "compressed"
        ? 0.4
        : spec.id === "off-center-top"
          ? 0.42
          : spec.id === "off-center-bottom"
            ? 0.58
            : 0.52;

  // Atmospheric depth bands — background → mid → foreground wash.
  p.noStroke();
  for (let y = 0; y < POSTER_H; y += 3) {
    const t = y / POSTER_H;
    const sky = t < horizon;
    const mix = sky ? t / horizon : (t - horizon) / (1 - horizon);
    const a = sky ? bg : primary;
    const b = sky ? secondary : bg;
    const depth = sky ? 55 : 75;
    p.fill(
      a[0] + (b[0] - a[0]) * mix,
      a[1] + (b[1] - a[1]) * mix,
      a[2] + (b[2] - a[2]) * mix,
      depth
    );
    p.rect(0, y, POSTER_W, 4);
  }

  // Mid-ground haze / material atmosphere before the anchor.
  if (material === "smoke" || material === "dust" || material === "water") {
    p.drawingContext.save();
    const haze = p.drawingContext.createRadialGradient(nx(fx), ny(fy), 10, nx(fx), ny(fy), POSTER_W * 0.55);
    const c = material === "water" ? secondary : primary;
    haze.addColorStop(0, `rgba(${c[0]},${c[1]},${c[2]},0.28)`);
    haze.addColorStop(1, "rgba(0,0,0,0)");
    p.drawingContext.fillStyle = haze;
    p.drawingContext.fillRect(0, 0, POSTER_W, POSTER_H);
    p.drawingContext.restore();
  }

  drawDirectionalLight(p, dna, seed, fx, fy);
  drawNarrativeAnchor(p, dna, seed, fx, fy);

  // Foreground environmental suggestion — restrained, material-led.
  p.stroke(...secondary, material === "concrete" || material === "stone" ? 55 : 28);
  p.strokeWeight(1.1);
  const groundLines = spatial === "expansive" ? 4 : spatial === "claustrophobic" ? 10 : 6;
  for (let i = 0; i < groundLines; i += 1) {
    const x = nx(0.06 + i * (0.88 / Math.max(1, groundLines - 1)));
    p.line(x, ny(horizon), x + (p.noise(i * 0.4) - 0.5) * 36, ny(0.94));
  }

  drawMaterialGrain(p, dna, seed, materialEmphasis(dna, seed));

  p.noFill();
  p.stroke(...hexToRgb(pal.accent), 14);
  p.strokeWeight(1);
  p.line(0, ny(horizon), POSTER_W, ny(horizon));

  // Depth vignette — stronger when shadow density is high.
  const shadow = Number(dnaLight(dna).shadowDensity ?? 0.55);
  p.drawingContext.save();
  const vig = p.drawingContext.createRadialGradient(
    POSTER_W / 2,
    POSTER_H / 2,
    POSTER_H * 0.16,
    POSTER_W / 2,
    POSTER_H / 2,
    POSTER_H * (0.68 + shadow * 0.08)
  );
  vig.addColorStop(0, "rgba(0,0,0,0)");
  vig.addColorStop(1, `rgba(0,0,0,${0.4 + shadow * 0.28})`);
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
  const lineKind = dnaSemantic(dna).lineSemantics || dnaProc(dna).lineSemantics || "threads";
  const count = Math.floor(280 * density * weight);
  p.noiseSeed(seed);
  p.randomSeed(seed);
  p.noFill();
  const strokeW =
    lineKind === "cracks" || lineKind === "wiring"
      ? 1.2
      : lineKind === "roots" || lineKind === "veins"
        ? 1.35
        : lineKind === "plans" || lineKind === "roads"
          ? 0.95
          : 1.05;
  p.strokeWeight(strokeW);
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
    p.stroke(...hexToRgb(c), 85 + lum * 75);
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
      let angle = n * p.TWO_PI * 2.4 + edgeBias * 1.8;
      if (lineKind === "cracks") {
        angle = Math.round(angle / (p.HALF_PI / 2)) * (p.HALF_PI / 2) + (n - 0.5) * 0.35;
      } else if (lineKind === "roads" || lineKind === "plans") {
        angle = Math.round(angle / p.HALF_PI) * p.HALF_PI + (n - 0.5) * 0.15;
      } else if (lineKind === "circuitry" || lineKind === "wiring") {
        angle = s % 5 < 3 ? Math.round(angle / p.HALF_PI) * p.HALF_PI : angle;
      } else if (lineKind === "roots" || lineKind === "veins") {
        angle = n * p.TWO_PI * 1.4 + edgeBias;
      }
      const step = lineKind === "cracks" ? 5.2 : lineKind === "threads" ? 3.6 : 4.4;
      x += Math.cos(angle) * step;
      y += Math.sin(angle) * step;
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
      p.stroke(...hexToRgb(lum > 0.5 ? pal.accent : pal.primary), 90 * protect * weight);
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
  const kind = dnaSemantic(dna).particleSemantics || dnaProc(dna).particleSemantics || "dust";
  const count = Math.floor(
    (kind === "stars" ? 160 : kind === "rain" ? 280 : kind === "ash" || kind === "dust" ? 200 : 180) +
      density * 220
  );
  const fx = Number(dnaComp(dna).focalX ?? 0.5);
  const fy = Number(dnaComp(dna).focalY ?? 0.42);
  p.noStroke();
  for (let i = 0; i < count; i += 1) {
    let u = p.random();
    let v = p.random();
    if (kind === "stars") {
      v = p.random() * 0.55;
    } else if (kind === "rain") {
      u = p.random();
      v = p.random();
    } else if (p.random() < 0.55) {
      u = fx + (p.random() - 0.5) * 0.42;
      v = fy + (p.random() - 0.5) * 0.36;
    }
    u = Math.max(0, Math.min(1, u));
    v = Math.max(0, Math.min(1, v));
    const protect = protectionWeight(u, v, dna, spec);
    const lum = sampler.at(u, v);
    if (protect < 0.22 || lum < 0.06) {
      continue;
    }
    const tier = p.random();
    let r = tier < 0.7 ? p.random(0.8, 1.8) : tier < 0.93 ? p.random(2.2, 4.5) : p.random(5, 9);
    const c = tier < 0.6 ? pal.primary : tier < 0.85 ? pal.secondary : pal.accent;
    const alpha = (80 + lum * 110) * protect * weight;
    if (kind === "rain") {
      p.stroke(...hexToRgb(c), alpha);
      p.strokeWeight(1);
      p.line(nx(u), ny(v), nx(u) + 1.5, ny(v) + 10 + density * 8);
      p.noStroke();
      continue;
    }
    if (kind === "sparks") {
      p.stroke(...hexToRgb(pal.accent), alpha);
      p.strokeWeight(1.1);
      const ang = p.random(p.TWO_PI);
      p.line(nx(u), ny(v), nx(u) + Math.cos(ang) * 6, ny(v) + Math.sin(ang) * 6);
      p.noStroke();
      r = p.random(1.2, 2.4);
    }
    if (kind === "ash") {
      r = p.random(1.2, 3.2);
      p.fill(...hexToRgb(c), alpha * 0.85);
      p.rect(nx(u), ny(v), r * weight, r * 0.6 * weight);
      continue;
    }
    if (kind === "stars") {
      p.fill(255, 255, 255, alpha * 0.9);
      p.circle(nx(u), ny(v), p.random(0.8, 2.2) * weight);
      continue;
    }
    p.fill(...hexToRgb(c), alpha);
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
    const alpha = (140 - t * 70) * weight;
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
      const alpha = (55 + a.edge * 120) * protect * weight;
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
  // Less-but-better: one strong primary; secondary is quiet support.
  if (role === "hybrid") {
    return [
      { name: primary, weight: 0.78, seedShift: 0 },
      { name: secondary, weight: 0.28, seedShift: 91 },
    ];
  }
  if (role === "alternative") {
    return [
      { name: secondary, weight: 0.82, seedShift: 17 },
      { name: primary, weight: 0.18, seedShift: 140 },
    ];
  }
  // Signature: one strong primary system — keep secondary off so the anchor can own the frame.
  return [{ name: primary, weight: 0.72, seedShift: 0 }];
}

function relativeLuminance(rgb) {
  return (rgb[0] * 0.299 + rgb[1] * 0.587 + rgb[2] * 0.114) / 255;
}

/** Mean and spread of artwork luminance under a normalised rect. */
function localLuminance(sampler, rect) {
  let sum = 0;
  let min = 1;
  let max = 0;
  let n = 0;
  for (let v = rect.y; v <= rect.y + rect.h; v += rect.h / 6) {
    for (let u = rect.x; u <= rect.x + rect.w; u += rect.w / 8) {
      const l = sampler.at(Math.min(1, Math.max(0, u)), Math.min(1, Math.max(0, v)));
      sum += l;
      min = Math.min(min, l);
      max = Math.max(max, l);
      n += 1;
    }
  }
  return { mean: n ? sum / n : 0.3, spread: max - min };
}

/**
 * Legibility is non-negotiable: risky structural treatments degrade to safer
 * ones when the artwork underneath does not support them.
 */
function resolveTitleTreatment(structure, sampler, rect, textLum) {
  const { mean, spread } = localLuminance(sampler, rect);
  const separation = Math.abs(mean - textLum);
  let resolved = structure;
  let scrim = 0;

  if (resolved === "outline" && (spread > 0.34 || separation < 0.42)) {
    resolved = "solid";
  }
  if (resolved === "fragmented" && separation < 0.24) {
    resolved = "solid";
  }
  if (separation < 0.34) {
    scrim = Math.min(1, (0.34 - separation) / 0.34 + 0.35);
  }
  if ((resolved === "textured" || resolved === "gradient") && separation < 0.42) {
    scrim = Math.max(scrim, 0.55);
  }
  return { structure: resolved, scrim, mean, spread };
}

function ctxAlign(align) {
  return align === "center" ? "center" : align === "right" ? "right" : "left";
}

function drawTexturedLine(p, ctx, line, x, y, size, face, baseColor, textureColor) {
  const w = measureStyled(ctx, face, size, line);
  if (w <= 0) {
    return;
  }
  const pad = Math.ceil(size * 0.6);
  const off = document.createElement("canvas");
  off.width = Math.ceil(w) + pad * 2;
  off.height = Math.ceil(size * 1.7) + pad * 2;
  const o = off.getContext("2d");
  styleCtx(o, face, size);
  o.textAlign = "left";
  o.textBaseline = "top";
  o.fillStyle = `rgb(${baseColor.join(",")})`;
  o.fillText(line, pad, pad);

  // Wear only where glyphs already are, so the material lives in the letters.
  o.globalCompositeOperation = "source-atop";
  for (let i = 0; i < 90; i += 1) {
    const sx = p.random(off.width);
    const sy = p.random(off.height);
    o.fillStyle = `rgba(${textureColor.join(",")},${0.1 + p.random() * 0.3})`;
    o.fillRect(sx, sy, p.random(3, 16), p.random(0.6, 1.8));
  }
  for (let i = 0; i < 40; i += 1) {
    o.fillStyle = `rgba(0,0,0,${0.08 + p.random() * 0.16})`;
    o.fillRect(p.random(off.width), p.random(off.height), p.random(1, 4), p.random(1, 4));
  }
  o.globalCompositeOperation = "source-over";

  const align = ctx.textAlign;
  const dx = align === "center" ? x - w / 2 - pad : align === "right" ? x - w - pad : x - pad;
  ctx.drawImage(off, dx, y - pad);
}

function drawTitleLine(p, ctx, line, x, y, size, face, treatment, colors, light) {
  const { text, secondary, accent, highlight } = colors;
  styleCtx(ctx, face, size);
  ctx.textBaseline = "top";

  if (treatment === "outline") {
    ctx.lineJoin = "round";
    ctx.lineWidth = Math.max(1, size / 24);
    ctx.strokeStyle = `rgb(${text.join(",")})`;
    ctx.fillStyle = `rgba(${text.join(",")},0.12)`;
    ctx.fillText(line, x, y);
    ctx.strokeText(line, x, y);
    return;
  }

  if (treatment === "gradient") {
    // Gradient runs with the scene's light, not an arbitrary axis.
    const w = measureStyled(ctx, face, size, line);
    const align = ctx.textAlign;
    const left = align === "center" ? x - w / 2 : align === "right" ? x - w : x;
    const g = ctx.createLinearGradient(
      left + w * (light.x > 0.5 ? 1 : 0),
      y,
      left + w * (light.x > 0.5 ? 0 : 1),
      y + size
    );
    g.addColorStop(0, `rgb(${highlight.join(",")})`);
    g.addColorStop(0.55, `rgb(${text.join(",")})`);
    g.addColorStop(1, `rgba(${secondary.join(",")},0.85)`);
    ctx.fillStyle = g;
    ctx.fillText(line, x, y);
    return;
  }

  if (treatment === "layered") {
    const off = Math.max(2, size * 0.045);
    ctx.fillStyle = `rgba(${secondary.join(",")},0.55)`;
    ctx.fillText(line, x + off, y + off);
    ctx.fillStyle = `rgba(${accent.join(",")},0.4)`;
    ctx.fillText(line, x - off * 0.6, y - off * 0.5);
    ctx.fillStyle = `rgb(${text.join(",")})`;
    ctx.fillText(line, x, y);
    return;
  }

  if (treatment === "fragmented") {
    const bands = 5;
    const bandH = (size * 1.25) / bands;
    for (let i = 0; i < bands; i += 1) {
      const shift = (p.random() - 0.5) * size * 0.12;
      ctx.save();
      ctx.beginPath();
      ctx.rect(0, y + i * bandH, POSTER_W, bandH + 0.6);
      ctx.clip();
      ctx.fillStyle = i % 2 === 0 ? `rgb(${text.join(",")})` : `rgba(${text.join(",")},0.82)`;
      ctx.fillText(line, x + shift, y);
      ctx.restore();
    }
    return;
  }

  if (treatment === "textured") {
    drawTexturedLine(p, ctx, line, x, y, size, face, text, accent);
    return;
  }

  // Solid, with a faint rim pickup on the lit side so type belongs to the scene.
  ctx.fillStyle = `rgba(${highlight.join(",")},0.35)`;
  ctx.fillText(line, x + (light.x > 0.5 ? -1.2 : 1.2), y - 1);
  ctx.fillStyle = `rgb(${text.join(",")})`;
  ctx.fillText(line, x, y);
}

function drawQuotePlate(ctx, rect, strength) {
  ctx.save();
  ctx.fillStyle = `rgba(0,0,0,${0.3 + strength * 0.34})`;
  const r = 6;
  ctx.beginPath();
  ctx.moveTo(rect.x + r, rect.y);
  ctx.lineTo(rect.x + rect.w - r, rect.y);
  ctx.quadraticCurveTo(rect.x + rect.w, rect.y, rect.x + rect.w, rect.y + r);
  ctx.lineTo(rect.x + rect.w, rect.y + rect.h - r);
  ctx.quadraticCurveTo(rect.x + rect.w, rect.y + rect.h, rect.x + rect.w - r, rect.y + rect.h);
  ctx.lineTo(rect.x + r, rect.y + rect.h);
  ctx.quadraticCurveTo(rect.x, rect.y + rect.h, rect.x, rect.y + rect.h - r);
  ctx.lineTo(rect.x, rect.y + r);
  ctx.quadraticCurveTo(rect.x, rect.y, rect.x + r, rect.y);
  ctx.closePath();
  ctx.fill();
  ctx.restore();
}

function drawTypography(p, dna, spec, sampler, seed) {
  const pal = dnaPalette(dna);
  const ctx = p.drawingContext;
  const colors = {
    text: hexToRgb(pal.text),
    secondary: hexToRgb(pal.secondary),
    accent: hexToRgb(pal.accent),
    highlight: hexToRgb(pal.highlight || pal.accent),
  };
  const face = titleFace(dna);
  const quote = quoteFace(dna);
  const light = lightOrigin(dna, seed);

  const isSplitColumn = spec.id === "split-editorial";
  const maxTitleWidth = isSplitColumn ? POSTER_W * 0.4 : POSTER_W * 0.8;
  const quoteWidth = isSplitColumn ? POSTER_W * 0.36 : POSTER_W * 0.72;
  const titleText = applyCase(dna.concept?.title || dna.title || "", face.case);

  // Scale from length and safe width, never a fixed size.
  const maxSize = face.letterforms === "hand-lettered" ? 68 : 58;
  const { lines: titleLines, size: titleSize } = fitTitleBlock(
    ctx,
    face,
    titleText,
    maxTitleWidth,
    maxSize,
    24
  );
  const lineHeight = titleSize * (face.letterforms === "hand-lettered" ? 0.92 : 1.06);
  const titleH = titleLines.length * lineHeight;

  const align = ctxAlign(spec.align);
  const x = nx(spec.title.x);
  const titleTop = ny(spec.title.y) - titleH / 2;

  const titleRect = {
    x: Math.max(0, (align === "center" ? x - maxTitleWidth / 2 : x) / POSTER_W),
    y: Math.max(0, titleTop / POSTER_H),
    w: maxTitleWidth / POSTER_W,
    h: Math.max(0.04, titleH / POSTER_H),
  };
  const textLum = relativeLuminance(colors.text);
  const treatment = resolveTitleTreatment(face.structure, sampler, titleRect, textLum);

  if (treatment.scrim > 0) {
    const contrast = dnaProc(dna).contrast || dna.contrast || 0.75;
    drawContrastBackdrop(p, titleTop + titleH / 2, titleH, contrast * treatment.scrim);
  }

  p.randomSeed(seed + 909);
  ctx.save();
  ctx.textAlign = align;
  titleLines.forEach((line, i) => {
    drawTitleLine(p, ctx, line, x, titleTop + i * lineHeight, titleSize, face, treatment.structure, colors, light);
  });
  ctx.restore();

  // --- Quote: its own face, hierarchy, placement, and legibility technique ---
  const quoteText = String(dna.concept?.quote || dna.quote || "").trim();
  if (!quoteText) {
    if (LETTER_SPACING_SUPPORTED) {
      ctx.letterSpacing = "0em";
    }
    return;
  }

  const quoteFaceSpec = {
    family: quote.family,
    weight: quote.weight,
    italic: quote.italic,
    tracking: quote.track ?? 0,
  };
  // Always clearly subordinate to the title.
  const quoteSize = Math.max(
    quote.min,
    Math.min(titleSize * quote.ratio, titleSize * 0.5, 22)
  );
  const quoteBody = quote.upper ? quoteText.toUpperCase() : quoteText;
  const quoteLead = quoteSize * quote.lead;
  const quoteLines = wrapStyled(ctx, quoteFaceSpec, quoteSize, quoteBody, quoteWidth, 3);
  const quoteH = quoteLines.length * quoteLead;

  let quoteTop;
  if (spec.quoteAnchor === null || spec.quoteAnchor === undefined) {
    quoteTop = titleTop + titleH + Math.max(14, titleSize * 0.34);
  } else {
    quoteTop = ny(spec.quoteAnchor) - quoteH / 2;
  }
  // Stay inside the safe margins regardless of style.
  const bottomLimit = POSTER_H - (spec.inset > 0 ? spec.inset * POSTER_W * 1.9 : 34) - quoteH;
  quoteTop = Math.max(28, Math.min(quoteTop, bottomLimit));

  const quoteRect = {
    x: Math.max(0, (align === "center" ? x - quoteWidth / 2 : x) / POSTER_W),
    y: quoteTop / POSTER_H,
    w: quoteWidth / POSTER_W,
    h: Math.max(0.02, quoteH / POSTER_H),
  };
  const quoteLum = localLuminance(sampler, quoteRect);
  const quoteColor = quote.style === "caption" ? colors.accent : colors.text;
  const quoteSeparation = Math.abs(quoteLum.mean - relativeLuminance(quoteColor));

  // Technique belongs to the style; it only escalates when legibility demands it.
  let technique = quote.legibility;
  if (technique === "none" && quoteSeparation < 0.3) {
    technique = "scrim";
  }
  if (technique === "scrim" && quoteSeparation < 0.16) {
    technique = "plate";
  }
  if (technique === "shadow" && quoteLum.spread > 0.42 && quoteSeparation < 0.24) {
    technique = "plate";
  }

  const measuredWidest = Math.max(
    ...quoteLines.map((l) => measureStyled(ctx, quoteFaceSpec, quoteSize, l)),
    0
  );
  if (technique === "plate") {
    const padX = 14;
    const padY = 10;
    const plateX =
      align === "center" ? x - measuredWidest / 2 - padX : align === "right" ? x - measuredWidest - padX : x - padX;
    drawQuotePlate(
      p.drawingContext,
      { x: plateX, y: quoteTop - padY, w: measuredWidest + padX * 2, h: quoteH + padY * 1.6 },
      1 - Math.min(1, quoteSeparation / 0.34)
    );
  } else if (technique === "scrim") {
    drawContrastBackdrop(p, quoteTop + quoteH / 2, quoteH * 0.9, 0.42);
  }

  ctx.save();
  ctx.textAlign = align;
  ctx.textBaseline = "top";
  styleCtx(ctx, quoteFaceSpec, quoteSize);
  if (technique === "shadow") {
    ctx.shadowColor = "rgba(0,0,0,0.85)";
    ctx.shadowBlur = Math.max(4, quoteSize * 0.5);
    ctx.shadowOffsetY = 1;
  }
  ctx.fillStyle = `rgba(${quoteColor.join(",")},${quote.style === "caption" ? 0.95 : 0.9})`;
  const decorated =
    quote.style === "editorial-italic" || quote.style === "handwritten"
      ? quoteLines.map((l, i) =>
          `${i === 0 ? "“" : ""}${l}${i === quoteLines.length - 1 ? "”" : ""}`
        )
      : quoteLines;
  decorated.forEach((line, i) => {
    ctx.fillText(line, x, quoteTop + i * quoteLead);
  });
  ctx.restore();

  if (LETTER_SPACING_SUPPORTED) {
    ctx.letterSpacing = "0em";
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
      // Type placement is resolved before the artwork so procedural marks can
      // protect whichever band the title actually occupies.
      const spec = typeLayout(current, layoutSpec(dnaComp(current).layout || current.layout));
      if (keyArt) {
        p.background(...hexToRgb(dnaPalette(current).background));
        drawKeyArt(p, keyArt, spec);
        // Material grain still sits on top of photographic plates for tactility.
        drawMaterialGrain(p, current, seed, materialEmphasis(current, seed) * 0.55);
        drawDirectionalLight(p, current, seed, Number(dnaComp(current).focalX ?? 0.5), Number(dnaComp(current).focalY ?? 0.42));
      } else {
        drawCinematicPlate(p, current, seed, spec);
      }
      finishFrame(p, current, spec);
      const sampler = buildSampler(p);
      const intensity = Number(dnaProc(current).intensity || 0.55);
      p.push();
      p.blendMode(p.BLEND);
      for (const layer of patternPlan(current, role)) {
        drawPattern(
          p,
          layer.name,
          current,
          seed + layer.seedShift,
          sampler,
          spec,
          layer.weight * (0.85 + intensity * 0.35)
        );
      }
      p.pop();
      p.blendMode(p.BLEND);
      drawTypography(p, current, spec, sampler, seed);
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
      // Canvas text does not trigger webfont loading, so redraw once the exact
      // weights this DNA asked for have arrived.
      ensureTypeFaces(params).then((loaded) => {
        if (loaded && current === params) {
          p5Instance.redraw();
        }
      });
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

window.FrameFluxPoster = {
  createPoster,
  layoutSpec,
  typeLayout,
  titleFace,
  quoteFace,
  applyCase,
  wrapStyled,
  fitTitleBlock,
  resolveTitleTreatment,
  LETTERFORM_FAMILY,
  QUOTE_STYLE_FACE,
};
