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
const posterSpinner = document.querySelector("#poster-spinner");
const posterSpinnerLabel = document.querySelector("#poster-spinner-label");

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
let baseSeed = Date.now() % 100000;
let selectedRole = "signature";
const GPT_IMAGE_KEY = "frameflux-gpt-image";
const waitClock = {
  running: false,
  startedAt: 0,
  expected: null,
  tickId: 0,
  gptImage: "no",
  mode: "generate",
  averages: { enabled: 45, disabled: 10 },
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
    return `Composing poster… ${waited}`;
  }
  if (left <= 0) {
    return `Composing poster… ${waited} · finishing`;
  }
  return `Composing poster… ${waited} · ${Math.ceil(left)}s left`;
}

function updateWaitLabel() {
  const elapsed = waitElapsedSeconds();
  const left = secondsLeftWaiting(elapsed, waitClock.expected);
  posterSpinnerLabel.textContent = formatWaitLabel(elapsed, left);
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

function recordWait(entry) {
  fetch("api/wait.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(entry),
  }).catch(() => {});
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
  updateWaitLabel();
  waitClock.tickId = window.setInterval(updateWaitLabel, 250);
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
  posterSpinnerLabel.textContent = "Composing poster…";
  // #region agent log
  fetch('http://127.0.0.1:7648/ingest/5ace3a12-def6-4947-b220-deb1d40a8b9e',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'78eac4'},body:JSON.stringify({sessionId:'78eac4',runId:'post-fix',hypothesisId:'W',location:'js/app.js:stopWaitClock',message:'wait recorded',data:{elapsed,left,gptImage:waitClock.gptImage,mode:waitClock.mode,ok:!!ok},timestamp:Date.now()})}).catch(()=>{});
  // #endregion
  return elapsed;
}

loadWaitAverages();

function setPosterBusy(isBusy, { mode, ok, title } = {}) {
  posterSpinner.hidden = !isBusy;
  posterFrame.classList.toggle("is-busy", isBusy);
  posterSpinner.setAttribute("aria-busy", isBusy ? "true" : "false");
  if (isBusy) {
    startWaitClock(mode);
  } else {
    stopWaitClock({ ok, title });
  }
  // #region agent log
  fetch('http://127.0.0.1:7648/ingest/5ace3a12-def6-4947-b220-deb1d40a8b9e',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'78eac4'},body:JSON.stringify({sessionId:'78eac4',runId:'post-fix',hypothesisId:'S',location:'js/app.js:setPosterBusy',message:'poster spinner',data:{isBusy,hidden:posterSpinner.hidden},timestamp:Date.now()})}).catch(()=>{});
  // #endregion
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
  mainPoster.render(visualDna, seedFor(selectedRole), selectedRole, keyArt);
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
  // #region agent log
  fetch('http://127.0.0.1:7648/ingest/5ace3a12-def6-4947-b220-deb1d40a8b9e',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'78eac4'},body:JSON.stringify({sessionId:'78eac4',runId:'post-fix',hypothesisId:'E',location:'js/app.js:requestDna',message:'generate response',data:{status:response.status,ok:response.ok,source:data.source||null},timestamp:Date.now()})}).catch(()=>{});
  // #endregion
  if (!response.ok) {
    throw new Error(data.error || "Could not create Visual DNA.");
  }
  return data;
}

async function requestImage(dna) {
  if (!useGptImage()) {
    // #region agent log
    fetch('http://127.0.0.1:7648/ingest/5ace3a12-def6-4947-b220-deb1d40a8b9e',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'78eac4'},body:JSON.stringify({sessionId:'78eac4',runId:'post-fix',hypothesisId:'E',location:'js/app.js:requestImage',message:'image skipped',data:{useGptImage:false},timestamp:Date.now()})}).catch(()=>{});
    // #endregion
    return null;
  }
  const response = await fetch("api/image.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ dna, useImage: true }),
  });
  const data = await response.json().catch(() => ({}));
  // #region agent log
  fetch('http://127.0.0.1:7648/ingest/5ace3a12-def6-4947-b220-deb1d40a8b9e',{method:'POST',headers:{'Content-Type':'application/json','X-Debug-Session-Id':'78eac4'},body:JSON.stringify({sessionId:'78eac4',runId:'post-fix',hypothesisId:'E',location:'js/app.js:requestImage',message:'image response',data:{status:response.status,ok:response.ok,source:data.source||null,hasImage:!!data.image,note:data.note||null},timestamp:Date.now()})}).catch(()=>{});
  // #endregion
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
  setPosterBusy(true, { mode });
  try {
    setStatus(interpreting);
    visualDna = await requestDna({ mode, previous });
    setStatus("Creating Visual DNA…");
    if (useGptImage()) {
      setStatus("Generating cinematic key art…");
    }
    keyArt = await requestImage(visualDna);
    setStatus("Building FrameFlux variations…");
    baseSeed = Math.floor(Math.random() * 1_000_000);
    selectedRole = "signature";
    renderAll();
    showMeta(visualDna);
    const quote = visualDna.concept?.quote || visualDna.quote || "";
    const mood = visualDna.concept?.mood || visualDna.mood || "";
    const verb = mode === "improve" ? "Improved" : mode === "reimagine" ? "Reimagined" : "New design";
    setStatus(`${verb}${mood ? ` · ${mood}` : ""}${quote ? ` · “${quote}”` : ""}`);
    ok = true;
  } finally {
    const title = visualDna?.concept?.title || visualDna?.title || filmPayload().title;
    setPosterBusy(false, { ok, title });
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
  baseSeed = Math.floor(Math.random() * 1_000_000);
  renderAll();
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
  mainPoster.render(visualDna, seedFor(selectedRole), selectedRole, keyArt);
  updateSelectionUi();
  const title = visualDna.concept?.title || visualDna.title || "poster";
  posterFrame.querySelector("#poster").setAttribute("aria-label", `Selected ${selectedRole} poster for ${title}`);
});
