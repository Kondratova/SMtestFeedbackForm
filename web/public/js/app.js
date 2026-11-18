class FeedbackApp {
    constructor() {
        this.form = document.getElementById('feedback-form');
        this.statusDiv = document.getElementById('form-status');
        this.messagesList = document.getElementById('messages-list');
        this.submitButton = this.form.querySelector('.btn-submit');

        // Список обязательных полей приходит с сервера
        this.requiredFields = this.parseRequiredFields();

        this.init();
    }

    parseRequiredFields() {
        const raw = this.form.dataset.requiredFields;
        if (!raw) {
            return [];
        }
        try {
            const parsed = JSON.parse(raw);
            return Array.isArray(parsed) ? parsed : [];
        } catch (error) {
            console.error('Не удалось разобрать список обязательных полей:', error);
            return [];
        }
    }

    init() {
        this.form.addEventListener('submit', (event) => this.handleSubmit(event));
    }

    /**
     * Клиентская валидация обязательных полей.
     * Возвращает объект { поле: сообщение } или null, если ошибок нет.
     */
    validateRequired() {
        const errors = {};

        this.requiredFields.forEach((field) => {
            const input = this.form.elements[field];
            if (!input) {
                return;
            }

            const value = (input.value || '').trim();
            if (value === '') {
                errors[field] = 'Поле обязательно для заполнения.';
            }
        });

        return Object.keys(errors).length ? errors : null;
    }

    handleSubmit(event) {
        event.preventDefault();
        this.clearErrors();
        this.setStatus('', '');

        // Проверка на клиенте — до отправки
        const clientErrors = this.validateRequired();
        if (clientErrors) {
            this.showErrors(clientErrors);
            this.setStatus('Заполните обязательные поля.', 'error');
            return;
        }

        const formData = new FormData(this.form);
        const email = (formData.get('email') || '').trim();

        this.submitButton.disabled = true;
        this.submitButton.textContent = 'Отправка...';

        fetch('/feedback', {
            method: 'POST',
            body: formData,
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    this.form.reset();
                    this.setStatus(data.response.message, 'success');
                    this.loadMessages(email);
                } else {
                    // Сервер прислал errors (список строк) и response.fields (объект)
                    this.showErrors(data.response.fields || {});
                    this.setStatus(data.errors.join(' ') || 'Произошла ошибка.', 'error');
                }
            })
            .catch((error) => {
                this.setStatus('Ошибка сети. Попробуйте позже.', 'error');
                console.error('Ошибка отправки:', error);
            })
            .finally(() => {
                this.submitButton.disabled = false;
                this.submitButton.textContent = 'Отправить';
            });
    }

    loadMessages(email) {
        const body = new URLSearchParams();
        body.append('email', email);

        fetch('/feedback/list', {
            method: 'POST',
            body: body,
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then((data) => {
                this.renderMessages(data.response.messages);
            })
            .catch((error) => {
                console.error('Не удалось загрузить сообщения:', error);
            });
    }

    renderMessages(messages) {
        if (!messages.length) {
            this.messagesList.innerHTML = '<p class="no-messages">Пока нет сообщений.</p>';
            return;
        }

        this.messagesList.innerHTML = messages.map((msg) => `
            <article class="message-card">
                <header class="message-header">
                    <strong class="message-author">${msg.full_name}</strong>
                    <time class="message-date">${msg.created_at}</time>
                </header>
                <p class="message-email">${msg.email}</p>
                <div class="message-body">${msg.message}</div>
            </article>
        `).join('');
    }

    showErrors(errors) {
        Object.entries(errors).forEach(([field, message]) => {
            const errorSpan = this.form.querySelector(`[data-error-for="${field}"]`);
            if (errorSpan) {
                errorSpan.textContent = message;
            }
        });
    }

    clearErrors() {
        this.form.querySelectorAll('.error-message').forEach((span) => {
            span.textContent = '';
        });
    }

    setStatus(text, type) {
        this.statusDiv.textContent = text;
        this.statusDiv.className = `form-status ${type}`;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new FeedbackApp();
});