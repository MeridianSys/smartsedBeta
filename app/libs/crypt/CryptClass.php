<?php
/**
 * @author Nicholas de Jong
 * @copyright Nicholas de Jong
 * @license BSD
 *
 **/
class CryptClass {

        /**
         * Enables / Disables compression
         *
         * @var bool
         */
        public $compression = TRUE;

        /**
         * Enables / Disables URL safe data encoding
         *
         * @var bool
         */
        public $url_safe = TRUE;

        /**
         * Enables / Disbles decrypt after encrypt with compare - useful in testing!
         *
         * @var bool
         */
        public $test_decrypt_before_return = FALSE;

        /**
         * The mcrypt setup
         *
         * @var array
         */
        public $mcrypt;

        /**
         * __construct()
         *
         * @param string $cipher
         * @param string $key
         * @param string $mode
         * @param string $iv
         */
        function __construct($cipher,$key,$mode,$iv) {

                $this->mcrypt['cipher'] = $cipher;
                $this->mcrypt['key'] = $key;
                $this->mcrypt['mode'] = $mode;
                $this->mcrypt['iv'] = $iv;

        }

        /**
         * encrypt()
         *
         * @param mixed $data
         * @return string
         */
        public function encrypt($data) {

                // Check mcrypt config looks complete -- we test here because a
                // user could change $this->mcrypt between calls
                $this->__checkMcryptConfig();

                // Return early if $data is empty
                if(empty($data)) { return $data; }

                // Make sure $data is cast as a JSON string if it is not an array
                if(is_string($data)) {
                        $encrypt_data = $data;
                } else {
                        $encrypt_data = json_encode($data);
                }

                // Compress if required
                if($this->compression) {
                        $encrypt_data = gzcompress($encrypt_data);
                }

                // Encrypt and base64 the data string
                $encrypted = base64_encode(mcrypt_encrypt(
                        $this->mcrypt['cipher'],
                        $this->mcrypt['key'],
                        $encrypt_data,
                        $this->mcrypt['mode'],
                        $this->mcrypt['iv']
                ));

                // Tweak the string to be url safe if required
                if($this->url_safe) {
                        $encrypted = strtr($encrypted,'+/=','-_,');
                }

                // Decrypt test if we need to
                if($this->test_decrypt_before_return) {

                        if($data != $this->decrypt($encrypted)) {

                                // Because it is possible for a JSON string itself to be passed such cases
                                if(json_decode($data,TRUE) != $this->decrypt($encrypted)) {
                                        throw new Exception('Unable to confirm encrypted data will match decrypted data!');
                                } else {
                                        return $encrypted;
                                }

                        } else {
                                return $encrypted;
                        }
                } else {
                        return $encrypted;
                }
        }

        /**
         * decrypt()
         *
         * @param string $data
         * @return mixed
         */
        public function decrypt($data) {
                // Check mcrypt config looks complete -- we test here because a
                // user could change $this->mcrypt between calls
                $this->__checkMcryptConfig();

                // Return early if $data is empty
                if(empty($data)) { return $data; }

                // Undo the url safe transform
                if($this->url_safe) {
                        $data = strtr($data,'-_,','+/=');
                }

                // base64 encode and encryption
                $data = mcrypt_decrypt(
                        $this->mcrypt['cipher'],
                        $this->mcrypt['key'],
                        base64_decode($data),
                        $this->mcrypt['mode'],
                        $this->mcrypt['iv']
                );

                // Uncompress if required - supress errors due to bad input data
                if($this->compression) {
                        $data = @gzuncompress($data);
                }

                // Attempt to JSON decode
                $json = json_decode($data,TRUE);
                if(is_array($json)) {
                        return $json;
                } else {
                        return $data;
                }
        }

        /**
         * __checkMcryptConfig
         *
         * @param array $mcrypt
         */
        private function __checkMcryptConfig() {

                // Make sure all the $mcrypt components are present
                if(!isset($this->mcrypt['cipher']) || empty($this->mcrypt['cipher'])) {
                        throw new Exception('Missing mcrypt cipher');
                }

                if(!isset($this->mcrypt['key']) || empty($this->mcrypt['key'])) {
                        throw new Exception('Missing mcrypt key');
                }

                if(!isset($this->mcrypt['mode']) || empty($this->mcrypt['mode'])) {
                        throw new Exception('Missing mcrypt mode');
                }

                if(!isset($this->mcrypt['iv'])) { // is optional, thus can be empty
                        throw new Exception('Missing mcrypt iv');
                }
        }
} 
