<?php

class OrderTask extends \Phalcon\CLI\Task
{
    const QUEUE_TUBE = 'add_oreder';

    public function addOrderAction()
    {
        $this->queue->watch(self::QUEUE_TUBE);
        while (true) {
            $job = $this->queue->peekReady();
            if (!$job) {
                break;
            }
            $order = $job->getBody();
            // TODO: sms,email and so on.
            $orderId = $order['order_id'];
        }
    }
}
