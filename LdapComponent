<?php

class LdapComponent extends CComponent
{
    public $host;
    public $port;
    public $domain;
    public $user;
    public $password;

    public $pageSize = 100;

    protected $connection;

    public function init()
    {
        $this->connection = ldap_connect($this->host);
        ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($this->connection, LDAP_OPT_REFERRALS, 0);
        if (!$this->connection) {
            Yii::log('Нет соединения с ActiveDirectory; ', 'warning', 'ActiveDirectory');
            throw new CHttpException(500, 'Нет соединения с контроллером домена!');
        }
        $this->login();
    }

    public function login($user = '', $password = '')
    {
        if (!empty($user) && !empty($password)) {
            $this->user = $user;
            $this->password = $password;
        } else
          return false;
          
        try {
            $bind = @ldap_bind($this->connection, $this->domain . "\\" . $this->user, $this->password);
        } catch (Exception $e) {
            Yii::log($e->getMessage(), 'warning', 'ActiveDirectory');
            return false;
        }
        
        if (!$bind) {
            Yii::log('Неверный логин или пароль для пользователя ' . $this->user, 'info', 'auth');
            return false;
        } else 
          return true;
    }

    /**
     * Получаем список всех пользователей со всеми параметрами
     * @param string $dn
     * @return array
     */
    public function getAllUsers($dn)
    {
        $result = array();
        $cookie = '';
        do {
            ldap_control_paged_result($this->connection, $this->pageSize, true, $cookie);

            $res  = ldap_search($this->connection, $dn, "cn=*");
            $entries = ldap_get_entries($this->connection, $res);

            foreach ($entries as $e) 
                if (is_array($e)) 
                    $result[] = $e;

            ldap_control_paged_result_response($this->connection, $res, $cookie);
        } while ($cookie !== null && $cookie != '');

        return $result;
    }
}
