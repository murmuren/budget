<?php
require_once("vendor/lucinda/internationalization/src/Reader.php");
require_once("src/XMLSessionSetup.php");

/**
 * Performs internationalization & localization by binding php-internationalization-api with XML tag:
 * <internationalization locale="LOCALE" domain="DOMAIN" folder="FOLDER" method="METHOD"/>
 * or:
 * <internationalization locale="LOCALE" domain="DOMAIN" folder="FOLDER" method="session">
 *  <session expiration="{value}" is_http_only="{value}" is_https_only="{value}" handler="{value}"/>
 * </internationalization>
 *
 * Where:
 * - LOCALE: (mandatory) value of default locale (eg: en_US)
 * - DOMAIN: (optional) name of MO file storing localized content (if not set defaults to: "messages")
 * - FOLDER: (optional) folder storing locales (if not set defaults to "locale")
 * - METHOD: (mandatory) method to be used in detecting locales. Possible values:
 *      - header: detects locale via "Accept-Language" HTTP header
 *      - request: detects locale via "locale" querystring parameter.
 *      - session: detects locale via "locale" session parameter, itself originating from a supported "locale" querystring parameter.
 *
 * If detected locale is not yet supported, uses "en_US". If latter is not supported either, a LocaleException is thrown!
 */
class LocalizationListener extends RequestListener
{
    const PARAMETER_NAME = "locale";

    public function run() {
        $xml = $this->application->getXML()->internationalization;
        if(empty($xml)) throw new ApplicationException("Tag missing/empty in configuration.xml: internationalization");

        // detect locale
        $defaultLocale =  (string) $xml["locale"];
        if(!$defaultLocale) throw new ApplicationException("Attribute missing/empty in configuration.xml: internationalization['locale]");
        $detectionMethod = (string) $xml["method"];
        if(!$detectionMethod) throw new ApplicationException("Attribute missing/empty in configuration.xml: internationalization['method]");
        $detectedLocale = $this->getLocale($xml);

        // compiles settings
        $settings = new Lucinda\Internationalization\Settings($detectedLocale);
        $charset = $this->application->getFormatInfo($this->application->getDefaultExtension())->getCharacterEncoding();
        if($charset) $settings->setCharset($charset);
        $domain = (string) $xml["domain"];
        if($domain) $settings->setDomain($domain);
        $folder = (string) $xml["folder"];
        if($folder) $settings->setFolder($folder);

        // if locale has no translations on disk, override it with default
        $file = $settings->getFolder().DIRECTORY_SEPARATOR.$settings->getLocale().DIRECTORY_SEPARATOR."LC_MESSAGES".DIRECTORY_SEPARATOR.$settings->getDomain().".mo";
        if(!file_exists($file)) {
            $detectedLocale = $defaultLocale;
            $settings->setLocale($detectedLocale);
        }

        // sets internationalization settings (throws LocaleException)
        new Lucinda\Internationalization\Reader($settings);

        // save locale in session
        if($detectionMethod == "session") {
            $this->request->getSession()->set(self::PARAMETER_NAME, $detectedLocale);
        }
    }

    private function getLocale(SimpleXMLElement $xml) {
        $method =  (string) $xml["method"];
        switch($method) {
            case "header":
                $header = $this->request->getHeader("Accept-Language");
                if($header) {
                    return str_replace("-", "_", substr($header, 0, strpos($header, ",")));
                }
                break;
            case "request":
                $parameter = $this->request->getURI()->getParameter(self::PARAMETER_NAME);
                if($parameter) {
                    return $parameter;
                }
                break;
            case "session":
                $session = $this->request->getSession();
                if(!$session->isStarted()) {
                    $tag = $xml->session;
                    if(!empty($tag)) {
                        $setup = new XMLSessionSetup($tag);
                        $session->start($setup->getSecurityOptions(), $setup->getHandler());
                    } else {
                        $session->start();
                    }
                }
                $parameter = $this->request->getURI()->getParameter(self::PARAMETER_NAME);
                if($parameter) {
                    return $parameter;
                }
                if($session->contains(self::PARAMETER_NAME)) {
                    return $session->get(self::PARAMETER_NAME);
                }
                break;
            default:
                throw new ApplicationException("Invalid detection method: ".$method);
                break;
        }
        return (string) $xml["locale"];
    }
}