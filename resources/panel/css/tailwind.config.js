import preset from '../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Administration/**/*.php',
        './app/Filament/**/*.php',
        './app/Livewire/**/*.php',
        './app/Panel/**/*.php',
        './resources/views/administration/**/*.blade.php',
        './resources/views/examples/**/*.blade.php',
        './resources/views/filament/**/*.blade.php',
        './resources/views/forms/**/*.blade.php',
        './resources/views/panel/**/*.blade.php',
        './resources/views/vendor/**/*.blade.php',
        './vendor/awcodes/filament-tiptap-editor/resources/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    plugins: [require('tailwindcss-animate')],
}
