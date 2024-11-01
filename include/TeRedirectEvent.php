<?php


class TeRedirectEvent
{
    public function redirect()
    {
        $redirects = get_option('te-redirect', true);
        $userrequest = rtrim(str_ireplace(get_option('home'), '', $this->get_address()), '/');

        if (!empty($redirects) && is_array($redirects)) {
            $do_redirect = '';

            foreach ($redirects as $key => $redirect) {
                if (true && strpos($redirect['from'], '*') !== false) {
                    if (strpos($userrequest, '/wp-login') !== 0 && strpos($userrequest, '/wp-admin') !== 0) {
                        $redirect['from'] = str_replace('*', '(.*)', $redirect['from']);
                        $pattern = '/^' . str_replace('/', '\/', rtrim($redirect['from'], '/')) . '/';
                        $redirect['to'] = str_replace('*', '$1', $redirect['to']);
                        $output = preg_replace($pattern, $redirect['to'], $userrequest);
                        if ($output !== $userrequest) {
                            $do_redirect = $output;
                        }
                    }
                } elseif (urldecode($userrequest) == rtrim($redirect['from'], '/')) {
                    $do_redirect = $redirect['to'];
                }

                if ($do_redirect !== '' && trim($do_redirect, '/') !== trim($userrequest, '/')) {
                    if (strpos($do_redirect, '/') === 0) {
                        $do_redirect = home_url() . $do_redirect;
                    }
                    header('HTTP/1.1 301 Moved Permanently');
                    header('Location: ' . $do_redirect);
                    exit();
                } else {
                    unset($redirects);
                }
            }
        }
    }


    public function get_address()
    {
        return $this->get_protocol() . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    public function get_protocol()
    {
        $protocol = 'http';
        if (isset($_SERVER["HTTPS"]) && strtolower($_SERVER["HTTPS"]) == "on") {
            $protocol .= "s";
        }

        return $protocol;
    }
}