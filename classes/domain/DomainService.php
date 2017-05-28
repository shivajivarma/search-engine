<?php

include_once(dirname(__DIR__) . '\CustomException.php');

class DomainService
{
    private static $DOMAIN_XML_PATH = '../data/domains.xml';


    public function getDomainXML(){
        if (!file_exists($this::$DOMAIN_XML_PATH)) {
            $domainsXML = new SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?" . "><domains/>");
        } else {
            $domainsXML = simplexml_load_file($this::$DOMAIN_XML_PATH);
        }
        return $domainsXML;
    }

    public function saveDomain($domainName){
        $domainsXML = $this->getDomainXML();
        if (!$domainObject = $domainsXML->XPath("/domains/domain[. = '$domainName']")) {
            $domain = $domainsXML->addChild('domain', $domainName);
            $id = count($domainsXML);
            $domain->addAttribute('id', $id);
            $domain->addAttribute('state', 'crawling');
            $domainsXML->asXML($this::$DOMAIN_XML_PATH);
        } else {
            $domain = $domainObject[0];
        }

        return $domain;
    }

    public function getDomainWithName($domainName){
        $domainsXML = $this->getDomainXML();
        if (!$domainObject = $domainsXML->XPath("/domains/domain[. = '$domainName']")) {
            throw new CustomException("Domain does exists, in crawled list." ,400);
        } else {
            $domain = $domainObject[0];
        }

        return $domain;
    }

}