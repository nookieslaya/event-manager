const FORM_SELECTOR = '.em-registration-form';

const initEventRegistration = () => {
    if (typeof EventManagerData === 'undefined') return;

    const form = document.querySelector(FORM_SELECTOR);
    if (!form) return;

    const messageBox = form.querySelector('.em-message');
    const submitButton = form.querySelector('button[type="submit"]');
    const countEl = document.querySelector('.em-registrations-count');
    const limitEl = document.querySelector('.em-registrations-limit');

    const setMessage = (text = '', type) => {
        if (!messageBox) return;
        messageBox.textContent = text;
        messageBox.className = 'em-message';
        if (type) {
            messageBox.classList.add(`em-${type}`);
        }
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        setMessage();

        if (submitButton) {
            submitButton.disabled = true;
        }

        const formData = new FormData(form);
        formData.append('action', 'register_event');
        formData.append('nonce', EventManagerData.nonce);

        try {
            const response = await fetch(EventManagerData.ajaxUrl, {
                method: 'POST',
                credentials: 'same-origin',
                body: formData,
            });

            let json = {};
            try {
                json = await response.json();
            } catch (e) {
                json = {};
            }

            const payload = json && json.data ? json.data : {};
            const success = json && json.success === true;
            const message = typeof payload.message === 'string' ? payload.message : EventManagerData.messages.genericError;
            const registrations = typeof payload.registrations !== 'undefined' ? payload.registrations : null;
            const limit = typeof payload.limit !== 'undefined' ? payload.limit : null;

            setMessage(message, success ? 'success' : 'error');

            if (success && countEl && registrations !== null) {
                countEl.textContent = registrations;
            }

            if (success && limitEl && limit !== null && Number(limit) > 0 && registrations !== null) {
                if (Number(registrations) >= Number(limit) && submitButton) {
                    submitButton.disabled = true;
                    submitButton.textContent = 'Brak miejsc';
                }
            }

            if (success) {
                form.reset();
            }
        } catch (error) {
            setMessage(EventManagerData.messages.genericError, 'error');
        } finally {
            if (submitButton && !(limitEl && countEl && Number(countEl.textContent) >= Number(limitEl.textContent || 0))) {
                submitButton.disabled = false;
            }
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    if (!document.querySelector(FORM_SELECTOR)) return;
    initEventRegistration();
});
