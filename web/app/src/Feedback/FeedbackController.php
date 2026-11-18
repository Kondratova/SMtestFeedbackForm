<?php

declare(strict_types=1);

namespace App\Acme\Feedback;

use App\Acme\Core\Response;

final class FeedbackController
{
    private FeedbackModel $model;
    private FeedbackValidator $validator;

    public function __construct()
    {
        $this->model = new FeedbackModel();
        $this->validator = new FeedbackValidator();
    }
    public function index(): void
    {
        $requiredFields = FeedbackValidator::requiredFields();

        require dirname(__DIR__) . '/Views/feedback.php';
    }

    public function store(): void
    {
        $data = [
            'full_name' => trim((string) ($_POST['full_name'] ?? '')),
            'email'     => trim((string) ($_POST['email'] ?? '')),
            'message'   => trim((string) ($_POST['message'] ?? '')),
        ];

        if (!$this->validator->validateForm($data)) {
            Response::error(
                ['Форма не была отправлена.'],
                422,
                ['fields' => $this->validator->getErrors()],
            )->send();
            return;
        }

        $created = $this->model->create($data['full_name'], $data['email'], $data['message']);

        if (!$created) {
            Response::error(
                ['Не удалось сохранить сообщение.'],
                500,
            )->send();
            return;
        }

        Response::success(
            ['message' => 'Сообщение успешно отправлено.'],
            201,
        )->send();
    }

    public function list(): void
    {
        $email = trim((string) ($_POST['email'] ?? ''));
        $this->validator->validateEmail($email);

        if (!$this->validator->isValid()) {
            Response::error(
                array_values($this->validator->getErrors()),
                422,
                ['fields' => $this->validator->getErrors()],
            )->send();
            return;
        }

        $messages = $this->model->getByEmail($email);

        $messages = array_map(function (array $msg): array {
            return [
                'id'         => (int) $msg['id'],
                'full_name'  => htmlspecialchars($msg['full_name'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'email'      => htmlspecialchars($msg['email'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'message'    => nl2br(htmlspecialchars($msg['message'], ENT_QUOTES | ENT_HTML5, 'UTF-8')),
                'created_at' => htmlspecialchars($msg['created_at'], ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            ];
        }, $messages);

        Response::success(['messages' => $messages])->send();
    }
}