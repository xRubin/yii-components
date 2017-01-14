<?php

/**
 * Class CdnSkyparkComponent
 */
class CdnSkyparkComponent extends CApplicationComponent {

    /** @var SelectelStorage2 */
    protected $_storage;

    /** @var string */
    public $host;

    /** @var integer */
    public $port = 22;

    /** @var string */
    public $login;

    /** @var string */
    public $password;

    /** @var string */
    public $email;

    /** @var string */
    public $home;

    /** @var string */
    public $url;

    /** @var string */
    public $bucket;

    /** @var string */
    public $fingerprint;

    /**
     * @return SkyparkStorage
     */
    public function getStorage() {
        if (null === $this->_storage) {
            $this->_storage = new SkyparkStorage($this->host, $this->port, $this->login, $this->password, $this->fingerprint);
        }
        return $this->_storage;
    }
}

class SkyparkStorage {

    /** @var resource  */
    protected $connection;

    /** @var string  */
    public $home;

    /** @var string */
    public $container;

    public function __construct($host, $port, $login, $password, $fingerprint, $home = '/home/')
    {
        if (!($this->connection = ssh2_connect($host, $port))) {
            throw new Exception('Cannot connect to server');
        }
        $finger = ssh2_fingerprint($this->connection, SSH2_FINGERPRINT_MD5 | SSH2_FINGERPRINT_HEX);
        if (strcmp($fingerprint, $finger) !== 0) {
            throw new Exception('Unnknown fingerprint: ' . $finger);
        }
        if (!ssh2_auth_password($this->connection, $login, $password)) {
            throw new Exception('Autentication rejected by server');
        }
        $this->home = $home;
    }

    public function getContainer($container) {
        // quick hack
        $this->container = $container;
        return $this;
    }

    public function createDirectory($name) {
        printf("[%s] createDirectory %s\n", date("Y-m-d H:i:s"), $name);

        $sftp = ssh2_sftp($this->connection);

        return @ssh2_sftp_mkdir($sftp, $this->home . $this->container . $name, 0755, true);
    }

    public function putFile($localFileName, $remoteFileName)
    {
        printf("[%s] putFile %s %s\n", date("Y-m-d H:i:s"), $localFileName, $remoteFileName);

        var_dump($this->home . $this->container . $remoteFileName);

        // так не работает
        //if (!ssh2_scp_send($this->connection, $localFileName, $this->home . $this->container . $remoteFileName, 0644))
        //    throw new Exception('putFile failed');

        $sftp = ssh2_sftp($this->connection);

        $sftpStream = @fopen('ssh2.sftp://'.$sftp.$this->home . $this->container . $remoteFileName, 'w');

        try {

            if (!$sftpStream) {
                throw new Exception("Could not open remote file: $this->home $this->container $remoteFileName");
            }

            $data_to_send = @file_get_contents($localFileName);

            if ($data_to_send === false) {
                throw new Exception("Could not open local file: $localFileName.");
            }

            if (@fwrite($sftpStream, $data_to_send) === false) {
                throw new Exception("Could not send data from file: $localFileName.");
            }

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        } finally {
            fclose($sftpStream);
        }

        return true;
    }

    public function delete($name)
    {
        printf("[%s] delete %s\n", date("Y-m-d H:i:s"), $name);

        $sftp = ssh2_sftp($this->connection);

        return ssh2_sftp_unlink($sftp, $this->home . $this->container . $name);
    }

}
