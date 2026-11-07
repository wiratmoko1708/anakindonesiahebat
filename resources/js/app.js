import { initQuestionnaire } from './questionnaire';
import { launchConfetti } from './confetti';

document.addEventListener('DOMContentLoaded', () => {
    initQuestionnaire();

    if (document.body.dataset.celebrate === 'true') {
        launchConfetti();
    }

    document.querySelectorAll('[data-dismiss]').forEach((button) => {
        button.addEventListener('click', () => button.closest('[data-dismissible]')?.remove());
    });

    document.querySelectorAll('[data-autosubmit]').forEach((control) => {
        control.addEventListener('change', () => control.form?.requestSubmit());
    });

    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    const menuToggle = document.querySelector('[data-menu-toggle]');
    menuToggle?.addEventListener('click', () => {
        document.querySelector('[data-menu]')?.classList.toggle('hidden');
    });
});
