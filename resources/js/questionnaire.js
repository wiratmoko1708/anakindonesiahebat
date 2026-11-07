/**
 * Turns the questionnaire form into a step-by-step wizard (one habit per step).
 * Without JavaScript every step stays visible, so the form still works.
 */
export function initQuestionnaire() {
    const form = document.querySelector('[data-questionnaire]');

    if (!form) {
        return;
    }

    const steps = [...form.querySelectorAll('[data-step]')];
    const progressBar = document.querySelector('[data-progress-bar]');
    const progressLabel = document.querySelector('[data-progress-label]');
    const answeredLabel = document.querySelector('[data-answered-label]');
    const stepDots = [...document.querySelectorAll('[data-step-dot]')];
    const totalQuestions = form.querySelectorAll('[data-question]').length;
    let current = Math.max(0, steps.findIndex((step) => step.querySelector('.has-error')));

    form.classList.add('is-wizard');

    const unansweredIn = (step) =>
        [...step.querySelectorAll('[data-question]')].filter((question) => !question.querySelector('input:checked'));

    const updateProgress = () => {
        const answered = form.querySelectorAll('[data-question] input:checked').length;
        const percentage = Math.round((answered / totalQuestions) * 100);

        if (progressBar) progressBar.style.width = `${percentage}%`;
        if (progressLabel) progressLabel.textContent = `${percentage}%`;
        if (answeredLabel) answeredLabel.textContent = `${answered}/${totalQuestions} terjawab`;

        stepDots.forEach((dot, index) => {
            const done = unansweredIn(steps[index]).length === 0;
            dot.dataset.state = index === current ? 'current' : done ? 'done' : 'todo';
        });
    };

    const show = (index) => {
        current = Math.min(Math.max(index, 0), steps.length - 1);
        steps.forEach((step, stepIndex) => step.classList.toggle('hidden', stepIndex !== current));
        updateProgress();
        steps[current].scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    const flagMissing = (step) => {
        const missing = unansweredIn(step);

        missing.forEach((question) => {
            question.classList.add('ring-2', 'ring-accent-400', 'animate-pop');
            setTimeout(() => question.classList.remove('animate-pop'), 400);
        });

        missing[0]?.scrollIntoView({ behavior: 'smooth', block: 'center' });

        return missing.length === 0;
    };

    form.addEventListener('change', (event) => {
        const question = event.target.closest('[data-question]');
        question?.classList.remove('ring-2', 'ring-accent-400');
        updateProgress();

        const step = steps[current];
        const isLastQuestionOfStep = unansweredIn(step).length === 0;
        const hasNextStep = current < steps.length - 1 && step.querySelector('[data-next]');

        if (isLastQuestionOfStep && hasNextStep && event.target.matches('input[type="radio"]')) {
            setTimeout(() => show(current + 1), 450);
        }
    });

    form.querySelectorAll('[data-next]').forEach((button) =>
        button.addEventListener('click', () => {
            if (flagMissing(steps[current])) show(current + 1);
        }),
    );

    form.querySelectorAll('[data-prev]').forEach((button) => button.addEventListener('click', () => show(current - 1)));

    stepDots.forEach((dot, index) => dot.addEventListener('click', () => show(index)));

    form.addEventListener('submit', (event) => {
        const firstIncomplete = steps.findIndex((step) => unansweredIn(step).length > 0);

        if (firstIncomplete !== -1) {
            event.preventDefault();
            show(firstIncomplete);
            flagMissing(steps[firstIncomplete]);
            return;
        }

        form.querySelector('[type="submit"]')?.setAttribute('disabled', 'disabled');
    });

    steps.forEach((step, index) => step.classList.toggle('hidden', index !== current));
    updateProgress();
}
