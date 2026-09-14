const form = document.querySelector("#film-form");
const generateBtn = document.querySelector("#generate");
const improveBtn = document.querySelector("#improve");
const regenerateBtn = document.querySelector("#regenerate");
const reimagineBtn = document.querySelector("#reimagine");
const downloadBtn = document.querySelector("#download");
const statusEl = document.querySelector("#status");
const errorEl = document.querySelector("#error");
const meta = document.querySelector("#meta");
const variationsEl = document.querySelector("#variations");
const posterFrame = document.querySelector("#poster-frame");

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
  document.querySelector("#meta-layout").textContent = (dna.composition && dna.composition.layout) || dna.layout;
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

function busy(isBusy) {
  generateBtn.disabled = isBusy;
  if (isBusy) {
    enablePosterActions(false);
  } else if (visualDna) {
    enablePosterActions(true);
  }
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
  if (!response.ok) {
    throw new Error(data.error || "Could not create Visual DNA.");
  }
  return data;
}

async function requestImage(dna) {
  const response = await fetch("api/image.php", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ dna }),
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
  setStatus(interpreting);
  visualDna = await requestDna({ mode, previous });
  setStatus("Creating Visual DNA…");
  setStatus("Generating cinematic key art…");
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
}

form.addEventListener("submit", async (event) => {
  event.preventDefault();
  busy(true);
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
  busy(true);
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
  busy(true);
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
  setStatus("Same Visual DNA and cinematic still. New procedural execution.");
});

downloadBtn.addEventListener("click", () => {
  if (!visualDna) {
    return;
  }
  const title = visualDna.concept?.title || visualDna.title || "poster";
  mainPoster.download(`frameflux-${slugify(title)}-${selectedRole}`);
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
