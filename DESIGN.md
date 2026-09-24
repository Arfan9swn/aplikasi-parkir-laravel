# DESIGN.md — park. (aplikasi tiket parkir)

Direction for this app, authored by the project owner during the 2026-09-24 UI redesign session.
The five answers below are the owner's; the agent transcribed them and did not invent them.
Anything marked *(derived)* is the agent's implementation detail, open to correction by the owner.

---

## Identity

**The owner at the end of the day, reading numbers.** Dense tables, charts, receipts, print.

Screens answer to that moment first: how many vehicles came in, what was collected, which areas
are full, and what comes out of the printer. The gate screens (`masuk`, `keluar`) must stay fast
and correct, but they are not the centre of gravity of this design.

## Personality

**Ledger: authoritative, quiet, paper-first, accountant precision.**

- Ruled lines, not floating cards.
- Tabular numerals aligned in columns.
- Ink on paper: high text contrast, no atmosphere effects.

## Palette

Cool neutral ground + ink + one navy accent, keeping a thread back to today's brand, so the
change reads as a refinement rather than a replacement.

*(derived)* Every token below is already in `resources/css/app.css`; no new colours are introduced.

| Role | Value | Source |
| --- | --- | --- |
| ground | `#eef3f8` (`primary-50`) | existing token; replaces the stray lavender `rgb(228, 227, 255)` on `body` |
| sheet | `#ffffff` | existing |
| ink | `#111e2d` (`primary-900`) | existing |
| rule (hairline) | `#d8e2ee` | existing border colour used by the pills and selects |
| accent | `#395a7f` (`primary-500`) | existing brand navy |
| accent, strong | `#263d56` (`primary-700`) | existing |

Semantic colours (emerald / amber / red) mark real states only: finished, needs attention, error.
They are never decoration, and never carry emphasis and state at the same time.

## Typography

Titles: a condensed grotesque, **Archivo Narrow** (fallback Barlow Condensed).
Text: **Poppins**, kept from the current build.

- *(derived)* The condensed face carries page titles, table column headers, and the large totals.
- *(derived)* Poppins carries body copy, form labels, and help text.
- *(derived)* Money, plates, ticket numbers, and timestamps use tabular numerals so columns line up.

Honest risk, recorded because it matters: Poppins is a wide geometric sans and pulls against
"ledger, authoritative". The condensed display face has to do that work, and the pairing must not
collapse into Poppins everywhere.

## Mood and dials

**ENERGY 2 / RHYTHM 4 / MOTION 2**

- ENERGY 2: quiet colour. One focal number per screen, everything else recedes.
- RHYTHM 4: strong structural variety between screens, so the app never reads as one repeated
  card grid, one repeated stat row, or one repeated table.
- MOTION 2: motion only where it explains something (hover, focus, a row arriving). No endless
  loops, no stacked entrance animations.

## What this direction forbids

- Decorative patterns with no function: page-wide gradients, background grids, glow.
- Uniform card recipe on every surface, with the same radius and the same soft shadow.
- Capsule pills for things that are not states.
- Endless animation, and entrance animations on every element.
