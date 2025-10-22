<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Wialon extends Model
{
    use HasFactory;

    /// PROPERTIES
    private $sid = null;
    private $base_api_url = '';
    private $default_params = array();

    /// METHODS

    /** constructor */
    function __construct($scheme = 'http', $host = 'hst-api.wialon.com', $port = '', $sid = '', $extra_params = array())
    {
        $this->sid = '';
        $this->default_params = array_replace(array(), (array)$extra_params);
        $this->base_api_url = sprintf('%s://%s%s/wialon/ajax.html?', $scheme, $host, mb_strlen($port) > 0 ? ':' . $port : '');
    }

    /** sid setter */
    function set_sid($sid)
    {
        $this->sid = $sid;
    }

    /** sid getter */
    function get_sid()
    {
        return $this->sid;
    }

    /** update extra parameters */
    public function update_extra_params($extra_params)
    {
        $this->default_params = array_replace($this->default_params, $extra_params);
    }

    /** RemoteAPI request performer
     * action - RemoteAPI command name
     * args - JSON string with request parameters
     */
    public function call($action, $args)
    {

        $url = $this->base_api_url;

        if (stripos($action, 'unit_group') === 0) {
            $svc = $action;
            $svc[mb_strlen('unit_group')] = '/';
        } else {
            $svc = preg_replace('\'_\'', '/', $action, 1);
        }

        $params = array(
            'svc' => $svc,
            'params' => $args,
            'sid' => $this->sid
        );
        $all_params = array_replace($this->default_params, $params);
        $str = '';
        foreach ($all_params as $k => $v) {
            if (mb_strlen($str) > 0)
                $str .= '&';
            $str .= $k . '=' . urlencode(is_object($v) || is_array($v) ? json_encode($v) : $v);
        }
        /* cUrl magic */
        $ch = curl_init();
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $str
        );
        curl_setopt_array($ch, $options);

        $result = curl_exec($ch);

        if ($result === FALSE)
            $result = '{"error":-1,"message":' . curl_error($ch) . '}';

        curl_close($ch);
        return $result;
    }

    /** Login
     * user - wialon username
     * password - password
     * return - server response
     */

    public function login($token)
    {
        $url = 'https://hst-api.wialon.com/wialon/ajax.html?svc=token/login';

        try {
            $response = Http::asForm()->post($url, [
                'params' => json_encode(['token' => $token]),
            ]);

            $json_result = $response->json();

            if ($response->successful()) {
                if (isset($json_result['eid'])) {
                    $this->sid = $json_result['eid'];
                }
                return $json_result;
            }

            throw new Exception('Помилка API: ' . ($json_result['error'] ?? 'Невідома помилка'));
        } catch (Exception $e) {
            throw new Exception('Помилка запиту: ' . $e->getMessage());
        }
    }

    /** Logout
     * return - server responce
     */
    public function logout()
    {
        $result = $this->core_logout();
        $json_result = json_decode($result, true);
        if ($json_result && $json_result['error'] == 0)
            $this->sid = '';
        return $result;
    }

    /** Unknonwn methods hadler */
    public function __call($name, $args)
    {
        return $this->call($name, count($args) === 0 ? '{}' : $args[0]);
    }
}
