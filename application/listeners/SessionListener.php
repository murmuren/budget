<?php

class SessionListener extends RequestListener{
    
    public function run() {
        $options = new SessionSecurityOptions();
        $options->setExpiredTime(time() + 3600 * 24 * 1); // 1 day
        $session = $this->request->getSession();
        if(!$session->isStarted()) {
            $session->start();
        }
    }
}