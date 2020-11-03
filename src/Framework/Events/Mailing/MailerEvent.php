<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 24.08.18
 * Time: 18:18
 */

namespace Jinya\Framework\Events\Mailing;

use Jinya\Entity\Form\Form;
use Jinya\Framework\Events\Common\CancellableEvent;

class MailerEvent extends CancellableEvent
{
    public const PRE_SEND_MAIL = 'MailerPreSendMail';

    public const POST_SEND_MAIL = 'MailerPostSendMail';

    private Form $form;

    private array $data;

    /**
     * MailerEvent constructor.
     */
    public function __construct(Form $form, array $data)
    {
        $this->form = $form;
        $this->data = $data;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
