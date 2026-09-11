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
  if (titleStyle === "elegant") {
    return "Playfair Display";
  }
  if (titleStyle === "condensed") {
    return "Oswald";
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
  const n = hex.replace("#", "");
  return [
    parseInt(n.slice(0, 2), 16),
    parseInt(n.slice(2, 4), 16),
    parseInt(n.slice(4, 6), 16),
  ];
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
  for (const word of words) {
    const next = current ? `${current} ${word}` : word;
    if (p.textWidth(next) > maxWidth && current) {
      lines.push(current);
      current = word;
      if (lines.length >= maxLines) {
        break;
      }
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
  if (layout === "editorial") {
    return {
      id: "editorial",
      align: "left",
      x: 48,
      centerY: 210,
      maxTitleWidth: POSTER_W - 120,
      quoteWidth: POSTER_W - 140,
    };
  }
  if (layout === "billing") {
    return {
      id: "billing",
      align: "left",
      x: 48,
      centerY: 730,
      maxTitleWidth: POSTER_W - 110,
      quoteWidth: POSTER_W - 130,
    };
  }
  return {
    id: "hero",
    align: "center",
    x: POSTER_W / 2,
    centerY: POSTER_H / 2,
    maxTitleWidth: POSTER_W - 100,
    quoteWidth: POSTER_W - 140,
  };
}

function drawContrastBackdrop(p, centerY, blockHeight) {
  const ctx = p.drawingContext;
  const padding = 44;
  const top = centerY - blockHeight / 2 - padding;
  const bottom = centerY + blockHeight / 2 + padding;

  const gradient = ctx.createLinearGradient(0, top, 0, bottom);
  gradient.addColorStop(0, "rgba(0,0,0,0)");
  gradient.addColorStop(0.28, "rgba(0,0,0,0.72)");
  gradient.addColorStop(0.72, "rgba(0,0,0,0.72)");
  gradient.addColorStop(1, "rgba(0,0,0,0)");

  ctx.save();
  ctx.fillStyle = gradient;
  ctx.fillRect(0, top, p.width, bottom - top);
  ctx.restore();
}

function drawFlowField(p, params, seed) {
  const { background, primary, secondary, accent } = params.palette;
  const density = params.density;
  p.background(...hexToRgb(background));
  const count = Math.floor(900 * density);
  p.noiseSeed(seed);
  p.noFill();
  p.strokeWeight(1.1);
  for (let i = 0; i < count; i += 1) {
    let x = p.random(POSTER_W);
    let y = p.random(POSTER_H);
    const mix = i / count;
    const c = mix < 0.45 ? primary : mix < 0.8 ? secondary : accent;
    p.stroke(...hexToRgb(c), 90);
    p.beginShape();
    for (let s = 0; s < 42; s += 1) {
      p.vertex(x, y);
      const n = p.noise(x * 0.004, y * 0.004);
      const angle = n * p.TWO_PI * 3;
      x += Math.cos(angle) * 4.2;
      y += Math.sin(angle) * 4.2;
      if (x < -20 || x > POSTER_W + 20 || y < -20 || y > POSTER_H + 20) {
        break;
      }
    }
    p.endShape();
  }
}

function drawGrid(p, params, seed) {
  const { background, primary, secondary, accent } = params.palette;
  p.background(...hexToRgb(background));
  p.randomSeed(seed);
  const cols = Math.floor(6 + params.density * 10);
  const rows = Math.floor(9 + params.density * 12);
  const cellW = POSTER_W / cols;
  const cellH = POSTER_H / rows;
  p.noStroke();
  for (let y = 0; y < rows; y += 1) {
    for (let x = 0; x < cols; x += 1) {
      const roll = p.random();
      const color = roll < 0.55 ? primary : roll < 0.85 ? secondary : accent;
      const inset = p.random(2, cellW * 0.35);
      p.push();
      p.translate(x * cellW + cellW / 2, y * cellH + cellH / 2);
      p.rotate(p.random() < 0.2 ? p.QUARTER_PI / 3 : 0);
      p.fill(...hexToRgb(color), 150 + p.random(80));
      p.rectMode(p.CENTER);
      p.rect(0, 0, cellW - inset, cellH - inset * 0.6);
      p.pop();
    }
  }
  p.stroke(...hexToRgb(accent), 40);
  p.strokeWeight(1);
  for (let i = 0; i < cols; i += 1) {
    p.line(i * cellW, 0, i * cellW, POSTER_H);
  }
}

function drawParticles(p, params, seed) {
  const { background, primary, secondary, accent } = params.palette;
  p.background(...hexToRgb(background));
  p.randomSeed(seed);
  const count = Math.floor(180 + params.density * 520);
  const points = [];
  for (let i = 0; i < count; i += 1) {
    points.push({
      x: p.random(POSTER_W),
      y: p.random(POSTER_H),
      r: p.random(1.2, 9 * params.density + 2),
      c: p.random() < 0.5 ? primary : p.random() < 0.7 ? secondary : accent,
    });
  }
  p.strokeWeight(0.7);
  for (let i = 0; i < points.length; i += 8) {
    const a = points[i];
    for (let j = i + 1; j < Math.min(i + 14, points.length); j += 1) {
      const b = points[j];
      const d = p.dist(a.x, a.y, b.x, b.y);
      if (d < 70) {
        p.stroke(...hexToRgb(accent), 28);
        p.line(a.x, a.y, b.x, b.y);
      }
    }
  }
  p.noStroke();
  for (const pt of points) {
    p.fill(...hexToRgb(pt.c), 210);
    p.circle(pt.x, pt.y, pt.r);
  }
}

function drawTypography(p, params) {
  const { text: textColor, accent } = params.palette;
  const font = fontFor(params.genre, params.titleStyle);
  const layout = layoutSpec(params.layout);
  const titleCase = String(params.title || "").toUpperCase();
  const startSize =
    params.titleStyle === "condensed" ? 50 : params.titleStyle === "elegant" ? 52 : 56;
  const minSize = 28;

  // Prefer a single-line title when fitTextSize can make it work; otherwise wrap.
  p.textFont(font);
  const fitted = fitTextSize(p, font, titleCase, layout.maxTitleWidth, startSize, minSize);
  let titleLines;
  let titleSize = fitted;
  if (fitted > minSize || p.textWidth(titleCase) <= layout.maxTitleWidth) {
    titleLines = [titleCase];
  } else {
    titleSize = Math.max(minSize, startSize - 10);
    p.textSize(titleSize);
    titleLines = wrapLines(p, titleCase, layout.maxTitleWidth, 3);
    // Second pass: shrink until the longest wrapped line fits.
    let longest = Math.max(...titleLines.map((line) => p.textWidth(line)), 0);
    while (longest > layout.maxTitleWidth && titleSize > minSize) {
      titleSize -= 2;
      p.textSize(titleSize);
      titleLines = wrapLines(p, titleCase, layout.maxTitleWidth, 3);
      longest = Math.max(...titleLines.map((line) => p.textWidth(line)), 0);
    }
  }

  const lineHeight = titleSize * 1.08;
  const genreSize = 12;
  const quoteSize = 15;
  const quoteLines = (() => {
    p.textFont("Cormorant Garamond");
    p.textSize(quoteSize);
    return wrapLines(p, params.quote || "", layout.quoteWidth, 2);
  })();

  const genreGap = 18;
  const quoteGap = 22;
  const blockHeight =
    genreSize +
    genreGap +
    titleLines.length * lineHeight +
    (quoteLines.length ? quoteGap + quoteLines.length * 22 : 0);

  drawContrastBackdrop(p, layout.centerY, blockHeight);

  const top = layout.centerY - blockHeight / 2;
  const alignMode =
    layout.align === "center" ? p.CENTER : layout.align === "right" ? p.RIGHT : p.LEFT;

  p.noStroke();
  p.textAlign(alignMode, p.TOP);

  p.fill(...hexToRgb(accent));
  p.textFont("IBM Plex Sans");
  p.textSize(genreSize);
  p.text(String(params.genre || "").toUpperCase(), layout.x, top);

  p.fill(...hexToRgb(textColor));
  p.textFont(font);
  p.textSize(titleSize);
  p.textLeading(lineHeight);
  p.text(titleLines.join("\n"), layout.x, top + genreSize + genreGap);

  if (quoteLines.length) {
    const quoteY = top + genreSize + genreGap + titleLines.length * lineHeight + quoteGap;
    p.fill(...hexToRgb(accent));
    p.textFont("Cormorant Garamond");
    p.textSize(quoteSize);
    p.textLeading(22);
    p.text(`“${quoteLines.join("\n")}”`, layout.x, quoteY);
  }
}

function createPoster(containerId) {
  let current = null;
  let seed = 1;
  let p5Instance = null;

  const sketch = (p) => {
    p.setup = function setup() {
      p.createCanvas(POSTER_W, POSTER_H);
      p.pixelDensity(2);
      p.noLoop();
      p.textAlign(p.LEFT, p.TOP);
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
      if (current.pattern === "grid") {
        drawGrid(p, current, seed);
      } else if (current.pattern === "particles") {
        drawParticles(p, current, seed);
      } else {
        drawFlowField(p, current, seed);
      }
      drawTypography(p, current);
    };
  };

  p5Instance = new p5(sketch, containerId);

  return {
    render(params, nextSeed) {
      current = params;
      seed = nextSeed;
      p5Instance.redraw();
    },
    download(filename) {
      p5Instance.saveCanvas(filename, "png");
    },
    hasPoster() {
      return current !== null;
    },
  };
}

window.FrameFluxPoster = { createPoster };
