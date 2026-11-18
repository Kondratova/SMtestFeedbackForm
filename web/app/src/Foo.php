<?php

/**  * I belong to a file
 */

namespace App\Acme;

use App\Acme\Feedback\FeedbackController;

/**
 * I belong to a class
 */
class Foo
{
    /**
     * Gets the name of the application.
     */
    public function getName()
    {
        $feedbackController = new FeedbackController();
        $feedbackController->index();

        return 'Nginx PHP MySQL';
    }
}
