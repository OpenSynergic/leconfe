# Filament Layout and Grid System Rules

This rule enforces correct column spanning and grid configurations for Filament forms and infolists in the codebase.

## 1. Grid Column Span Constraints
When the parent form or infolist defines a multi-column system (e.g., `->columns(12)`), layout wrappers or major child components (like `Grid` or `Section`) must span the full 12 columns on smaller screens/default views, and only restrict span on larger breakpoints:
- **Correct**: `->columnSpan(['default' => 12, 'lg' => 8])`
- **Incorrect**: `->columnSpan(['default' => 1, 'lg' => 8])` (this shrinks the element to 1/12 of the screen).

## 2. Explicit Column Definition on Grids
Avoid calling `Grid::make()` without arguments when a single column or specific column structure is desired.
- In Filament, `Grid::make()` without arguments defaults to 2 columns. If it contains a single child (like a `Section`), that child will shrink to 50% width of the grid.
- Always specify the column count explicitly:
  - Use `Grid::make(1)` for a vertical 1-column stack.
  - Use `Grid::make(3)` for a 3-column layout.

## 3. Form Fieldsets and Full Width Components
Component groups like `Fieldset::make()` inside a `Grid` must be given `->columnSpanFull()` or `->columnSpan('full')` if they are intended to occupy the full width of the parent grid.
- Without it, they default to spanning a single grid column, causing layout shrinking.
