const COLORS = ['#304070', '#e03030', '#f59e0b', '#10b981', '#0ea5e9', '#f43f5e', '#8b5cf6'];

/**
 * Lightweight canvas confetti burst, no dependencies.
 */
export function launchConfetti(durationMs = 2600) {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const canvas = document.createElement('canvas');
    canvas.className = 'pointer-events-none fixed inset-0 z-[100]';
    document.body.appendChild(canvas);

    const context = canvas.getContext('2d');
    const resize = () => {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    };
    resize();
    window.addEventListener('resize', resize);

    const pieces = Array.from({ length: 160 }, () => ({
        x: Math.random() * canvas.width,
        y: -20 - Math.random() * canvas.height * 0.5,
        size: 6 + Math.random() * 6,
        speedY: 2 + Math.random() * 3.5,
        speedX: -1.5 + Math.random() * 3,
        rotation: Math.random() * Math.PI,
        spin: -0.15 + Math.random() * 0.3,
        color: COLORS[Math.floor(Math.random() * COLORS.length)],
    }));

    const startedAt = performance.now();

    const frame = (now) => {
        context.clearRect(0, 0, canvas.width, canvas.height);
        const fade = Math.max(0, 1 - (now - startedAt - durationMs * 0.6) / (durationMs * 0.4));

        pieces.forEach((piece) => {
            piece.x += piece.speedX;
            piece.y += piece.speedY;
            piece.rotation += piece.spin;

            context.save();
            context.globalAlpha = Math.min(1, fade);
            context.translate(piece.x, piece.y);
            context.rotate(piece.rotation);
            context.fillStyle = piece.color;
            context.fillRect(-piece.size / 2, -piece.size / 4, piece.size, piece.size / 2);
            context.restore();
        });

        if (now - startedAt < durationMs) {
            requestAnimationFrame(frame);
        } else {
            window.removeEventListener('resize', resize);
            canvas.remove();
        }
    };

    requestAnimationFrame(frame);
}
