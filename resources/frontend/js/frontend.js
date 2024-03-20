import { Livewire, Alpine } from '../../../vendor/livewire/livewire/dist/livewire.esm';

import "./alpine/components/navigation";
import "./alpine/components/masonry";
import "./alpine/components/slide-over";
import "./alpine/components/calendar";

import anchor from '@alpinejs/anchor'

Alpine.plugin(anchor)

Livewire.start()