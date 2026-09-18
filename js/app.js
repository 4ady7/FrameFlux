const form = document.querySelector("#film-form");
const generateBtn = document.querySelector("#generate");
const improveBtn = document.querySelector("#improve");
const regenerateBtn = document.querySelector("#regenerate");
const reimagineBtn = document.querySelector("#reimagine");
const downloadBtn = document.querySelector("#download");
const gptImageBtn = document.querySelector("#gpt-image");
const statusEl = document.querySelector("#status");
const errorEl = document.querySelector("#error");
const meta = document.querySelector("#meta");
const variationsEl = document.querySelector("#variations");
const posterFrame = document.querySelector("#poster-frame");
const exposeRail = document.querySelector("#expose-rail");
const exposeRailFill = document.querySelector("#expose-rail-fill");
const variationSpinners = document.querySelectorAll(".variation-spinner");
const variationWaits = document.querySelectorAll(".variation-wait");
const keyArtOverlay = document.querySelector("#key-art-overlay");
const keyArtLabel = document.querySelector("#key-art-label");
const developTeaser = document.querySelector("#develop-teaser");
const teaserMetaphor = document.querySelector("#teaser-metaphor");
const teaserMaterial = document.querySelector("#teaser-material");
const teaserQuote = document.querySelector("#teaser-quote");

const ROLES = ["signature", "hybrid", "alternative"];
const SEED_SHIFTS = { signature: 0, hybrid: 101, alternative: 211 };

const mainPoster = window.FrameFluxPoster.createPoster("poster", { pixelDensity: 2 });
const thumbs = {
  signature: window.FrameFluxPoster.createPoster("v-signature", { pixelDensity: 1 }),
  hybrid: window.FrameFluxPoster.createPoster("v-hybrid", { pixelDensity: 1 }),
  alternative: window.FrameFluxPoster.createPoster("v-alternative", { pixelDensity: 1 }),
};

let visualDna = null;
let keyArt = null;
let incomingKeyArt = null;
let composeJob = 0;
let baseSeed = Date.now() % 100000;
let selectedRole = "signature";
const GPT_IMAGE_KEY = "frameflux-gpt-image";
const waitClock = {
  running: false,
  startedAt: 0,
  plateAt: 0,
  expected: null,
  tickId: 0,
  gptImage: "no",
  mode: "generate",
  hintDna: null,
  averages: { enabled: 45, disabled: 10 },
};
const plateDevelop = {
  raf: 0,
  startedAt: 0,
  speed: 1,
  last: null,
};

function useGptImage() {
  return gptImageBtn.getAttribute("aria-pressed") === "true";
}

function syncGptImageButton() {
  const on = useGptImage();
  gptImageBtn.textContent = on ? "GPT Image on" : "GPT Image off";
}

try {
  const stored = localStorage.getItem(GPT_IMAGE_KEY);
  if (stored === "off") {
    gptImageBtn.setAttribute("aria-pressed", "false");
  }
} catch (err) {
  /* ignore quota / private mode */
}
syncGptImageButton();

function setStatus(message) {
  statusEl.textContent = message;
  errorEl.hidden = true;
  errorEl.textContent = "";
}

function setError(message) {
  errorEl.hidden = !message;
  errorEl.textContent = message || "";
  if (message) {
    statusEl.textContent = "";
  }
}

function slugify(value) {
  return (
    value
      .toLowerCase()
      .replace(/[^a-z0-9]+/g, "-")
      .replace(/^-|-$/g, "")
      .slice(0, 40) || "poster"
  );
}

function filmPayload() {
  return {
    title: document.querySelector("#title").value.trim(),
    genre: document.querySelector("#genre").value.trim(),
    pitch: document.querySelector("#pitch").value.trim(),
  };
}

function showMeta(dna) {
  meta.hidden = false;
  const proc = dna.procedural || {};
  const semantic = dna.semantic || {};
  document.querySelector("#meta-metaphor").textContent =
    semantic.visualMetaphor || dna.visualMetaphor || "—";
  document.querySelector("#meta-material").textContent =
    `${semantic.material || dna.material || "—"} · ${semantic.texture || dna.texture || ""}`.replace(/\s·\s$/, "");
  document.querySelector("#meta-pattern").textContent =
    `${proc.primaryPattern || dna.pattern} / ${proc.secondaryPattern || "–"}`;
  document.querySelector("#meta-layout").textContent =
    `${(dna.composition && dna.composition.mode) || (dna.composition && dna.composition.layout) || dna.layout}` +
    (semantic.grammarFamily ? ` · ${semantic.grammarFamily}` : "") +
    (semantic.artFamily ? ` · ${semantic.artFamily}` : "");
  const sourceLabel = dna.source === "ai" ? "AI DNA" : "local DNA";
  const art = keyArt ? " · cinematic still" : " · local plate";
  document.querySelector("#meta-source").textContent = sourceLabel + art;
}

function enablePosterActions(enabled) {
  improveBtn.disabled = !enabled;
  regenerateBtn.disabled = !enabled;
  reimagineBtn.disabled = !enabled;
  downloadBtn.disabled = !enabled;
}

function expectedWaitSeconds(gptOn) {
  const avg = gptOn ? waitClock.averages.enabled : waitClock.averages.disabled;
  if (typeof avg === "number" && avg > 0) {
    return avg;
  }
  return gptOn ? 45 : 10;
}

function waitElapsedSeconds() {
  if (!waitClock.startedAt) {
    return 0;
  }
  return Math.max(0, (performance.now() - waitClock.startedAt) / 1000);
}

function secondsLeftWaiting(elapsed, expected) {
  if (typeof expected !== "number" || expected <= 0) {
    return null;
  }
  return Math.max(0, expected - elapsed);
}

function formatWaitLabel(elapsed, left) {
  const waited = `${elapsed.toFixed(0)}s waited`;
  if (left === null) {
    return waited;
  }
  if (left <= 0) {
    return `${waited}\nfinishing`;
  }
  return `${waited}\n${Math.ceil(left)}s left`;
}

function setVariationSpinners(isBusy) {
  variationSpinners.forEach((spinner) => {
    spinner.hidden = !isBusy;
  });
  variationsEl.setAttribute("aria-busy", isBusy ? "true" : "false");
}

function humanizeDna(value) {
  return String(value || "")
    .replace(/-/g, " ")
    .replace(/\s+/g, " ")
    .trim();
}

function clipLine(value, max = 72) {
  const text = String(value || "").trim();
  if (!text || text.length <= max) {
    return text;
  }
  return `${text.slice(0, max - 1).replace(/\s+\S*$/, "")}…`;
}

function dnaCopy(dna) {
  const semantic = dna?.semantic || {};
  const concept = dna?.concept || {};
  return {
    metaphor: humanizeDna(semantic.visualMetaphor || dna?.visualMetaphor || ""),
    material: humanizeDna(semantic.material || dna?.material || ""),
    quote: String(concept.quote || dna?.quote || "").trim(),
  };
}

function pipelineStage() {
  if (!posterFrame.classList.contains("is-developing")) {
    return "interpreting";
  }
  const sincePlate = waitClock.plateAt
    ? (performance.now() - waitClock.plateAt) / 1000
    : 0;
  if (sincePlate < 10) {
    return "setting";
  }
  if (sincePlate < 20) {
    return "exposing";
  }
  if (sincePlate < 40) {
    return "grading";
  }
  return "grading";
}

function stageLabel(stage, dna) {
  const { metaphor, material, quote } = dnaCopy(dna);
  const quoted = clipLine(quote, 64);
  if (stage === "interpreting") {
    return metaphor ? `Interpreting the film · ${metaphor}` : "Interpreting the film…";
  }
  if (stage === "setting") {
    return material ? `Setting type · ${material} on the plate` : "Setting type";
  }
  if (stage === "exposing") {
    return quoted ? `Exposing the still · ${quoted}` : "Exposing the still";
  }
  if (stage === "grading") {
    return metaphor ? `Grading · ${metaphor} into the light` : "Grading";
  }
  return "Interpreting the film";
}

function updatePipelineCopy() {
  const developing = posterFrame.classList.contains("is-developing");
  const dna = developing ? visualDna : waitClock.hintDna;
  const label = stageLabel(pipelineStage(), dna);
  keyArtLabel.textContent = label;
  const elapsed = waitElapsedSeconds();
  const left = secondsLeftWaiting(elapsed, waitClock.expected);
  const waitText = formatWaitLabel(elapsed, left);
  variationWaits.forEach((el) => {
    el.textContent = waitText;
  });
  if (exposeRailFill && waitClock.expected) {
    const pct = Math.max(0.06, Math.min(0.94, elapsed / waitClock.expected));
    exposeRailFill.style.width = `${pct * 100}%`;
  }
}

function showDevelopTeaser(dna) {
  const { metaphor, material, quote } = dnaCopy(dna);
  teaserMetaphor.textContent = metaphor || "—";
  teaserMaterial.textContent = material || "—";
  if (quote) {
    teaserQuote.hidden = false;
    teaserQuote.textContent = `“${quote}”`;
  } else {
    teaserQuote.hidden = true;
    teaserQuote.textContent = "";
  }
  developTeaser.hidden = false;
}

function hideDevelopTeaser() {
  developTeaser.hidden = true;
}

function currentDevelop() {
  return plateDevelop.last && plateDevelop.last.living ? plateDevelop.last : null;
}

function stopPlateDevelop() {
  if (mainPoster.stopLiving) {
    mainPoster.stopLiving();
  }
  if (plateDevelop.raf) {
    cancelAnimationFrame(plateDevelop.raf);
  }
  plateDevelop.raf = 0;
  plateDevelop.last = null;
  plateDevelop.living = false;
}

function startPlateDevelop({ speed = 1 } = {}) {
  stopPlateDevelop();
  plateDevelop.speed = speed;
  plateDevelop.startedAt = performance.now();
  plateDevelop.living = true;
  plateDevelop.last = {
    living: true,
    startedAt: plateDevelop.startedAt,
    speed,
  };
  if (visualDna) {
    mainPoster.startLiving(visualDna, seedFor(selectedRole), selectedRole, plateDevelop.last);
  }
}

function recordWait(entry) {
  fetch("api/wait.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(entry),
  }).catch(() => {});
}

async function loadWaitAverages() {
  try {
    const response = await fetch("api/wait.php");
    const data = await response.json().catch(() => ({}));
    if (typeof data.enabled === "number") {
      waitClock.averages.enabled = data.enabled;
    }
    if (typeof data.disabled === "number") {
      waitClock.averages.disabled = data.disabled;
    }
  } catch (err) {
    /* keep defaults */
  }
}

function startWaitClock(mode) {
  if (waitClock.running) {
    return;
  }
  waitClock.running = true;
  waitClock.startedAt = performance.now();
  waitClock.gptImage = useGptImage() ? "yes" : "no";
  waitClock.mode = mode || "generate";
  waitClock.expected = expectedWaitSeconds(waitClock.gptImage === "yes");
  waitClock.plateAt = 0;
  waitClock.hintDna = mode === "improve" || mode === "reimagine" ? visualDna : null;
  updatePipelineCopy();
  waitClock.tickId = window.setInterval(updatePipelineCopy, 400);
}

function stopWaitClock({ ok, title } = {}) {
  if (!waitClock.running) {
    return 0;
  }
  const elapsed = waitElapsedSeconds();
  const left = secondsLeftWaiting(elapsed, waitClock.expected);
  window.clearInterval(waitClock.tickId);
  waitClock.tickId = 0;
  waitClock.running = false;
  recordWait({
    waited_seconds: Number(elapsed.toFixed(2)),
    seconds_left: Number((left || 0).toFixed(2)),
    expected_seconds: Number((waitClock.expected || 0).toFixed(2)),
    gpt_image: waitClock.gptImage,
    mode: waitClock.mode,
    ok: !!ok,
    title: title || "",
  });
  variationWaits.forEach((el) => {
    el.textContent = "0s waited";
  });
  return elapsed;
}

loadWaitAverages();

function setPosterBusy(isBusy, { mode, ok, title } = {}) {
  if (isBusy) {
    startWaitClock(mode);
    variationsEl.hidden = false;
    setVariationSpinners(true);
    keyArtOverlay.hidden = false;
    hideDevelopTeaser();
    posterFrame.classList.add("is-busy");
    posterFrame.classList.remove("is-developing");
    if (exposeRail) {
      exposeRail.hidden = true;
    }
    updatePipelineCopy();
  } else {
    stopPlateDevelop();
    setVariationSpinners(false);
    keyArtOverlay.hidden = true;
    hideDevelopTeaser();
    posterFrame.classList.remove("is-busy", "is-developing");
    if (exposeRail) {
      exposeRail.hidden = true;
    }
    stopWaitClock({ ok, title });
  }
}

function setPlateDeveloping(isDeveloping, { teaser = true, speed = 1 } = {}) {
  if (isDeveloping) {
    waitClock.plateAt = performance.now();
    keyArtOverlay.hidden = false;
    posterFrame.classList.add("is-developing", "is-busy");
    if (exposeRail) {
      exposeRail.hidden = false;
    }
    if (teaser) {
      showDevelopTeaser(visualDna);
    } else {
      hideDevelopTeaser();
    }
    if (!prefersReducedMotion()) {
      startPlateDevelop({ speed });
    } else if (visualDna) {
      mainPoster.render(visualDna, seedFor(selectedRole), selectedRole, null, null);
    }
  } else {
    stopPlateDevelop();
    keyArtOverlay.hidden = true;
    hideDevelopTeaser();
    posterFrame.classList.remove("is-developing");
    if (exposeRail) {
      exposeRail.hidden = true;
    }
  }
  updatePipelineCopy();
}

function prefersReducedMotion() {
  return window.matchMedia("(prefers-reduced-motion: reduce)").matches;
}

function discardIncomingArt() {
  incomingKeyArt = null;
  mainPoster.cancelExpose();
}

function commitIncomingArt() {
  mainPoster.cancelExpose();
  if (incomingKeyArt) {
    keyArt = incomingKeyArt;
    incomingKeyArt = null;
  }
}

async function revealKeyArt(image) {
  incomingKeyArt = image;
  keyArtOverlay.hidden = true;
  hideDevelopTeaser();
  posterFrame.classList.remove("is-developing");
  if (exposeRail) {
    exposeRail.hidden = true;
  }
  const duration = prefersReducedMotion() ? 0 : 1400;
  const exposing = mainPoster.exposeKeyArt(image, { duration });
  requestAnimationFrame(() => {
    if (incomingKeyArt !== image || !visualDna) {
      return;
    }
    for (const role of ROLES) {
      thumbs[role].render(visualDna, seedFor(role), role, image);
    }
  });
  const ok = await exposing;
  if (ok || incomingKeyArt === image) {
    keyArt = image;
    incomingKeyArt = null;
  }
  showMeta(visualDna);
}

function seedFor(role) {
  return baseSeed + SEED_SHIFTS[role];
}

function renderAll() {
  if (!visualDna) {
    return;
  }
  for (const role of ROLES) {
    thumbs[role].render(visualDna, seedFor(role), role, keyArt);
  }
  mainPoster.render(visualDna, seedFor(selectedRole), selectedRole, keyArt, currentDevelop());
  variationsEl.hidden = false;
  updateSelectionUi();
  const title = visualDna.concept?.title || visualDna.title || "poster";
  posterFrame.querySelector("#poster").setAttribute("aria-label", `Selected ${selectedRole} poster for ${title}`);
}

function updateSelectionUi() {
  document.querySelectorAll(".variation").forEach((btn) => {
    btn.setAttribute("aria-pressed", btn.dataset.role === selectedRole ? "true" : "false");
  });
}

function previousPayload() {
  if (!visualDna) {
    return null;
  }
  return visualDna;
}

async function requestDna({ mode = "generate", previous = null } = {}) {
  const payload = {
    ...filmPayload(),
    mode,
    variation: Math.floor(Math.random() * 1_000_000_000),
  };
  if (previous) {
    payload.previous = previous;
  }

  const response = await fetch("api/generate.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(payload),
  });
  const data = await response.json().catch(() => ({}));
  if (!response.ok) {
    throw new Error(data.error || "Could not create Visual DNA.");
  }
  return data;
}

async function requestImage(dna) {
  if (!useGptImage()) {
    return null;
  }
  const response = await fetch("api/image.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ dna, useImage: true }),
  });
  const data = await response.json().catch(() => ({}));
  if (!response.ok) {
    return null;
  }
  if (!data.image) {
    return null;
  }
  return mainPoster.loadImage(data.image);
}

async function compose({ mode, previous, interpreting }) {
  setError("");
  let ok = false;
  const job = ++composeJob;
  discardIncomingArt();
  setPosterBusy(true, { mode });
  try {
    setStatus(interpreting);
    visualDna = await requestDna({ mode, previous });
    if (job !== composeJob) {
      return;
    }
    keyArt = null;
    incomingKeyArt = null;
    baseSeed = Math.floor(Math.random() * 1_000_000);
    selectedRole = "signature";
    showMeta(visualDna);
    regenerateBtn.disabled = false;
    downloadBtn.disabled = false;
    for (const role of ROLES) {
      thumbs[role].render(visualDna, seedFor(role), role, null);
    }
    variationsEl.hidden = false;
    updateSelectionUi();
    const title = visualDna.concept?.title || visualDna.title || "poster";
    posterFrame.querySelector("#poster").setAttribute("aria-label", `Selected ${selectedRole} poster for ${title}`);
    const quote = visualDna.concept?.quote || visualDna.quote || "";
    const mood = visualDna.concept?.mood || visualDna.mood || "";
    const verb = mode === "improve" ? "Improved" : mode === "reimagine" ? "Reimagined" : "New design";
    if (useGptImage()) {
      setPlateDeveloping(true, { teaser: true, speed: 1 });
      setStatus("Visual DNA resolved. Synthesizing cinematic key art in background...");
      const still = await requestImage(visualDna);
      if (job !== composeJob) {
        return;
      }
      if (still) {
        await revealKeyArt(still);
        if (job !== composeJob) {
          return;
        }
      } else {
        stopPlateDevelop();
        renderAll();
      }
    } else {
      renderAll();
    }
    if (job !== composeJob) {
      return;
    }
    setStatus(`${verb}${mood ? ` · ${mood}` : ""}${quote ? ` · “${quote}”` : ""}`);
    ok = true;
  } finally {
    if (job === composeJob) {
      const title = visualDna?.concept?.title || visualDna?.title || filmPayload().title;
      setPosterBusy(false, { ok, title });
    }
  }
}

function busy(isBusy, mode) {
  generateBtn.disabled = isBusy;
  if (isBusy) {
    enablePosterActions(false);
    setPosterBusy(true, { mode });
  } else if (visualDna) {
    enablePosterActions(true);
  }
}

form.addEventListener("submit", async (event) => {
  event.preventDefault();
  busy(true, "generate");
  try {
    await document.fonts.ready;
    await compose({
      mode: "generate",
      interpreting: "Interpreting the film…",
    });
  } catch (error) {
    setError(error.message);
  } finally {
    busy(false);
  }
});

improveBtn.addEventListener("click", async () => {
  if (!visualDna) {
    return;
  }
  busy(true, "improve");
  try {
    await compose({
      mode: "improve",
      previous: previousPayload(),
      interpreting: "Sending the current design back for a stronger pass…",
    });
  } catch (error) {
    setError(error.message);
  } finally {
    busy(false);
  }
});

reimagineBtn.addEventListener("click", async () => {
  if (!visualDna) {
    return;
  }
  busy(true, "reimagine");
  try {
    await compose({
      mode: "reimagine",
      previous: previousPayload(),
      interpreting: "Asking for a new interpretation of the same film…",
    });
  } catch (error) {
    setError(error.message);
  } finally {
    busy(false);
  }
});

regenerateBtn.addEventListener("click", () => {
  if (!visualDna) {
    return;
  }
  const waitingForStill = !keyArt && !incomingKeyArt && posterFrame.classList.contains("is-busy");
  if (incomingKeyArt) {
    commitIncomingArt();
  } else {
    mainPoster.cancelExpose();
  }
  stopPlateDevelop();
  baseSeed = Math.floor(Math.random() * 1_000_000);
  if (waitingForStill && useGptImage()) {
    startPlateDevelop({ speed: 1 });
    for (const role of ROLES) {
      thumbs[role].render(visualDna, seedFor(role), role, null);
    }
  } else {
    renderAll();
  }
  setStatus("Same Visual DNA" + (keyArt ? " and cinematic still" : "") + ". New procedural execution.");
});

downloadBtn.addEventListener("click", () => {
  if (!visualDna) {
    return;
  }
  const title = visualDna.concept?.title || visualDna.title || "poster";
  mainPoster.download(`frameflux-${slugify(title)}-${selectedRole}`);
});

gptImageBtn.addEventListener("click", () => {
  const next = !useGptImage();
  gptImageBtn.setAttribute("aria-pressed", next ? "true" : "false");
  syncGptImageButton();
  try {
    localStorage.setItem(GPT_IMAGE_KEY, next ? "on" : "off");
  } catch (err) {
    /* ignore quota / private mode */
  }
});

variationsEl.addEventListener("click", (event) => {
  const btn = event.target.closest(".variation");
  if (!btn || !visualDna) {
    return;
  }
  selectedRole = btn.dataset.role;
  if (incomingKeyArt) {
    commitIncomingArt();
    stopPlateDevelop();
    mainPoster.render(visualDna, seedFor(selectedRole), selectedRole, keyArt);
  } else {
    mainPoster.render(
      visualDna,
      seedFor(selectedRole),
      selectedRole,
      keyArt,
      currentDevelop(),
      { cancelExpose: false }
    );
  }
  updateSelectionUi();
  const title = visualDna.concept?.title || visualDna.title || "poster";
  posterFrame.querySelector("#poster").setAttribute("aria-label", `Selected ${selectedRole} poster for ${title}`);
});
