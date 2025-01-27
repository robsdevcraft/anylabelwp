<?php
namespace AnylabelWP;

/**
 * General Settings class for AnylabelWP
 */
class AnylabelWP_General_Settings
{
    /**
     * Constructor: Register the settings and hooks for rendering
     */
    public function __construct()
    {
        add_action('admin_init', [$this, 'register_settings']);
        add_action('anylabelwp_render_general_settings', [$this, 'render_settings']);
    }

    /**
     * Register settings related to the "General" tab
     */
    public function register_settings()
    {
        register_setting(
            'anylabelwp_general',
            'anylabelwp_allowed_roles',
            [
                'type'              => 'array',
                'sanitize_callback' => [$this, 'sanitize_roles'],
                'default'           => [],
            ]
        );
    }

    /**
     * Sanitize the roles array, ensuring only valid roles are stored
     *
     * @param mixed $roles_input
     * @return array
     */
    public function sanitize_roles($roles_input)
    {
        if (!is_array($roles_input)) {
            return [];
        }
        $editable_roles = array_keys(get_editable_roles());
        $sanitized = [];
        foreach ($roles_input as $role) {
            if (in_array($role, $editable_roles, true)) {
                $sanitized[] = $role;
            }
        }
        return $sanitized;
    }

    /**
     * Render the General tab content in admin-settings.php
     * Showing multi-select regardless of the user's role so you can confirm it appears.
     */
    public function render_settings()
    {
        // Removed the manage_options check for debugging.
        wp_nonce_field('anylabelwp_settings', 'anylabelwp_settings_nonce');
        $allowed_roles = get_option('anylabelwp_allowed_roles', []);
        $all_roles = get_editable_roles(); // returns role_key => [details]
        ?>
        <div class="tab-content general-settings">
            <h2><?php echo esc_html__('General Settings', 'anylabelwp-plugin'); ?></h2>
            <p><?php echo esc_html__('Select the WordPress roles that can view and manage AnylabelWP.', 'anylabelwp-plugin'); ?></p>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php echo esc_html__('Allowed Roles', 'anylabelwp-plugin'); ?></th>
                    <td>
                        <select name="anylabelwp_allowed_roles[]" multiple style="min-width:200px;">
                            <?php
                            foreach ($all_roles as $role_key => $role_data) {
                                $selected = in_array($role_key, $allowed_roles, true) ? 'selected="selected"' : '';
                                echo '<option value="' . esc_attr($role_key) . '" ' . $selected . '>';
                                echo esc_html($role_data['name']);
                                echo '</option>';
                            }
                            ?>
                        </select>
                        <p class="description">
                            <?php echo esc_html__('Users in these roles can see the AnylabelWP menu and configure settings.', 'anylabelwp-plugin'); ?>
                        </p>
                    </td>
                </tr>
            </table>
        </div>
        <?php
    }
}
