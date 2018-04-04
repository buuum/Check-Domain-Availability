<?php


namespace DomainAvailability;

class DomainAvailability
{

    protected $whoisClient;
    protected $servers;

    public function __construct()
    {
        $this->whoisClient = new WhoIsClient();
        $this->servers = json_decode(file_get_contents(__DIR__ . '/servers.json'), true);
    }

    public function isAvailable($domain, $quick = false)
    {

        if ($quick) {
            if (gethostbyname($domain) !== $domain) {
                // The domain is taken
                return false;
            }
        }

        $domainInfo = $this->parse($domain);

        if (!isset($this->servers[$domainInfo["tld"]])) {
            throw new \Exception("No WHOIS entry was found for that TLD");
        }

        $whoisServerInfo = $this->servers[$domainInfo["tld"]];

        // Fetch the WHOIS server from the serverlist
        $this->whoisClient->setServer($whoisServerInfo["server"]);

        // If the query fails, it returns false
        if (!$this->whoisClient->query($domainInfo["domain"])) {
            throw new \Exception("WHOIS Query failed");
        }

        // Fetch the response from the WHOIS server.
        $whoisData = $this->whoisClient->getResponse();

        // Check if the WHOIS data contains the "not found"-string
        if (strpos($whoisData, $whoisServerInfo["not_found"]) !== false) {
            // The domain is available
            return true;
        }

        // If we've come this far, the domain is not available.
        return false;
    }


    /**
     * Returns an array of all TLDs supported by the service.
     */
    public function supportedTlds()
    {
        return array_keys($this->servers);
    }


    private function parse($domain)
    {
        $host = parse_url('http://' . $domain, PHP_URL_HOST);
        $host = str_ireplace('www', '', $host);
        $tld = strstr($host, '.');

        $components = [];
        $components["tld"] = substr($tld, 1);
        $components["domain"] = $domain;

        return $components;
    }

}
