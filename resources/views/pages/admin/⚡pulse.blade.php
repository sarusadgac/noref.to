<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.app')] #[Title('Pulse')] class extends Component {
    //
}; ?>

<div class="-m-6 lg:-m-8 h-[calc(100vh)]">
    <iframe
        src="{{ url('/_pulse') }}"
        class="w-full h-full block"
        frameborder="0"
    ></iframe>
</div>
