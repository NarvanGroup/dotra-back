<?php

namespace App\Traits;

trait MessageFormaterTrait
{
    protected string $message = '';

    /**
     * Add a message line with a newline.
     *
     * @param  string  $text
     * @return $this
     */
    public function line(string $text): self
    {
        $this->message .= $text."\n";
        return $this;
    }

    /**
     * Add a cancel message.
     *
     * @return string
     */
    public function cancel(): string
    {
        return $this->message."لغو 11";
    }

    /**
     * Get the final message.
     *
     * @return string
     */
    public function getMessage(): string
    {
        return trim($this->message); // Trim to remove any trailing newline
    }
}