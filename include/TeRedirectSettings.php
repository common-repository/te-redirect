<?php


class TeRedirectSettings
{
    public function add_menu()
    {
        add_menu_page(
            __('301 Redirect settings', 'te-redirect'),
            __('301 Redirect', 'te-redirect'),
            apply_filters('te_redirect_menu_access', 'manage_options'),
            'te-redirect',
            [$this, 'get_page'],
            '',
            85
        );
    }

    public function register_settings()
    {
        register_setting('te-redirect-group', 'te-redirect-name', [$this, 'validation']);
    }

    public function validation($data)
    {
        return $data;
    }

    public function save_settings()
    {
        $post = $_POST;

        if (!$this->is_save($post)) {
            return;
        }

        update_option('te-redirect', $this->normalize_item($post));
    }


    public function normalize_item($post)
    {
        $redirects = array_map(function ($from, $to) {
            return ['from' => trim(esc_url_raw($from)), 'to' => trim(esc_url_raw($to))];
        }, $post['from'], $post['to']);

        $redirects = array_filter($redirects, function ($item) {
            return !empty($item) && !empty($item['from']) && !empty($item['to']);
        });

        return $redirects;
    }


    public function is_save($post)
    {
        if (empty($post['_te_nonce'])) {
            return false;
        }

        if (!wp_verify_nonce($post['_te_nonce'], 'te-301redirect-nonce')) {
            return false;
        }

        if (empty($post['option_page']) && $post['option_page'] !== 'te-redirect-group') {
            return false;
        }

        if (empty($post['from']) || empty($post['to'])) {
            return false;
        }

        return true;
    }

    public function get_page()
    {
        ?>
        <div class="wrap">
            <h1><?php _e('301 Redirect', 'te-redirect'); ?></h1>
            <div class="te-redirect-container">
                <div class="te-redirect-processing">
                    <div class="te-redirect-processing-text"><?php _e('processing', 'te-redirect'); ?></div>
                </div>
                <form id="te-redirect" method="post" action="options.php">
                    <?php
                    settings_fields('te-redirect-group');
                    do_settings_sections('te-redirect-group');
                    $redirects = get_option('te-redirect');
                    ?>
                    <div class="te-redirect-list">
                        <?php
                        if (!empty($redirects)) {
                            foreach ($redirects as $key => $redirect) {
                                ?>
                                <div class="te-redirect-item">
                                    <input class="item-from" name="from[]" type="text"
                                           value="<?= esc_url_raw($redirect['from']) ?>"
                                           placeholder="<?php _e('from', 'te-redirect'); ?>">
                                    -> <input class="item-to" name="to[]" type="text"
                                              value="<?= esc_url_raw($redirect['to']) ?>"
                                              placeholder="<?php _e('to', 'te-redirect'); ?>">
                                    <a class="te-redirect-item-remove"
                                       href="#"><?php _e('remove', 'te-redirect'); ?></a>
                                    <a class="te-redirect-item-add" href="#"><?php _e('Add item', 'te-redirect'); ?></a>
                                </div>
                                <?php
                            }
                        }
                        ?>
                        <div class="te-redirect-item">
                            <input class="item-from" name="from[]" type="text" value=""
                                   placeholder="<?php _e('from', 'te-redirect'); ?>">
                            -> <input class="item-to" name="to[]" type="text" value=""
                                      placeholder="<?php _e('to', 'te-redirect'); ?>">
                            <a class="te-redirect-item-remove" href="#"><?php _e('remove', 'te-redirect'); ?></a>
                            <a class="te-redirect-item-add" href="#"><?php _e('Add item', 'te-redirect'); ?></a>
                        </div>
                    </div>
                    <div class="te-error"></div>
                    <input type="hidden" name="_te_nonce"
                           value="<?php echo wp_create_nonce('te-301redirect-nonce'); ?>">
                    <?php submit_button(); ?>
                </form>
            </div>
        </div>
        <?php
    }
}