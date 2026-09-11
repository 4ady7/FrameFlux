# FrameFlux

Conceptual movie posters from a film title, genre, and optional one-line pitch.

The browser sends that information to a PHP endpoint. The endpoint asks an AI model for visual parameters (palette, pattern, layout, quote). Those parameters drive a p5.js canvas: Perlin-noise flow field, geometric grid, or particle scatter. Typography sits on a soft feathered gradient band so titles stay readable over busy patterns.

## Layouts

- **hero** — symmetric, centre-aligned title block
- **editorial** — left-aligned composition near the top
- **billing** — traditional bottom-billing poster stack

## Buttons

- **Generate** — new visual direction and quote, even with the same film inputs
- **Improve** — sends the current parameters back to the model for a stronger redesign (self-feedback loop)
- **Regenerate** — either re-rolls noise only, or asks AI for a new background from the pitch (choose under *When regenerating*)
- **Download PNG** — saves the canvas

## How Improve works

FrameFlux keeps the last parameter object in the browser. Clicking **Improve** POSTs that object as `previous` with `mode: "improve"`. The PHP endpoint asks the model to refine palette, pattern, layout, and quote without abandoning the film identity. Offline, the local fallback also mutates pattern/layout/palette from the previous result.

This is the practical feedback loop: **output → previous params → refined params → redraw**. You do not need to upload the PNG; the structured parameters are enough for the model to improve the design.

## Run locally

PHP 8 is enough. From this folder:

```bash
php -S localhost:8080
```

Open [http://localhost:8080](http://localhost:8080).

## Optional AI key

Copy `config.example.php` to `config.php` and set `openai_api_key`.

Without a key, FrameFlux still generates posters using a genre-based fallback so you can develop offline. The UI labels that as **local fallback**.

## Stack

- Frontend: HTML, CSS, JavaScript, p5.js
- Backend: PHP (`api/generate.php`)
- AI: OpenAI Chat Completions with JSON object responses
