import './bootstrap';

document.addEventListener("livewire:navigated", () => {
    console.log('Livewire navigated');
    Livewire.start();
});

