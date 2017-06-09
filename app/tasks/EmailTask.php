<?php

class EmailTask extends \Phalcon\CLI\Task
{
    public function sendAction()
    {
        $this->queue->watch('service_email');
        while (true) {
            $job = $this->queue->peekReady();
            if (!$job) {
                continue;
            }
            $mail = $job->getBody();
            $email = new Trest\Model\Email($this);
            $email->sendDirect($mail['to'], $mail['from'], $mail['subject'], $mail['content']);
        }
    }
}
