<?php

namespace Fruit\Model;

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Regex;
use Phalcon\Validation\Validator\Numericality;

class UserAddress extends Model
{
    public function getSource() {
        return "user_address";
    }

    public function validation() {
        $validator = new Validation();
        $validator->add('receive_name', new PresenceOf(
            [
                "message" => "receive_name is required",
            ]
        ));

        $validator->add('receive_region', new Numericality(
            [
                "message" => "receive_region is not numeric",
            ]
        ));

        $validator->add('receive_detail', new PresenceOf(
            [
                "message" => "receive_detail is required",
            ]
        ));

        $validator->add(
            "receive_phone",
            new Regex(
                [
                    "message" => "The receive_phone is required",
                    "pattern" => "/^1[34578]\d{9}$/",
                ]
            )
        );

        return $this->validate($validator);
    }

    public function getMessages()
    {
        $messages = [];

        foreach (parent::getMessages() as $message) {
            switch ($message->getType()) {
                case "InvalidValue":
                    $messages[] = "The  field" . $message->getField() . " is invalid";
                    break;

                case "InvalidCreateAttempt":
                    $messages[] = "The record cannot be created because it already exists";
                    break;

                case "InvalidUpdateAttempt":
                    $messages[] = "The record cannot be updated because it doesn't exist";
                    break;

                case "PresenceOf":
                    $messages[] = "The field " . $message->getField() . " is mandatory";
                    break;
            }
        }

        return $messages;
    }
}
