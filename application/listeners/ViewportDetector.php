<?php
require_once 'hlis/Bridge.php';
Bridge::load('MOBILE_DETECT');
/**
 * Detects device, on which application is being viewed.
 * [0, d, desktop] - desktop
 * [2, m, mobile] - mobile
 */
class ViewportDetector  extends RequestListener {
    
    public function run() {
        $this->request->setAttribute("viewport", $this->getViewport());
    }
    
    
    /**
     * Set viewport to mobile only if:
     * 1) Device is mobile
     * 2) User hasn't changed viewport to desktop ( SESSION["mobile"] = 0 )
     * @return array
     */
    private function getViewport() {
        $session = $this->request->getSession();
        // Set tablet apart from mobile
        if(DeviceDetection::getInstance()->is_mobile() && !DeviceDetection::getInstance()->is_tablet()){
            if($session->contains('opposite_viewport') && $session->get('opposite_viewport') === true){
                // device is mobile, but it is negated
                return ['number'=>0, 'code'=>'d', 'string'=>'desktop'];
            }
            // device is mobile
            return ['number'=>2, 'code'=>'m', 'string'=>'mobile'];
        }
        // Desktop and tablet are the same
        else{
            if($session->contains('opposite_viewport') && $session->get('opposite_viewport') === true){
                // device is desktop, but it is negated
                return ['number'=>2, 'code'=>'m', 'string'=>'mobile'];
            }
            // device is desktop
            return ['number'=>0, 'code'=>'d', 'string'=>'desktop'];
        }
    }
}