import preset from '../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Livewire/**/*.php',
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './resources/views/panel/**/*.blade.php',
        './resources/views/forms/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './vendor/awcodes/filament-tiptap-editor/resources/**/*.blade.php',
    ],
    plugins: [require('tailwindcss-animate')],
}
