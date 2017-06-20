<?php

namespace Fruit\Model;

use Phalcon\Mvc\Model;
use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Regex;

class Products extends Model
{
    public function getSource() {
        return "products";
    }

    public function validation() {
        $validator = new Validation();
        $validator->add('name', new PresenceOf(
            [
                "message" => "name is required",
            ]
        ));

        $validator->add('shop_id', new PresenceOf(
            [
                "message" => "shop_id is required",
            ]
        ));

        $validator->add('user_id', new PresenceOf(
            [
                "message" => "user_id is required",
            ]
        ));

        $validator->add('product_category_id', new PresenceOf(
            [
                "message" => "product_category_id is required",
            ]
        ));

        $validator->add(
            "price",
            new Regex(
                [
                    "message" => "The price is required and must be a number",
                    "pattern" => "/\d+(\.\d+)?/",
                ]
            )
        );

        $validator->add(
            "inventory",
            new Regex(
                [
                    "message" => "The inventory is required and must be a number",
                    "pattern" => "/\d+(\.\d+)?/",
                ]
            )
        );

        $validator->add(
            "pic_url",
            new PresenceOf(
                [
                    "message" => "pic_url is required",
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
