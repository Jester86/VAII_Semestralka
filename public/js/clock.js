document.addEventListener('DOMContentLoaded', () => {

    const clockEl = document.getElementById('live-clock');
    const uptimeEl = document.getElementById('uptime');

    // Get or set the session start time in localStorage
    const SESSION_KEY = 'session_start_time';
    let startTime;

    const storedTime = localStorage.getItem(SESSION_KEY);
    if (storedTime) {
        startTime = new Date(parseInt(storedTime));
    } else {
        startTime = new Date();
        localStorage.setItem(SESSION_KEY, startTime.getTime().toString());
    }

    function pad(num) {
        return num.toString().padStart(2, '0');
    }

    function updateClock() {
        if (!clockEl) return;
        const now = new Date();
        const hours = pad(now.getHours());
        const minutes = pad(now.getMinutes());
        const seconds = pad(now.getSeconds());
        clockEl.textContent = `${hours}:${minutes}:${seconds}`;
    }

    function updateUptime() {
        if (!uptimeEl) return;
        const now = new Date();
        let diff = Math.floor((now - startTime) / 1000);
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

// Clear session start time on logout (call this function when logging out)
function clearSessionUptime() {
    localStorage.removeItem('session_start_time');
}
