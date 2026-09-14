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

The genre is an input for interpretation only. It steers colour, material, lighting, letterforms and procedural choices, but is never printed on the poster — `typography.genreVisibility` is locked to `hidden` and covered by a test.

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

## Typography

Title and quote are separate art-directed decisions in the Visual DNA, derived from the story rather than the genre.

- **Title** (`typography.title`): weight, case, letterforms, tracking, structural treatment (solid, outline, fragmented, layered, textured, gradient) and placement. Scale comes from title length and the safe width; long titles wrap, and a single unbreakable word hard-breaks rather than overflowing.
- **Quote** (`typography.quote`): style (editorial italic, caption, cinematic subtitle, typewriter, handwritten), its own legibility technique (scrim, shadow, plate) and placement. The quote face is always forced to contrast with the title face instead of repeating it smaller.

Risky treatments are validated against the artwork underneath and degrade to a safer one when local contrast is insufficient. Readability wins over style.

## Tests

```bash
php tests/test_dna.php
```

## Stack

- Frontend: HTML, CSS, JavaScript, p5.js
- Backend: PHP (`api/generate.php`, `api/image.php`)
- AI: OpenAI Chat Completions (JSON Visual DNA) and Images (cinematic still)
