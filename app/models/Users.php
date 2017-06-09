<?php

namespace Fruits\Model;

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Regex;

class Users extends Model
{
    public function getSource() {
        return "users";
    }

    public function validation() {
        $validator = new Validation();
        $validator->add('nickname', new PresenceOf(
            [
                "message" => "nickname is required",
            ]
        ));

        $validator->add('user_type', new PresenceOf(
            [
                "message" => "user_type is required",
            ]
        ));

        $validator->add(
            "phone",
            new Regex(
                [
                    "message" => "The phone is required",
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
