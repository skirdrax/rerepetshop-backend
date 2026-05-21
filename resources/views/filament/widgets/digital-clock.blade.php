<x-filament-widgets::widget>
    <x-filament::section class="h-full bg-white border-[#2d2d4e] dark:bg-gray-900 dark:border-gray-700">
        <div
            x-data="{
                time: '',
                date: '',
                darkMode: document.documentElement.classList.contains('dark'),
                update() {
                    const now = new Date();
                    this.time = now.toLocaleTimeString('id-ID');
                    this.date = now.toLocaleDateString('id-ID', {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                }
            }"
            x-init="
                update();
                setInterval(() => update(), 1000);

                const observer = new MutationObserver(() => {
                    darkMode = document.documentElement.classList.contains('dark');
                });

                observer.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class'],
                });
            "
            class="flex h-full flex-col items-center justify-center py-6 text-center"
        >
            <p
                x-text="date"
                x-bind:style="darkMode
                    ? 'font-size:20px; font-weight:600; letter-spacing:0.15em; color:#fbbf24;'
                    : 'font-size:20px; font-weight:600; letter-spacing:0.15em; color:#EF9F27;'"
            ></p>

            <h1
                x-text="time"
                x-bind:style="darkMode
                    ? 'font-size:42px; font-weight:900; color:#FB2F2F; font-family:monospace;'
                    : 'font-size:42px; font-weight:900; color:#850909 ; font-family:monospace;'"
            ></h1>

            <div style="margin-top:16px;">
                <span x-bind:style="darkMode ? 'color:#d1d5db;' : 'color:#000000;'">System Live</span>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
