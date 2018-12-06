<?php

namespace BeGateway\Response;

class CheckoutResponse extends BaseResponse
{
    public function isSuccess()
    {
        return isset($this->getResponse()->checkout);
    }

    public function isError()
    {
        $error = parent::isError();

        if (isset($this->getResponse()->checkout) && isset($this->getResponse()->checkout->status)) {
            $error = $error || $this->getResponse()->checkout->status == 'error';
        }

        return $error;
    }

    public function getMessage()
    {
        if (isset($this->getResponse()->message)) {
            return $this->getResponse()->message;
        } elseif (isset($this->getResponse()->response) && isset($this->getResponse()->response->message)) {
            return $this->getResponse()->response->message;
        } elseif ($this->isError()) {
            return $this->compileErrors();
        } else {
            return '';
        }
    }

    public function getToken()
    {
        return $this->getResponse()->checkout->token;
    }

    public function getRedirectUrl()
    {
        return $this->getResponse()->checkout->redirect_url;
    }

    public function getRedirectUrlScriptName()
    {
        return preg_replace('/(.+)\?token=(.+)/', '$1', $this->getRedirectUrl());
    }

    private function compileErrors()
    {
        $message = 'there are errors in request parameters.';

        if (isset($this->getResponse()->errors)) {
            foreach ($this->getResponse()->errors as $name => $desc) {
                $message .= ' ' . $name;
                foreach ($desc as $value) {
                    $message .= ' ' . $value . '.';
                }
            }
        } elseif (isset($this->getResponse()->checkout->message)) {
            $message = $this->getResponse()->checkout->message;
        }

        return $message;
    }
}
