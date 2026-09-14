# FrameFlux

Conceptual movie posters from a film title, genre, and optional one-line pitch.

```text
human concept
  → AI Art Director (Visual DNA)
  → cinematic key art
  → p5.js interpretation (five visual languages)
  → three related directions
  → poster
```

AI establishes the cinematic world. Visual DNA structures that world. p5.js transforms it. Typography is drawn last, deterministically, never by the image model.

## Use

```bash
php -S localhost:8080
```

Open [http://localhost:8080](http://localhost:8080).

1. Enter a title, genre, and optional pitch.
2. **Generate** creates Visual DNA, cinematic key art, and three variations.
3. Select Signature, Hybrid, or Alternative.
4. **Regenerate** re-rolls the procedural seed only — no AI, no new still.
5. **Reimagine** keeps the film and asks for a new interpretation (new DNA, new still).
6. **Improve** sends the current DNA back for a stronger pass of the same identity.
7. **Download PNG** exports the selected poster.

## Optional AI key

Copy `config.example.php` to `config.php` and set `openai_api_key`.
`OPENAI_API_KEY` in the environment is used when the file key is empty.

Without a key, Visual DNA is authored by a local heuristic and the cinematic still is a DNA-driven plate. The rest of the hybrid pipeline still runs.

## Visual languages

Flow field, geometric grid, particle scatter, concentric rings, angular mesh.

## Layouts

Centered, off-center top, off-center bottom, split editorial, frame inset.

## Tests

```bash
php tests/test_dna.php
```

## Stack

- Frontend: HTML, CSS, JavaScript, p5.js
- Backend: PHP (`api/generate.php`, `api/image.php`)
- AI: OpenAI Chat Completions (JSON Visual DNA) and Images (cinematic still)
