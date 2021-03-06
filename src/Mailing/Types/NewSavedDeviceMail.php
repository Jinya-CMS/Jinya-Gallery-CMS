<?php

namespace App\Mailing\Types;

use App\Database\ApiKey;
use App\Database\KnownDevice;
use App\Mailing\Factory\MailerFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Jenssegers\Agent\Agent;
use JsonException;
use League\Plates\Engine;
use PHPMailer\PHPMailer\Exception;

class NewSavedDeviceMail
{
    private Engine $templateEngine;

    /**
     * NewSavedDeviceMail constructor.
     * @param Engine $templateEngine
     */
    public function __construct(Engine $templateEngine)
    {
        $this->templateEngine = $templateEngine;
    }

    /**
     * Sends the two factor email
     *
     * @param string $artistEmail
     * @param string $artistName
     * @param ApiKey $knownDevice
     * @throws Exception
     * @throws JsonException
     * @throws GuzzleException
     */
    public function sendMail(string $artistEmail, string $artistName, KnownDevice $knownDevice): void
    {
        $client = new Client();
        $result = $client->get("https://freegeoip.app/json/$knownDevice->remoteAddress");
        $userAgent = new Agent(userAgent: $knownDevice->userAgent);
        $browser = $userAgent->browser();
        $platform = $userAgent->platform();
        $location = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        $renderedHtmlMail = $this->templateEngine->render(
            'mailing::NewSavedDeviceHtml',
            [
                'artistName' => $artistName,
                'location' => $location,
                'remoteAddress' => $knownDevice->remoteAddress,
                'platform' => $platform,
                'browser' => $browser,
            ],
        );
        $renderedTextMail = $this->templateEngine->render(
            'mailing::NewSavedDeviceText',
            [
                'artistName' => $artistName,
                'location' => $location,
                'remoteAddress' => $knownDevice->remoteAddress,
                'platform' => $platform,
                'browser' => $browser,
            ],
        );

        $mailer = MailerFactory::getMailer();
        $mailer->Subject = 'New saved device for your account';
        $mailer->setFrom(getenv('MAILER_FROM'));
        $mailer->addAddress($artistEmail);
        $mailer->AltBody = $renderedTextMail;
        $mailer->Body = $renderedHtmlMail;
        $mailer->isHTML();

        $mailer->send();
    }
}
