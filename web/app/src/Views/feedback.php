<?php
/** @var string[] $requiredFields */
$isRequired = static fn(string $field): bool => in_array($field, $requiredFields, true);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма обратной связи</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<main class="container">
    <section class="feedback-form-section">
        <h1>Свяжитесь с нами</h1>

        <form
                id="feedback-form"
                novalidate
                data-required-fields='<?= htmlspecialchars(json_encode($requiredFields), ENT_QUOTES | ENT_HTML5, 'UTF-8') ?>'
        >
            <div class="form-group">
                <label for="full_name">
                    ФИО<?php if ($isRequired('full_name')): ?> <span class="required">*</span><?php endif; ?>
                </label>
                <input
                        type="text"
                        id="full_name"
                        name="full_name"
                        <?php if ($isRequired('full_name')): ?>required<?php endif; ?>
                        maxlength="255"
                        autocomplete="name"
                >
                <span class="error-message" data-error-for="full_name"></span>
            </div>

            <div class="form-group">
                <label for="email">
                    Email<?php if ($isRequired('email')): ?> <span class="required">*</span><?php endif; ?>
                </label>
                <input
                        type="email"
                        id="email"
                        name="email"
                        <?php if ($isRequired('email')): ?>required<?php endif; ?>
                        maxlength="254"
                        autocomplete="email"
                >
                <span class="error-message" data-error-for="email"></span>
            </div>

            <div class="form-group">
                <label for="message">
                    Сообщение<?php if ($isRequired('message')): ?> <span class="required">*</span><?php endif; ?>
                </label>
                <textarea
                        id="message"
                        name="message"
                        rows="5"
                        <?php if ($isRequired('message')): ?>required<?php endif; ?>
                    maxlength="5000"
                ></textarea>
                <span class="error-message" data-error-for="message"></span>
            </div>

            <button type="submit" class="btn-submit">Отправить</button>
            <div id="form-status" class="form-status"></div>
        </form>
    </section>

    <section class="messages-section" id="messages-section" hidden>
        <h2>Ранее отправленные сообщения</h2>
        <div id="messages-list" class="messages-list"></div>
    </section>
</main>

<script src="/js/app.js" type="module"></script>
</body>
</html>