<?php

namespace Modules\Sms\App\Channels;

use CURLFile;

class DiscordWebhookChannel
{
    private ?string $content = null;
    private ?string $username = null;
    private ?string $title = null;
    private ?string $description = null;
    private ?string $color = null;
    private ?string $footerIcon = null;
    private ?string $footerText = null;
    private ?string $thumbnailUrl = null;
    private ?string $url = null;
    private ?string $avatarUrl = null;
    private ?string $imageUrl = null;
    private ?string $timestamp = null;
    private ?string $authorName = null;
    private ?string $authorUrl = null;
    private ?string $authorIcon = null;
    private array $fields = [];
    private string $file = '';
    private bool $tts = false;

    /**
     * @param  string|null  $content
     * @return DiscordWebhookChannel
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param  string|null  $username
     * @return DiscordWebhookChannel
     */
    public function setUsername(?string $username): self
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @param  string|null  $title
     * @return DiscordWebhookChannel
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param  string|null  $description
     * @return DiscordWebhookChannel
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param  string|null  $color
     * @return DiscordWebhookChannel
     */
    public function setColor(?string $color): self
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @param  string|null  $footerIcon
     * @return DiscordWebhookChannel
     */
    public function setFooterIcon(?string $footerIcon): self
    {
        $this->footerIcon = $footerIcon;
        return $this;
    }

    /**
     * @param  string|null  $footerText
     * @return DiscordWebhookChannel
     */
    public function setFooterText(?string $footerText): self
    {
        $this->footerText = $footerText;
        return $this;
    }

    /**
     * @param  string|null  $thumbnailUrl
     * @return DiscordWebhookChannel
     */
    public function setThumbnailUrl(?string $thumbnailUrl): self
    {
        $this->thumbnailUrl = $thumbnailUrl;
        return $this;
    }

    /**
     * @param  string|null  $url
     * @return DiscordWebhookChannel
     */
    public function setUrl(?string $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param  string|null  $avatarUrl
     * @return DiscordWebhookChannel
     */
    public function setAvatarUrl(?string $avatarUrl): self
    {
        $this->avatarUrl = $avatarUrl;
        return $this;
    }

    /**
     * @param  string|null  $imageUrl
     * @return DiscordWebhookChannel
     */
    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    /**
     * @param  string|null  $timestamp
     * @return DiscordWebhookChannel
     */
    public function setTimestamp(?string $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @param  string|null  $authorName
     * @return DiscordWebhookChannel
     */
    public function setAuthorName(?string $authorName): self
    {
        $this->authorName = $authorName;
        return $this;
    }

    /**
     * @param  string|null  $authorUrl
     * @return DiscordWebhookChannel
     */
    public function setAuthorUrl(?string $authorUrl): self
    {
        $this->authorUrl = $authorUrl;
        return $this;
    }

    /**
     * @param  string|null  $authorIcon
     * @return DiscordWebhookChannel
     */
    public function setAuthorIcon(?string $authorIcon): self
    {
        $this->authorIcon = $authorIcon;
        return $this;
    }

    /**
     * @param  array  $fields
     * @return DiscordWebhookChannel
     */
    public function setFields(array $fields): self
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * @param  bool  $tts
     * @return DiscordWebhookChannel
     */
    public function setTts(bool $tts): self
    {
        $this->tts = $tts;
        return $this;
    }

    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function toArray(): array
    {
        return [
            'username'   => $this->username,
            'content'    => $this->content,
            'avatar_url' => $this->avatarUrl,
            'tts'        => $this->tts,
            'embeds'     => [
                [
                    'title'       => $this->title,
                    'description' => $this->description,
                    'url'         => $this->url,
                    'timestamp'   => $this->timestamp,
                    'color'       => $this->color,
                    'footer'      => [
                        'text'     => $this->footerText,
                        'icon_url' => $this->footerIcon
                    ],
                    'thumbnail'   => [
                        'url' => $this->thumbnailUrl
                    ],
                    'image'       => [
                        'url' => $this->imageUrl
                    ],
                    'author'      => [
                        'name'     => $this->authorName,
                        'url'      => $this->authorUrl,
                        'icon_url' => $this->authorIcon
                    ],
                    'fields'      => $this->fields
                ]
            ]
        ];
    }

    /**
     * @param  object  $receiver
     * @return $this
     */
    public function setReceiver(object $receiver): self
    {
        $this->data['webHookUrl'] = $receiver->value;

        return $this;
    }

    public function send(): void
    {
        $ch = curl_init($this->data['webHookUrl']);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER     => ['Content-type: multipart/form-data'],
            CURLOPT_POST           => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER         => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => [(isset($this->data['file'])) ? new CURLFile($this->data['file']) : null, 'payload_json' => $this->toJson()]
        ]);

        curl_exec($ch);
        curl_close($ch);
    }

    public function setFile(string $url)
    {
        $this->data['file'] = $url;

        return $this;
    }
}
