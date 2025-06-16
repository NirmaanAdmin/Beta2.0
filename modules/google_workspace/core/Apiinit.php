<?php

namespace modules\google_workspace\core;

require_once __DIR__.'/../third_party/node.php';
require_once __DIR__.'/../vendor/autoload.php';
use Firebase\JWT\JWT as Google_Workspace_JWT;
use Firebase\JWT\Key as Google_Workspace_Key;
use WpOrg\Requests\Requests as Google_Workspace_Requests;

class Apiinit
{
    public static function the_da_vinci_code($module_name)
    {
        $module = get_instance()->app_modules->get($module_name);

        // get verfication id form option table
        $verification_id = !empty(get_option($module_name . '_verification_id')) ? base64_decode(get_option($module_name . '_verification_id')) : '';

        // get token form option table
        $token = get_option($module_name . '_product_token');

        $id_data         = explode('|', $verification_id);
        $verified        = !((empty($verification_id)) || (4 != \count($id_data)));

        if (4 === \count($id_data)) {
            $verified = !empty($token);
            try {
                $data = Google_Workspace_JWT::decode($token, new Google_Workspace_Key($id_data[3], 'HS512'));
                if (!empty($data)) {
                    $verified = basename($module['headers']['uri']) == $data->item_id && $data->item_id == $id_data[0] && $data->buyer == $id_data[2] && $data->purchase_code == $id_data[3];
                }
            } catch (Exception $e) {
                $verified = false;
            }

            $last_verification = (int) get_option($module_name . '_last_verification');
            $seconds           = $data->check_interval ?? 0;

            if (!empty($seconds) && time() > ($last_verification + $seconds)) {
                $verified = false;
                try {
                    $request = Google_Workspace_Requests::post(VAL_PROD_POINT, ['Accept' => 'application/json', 'Authorization' => $token], json_encode(['verification_id' => $verification_id, 'item_id' => basename($module['headers']['uri']), 'activated_domain' => base_url()]));
                    $status  = $request->status_code;
                    if ((500 <= $status && $status <= 599) || 404 == $status) {
                        update_option($module_name . '_heartbeat', base64_encode(json_encode(['status' => $status, 'id' => $token, 'end_point' => VAL_PROD_POINT])));
                        $verified = false;
                    } else {
                        $result   = json_decode($request->body);
                        $verified = !empty($result->valid);
                        if ($verified) {
                            delete_option($module_name . '_heartbeat');
                        }
                    }
                } catch (Exception $e) {
                    $verified = false;
                }
                update_option($module_name . '_last_verification', time());
            }
        }

        if (!$verified) {
            get_instance()->app_modules->deactivate($module_name);
        }

        return $verified;
    }

    /**
     * [ease_of_mind checkes that if functions are comented or removed then it will disable module].
     *
     * @param  [string] $module_name [module name]
     *
     * @return [void]              [delete specific module]
     */
    public static function ease_of_mind($module_name)
    {
        if (!\function_exists($module_name . '_actLib') || !\function_exists($module_name . '_sidecheck') || !\function_exists($module_name . '_deregister')) {
            get_instance()->app_modules->deactivate($module_name);
        }
    }

    /**
     * [activate module activatation screen that will load activate.php view].
     *
     * @param  [string] $module [modulename]
     *
     * @return [void]         [loads activate view]
     */
    public static function activate($module)
    {
        if (!option_exists($module['system_name'] . '_verification_id') && empty(get_option($module['system_name'] . '_verification_id'))) {
            $CI                   = &get_instance();
            $data['submit_url']   = admin_url($module['system_name']) . '/env_ver/activate';
            $data['original_url'] = admin_url('modules/activate/' . $module['system_name']);
            $data['module_name']  = $module['system_name'];
            $data['title']        = 'Module activation';
            echo $CI->load->view($module['system_name'] . '/activate', $data, true);
            exit;
        }
    }

    /**
     * [getUserIP get server ip evev server is behind reverse proxy ].
     *
     * @return [string] [it will return ip address]
     */
    public static function getUserIP()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

    /**
     * [it will check entered purchased code with all installed module and confirm same purchase code is being used for multiple modules].
     *
     * @param  [string] $module_name [module name]
     * @param string $code [purchase code]
     *
     * @return [array]              [array message]
     */
    public static function pre_validate($module_name, $code = '')
    {
        get_instance()->load->library('google_workspace_aeiou');
        $module = get_instance()->app_modules->get($module_name);
        if (empty($code)) {
            return ['status' => false, 'message' => 'Purchase key is required'];
        }
        $all_activated = get_instance()->app_modules->get_activated();
        foreach ($all_activated as $active_module => $value) {
            $verification_id =  get_option($active_module.'_verification_id');
            if (!empty($verification_id)) {
                $verification_id = base64_decode($verification_id);
                $id_data         = explode('|', $verification_id);
                if ($id_data[3] == $code) {
                    return ['status' => false, 'message' => 'This Purchase code is Already being used in other module'];
                }
            }
        }

        $envato_res = get_instance()->google_workspace_aeiou->getPurchaseData($code);

        if (empty($envato_res) || !\is_object($envato_res) || isset($envato_res->error) || !isset($envato_res->sold_at)) {
            return ['status' => false, 'message' => 'Something went wrong'];
        }

        // basename($module['headers']['uri'] means itemid
        if (basename($module['headers']['uri']) != $envato_res->item->id) {
            return ['status' => false, 'message' => 'Purchase key is not valid'];
        }
        get_instance()->load->library('user_agent');
        $data['user_agent']       = get_instance()->agent->browser().' '.get_instance()->agent->version();
        $data['activated_domain'] = base_url();
        $data['requested_at']     = date('Y-m-d H:i:s');
        $data['ip']               = self::getUserIP();
        $data['os']               = get_instance()->agent->platform();
        $data['purchase_code']    = $code;
        $data['envato_res']       = $envato_res;
        $data                     = json_encode($data);

        try {
            /**
             * [$request send request on backend server and register on admin panel].
             *
             * @var [array]
             */
            $response = Google_Workspace_Requests::post(REG_PROD_POINT, ['Accept' => 'application/json'], $data);

            /*
             *  if status code is greater then 500 and 404 that means problem in backend server
             *  in this case give one week extenstion and allow user to user the module
             *  also add _heartbeat entry with status code, endpoint, and purchase code
             */
            if ($response->status_code >= 500 || 404 == $response->status_code) {
                update_option($module_name . '_verification_id', '');
                update_option($module_name . '_last_verification', time());
                update_option($module_name . '_heartbeat', base64_encode(json_encode(['status' => $response->status_code, 'id' => $code, 'end_point' => REG_PROD_POINT])));

                return ['status' => true];
            }

            // if we have valid response then convert to array
            $response = json_decode($response->body);

            // response status is not 200 that means something is wrong, return response message
            if (200 != $response->status) {
                return ['status' => false, 'message' => $response->message];
            }
            $return = $response->data ?? [];

            // if $return is not empty that means we ahve valid and correct data. now let's update values in options table
            if (!empty($return)) {
                update_option($module_name . '_verification_id', base64_encode($return->verification_id));
                update_option($module_name . '_last_verification', time());
                update_option($module_name . '_product_token', $return->token);
                delete_option($module_name . '_heartbeat');

                return ['status' => true];
            }

            // if there any error then give one week extension to buyer
        } catch (Exception $e) {
            update_option($module_name . '_verification_id', '');
            update_option($module_name . '_last_verification', time());
            update_option($module_name . '_heartbeat', base64_encode(json_encode(['status' => $request->status_code, 'id' => $code, 'end_point' => REG_PROD_POINT])));

            return ['status' => true];
        }

        return ['status' => false, 'message' => 'Something went wrong'];
    }
}