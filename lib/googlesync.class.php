<?php

define('GOOGLE_CONTACTS_URL', 'https://www.google.com/m8/feeds/contacts/default/full');
define('GOOGLE_GROUP_CONTACTS_URL', 'https://www.google.com/m8/feeds/contacts/default/base/');
define('GOOGLE_GROUPS_URL', 'https://www.google.com/m8/feeds/groups/default/full');

define('GOOGLE_CONTACTS_MAX_RESULTS', 1000);

require_once 'configuratie.include.php';

/**
 * Documentatie voor Google Contacts API:
 * algemeen, interactie: https://developers.google.com/google-apps/contacts/v3/
 * alle referentie https://developers.google.com/google-apps/contacts/v3/reference
 */
class GoogleSync {

	private $gdata = null;
	private $groupname = 'C.S.R.-import';
    /**
     * @var SimpleXMLElement[]
     */
	private $groupFeed = null; // Zend GData feed object for groups
	private $groupid = null;  // google-id van de groep waar alles in terecht moet komen...
    /**
     * @var SimpleXMLElement[]
     */
	private $contactFeed = null;
	private $contactData = null; // an array containing array's with some data for each contact.
	//sigleton pattern
	private static $instance;
    private $client; // GoogleClient

    private $access_token;

    public static function instance() {
		if (!isset(self::$instance)) {
			self::$instance = new GoogleSync();
		}
		return self::$instance;
	}

	private function __construct() {
		if (!isset($_SESSION['google_token'])) {
			throw new Exception('Authsub token not available');
		}

		if (LidInstellingen::get('googleContacts', 'groepnaam') != '') {
			$this->groupname = trim(LidInstellingen::get('googleContacts', 'groepnaam'));
			if ($this->groupname == '') {
				$this->groupname = 'C.S.R.-import';
			}
		}

        $redirect_uri = CSR_ROOT . '/googlecallback';
        $client = new Google_Client();
        $client -> setApplicationName('Stek');
        $client -> setClientId(GOOGLE_CLIENT_ID);
        $client -> setClientSecret(GOOGLE_CLIENT_SECRET);
        $client -> setRedirectUri($redirect_uri);
        $client -> setAccessType('online');
        $client -> setScopes('https://www.google.com/m8/feeds');
        if (!isset($_SESSION['access_token'])) {
            $client->authenticate($_SESSION['google_token']);
            $_SESSION['access_token'] = $client->getAccessToken();
        }

        $client->setAccessToken($_SESSION['access_token']);

        $this->access_token = json_decode($_SESSION['access_token'])->access_token;

        $this->client = $client;

		//$client->setHeaders('If-Match: *'); //delete or update only if not changed since it was last read.
		//$this->gdata = new Zend_Gdata($this->client);
		//$this->gdata->setMajorProtocolVersion(3);


        //first load group feed, find or create the groupname from the user settings.
		$this->loadGroupFeed();
		$this->groupid = $this->getGroupId();

		//then load the contacts for this group.
		$this->loadContactsForGroup($this->groupid);

		//copy setting from settings manager.
		$this->extendedExport = LidInstellingen::get('googleContacts', 'extended') == 'ja';
	}

	private static function doGoogleRequest($url, $token) {
        $ch = curl_init($url);
        $headers = array(
            'Authorization: Bearer ' . $token,
            'GData-Version: 3.0'
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) === 401)
        {
            curl_close($ch);
            return NULL;
        }

        curl_close($ch);

        return simplexml_load_string($response);

    }

	/**
	 * Load all contactgroups.
	 */
	private function loadGroupFeed() {
		$this->groupFeed = GoogleSync::doGoogleRequest(GOOGLE_GROUPS_URL, $this->access_token)->entry;
	}

	/**
	 * Load contacts from certain contact group.
	 */
	private function loadContactsForGroup($groupId) {
        // Default max-results is 25, laad alles in 1 keer
        $this->contactFeed = GoogleSync::doGoogleRequest(GOOGLE_CONTACTS_URL . '?max-results=1000&group=' . urlencode($groupId), $this->access_token)->entry;
	}

	/**
	 * Trek naam googleId en wat andere relevante meuk uit de feed-objecten
	 * en stop dat in een array.
	 */
	public function getGoogleContacts() {
		if ($this->contactData == null) {
			$this->contactData = array();
			foreach ($this->contactFeed as $contact) {

                //digg up uid.
                $uid = null;
                foreach($contact->{'gContact$userDefinedField'} as $field) {
                    if ($field->key == 'csruid') {
                        $uid = $field->value;
                    }
                }
                $link = null;
                foreach($contact->link as $user_link) {
                    if ($user_link->rel == 'self') {
                        $link = $user_link;
                    }
                }

				$etag = substr($contact->{'gd$etag'}, 1, strlen($contact->{'gd$etag'}) - 2);
				$this->contactData[] = array(
					'name'	 => (string) $contact->title->{'$t'},
					'etag'	 => $etag,
					'id'	 => (string) $contact->id->{'$t'},
					'self'	 => $link->href,
					'csruid' => $uid
						//, 'xml'=>htmlspecialchars(str_replace('><', ">\n<", $contact->getXML()))
				);
			}
		}

		return $this->contactData;
	}

	/**
	 * Plaats een foto voor een google contact.
	 *
	 * @param $photolink link uit een google-entry waar de foto naartoe moet.
	 * @param $filename bestandsnaam van de foto die moet worden opgestuurd.
	 */
	private function putPhoto($photolink, $filename) {
		$this->gdata->put(file_get_contents($filename), $photolink, null, 'image/*');
	}

	/**
	 * Check of een Lid al voorkomt in de lijst met contacten zoals ingeladen van google.
	 *
	 * @param $profiel Lid waarvan de aanwezigheid gechecked moet worden.
	 *
	 * @return string met het google-id in het geval van voorkomen, anders null.
	 */
	public function existsInGoogleContacts(Profiel $profiel) {
		$name = strtolower($profiel->getNaam());
		foreach ($this->getGoogleContacts() as $contact) {

			if (
					$contact['csruid'] == $profiel->uid OR
					strtolower($contact['name']) == $name OR
					str_replace(' ', '', strtolower($contact['name'])) == str_replace(' ', '', $name)
			) {
				return $contact['id'];
			}
		}
		return null;
	}

	/**
	 * return the etag for any matching contact in this->contactFeed.
	 */
	public function getEtag($googleid) {
		foreach ($this->getGoogleContacts() as $contact) {
			if (strtolower($contact['id']) == $googleid) {
				return $contact['etag'];
			}
		}
		return null;
	}

	public function getLinkSelf($googleid) {
		foreach ($this->getGoogleContacts() as $contact) {
			if (strtolower($contact['id']) == $googleid) {
				return $contact['self'];
			}
		}
		return null;
	}

	/**
	 * Get array with group[name] => id
	 */
	function getGroups() {
		$return = array();
		foreach ($this->groupFeed as $group) {
			$title = (string) $group->title->{'$t'};

			if (substr($title, 0, 13) == 'System Group:') {
				$title = substr($title, 14);
			}
			//viesss, check of er een SystemGroup-tag bestaat, zo ja, het systemgroupid
			//opslaan in de array.
			//Dit ID hebben we nodig om onafhankelijk van de ingestelde taal @google de system
			//group 'My Contacts' te kunnen gebruiken
			$systemgroup = null;
            if (isset($group->{'gContact$systemGroup'})) {
                $systemgroup = $group->{'gContact$systemGroup'}->id;
            }

			$return[] = array(
				'id'			 => $group->id->{'$t'},
				'name'			 => $title,
				'systemgroup'	 => $systemgroup
			);
		}
		return $return;
	}

	/**
	 * id van de systemgroup aan de hand van de system-group-id ophalen
	 *
	 * http://code.google.com/apis/contacts/docs/2.0/reference.html#GroupElements
	 */
	private function getSystemGroupId($name) {
		//kijken of we al een grop hebben met de naam
		foreach ($this->getGroups() as $group) {
			if ($group['systemgroup'] == $name) {
				return $group['id'];
			}
		}
		return null;
	}

	/**
	 * Get the groupid for the group $this->groupname, or create and return groupname.
	 *
	 * @return string met het google group-id.
	 */
	private function getGroupId($groupname = null) {
		if ($groupname == null) {
			$groupname = $this->groupname;
		}
		//kijken of we al een grop hebben met de naam
		foreach ($this->getGroups() as $group) {
			if ($group['name'] == $groupname) {
				return $group['id'];
			}
		}

		//zo niet, dan maken deze groep nieuw aan.
		$doc = new DOMDocument();
		$doc->formatOutput = true;
		$entry = $doc->createElement('atom:entry');
		$entry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:atom', 'http://www.w3.org/2005/Atom');
		$entry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gd', 'http://schemas.google.com/g/2005');
		$doc->appendChild($entry);

		$title = $doc->createElement('atom:title', $groupname);
		$title->setAttribute('type', 'text');
		$entry->appendChild($title);

		try {
			$ch = curl_init(GOOGLE_GROUPS_URL . '?alt=json&v=3.0&oauth_token=' . $this->access_token);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $doc->saveXML());
			curl_setopt($ch, CURLOPT_NOBODY, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/atom+xml',
				'Connection: Keep-Alive'
			));
			$entryResult = curl_exec($ch);

			if (FALSE === $entryResult)
				throw new Exception(curl_error($ch), curl_errno($ch));
		} catch (Exception $e) {
			trigger_error(sprintf(
				'Curl failed with error #%d: %s',
				$e->getCode(), $e->getMessage()),
				E_USER_ERROR);
		}

		$data = json_decode($entryResult);

		//herlaad groupFeed om de nieuw gemaakte daar ook in te hebben.
		$this->loadGroupFeed();

		return (string) $data->entry->id;
	}

	/**
	 * Een hele serie leden syncen naar google contacts.
	 *
	 * @param $leden array van uid's of Lid-objecten die moeten worden gesynced
	 *
	 * @return string met foutmeldingen en de namen van de gesyncte leden.
	 */
	public function syncLidBatch($leden) {
		//kan veel tijd kosten, dus time_limit naar 0 zodat het oneindig door kan gaan.
		set_time_limit(0);

		$profielBatch = array();
		foreach ($leden as $profiel) {
			if ($profiel instanceof Profiel) {
				$profielBatch[] = $profiel;
			} else {
				try {
					$profielBatch[] = ProfielModel::get($profiel);
				} catch (Exception $e) {
					// omit faulty/non-existant uid's
				}
			}
		}
		$message = '';

		//dit zou netjes kunnen door één xml-bestand te maken en dat één
		//keer te posten, maar daar heb ik nu even geen zin in.
		//btw: google heeft een batch-limit van 100 acties.
		//zie ook: http://code.google.com/apis/gdata/docs/batch.html
		foreach ($profielBatch as $profiel) {
			$message.=$this->syncLid($profiel) . ', ';
		}
		return $message;
	}

	/**
	 * Een enkel lid syncen naar Google contacts.
	 *
	 * @param $profiel Profiel
	 *
	 * @return string met foutmelding of naam van lid bij succes.
	 */
	public function syncLid(Profiel $profiel) {
		if (!$profiel instanceof Profiel) {
			$profiel = ProfielModel::get($profiel);
		}
		//kijk of het lid al bestaat in de googlecontacs-feed.
		$googleid = $this->existsInGoogleContacts($profiel);

		$error_message = '<div>Fout in Google-sync#%s: <br />' .
				'Lid: %s<br />Foutmelding: %s</div>';

		$doc = $this->createXML($profiel);

		if ($googleid != '') {
			try {
				//post to original entry's link[rel=self], set ETag in HTTP-headers for versioning
                $ch = curl_init($googleid);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $doc->saveXML());
                curl_setopt($ch, CURLOPT_NOBODY, false);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Authorization: Bearer ' . $this->access_token,
					'GData-Version: 3.0',
                    'Content-Type: application/atom+xml',
                    'Connection: Keep-Alive',
                    'If-None-Match: '.$this->getEtag($googleid)
                ));
                $entryResult = curl_exec($ch);

				return 'Update: ' . $profiel->getNaam() . ' ';
			} catch (Exception $e) {
				return sprintf($error_message, 'update', $profiel->getNaam(), $e->getMessage());
			}
		} else {
			try {
                try {
                    $ch = curl_init(GOOGLE_CONTACTS_URL);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $doc->saveXML());
                    curl_setopt($ch, CURLOPT_NOBODY, false);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
						'Authorization: Bearer ' . $this->access_token,
						'GData-Version: 3.0',
                        'Content-Type: application/atom+xml',
                        'Connection: Keep-Alive'
                    ));
                    $entryResult = curl_exec($ch);

                    if (FALSE === $entryResult)
                        throw new Exception(curl_error($ch), curl_errno($ch));
                } catch (Exception $e) {
                    trigger_error(sprintf(
                        'Curl failed with error #%d: %s',
                        $e->getCode(), $e->getMessage()),
                        E_USER_ERROR);
                }

				return 'Ingevoegd: ' . $profiel->getNaam() . ' ';
			} catch (Exception $e) {
				return sprintf($error_message, 'insert', $profiel->getNaam(), $e->getMessage());
			}
		}
	}

	/**
	 * Create a XML document for this Lid.
	 * @param $profiel create XML feed for this object
	 */
	private function createXML(Profiel $profiel) {

		$doc = new DOMDocument();
		$doc->formatOutput = true;
		$entry = $doc->createElement('atom:entry');
		$entry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:atom', 'http://www.w3.org/2005/Atom');
		$entry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gd', 'http://schemas.google.com/g/2005');
		$entry->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:gContact', 'http://schemas.google.com/contact/2008');
		$doc->appendChild($entry);

		// add name element
		$name = $doc->createElement('gd:name');
		$entry->appendChild($name);
		$fullName = $doc->createElement('gd:fullName', $profiel->getNaam());
		$name->appendChild($fullName);


		if ($this->extendedExport) {
			//nickname
			if ($profiel->nickname != '') {
				$nick = $doc->createElement('gContact:nickname', $profiel->nickname);
				$entry->appendChild($nick);
			}
			//duckname
			if ($profiel->duckname != '') {
				$duck = $doc->createElement('gContact:duckname', $profiel->duckname);
				$entry->appendChild($duck);
			}
			//initialen
			if ($profiel->voorletters != '') {
				$entry->appendChild($doc->createElement('gContact:initials', $profiel->voorletters));
			}
			//geslacht?
			$gender = $doc->createElement('gContact:gender');
			$gender->setAttribute('value', $profiel->geslacht == Geslacht::Man ? 'male' : 'female');
			//$entry->appendChild($gender);
		}

		//add home address
		if ($profiel->adres != '') {
			$address = $doc->createElement('gd:structuredPostalAddress');
			$address->setAttribute('primary', 'true');

			//only rel OR label (XOR) can (and must) be set
			$woonoord = $profiel->getWoonoord();
			if ($woonoord) {
				$house = $doc->createElement('gd:housename');
				$house->appendChild(new DOMText($woonoord->naam));
				$address->appendChild($house);
				$address->setAttribute('label', $woonoord->naam);
			} else {
				$address->setAttribute('rel', 'http://schemas.google.com/g/2005#home');
			}

			$address->appendChild($doc->createElement('gd:street', $profiel->adres));
			if ($profiel->postcode != '') {
				$address->appendChild($doc->createElement('gd:postcode', $profiel->postcode));
			}
			$address->appendChild($doc->createElement('gd:city', $profiel->woonplaats));
			if ($profiel->land != '') {
				$address->appendChild($doc->createElement('gd:country', $profiel->land));
			}
			$address->appendChild($doc->createElement('gd:formattedAddress', $profiel->getFormattedAddress()));
			$entry->appendChild($address);
		}

		if ($this->extendedExport) {
			//adres ouders toevoegen, alleen bij leden...
			if ($profiel->isLid() AND $profiel->o_adres != '' AND $profiel->adres != $profiel->o_adres) {
				$address = $doc->createElement('gd:structuredPostalAddress');
				//$address->setAttribute('rel', 'http://schemas.google.com/g/2005#other');
				$address->setAttribute('label', 'Ouders');

				$address->appendChild($doc->createElement('gd:street', $profiel->o_adres));
				if ($profiel->o_postcode != '') {
					$address->appendChild($doc->createElement('gd:postcode', $profiel->o_postcode));
				}
				$address->appendChild($doc->createElement('gd:city', $profiel->o_woonplaats));
				if ($profiel->o_land != '') {
					$address->appendChild($doc->createElement('gd:country', $profiel->o_land));
				}
				$address->appendChild($doc->createElement('gd:formattedAddress', $profiel->getFormattedAddressOuders()));
				$entry->appendChild($address);
			}
		}

		// add email element
		$email = $doc->createElement('gd:email');
		$email->setAttribute('address', $profiel->getPrimaryEmail());
		$email->setAttribute('rel', 'http://schemas.google.com/g/2005#home');
		$email->setAttribute('primary', 'true');
		$entry->appendChild($email);

		if ($this->extendedExport) {
			// add IM adresses.
			$ims = array(
				array('msn', 'http://schemas.google.com/g/2005#MSN'),
				array('skype', 'http://schemas.google.com/g/2005#SKYPE'),
				array('icq', 'http://schemas.google.com/g/2005#ICQ'),
				array('jid', 'http://schemas.google.com/g/2005#JABBER')
			);
			foreach ($ims as $im) {
				if ($profiel->$im[0] != '') {
					$imEntry = $doc->createElement('gd:im');
					$imEntry->setAttribute('address', $profiel->$im[0]);
					$imEntry->setAttribute('protocol', $im[1]);
					$imEntry->setAttribute('rel', 'http://schemas.google.com/g/2005#home');
					$entry->appendChild($imEntry);
				}
			}
		}

		//phone numbers
		$telefoons = array();

		//ouders nummer...
		if ($this->extendedExport && $profiel->isLid()) {
			$telefoons[] = array('o_telefoon', 'http://schemas.google.com/g/2005#other');
		}
		$telefoons[] = array('telefoon', 'http://schemas.google.com/g/2005#home');
		$telefoons[] = array('mobiel', 'http://schemas.google.com/g/2005#mobile');

		foreach ($telefoons as $telefoon) {
			if ($profiel->$telefoon[0] != '') {
				$number = $doc->createElement('gd:phoneNumber', internationalizePhonenumber($profiel->$telefoon[0]));
				if ($telefoon[0] == 'mobiel') {
					$number->setAttribute('primary', 'true');
				}
				if ($telefoon[0] == 'o_telefoon') {
					$number->setAttribute('label', 'Ouders');
				} else {
					$number->setAttribute('rel', $telefoon[1]);
				}
				$entry->appendChild($number);
			}
		}

		if ($profiel->gebdatum != '' AND $profiel->gebdatum != '0000-00-00') {
			$geboortedatum = $doc->createElement('gContact:birthday');
			$geboortedatum->setAttribute('when', $profiel->gebdatum);
			$entry->appendChild($geboortedatum);
		}

		if ($this->extendedExport) {
			if ($profiel->website != '') {
				$website = $doc->createElement('gContact:website');

				$website->setAttribute('href', $profiel->website);
				$website->setAttribute('rel', 'home');
				$entry->appendChild($website);
			}

			if ($profiel->eetwens != '') {
				$eetwens = $doc->createElement('gContact:userDefinedField');
				$eetwens->setAttribute('key', 'Eetwens');
				$eetwens->setAttribute('value', $profiel->eetwens);
				$entry->appendChild($eetwens);
			}
		}

		//system group 'my contacts' er bij, als die bestaat..
		if ($this->getSystemGroupId('Contacts') !== null) {
			$systemgroup = $doc->createElement('gContact:groupMembershipInfo');
			$systemgroup->setAttribute('href', $this->getSystemGroupId('Contacts'));
			$entry->appendChild($systemgroup);
		}

		//in de groep $this->groepname
		$group = $doc->createElement('gContact:groupMembershipInfo');
		$group->setAttribute('href', $this->groupid);
		$entry->appendChild($group);


		//last updated
		if (LoginModel::mag('P_ADMIN')) {
			$update = $doc->createElement('gContact:userDefinedField');
			$update->setAttribute('key', 'update');
			$update->setAttribute('value', getDateTime());
			$entry->appendChild($update);
		}

		//csr uid
		$uid = $doc->createElement('gContact:userDefinedField');
		$uid->setAttribute('key', 'csruid');
		$uid->setAttribute('value', $profiel->uid);
		$entry->appendChild($uid);

		return $doc;
	}

	public static function isAuthenticated() {
		return isset($_SESSION['google_token']);
	}

	/**
	 * Vraag een Authsub-token aan bij google, plaats bij ontvangen in _SESSION['google_token'].
	 */
	public static function doRequestToken($self) {
        $redirect_uri = CSR_ROOT . '/googlecallback';
        $client = new Google_Client();
        $client -> setApplicationName('Stek');
        $client -> setClientId(GOOGLE_CLIENT_ID);
        $client -> setClientSecret(GOOGLE_CLIENT_SECRET);
        $client -> setRedirectUri($redirect_uri);
        $client -> setAccessType('online');
        $client -> setScopes('https://www.google.com/m8/feeds');

        if (isset($_SESSION['google_token']) && isset($_SESSION['access_token'])) {
            // Result is NULL als de google_token gerevoked is door de gebruiker.
            $result = static::doGoogleRequest(GOOGLE_CONTACTS_URL, json_decode($_SESSION['access_token'])->access_token);

            if ($result === NULL) {
                unset($_SESSION['google_token'], $_SESSION['access_token']);
            }
		}

		if (!isset($_SESSION['google_token'])) {
            $googleImportUrl = $client -> createAuthUrl();
            header("HTTP/1.0 307 Temporary Redirect");
			header("Location: $googleImportUrl&state=$self");
			exit;
		}
	}
}
