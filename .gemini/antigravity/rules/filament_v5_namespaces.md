# Filament v5 Namespace and Signature Rules

This rule ensures that all Filament pages, components, and plugins in the codebase use the correct namespaces and signatures compatible with Filament v5 (unified Schemas architecture).

## 1. Form Method Signatures
In Filament v5, forms are built using unified Schemas. Always use the `Schema` class for parameter and return types:
- **Correct**:
  ```php
  use Filament\Schemas\Schema;

  public function form(Schema $schema): Schema
  {
      return $schema->schema([...]);
  }
  ```
- **Incorrect**:
  ```php
  use Filament\Forms\Form;

  public function form(Form $form): Form
  {
      return $form->schema([...]);
  }
  ```

## 2. Layout Components
Layout components (e.g., `Livewire`, `Section`, `Actions`, `Grid`, `Tabs`) are unified and must be imported from the `Filament\Schemas\Components` namespace, not from the legacy Infolists or Forms namespaces:
- **Correct**:
  ```php
  use Filament\Schemas\Components\Livewire;
  use Filament\Schemas\Components\Section;
  use Filament\Schemas\Components\Actions;
  ```
- **Incorrect**:
  ```php
  use Filament\Infolists\Components\Livewire;
  use Filament\Infolists\Components\Section;
  use Filament\Forms\Components\Actions;
  ```

## 3. Schema Actions
Actions used inside layouts/forms must be imported from `Filament\Actions` instead of the legacy `Filament\Forms\Components\Actions\Action`:
- **Correct**:
  `use Filament\Actions\Action;`
- **Incorrect**:
  `use Filament\Forms\Components\Actions\Action;`
