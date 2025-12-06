document.addEventListener('DOMContentLoaded', () => {

    const clockEl = document.getElementById('live-clock');
    const uptimeEl = document.getElementById('uptime');

    // Store the start time when page loaded
    const startTime = new Date();

    function pad(num) {
        return num.toString().padStart(2, '0');
    }

    function updateClock() {
        const now = new Date();
        const hours = pad(now.getHours());
        const minutes = pad(now.getMinutes());
        const seconds = pad(now.getSeconds());
        clockEl.textContent = `${hours}:${minutes}:${seconds}`;
    }

    function updateUptime() {
        const now = new Date();
        let diff = Math.floor((now - startTime) / 1000); // seconds elapsed
        const hours = pad(Math.floor(diff / 3600));
        diff %= 3600;
        const minutes = pad(Math.floor(diff / 60));
        const seconds = pad(diff % 60);
        uptimeEl.textContent = `${hours}:${minutes}:${seconds}`;
    }

    // Initial update
    updateClock();
    updateUptime();

    // Update every second
    setInterval(() => {
        updateClock();
        updateUptime();
    }, 1000);

});
