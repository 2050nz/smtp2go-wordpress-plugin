<?php

namespace SMTP2GOWPPlugin\SMTP2GO\Types\Mail;

use SMTP2GOWPPlugin\SMTP2GO\Types\Mail\Attachment;
/** @internal */
class InlineAttachment extends Attachment
{
    public function __construct(string $filename, string $data, string $mimetype)
    {
        $this->filename = $filename;
        $this->fileblob = \base64_encode($data);
        $this->mimetype = $mimetype;
    }
}
