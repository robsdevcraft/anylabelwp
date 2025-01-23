<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    
    <h2 class="nav-tab-wrapper">
        <a href="?page=anylabelwp-settings&tab=smtp" class="nav-tab <?php echo empty($_GET['tab']) || $_GET['tab'] === 'smtp' ? 'nav-tab-active' : ''; ?>">Fluent SMTP</a>
        <a href="?page=anylabelwp-settings&tab=forms" class="nav-tab <?php echo isset($_GET['tab']) && $_GET['tab'] === 'forms' ? 'nav-tab-active' : ''; ?>">Fluent Forms</a>
        <a href="?page=anylabelwp-settings&tab=crm" class="nav-tab <?php echo isset($_GET['tab']) && $_GET['tab'] === 'crm' ? 'nav-tab-active' : ''; ?>">Fluent CRM</a>
    </h2>

    <?php
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'smtp';
    ?>

    <form action="options.php" method="post">
        <?php settings_fields( 'anylabelwp_settings' ); ?>
        
        <?php if ($active_tab === 'smtp'): ?>
            <div class="tab-content smtp-settings">
                <?php
                do_settings_sections( 'anylabelwp_settings' );
                do_action( 'anylabelwp_render_module_settings' );
                ?>
            </div>
        <?php elseif ($active_tab === 'forms'): ?>
            <div class="tab-content forms-settings">
                <p>Fluent Forms settings go here</p>
            </div>
        <?php elseif ($active_tab === 'crm'): ?>
            <div class="tab-content crm-settings">
                <p>Fluent CRM settings go here</p>
            </div>
        <?php endif; ?>

        <?php wp_nonce_field( 'anylabelwp_settings', 'anylabelwp_settings_nonce' ); ?>
        <?php submit_button( 'Save Changes' ); ?>
    </form>
</div>

<style>
.tab-content {
    margin-top: 20px;
}
</style>
