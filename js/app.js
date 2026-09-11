const form = document.querySelector("#film-form");
const generateBtn = document.querySelector("#generate");
const improveBtn = document.querySelector("#improve");
const regenerateBtn = document.querySelector("#regenerate");
const downloadBtn = document.querySelector("#download");
const statusEl = document.querySelector("#status");
const meta = document.querySelector("#meta");

const poster = window.FrameFluxPoster.createPoster("poster");

let visualParams = null;
let seed = Date.now() % 100000;

function setStatus(message, isError = false) {
  statusEl.textContent = message;
  statusEl.classList.toggle("error", isError);
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

function selectedRegenMode() {
  const picked = document.querySelector('input[name="regen-mode"]:checked');
  return picked ? picked.value : "noise";
}

function showMeta(params) {
  meta.hidden = false;
  document.querySelector("#meta-pattern").textContent = params.pattern;
  document.querySelector("#meta-layout").textContent = params.layout;
  const sourceLabel =
    params.source === "ai" ? "AI parameters" : "local fallback";
  const modeLabel = params.mode && params.mode !== "generate" ? ` · ${params.mode}` : "";
  document.querySelector("#meta-source").textContent = sourceLabel + modeLabel;
}

function enablePosterActions(enabled) {
  improveBtn.disabled = !enabled;
  regenerateBtn.disabled = !enabled;
  downloadBtn.disabled = !enabled;
}

async function requestParams({ mode = "generate", previous = null } = {}) {
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
    throw new Error(data.error || "Could not generate visual parameters.");
  }
  return data;
}

async function composePoster({ mode = "generate", previous = null, statusMessage }) {
  setStatus(statusMessage);
  visualParams = await requestParams({ mode, previous });
  seed = Math.floor(Math.random() * 1_000_000);
  poster.render(visualParams, seed);
  enablePosterActions(true);
  showMeta(visualParams);
  const quoteBit = visualParams.quote ? ` · “${visualParams.quote}”` : "";
  setStatus(
    visualParams.mood
      ? `${mode === "improve" ? "Improved" : mode === "background" ? "New background" : "New design"} · ${visualParams.mood}${quoteBit}`
      : "Poster ready."
  );
}

form.addEventListener("submit", async (event) => {
  event.preventDefault();
  generateBtn.disabled = true;
  improveBtn.disabled = true;
  regenerateBtn.disabled = true;
  try {
    await document.fonts.ready;
    await composePoster({
      mode: "generate",
      statusMessage: "Interpreting the film and composing a poster…",
    });
  } catch (error) {
    setStatus(error.message, true);
  } finally {
    generateBtn.disabled = false;
    if (visualParams) {
      enablePosterActions(true);
    }
  }
});

improveBtn.addEventListener("click", async () => {
  if (!visualParams) {
    return;
  }
  generateBtn.disabled = true;
  improveBtn.disabled = true;
  regenerateBtn.disabled = true;
  try {
    await composePoster({
      mode: "improve",
      previous: visualParams,
      statusMessage: "Sending the current design back for a stronger pass…",
    });
  } catch (error) {
    setStatus(error.message, true);
  } finally {
    generateBtn.disabled = false;
    if (visualParams) {
      enablePosterActions(true);
    }
  }
});

regenerateBtn.addEventListener("click", async () => {
  if (!visualParams) {
    return;
  }

  const regenMode = selectedRegenMode();
  if (regenMode === "noise") {
    seed = Math.floor(Math.random() * 1_000_000);
    poster.render(visualParams, seed);
    setStatus("Noise re-rolled. Palette, pattern, layout, and quote are unchanged.");
    return;
  }

  generateBtn.disabled = true;
  improveBtn.disabled = true;
  regenerateBtn.disabled = true;
  try {
    await composePoster({
      mode: "background",
      previous: visualParams,
      statusMessage: "Asking AI for a new background from the pitch…",
    });
  } catch (error) {
    setStatus(error.message, true);
  } finally {
    generateBtn.disabled = false;
    if (visualParams) {
      enablePosterActions(true);
    }
  }
});

downloadBtn.addEventListener("click", () => {
  if (!visualParams) {
    return;
  }
  poster.download(`frameflux-${slugify(visualParams.title)}`);
});
