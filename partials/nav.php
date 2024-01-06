<ul class="tabbed_section_nav_wrap">
    <li class="tabbed_section_nav <?= ($_GET['tab'] == "" || $_GET['tab'] == "settings") ? 'tabbed_section_nav_active' : '' ?>">
        <a href="addonmodules.php?module=sms_net_bd&tab=settings">
            <i class="fas fa-cogs always"></i>
            <span>Settings</span>
        </a>
    </li>
    <li class="tabbed_section_nav <?= $_GET['tab'] == "client_templates" ? 'tabbed_section_nav_active' : '' ?>">
        <a href="addonmodules.php?module=sms_net_bd&tab=client_templates">
            <i class="fas fa-user"></i> <span>Client Templates</span>
        </a>
    </li>
    <li class="tabbed_section_nav <?= $_GET['tab'] == "admin_templates" ? 'tabbed_section_nav_active' : '' ?>">
        <a href="addonmodules.php?module=sms_net_bd&tab=admin_templates">
            <i class="fas fa-user-shield"></i> <span>Admin Templates</span>
        </a>
    </li>
    <li class="tabbed_section_nav <?= $_GET['tab'] == "send_sms" ? 'tabbed_section_nav_active' : '' ?>">
        <a href="addonmodules.php?module=sms_net_bd&tab=send_sms">
            <i class="fas fa-envelope"></i> <span>Send Sms</span>
        </a>
    </li>
    <li class="tabbed_section_nav <?= $_GET['tab'] == "sent_sms" ? 'tabbed_section_nav_active' : '' ?>">
        <a href="addonmodules.php?module=sms_net_bd&tab=sent_sms">
            <i class="fas fa-list"></i> <span>Sms Logs</span>
        </a>
    </li>
</ul>