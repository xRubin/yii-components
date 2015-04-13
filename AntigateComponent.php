class Antigate
{

    public $key = 'key here';

    public static $errors = array(
        'ERROR_WRONG_USER_KEY' => 'неправильный формат ключа учетной записи (длина не равняется 32 байтам)',
        'ERROR_KEY_DOES_NOT_EXIST' => 'вы использовали неверный captcha ключ в запросе',
        'ERROR_ZERO_BALANCE' => 'нулевой либо отрицательный баланс',
        'ERROR_NO_SLOT_AVAILABLE' => 'нет свободных работников в данный момент, попробуйте позже либо повысьте свою максимальную ставку',
        'ERROR_ZERO_CAPTCHA_FILESIZE' => 'размер капчи которую вы загружаете менее 100 байт',
        'ERROR_TOO_BIG_CAPTCHA_FILESIZE' => 'ваша капча имеет размер более 100 килобайт',
        'ERROR_WRONG_FILE_EXTENSION' => 'ваша капча имеет неверное расширение, допустимые расширения jpg, jpeg, gif, png',
        'ERROR_IMAGE_TYPE_NOT_SUPPORTED' => 'Невозможно определить тип файла капчи, принимаются только форматы JPG, GIF, PNG',
        'ERROR_IP_NOT_ALLOWED' => 'Запрос с этого IP адреса с текущим ключом отклонен. Пожалуйста смотрите раздел управления доступом по IP',
    );

    public static function getStatusPath($captcha_id)
    {
        return sprintf('http://antigate.com/res.php?key=%s&action=get&id=%s', $this->key, $captcha_id);
    }

    public static function getUploadPath()
    {
        return 'http://antigate.com/in.php';
    }

    public static function getResultPath()
    {
        return 'http://antigate.com/res.php';
    }

    public static function getBalance()
    {
        return sprintf('http://antigate.com/res.php?key=%s&action=getbalance', $this->key);
    }

    public static function uploadImg($captcha, array $params)
    {

        $postdata = array_merge(
            $params,
            array(
                'method' => 'base64',
                'key' => $this->key,
                'body' => base64_encode($captcha),
                'ext' => 'jpg'
            )
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::getUploadPath());
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $rev = curl_exec($ch);
        curl_close($ch);

        if (substr($rev, 0, 2) == 'OK') {
            $anti_captcha_id = substr($rev, 3);
            echo "anti_id: { $anti_captcha_id}";
            return $anti_captcha_id;
        } else {
            echo "Upload ERROR";
            return false;
        }
    }

    public static function resolveCaptcha($captcha_id)
    {

        sleep(10);

        $postdata = array(
            'key' => $this->key,
            'action' => 'get',
            'id' => $captcha_id
        );

        $i = 0;
        do {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::getResultPath());
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $cap = curl_exec($ch);
            curl_close($ch);

            if (substr($cap, 0, 2) == 'OK') {
                $captcha_value = substr($cap, 3);
                echo "captcha value: { $captcha_value}";
                return $captcha_value;
            } else {
                echo "ERROR";
            }

            sleep(5);

        } while ($i++ < 20);
    }
}
