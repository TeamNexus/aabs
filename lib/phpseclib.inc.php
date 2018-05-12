<?php
/*
 * Copyright (C) 2017 Lukas Berger <mail@lukasberger.at>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
 
define('PHPSECLIB_BASEDIR', AABS_BASEDIR . '/lib/phpseclib/vendor/phpseclib/phpseclib/phpseclib');

require_once PHPSECLIB_BASEDIR . "/System/SSH/Agent.php";
require_once PHPSECLIB_BASEDIR . "/System/SSH/Agent/Identity.php";

require_once PHPSECLIB_BASEDIR . "/Math/BigInteger.php";

require_once PHPSECLIB_BASEDIR . "/File/ANSI.php";
require_once PHPSECLIB_BASEDIR . "/File/ASN1.php";
require_once PHPSECLIB_BASEDIR . "/File/ASN1/Element.php";
require_once PHPSECLIB_BASEDIR . "/File/X509.php";

require_once PHPSECLIB_BASEDIR . "/Crypt/Base.php";
require_once PHPSECLIB_BASEDIR . "/Crypt/Rijndael.php";
require_once PHPSECLIB_BASEDIR . "/Crypt/AES.php";
require_once PHPSECLIB_BASEDIR . "/Crypt/Blowfish.php";
require_once PHPSECLIB_BASEDIR . "/Crypt/DES.php";
require_once PHPSECLIB_BASEDIR . "/Crypt/Hash.php";
require_once PHPSECLIB_BASEDIR . "/Crypt/RC2.php";
require_once PHPSECLIB_BASEDIR . "/Crypt/RSA.php";
require_once PHPSECLIB_BASEDIR . "/Crypt/Random.php";
require_once PHPSECLIB_BASEDIR . "/Crypt/RC4.php";
require_once PHPSECLIB_BASEDIR . "/Crypt/TripleDES.php";
require_once PHPSECLIB_BASEDIR . "/Crypt/Twofish.php";

require_once PHPSECLIB_BASEDIR . "/Net/SCP.php";
require_once PHPSECLIB_BASEDIR . "/Net/SSH1.php";
require_once PHPSECLIB_BASEDIR . "/Net/SSH2.php";
require_once PHPSECLIB_BASEDIR . "/Net/SFTP.php";
require_once PHPSECLIB_BASEDIR . "/Net/SFTP/Stream.php";
