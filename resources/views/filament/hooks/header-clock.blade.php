<div style="display: flex; align-items: center; gap: 0.5rem; margin-right: 1rem; font-size: 0.875rem; font-weight: 500;" class="text-gray-500 dark:text-gray-400"
     x-data="{
        time: '',
        date: '',
        updateTime() {
            const now = new Date();
            this.time = now.toLocaleTimeString('es-VE', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
            this.date = now.toLocaleDateString('es-VE', { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric' });
        }
     }"
     x-init="updateTime(); setInterval(() => updateTime(), 1000)">
    <svg style="width: 1.25rem; height: 1.25rem; color: currentColor;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
    </svg>
    <span style="text-transform: capitalize;" x-text="date + ' | ' + time"></span>
</div>
